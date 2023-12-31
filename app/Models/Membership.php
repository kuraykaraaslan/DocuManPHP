<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use soft delete and uuids
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Log;

/*
Thic model can have only one owner team and have only one user
*/



class Membership extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /* This model will describe the memberships of a team
    - team_id: the team id of the membership
    - user_id: the user id of the membership
    - role: the role of the membership
    */


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'team_id',
        'user_id',
        'role', // 'admin', 'editor', 'viewer'
    ];

    function user()
    {
        Log::info('Membership user called', ['membership' => $this->id]);
        return $this->belongsTo(User::class);
    }

    function team()
    {
        Log::info('Membership team called', ['membership' => $this->id]);
        return $this->belongsTo(Team::class);
    }

}
