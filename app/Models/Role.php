<?php

namespace App\Models;

use App\Traits\HasStringIds;
use App\Traits\Loggable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Role extends Model
{
    use HasStringIds, Loggable, Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array<string, mixed>
     */
    public function sluggable(): array
    {
        return [
            'id' => [
                'source' => 'name',
                'onUpdate' => true
            ]
        ];
    }

    /**
     * Sync the role's permissions.
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
            ->where('role_id', $this->id)
            ->whereNotIn('permission_id', $permission_ids)
            ->delete();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }
}
