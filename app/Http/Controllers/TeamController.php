<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Show team 
     * @param Request $request
     * @param Team $team
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Team $team)
    {
        // get team
        $team = Team::find($team->id);

        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // return team
        return response()->json(['team' => $team], 200);
    }

    /**
     * Create a new team
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // only admins can create teams
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'You do not have permission to create a team'], 403);
        }

        // create team
        $team = $request->user()->teams()->create([
            'title' => $request->title,
            'description' => $request->description,
            'data' => $request->data
        ]);

        // return team
        return response()->json(['team' => $team], 200);
    }

    public function showUsers(Request $request, Team $team)
    {
        // get users
        $users = $team->users()->get();

        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // return users
        return response()->json(['users' => $users], 200);
    }

    public function addUser(Request $request, Team $team)
    {

        // get user
        $user = User::where('id', $request->user)->first();

        // response al request if user does not exist
        if (!$user) {
            return response()->json(['request' => request()->all(), 'message' => 'User does not exist'], 404);
        }



        // check access level
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // add user to team
        $team->addUser($user);

        // return user
        return response()->json(['user' => $user], 200);

    }

    public function removeUser(Request $request, Team $team)
    {

        // get user
        $user = User::where('id', $request->user)->first();

        // response al request if user does not exist
        if (!$user) {
            return response()->json(['request' => request()->all(), 'message' => 'User does not exist'], 404);
        }

        // check access level
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // remove user from team
        $team->removeUser($user);

        // return user
        return response()->json(['user' => $user], 200);

    }

}



        