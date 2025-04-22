<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{

    use HasFactory;

    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
            ->withTimestamps();
    }

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'permission_id', 'user_id')
            ->withPivot('granted')
            ->withTimestamps();
    }

    public function teams() : BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_permissions', 'permission_id', 'team_id')
            ->withTimestamps();
    }

}
