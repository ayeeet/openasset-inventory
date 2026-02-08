<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'role',
        'is_active',
        'accessible_modules',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'accessible_modules' => 'array',
        ];
    }
    
    /**
     * Check if user has access to a specific module.
     */
    public function hasAccess(string $module): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (empty($this->accessible_modules)) {
            return false;
        }

        return in_array($module, $this->accessible_modules);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'assigned_to_user_id');
    }

    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    /**
     * Whether the user is an employee (read-only in modules they have access to).
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /**
     * Whether the user can create, update, or delete in modules (admin and manager only).
     * Employees with module access have read-only access.
     */
    public function hasWriteAccess(): bool
    {
        return ! $this->isEmployee();
    }

    /**
     * Route name to use as "home" for this user. Employees go to their first module, not dashboard.
     */
    public function homeRouteName(): string
    {
        if (! $this->isEmployee()) {
            return 'dashboard';
        }

        $order = ['assets', 'organization', 'people', 'resources'];
        foreach ($order as $module) {
            if ($this->hasAccess($module)) {
                return match ($module) {
                    'assets' => 'assets.index',
                    'organization' => 'departments.index',
                    'people' => 'users.index',
                    'resources' => 'resources.index',
                    default => 'dashboard',
                };
            }
        }

        return 'dashboard';
    }
}
