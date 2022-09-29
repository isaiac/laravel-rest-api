<?php

namespace App\Models;

use App\Traits\HasStringIds;
use App\Traits\Loggable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
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

    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }
}
