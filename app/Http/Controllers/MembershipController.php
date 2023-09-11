<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\Team;

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
        // get memberships
        $memberships = $team->memberships()->get();

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // return memberships
        return response()->json(['memberships' => $memberships], 200);
    }

    /**
     * Create a new membership
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, Team $team)
    {
        // validate request
        $request->validate([
            'user_id' => 'required|uuid',
            'role' => 'required|string',
            'data' => 'nullable|array'
        ]);

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // create membership
        $membership = Membership::create([
            'team_id' => $team->id,
            'user_id' => $request->user_id,
            'role' => $request->role,
            'data' => $request->data
        ]);

        // return membership
        return response()->json(['membership' => $membership], 201);
    }

    /**
     * Get membership
     *
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Team $team, Membership $membership)
    {
        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // return membership
        return response()->json(['membership' => $membership], 200);
    }

    /**
     * Update membership
     *
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Membership $membership)
    {
        // validate request
        $request->validate([
            'user_id' => 'required|uuid',
            'role' => 'required|string',
            'data' => 'nullable|array'
        ]);

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // update membership
        $membership->update([
            'user_id' => $request->user_id,
            'role' => $request->role,
            'data' => $request->data
        ]);

        // return membership
        return response()->json(['membership' => $membership], 200);
    }

    /**
     * Delete membership
     *
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team, Membership $membership)
    {
        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // delete membership
        $membership->delete();

        // return membership
        return response()->json(['message' => 'Membership deleted'], 200);
    }
}


