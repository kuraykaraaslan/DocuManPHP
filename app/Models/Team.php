<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Membership;

// use soft delete and uuids
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Log;

class Team extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'title',
        'description',
    ];

    //hide these attributes when serializing
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    function memberships()
    {
        return $this->belongsToMany('App\Models\User', 'memberships')
            ->withPivot('role')
            ->withTimestamps();

    }

    function documents()
    {
        return $this->belongsToMany('App\Models\Document', 'ownerships')
            ->withTimestamps();
    }

    function templates()
    {
        return $this->hasMany('App\Models\Template');
    }

}
