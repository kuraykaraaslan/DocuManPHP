<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Team;
use App\Models\Ownership;
use App\Models\Membership;
use App\Models\Input;
use App\Models\Template;
use App\Models\Instance;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /* This model will describe the documents of a team
    - title: the title of the document
    - description: the description of the document
    - template_id: the template id of the document
    - instances: the instances of the document
    - teams: the teams of the document
    - inputs: the inputs of the document's template
    */

    /* Must have a Ownership model
    - team_id: the team id of the ownership
    - document_id: the document id of the ownership
    - data: Empty
    */

    /* Must have a Instance models
    - document_id: the document id of the instance
    - input_id: the input id of the instance
    - value: the value of the instance
    */

    /**
     * Give all documents that team owns
     * 
     * @param Request $request
     * @param string $team
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, Team $team)
    {

        $user = $request->user();

        Log::info('Document index called', ['user' => $user->id, 'team' => $team->id]);

        return response()->json(['documents' => $team->documents], 200);
    }

    /**
     * Create a new document
     * 
     * @param Request $request
     * @param string $team
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, Team $team)
    {

        $user = $request->user();

        Log::info('Document store called', ['user' => $user->id, 'team' => $team->id]);

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'template' => 'required|string|max:255'
        ]);

        // check if team has template
        $template = Template::where('team_id', $team->id)->where('id', $request->template)->first();

        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        // create document

        $document = new Document([
            'title' => $request->title,
            'description' => $request->description,
            'template_id' => $request->template,
        ]);

        $document->save();

        // create instances
        $inputs = $template->inputs;

        foreach ($inputs as $input) {
            $instance = new Instance([
                'input_id' => $input->id,
                'document_id' => $document->id,
                'value' => $input->default,
            ]);
            $instance->save();
        }

        // create ownership

        $ownership = new Ownership([
            'team_id' => $team->id,
            'document_id' => $document->id,
        ]);

        $ownership->save();

        return response()->json(['document' => $document], 200);
    }

    /**
     * Show a document
     * 
     * @param Request $request
     * @param string $team
     * @param Document $document
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Team $team, Document $document)
    {

        $user = $request->user();

        Log::info('Document show called', ['user' => $user->id, 'team' => $team->id, 'document' => $document->id]);

        return response()->json(['document' => $document], 200);
    }

    /**
     * Update a document
     * 
     * @param Request $request
     * @param string $team
     * @param Document $document
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Document $document)
    {

        $user = $request->user();

        Log::info('Document update called', ['user' => $user->id, 'team' => $team->id, 'document' => $document->id]);

        //validate request

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        // update document

        $document->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json(['document' => $document], 200);
    }

    /**
     * Delete a document
     * 
     * @param Request $request
     * @param string $team
     * @param Document $document
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team, Document $document)
    {

        $user = $request->user();

        Log::info('Document destroy called', ['user' => $user->id, 'team' => $team->id, 'document' => $document->id]);

        // delete document

        $document->delete();

        return response()->json(['document' => $document], 200);
    }
}
