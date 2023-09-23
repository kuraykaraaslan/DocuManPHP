<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Log;

/*
    protected $fillable = [
        'team_id',
        'user_id',
        'role', // 'admin', 'editor', 'viewer'
        'data'
    ];

*/


class MembershipController extends Controller
{

    /**
     * Get all memberships of team
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, Team $team)
    {
        // Log request
        Log::info('Membership index called', ['team' => $team->id]);
        
        // get memberships
        $memberships = $team->memberships()->get();
        return response()->json(['memberships' => $memberships], 200);
    }

    /**
     * Create a new membership
     *
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, Team $team)
    {
        // Log request
        Log::info('Membership store called', ['team' => $team->id]);

        // validate request
        $this->validate($request, [
            'user_id' => 'required|string|max:255',
            'role' => 'required|string|max:255'
        ]);

        // create membership
        $membership = new Membership([
            'user_id' => $request->user_id,
            'role' => $request->role,
            'team_id' => $team->id
        ]);
        $membership->save();

        // return response
        return response()->json(['membership' => $membership], 200);
    }

    /**
     * Get membership
     *
     * @param Request $request
     * @param Team $team
     * @param Membership $membership
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Team $team, Membership $membership)
    {
        // Log request
        Log::info('Membership show called', ['team' => $team->id, 'membership' => $membership->id]);

        // return response
        return response()->json(['membership' => $membership], 200);
    }

    /**
     * Update a membership
     *
     * @param Request $request
     * @param Team $team
     * @param Membership $membership
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Membership $membership)
    {
        // Log request
        Log::info('Membership update called', ['team' => $team->id, 'membership' => $membership->id]);

        // validate request
        $this->validate($request, [
            'role' => 'required|string|max:255'
        ]);

        // update membership
        $membership->update([
            'role' => $request->role
        ]);

        // return response
        return response()->json(['membership' => $membership], 200);
    }

    /**
     * Delete a membership
     *
     * @param Request $request
     * @param Team $team
     * @param Membership $membership
     * @return \Illuminate\Http\Response
     */
    
    public function destroy(Request $request, Team $team, Membership $membership)
    {
        // Log request
        Log::info('Membership destroy called', ['team' => $team->id, 'membership' => $membership->id]);

        // delete membership
        $membership->delete();

        // return response
        return response()->json(['membership' => $membership], 200);
    }
}
