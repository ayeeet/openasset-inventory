#!/bin/sh
# Docker entrypoint script
# This script handles initialization and service orchestration

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

log_error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" >&2
}

# Check required environment variables
check_env() {
    required_vars="APP_KEY DB_HOST DB_DATABASE DB_USERNAME DB_PASSWORD"
    
    for var in $required_vars; do
        if [ -z "$(eval echo \$$var)" ]; then
            log_error "ERROR: Required environment variable $var is not set"
            return 1
        fi
    done
    return 0
}

# Wait for database to be ready
wait_for_db() {
    log_info "Waiting for database to be ready..."
    max_attempts=30
    attempt=0
    
    while [ $attempt -lt $max_attempts ]; do
        if php -r "
            \$mysqli = new mysqli('$DB_HOST', '$DB_USERNAME', '$DB_PASSWORD', '$DB_DATABASE');
            if (\$mysqli->connect_error) {
                exit(1);
            }
            exit(0);
        " 2>/dev/null; then
            log_info "Database is ready!"
            return 0
        fi
        
        attempt=$((attempt + 1))
        echo "Database connection attempt $attempt/$max_attempts..."
        sleep 2
    done
    
    log_error "Database is not ready after $max_attempts attempts"
    return 1
}

# Run database migrations
run_migrations() {
    log_info "Running database migrations..."
    
    if php artisan migrate --force --no-interaction; then
        log_info "Migrations completed successfully"
        return 0
    else
        log_error "Migration failed"
        return 1
    fi
}

# Optimize application for production
optimize_production() {
    if [ "$APP_ENV" = "production" ]; then
        log_info "Optimizing application for production..."
        
        php artisan config:cache --no-interaction || log_warn "config:cache failed (may be normal if using env())"
        php artisan event:cache --no-interaction || log_warn "event:cache skipped"
        php artisan route:cache --no-interaction || log_warn "route:cache failed"
        
        log_info "Production optimization completed"
    fi
}

# Main execution
main() {
    log_info "Starting application initialization..."
    log_info "Environment: $APP_ENV"
    log_info "Debug mode: $APP_DEBUG"
    
    # Check environment variables
    if ! check_env; then
        log_error "Environment validation failed"
        exit 1
    fi
    
    # Wait for database
    if ! wait_for_db; then
        log_error "Database connection failed"
        exit 1
    fi
    
    # Run migrations
    if ! run_migrations; then
        log_error "Failed to run migrations"
        exit 1
    fi
    
    # Optimize for production
    optimize_production
    
    log_info "Application initialization completed"
    log_info "Starting services with supervisord..."
    log_info "=================================="
    
    # Start supervisord
    exec supervisord -c /etc/supervisord.conf
}

# Trap signals for graceful shutdown
trap 'log_info "Received SIGTERM, shutting down gracefully..."; kill -TERM "$PID"' SIGTERM
trap 'log_info "Received SIGINT, shutting down gracefully..."; kill -INT "$PID"' SIGINT

# Run main function
main "$@" &
PID=$!

# Wait for process to exit
wait $PID
