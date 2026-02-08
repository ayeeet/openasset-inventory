@echo off
REM Docker deployment script for Windows
REM Usage: deploy.bat [environment] [version]

setlocal enabledelayedexpansion

set ENVIRONMENT=%1
set VERSION=%2

if "%ENVIRONMENT%"=="" set ENVIRONMENT=production
if "%VERSION%"=="" set VERSION=latest

cls
echo ==================================
echo Asset Inventory Docker Deployment
echo ==================================
echo Environment: %ENVIRONMENT%
echo Version: %VERSION%
echo.

REM Check if .env exists
if not exist ".env" (
    echo ERROR: .env file not found!
    echo Please copy .env.docker to .env and update with your values
    pause
    exit /b 1
)

REM Check if Docker is installed
where docker >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Docker is not installed or not in PATH
    pause
    exit /b 1
)

where docker-compose >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Docker Compose is not installed or not in PATH
    pause
    exit /b 1
)

echo ^✓ Docker and Docker Compose are installed
echo.

REM Select compose file
if "%ENVIRONMENT%"=="production" (
    set COMPOSE_FILE=docker-compose.prod.yml
) else (
    set COMPOSE_FILE=docker-compose.yml
)

if not exist "%COMPOSE_FILE%" (
    echo ERROR: %COMPOSE_FILE% not found!
    pause
    exit /b 1
)

echo Using: %COMPOSE_FILE%
echo.

REM Pull latest images
echo Pulling latest images...
docker-compose -f %COMPOSE_FILE% pull
if %ERRORLEVEL% NEQ 0 goto error

REM Stop existing containers
echo Stopping existing containers...
docker-compose -f %COMPOSE_FILE% down

REM Build images
echo Building Docker images...
docker-compose -f %COMPOSE_FILE% build
if %ERRORLEVEL% NEQ 0 goto error

REM Start services
echo Starting services...
docker-compose -f %COMPOSE_FILE% up -d
if %ERRORLEVEL% NEQ 0 goto error

REM Wait for services
echo Waiting for services to be ready...
timeout /t 10 /nobreak

REM Run migrations
echo Running database migrations...
docker-compose -f %COMPOSE_FILE% exec -T app php artisan migrate --force
if %ERRORLEVEL% NEQ 0 goto error

REM Clear cache
echo Clearing application cache...
docker-compose -f %COMPOSE_FILE% exec -T app php artisan cache:clear

REM Optimize for production
if "%ENVIRONMENT%"=="production" (
    echo Optimizing for production...
    docker-compose -f %COMPOSE_FILE% exec -T app php artisan config:cache
    docker-compose -f %COMPOSE_FILE% exec -T app php artisan event:cache
    docker-compose -f %COMPOSE_FILE% exec -T app php artisan route:cache
)

REM Health check
echo Running health checks...
docker-compose -f %COMPOSE_FILE% ps

echo.
echo ==================================
echo ^✓ Deployment completed successfully!
echo ==================================
echo.
echo Useful commands:
echo   - View logs: docker-compose -f %COMPOSE_FILE% logs -f
echo   - Run artisan: docker-compose -f %COMPOSE_FILE% exec app php artisan ^<command^>
echo   - Stop all: docker-compose -f %COMPOSE_FILE% down
echo.
pause
exit /b 0

:error
echo.
echo ERROR: Deployment failed!
echo.
pause
exit /b 1
