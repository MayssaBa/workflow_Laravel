<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'role',
    'team_id',
    'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users_app';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_user_app',
            'user_id',
            'project_id'
        )->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function sentRequests(): HasMany
    {
        return $this->hasMany(
            \App\Models\Request::class,
            'sender_id'
        );
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
