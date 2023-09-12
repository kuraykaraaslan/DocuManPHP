<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Team;
use App\Models\Ownership;
use App\Models\Membership;
use App\Models\Input;

use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /*
        Document model
        protected $fillable = [
        'title',
        'description',
        'template_id',
        'values'
    ];

    */

    /*
        Ownership model
        protected $fillable = [
        'team_id',
        'document_id',
        'data'

    ];
    */

    /**
     * Get all documents of team
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, Team $team)
    {
        // get documents
        $documents = $team->documents()->get();

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // return documents
        return response()->json(['documents' => $documents], 200);
    }

    /**
     * Create a new document
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, Team $team)
    {
        // validate request
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'template' => 'required|uuid',
            'values' => 'nullable|array'
        ]);

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have write access to this team'], 403);
        }

        // if values are provided, return error
        if ($request->values) {
            return response()->json(['message' => 'Values are not allowed when creating a document'], 400);
        }

        // create document

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'template_id' => $request->template,
            'values' => null
        ]);

        // create ownership

        $ownership = Ownership::create([
            'team_id' => $team->id,
            'document_id' => $document->id,
            'data' => null
        ]);

        // return document
        return response()->json(['document' => $document], 201);
    }

    /**
     * Get document
     *
     * @return \Illuminate\Http\Response
     */


    public function show(Request $request, Team $team, Document $document)
    {
        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // get document teams
        $document_teams = $document->teams()->get();

        // if document teams does not contain team, return error
        if (!$document_teams->contains($team)) {
            return response()->json(['message' => 'You do not have access to this document'], 403);
        }

        // return document
        return response()->json(['document' => $document], 200);
    }

    /**
     * Update document
     * Updating values is not allowed here, use the values endpoint instead
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Document $document)
    {
        // validate request
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'template_id' => 'required|uuid',
            'values' => 'nullable|array'
        ]);

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have write access to this team'], 403);
        }

        // if values are provided, return error
        if ($request->values) {
            return response()->json(['message' => 'Values are not allowed when updating a document'], 400);
        }

        // update document
        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'template_id' => $request->template_id
        ]);

        // return document
        return response()->json(['document' => $document], 200);
    }

    /**
     * Delete document
     *
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team, Document $document)
    {
        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have write access to this team'], 403);
        }

        // delete document
        $document->delete();

        // return document
        return response()->json(['message' => 'Document deleted'], 200);
    }

    /**
     * value endpoint
     *
     * @return \Illuminate\Http\Response
     */

    public function value(Request $request, Team $team, Document $document)
    {
        // validate request
        // has input_id and value

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have write access to this team'], 403);
        }

        // get ownership
        $ownership = $document->teams()->where('team_id', $team->id)->first();

        // if ownership does not exist, return error
        if (!$ownership) {
            return response()->json(['message' => 'You do not have access to this document'], 403);
        }

        // get input
        $input = $document->template->inputs()->find($request->input);

        // if input does not exist, return error
        if (!$input) {
            return response()->json(['message' => 'Input does not exist'], 404);
        }

        // if the method POST is used, create value
        if ($request->method() == 'POST') {

            $request->validate([
                'input' => 'required|uuid',
                'data' => 'required'
            ]);

            // create value
            $document->set_value($input, $request->data);

            // return document
            return response()->json(['document' => $document], 200);
        }

        // if the method GET is used, get value
        if ($request->method() == 'GET') {
            // get value
            $value = $document->get_value($input);

            // return value
            return response()->json(['value' => $value], 200);
        }

        // if the method DELETE is used, delete value
        if ($request->method() == 'DELETE') {
            // delete value
            $document->delete_value($input);

            // return document
            return response()->json(['document' => $document], 200);
        }
    }

    public function fields(Request $request, Team $team, Document $document)
    {

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 2) {
            return response()->json(['message' => 'You do not have write access to this team'], 403);
        }

        // get ownership
        $ownership = $document->teams()->where('team_id', $team->id)->first();

        // if ownership does not exist, return error
        if (!$ownership) {
            return response()->json(['message' => 'You do not have access to this document'], 403);
        }

        // get input
        $inputs = $document->template->inputs()->get();

        // return inputs
        return response()->json(['fields' => $inputs], 200);
    }
}
