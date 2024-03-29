<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'username', 'password', 'status'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, mixed>
     */
    protected $casts = [
        'status' => UserStatus::class,
        'email_verified_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => UserStatus::ACTIVE
    ];

    /**
     * Create the accessors and mutators for the user's password.
     *
     * @return Attribute
     */
    public function password(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value,
            set: fn ($value) => Hash::make($value)
        );
    }

    /**
     * Scope a query to only include verified users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Check if the user is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    /**
     * Check if the user is verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Check if the given password is equal to the user's password.
     *
     * @param  string  $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Check if the user has one of the given roles.
     *
     * @param  string|array  $role_ids
     * @return bool
     */
    public function hasRole($role_ids): bool
    {
        $role_ids = is_string($role_ids) ? explode(',', $role_ids) : $role_ids;

        return $this->roles()
            ->newPivotStatement()
            ->where('user_id', $this->id)
            ->whereIn('role_id', $role_ids)
            ->exists();
    }

    /**
     * Check if the user has all given roles.
     *
     * @param  string|array  $role_ids
     * @return bool
     */
    public function hasRoles($role_ids): bool
    {
        $role_ids = is_string($role_ids) ? explode(',', $role_ids) : $role_ids;

        return $this->roles()
            ->newPivotStatement()
            ->where('user_id', $this->id)
            ->whereIn('role_id', $role_ids)
            ->count() === count($role_ids);
    }

    /**
     * Check if the user has one of the given permissions.
     *
     * @param  string|array  $permission_ids
     * @return bool
     */
    public function hasPermission($permission_ids): bool
    {
        $permission_ids = is_string($permission_ids) ? explode(',', $permission_ids) : $permission_ids;

        return $this->permissions()
            ->newPivotStatement()
            ->where('user_id', $this->id)
            ->whereIn('permission_ids', $permission_ids)
            ->exists();
    }

    /**
     * Check if the user has all given permissions.
     *
     * @param  string|array  $permission_ids
     * @return bool
     */
    public function hasPermissions($permission_ids): bool
    {
        $permission_ids = is_string($permission_ids) ? explode(',', $permission_ids) : $permission_ids;

        return $this->permissions()
            ->newPivotStatement()
            ->where('user_id', $this->id)
            ->whereIn('permission_ids', $permission_ids)
            ->count() === count($permission_ids);
    }

    /**
     * Get the user's abilities.
     *
     * @return array<int, string>
     */
    public function getAbilities(): array
    {
        if ($this->roles()
            ->newPivotStatementForId('super-admin')
            ->exists()
        ) {
            return ['*'];
        }

        return array_merge(
            array_map(
                function ($role) {
                    return $role['id'];
                },
                $this->roles->toArray()
            ),
            array_map(
                function ($permission) {
                    return $permission['id'];
                },
                $this->permissions->toArray()
            )
        );
    }

    /**
     * Sync the user's roles.
     *
     * @param  array  $roles
     * @return void
     */
    public function syncRoles(array $roles): void
    {
        $role_ids = [];

        foreach ($roles as $role) {
            $pivot = $role['pivot'] ?? [];
            $role = Arr::except($role, 'pivot');
            $role_id = $role['id'];

            if (! $this->roles()
                ->newPivotStatementForId($role_id)
                ->exists()
            ) {
                $this->roles()->attach($role_id, $pivot);
            } elseif (count($pivot)) {
                $this->roles()
                    ->newPivotStatementForId($role_id)
                    ->update($pivot);
            }

            $role_ids[] = $role_id;
        }

        $this->roles()
            ->newPivotStatement()
            ->where('user_id', $this->id)
            ->whereNotIn('role_id', $role_ids)
            ->delete();
    }

    /**
     * Sync the user's permissions.
     *
     * @param  array  $permissions
     * @return void
     */
    public function syncPermissions(array $permissions): void
    {
        $permission_ids = [];

        foreach ($permissions as $permission) {
            $pivot = $permission['pivot'] ?? [];
            $permission = Arr::except($permission, 'pivot');
            $permission_id = $permission['id'];

            if (! $this->permissions()
                ->newPivotStatementForId($permission_id)
                ->exists()
            ) {
                $this->permissions()->attach($permission_id, $pivot);
            } elseif (count($pivot)) {
                $this->permissions()
                    ->newPivotStatementForId($permission_id)
                    ->update($pivot);
            }

            $permission_ids[] = $permission_id;
        }

        $this->permissions()
            ->newPivotStatement()
            ->where('user_id', $this->id)
            ->whereNotIn('permission_id', $permission_ids)
            ->delete();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->withTimestamps();
    }
}
