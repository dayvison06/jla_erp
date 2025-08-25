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

    public function teams() : BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_permissions', 'permission_id', 'team_id')
            ->withTimestamps();
    }

}
