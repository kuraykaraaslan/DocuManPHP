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

    function users()
    {
        return $this->belongsToMany('App\Models\User', 'memberships')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function addUser(User $user, $role = 'editor')
    {
        //check if user is already member of team
        if ($this->users()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // add user to team
        $membership = new Membership(); 
        $membership->user_id = $user->id;
        $membership->team_id = $this->id;
        $membership->role = $role;
        $membership->save();

        return true;

    }
    

    function documents()
    {
        return $this->hasManyThrough(Document::class, Ownership::class,
            'team_id', 'id', 'id', 'document_id');
    }

    function templates()
    {
        return $this->hasMany(Template::class);
    }


        /** 
     * Check Access Level
     * 
     * @param User $user
     * @param Team $team
     *  Membership roles: admin, editor, viewer
     * @return int // 0 = no access, 1 = read access, 2 = write access
     */

    public function checkAccessLevel($user)
    {
        // consolo user
        if (!$user) {
            return 0;
        }
        // check if user is admin
        if ($user->is_admin) {
            return 2;
        }

        // get membership that user and team have in common
        $memberships = $user->memberships()->where('team_id', $this->id)->first();


        // check if user is member of team
        if ($memberships) {
            // check if user is admin of team
            if ($memberships->role == 'admin') {
                return 2;
            }
            // check if user is editor of team
            if ($memberships->role == 'editor') {
                return 1;
            }
            // check if user is viewer of team
            if ($memberships->role == 'viewer') {
                return 0;
            }
        }

        // user is not member of team
        return 0;
    }
}
