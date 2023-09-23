<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class TeamController extends Controller
{
    /**
     * Give all teams that user is a member of
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $user = $request->user();

        Log::info('Team index called', ['user' => $user->id]);

        $teams = $user->teams;

        return response()->json(['teams' => $teams], 200);
    }


    /**
     * Create a new team
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $user = $request->user();

        Log::info('Team store called', ['user' => $user->id]);

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        // create team

        $team = new Team();
        $team->name = $request->name;
        $team->description = $request->description;
        $team->save();

        // create membership

        $membership = new Membership();
        $membership->user_id = $user->id;
        $membership->team_id = $team->id;
        $membership->role = 'admin';
        $membership->save();

        return response()->json(['team' => $team], 200);
    }


    /*
        * Show a team
        * 
        * @param Request $request
        * @param Team $team
        * @return \Illuminate\Http\Response
        */

    public function show(Request $request, Team $team)
    {
        $user = $request->user();

        Log::info('Team show called', ['user' => $user->id, 'team' => $team->id]);

        return response()->json(['team' => $team], 200);
    }


    /**
     * Update a team
     * 
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team)
    {
        $user = $request->user();

        Log::info('Team update called', ['user' => $user->id, 'team' => $team->id]);

        //validate request

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        // update team

        $team->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['team' => $team], 200);
    }


    /**
     * Delete a team
     * 
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team)
    {
        $user = $request->user();

        Log::info('Team destroy called', ['user' => $user->id, 'team' => $team->id]);


        // delete team

        $team->delete();

        return response()->json(['message' => 'Team deleted'], 200);
    }

}

