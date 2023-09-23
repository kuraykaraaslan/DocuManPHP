<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Ownership;
use Illuminate\Support\Facades\Log;

class OwnershipController extends Controller
{

    /* This model will describe the ownerships of a team
    - team_id: the team id of the ownership
    - document_id: the document id of the ownership
    - data: the data of the ownership
    */

    /**
     * Get all ownerships of team
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, Team $team)
    {
        // Log request
        Log::info('Ownership index called', ['team' => $team->id]);

        // get ownerships
        $ownerships = $team->ownerships()->get();
        return response()->json(['ownerships' => $ownerships], 200);
    }

    /**
     * Create a new ownership
     *
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, Team $team)
    {
        // Log request
        Log::info('Ownership store called', ['team' => $team->id]);

        // validate request
        $this->validate($request, [
            'document_id' => 'required|string|max:255',
        ]);

        // create ownership
        $ownership = new Ownership([
            'team_id' => $team->id,
            'document_id' => $request->document_id,
        ]);
        $ownership->save();

        // return response
        return response()->json(['ownership' => $ownership], 200);
    }

    /**
     * Get ownership
     *
     * @param Request $request
     * @param Team $team
     * @param Ownership $ownership
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Team $team, Ownership $ownership)
    {
        // Log request
        Log::info('Ownership show called', ['team' => $team->id, 'ownership' => $ownership->id]);

        // return response
        return response()->json(['ownership' => $ownership], 200);
    }

    /**
     * Update a ownership
     *
     * @param Request $request
     * @param Team $team
     * @param Ownership $ownership
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Ownership $ownership)
    {
        // Nothipg to do here
        return response()->json(['ownership' => $ownership], 200);

    }

    /**
     * Delete a ownership
     *
     * @param Request $request
     * @param Team $team
     * @param Ownership $ownership
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team, Ownership $ownership)
    {
        // Log request
        Log::info('Ownership destroy called', ['team' => $team->id, 'ownership' => $ownership->id]);

        // delete ownership
        $ownership->delete();

        // return response
        return response()->json(['message' => 'Ownership deleted'], 200);
    }

}
