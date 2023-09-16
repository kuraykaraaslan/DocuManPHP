<?php

namespace App\Http\Controllers;

use App\Models\Releationship;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReleationshipController extends Controller
{
    /*
        protected $fillable = [
        'related_documents' //json
    ];
    */

    /*
    create releationship
    */

    public function store(Request $request, Team $team)
    {
        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // check if releated documents is present
        // if it is give error

        if ($request->related_documents) {
            return response()->json(['message' => 'You can not create a releationship with releated documents'], 403);
        }

        // if owner id is present
        //validate owner id

        if ($request->owner_id) {
            // find document
            $document = $team->documents()->find($request->owner_id);

            if (!$document) {
                return response()->json(['message' => 'Document not found'], 404);
            }
        }


        // create releationship
        $releationship = Releationship::create([
            'owner_id' => $request->owner_id,
        ]);

        // return releationship
        return response()->json([
            'releationship' => $releationship,
        ], 200);

    }

    /*
    show releationship
    */

    public function show(Request $request, Team $team, Releationship $releationship)
    {
        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // return releationship
        return response()->json([
            'releationship' => $releationship,
        ], 200);

    }

    


    public function addDocument(Request $request, Team $team, Releationship $releationship)
    {
        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // check if document is present
        // if it is give error

        if (!$request->document) {
            return response()->json(['message' => 'You need to provide a document'], 403);
        }

        // find document
        $document = $team->documents()->find($request->document);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        // add document to releationship
        $releationship->addDocument($document);

        // return releationship
        return response()->json([
            'releationship' => $releationship,
        ], 200);

    }

    
}
