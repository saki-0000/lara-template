<?php

namespace App\Auth\Permissions;

use App\Auth\Role;
use App\Entities\Models\Entity;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class JointPermission extends Model
{
    protected $primaryKey = null;
    public $timestamps = false;

    // /**
    //  * Get the role that this points to.
    //  */
    // public function role(): BelongsTo
    // {
    //     return $this->belongsTo(Role::class);
    // }

    // /**
    //  * Get the entity this points to.
    //  */
    // public function entity(): MorphOne
    // {
    //     return $this->morphOne(Entity::class, 'entity');
    // }
}
