<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Team;
use App\Models\Document;
use App\Models\Input;
use App\Models\Instance;


class InstanceController extends Controller
{

    /* This model will describe the instances of a template input field of a document
    - document_id: the document id of the instance
    - input_id: the input id of the instance
    - value: the value of the instance
    */

    /**
     * Get all instances of document
     * 
     * @param Request $request
     * @param string $team
     * @param string $document
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, Team $team, Document $document)
    {
        $user = $request->user();

        Log::info('Document instances called', ['user' => $user->id, 'team' => $team->id, 'document' => $document->id]);

        // get instances

        $instances = $document->instances();
       
        // return response

        return response()->json(['instances' => $instances], 200);
    }


    /**
     * Create a new instance
     * 
     * @param Request $request
     * @param string $team
     * @param string $document
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, Team $team, Document $document)
    {

        // Manual instance creation is not allowed. Instances are created when a document is created.
        // This is because the instance is created when the document is created, and the instance is created for each input of the template.
        return response()->json(['message' => 'Manual instance creation is not allowed. Instances are created when a document is created.'], 403);
    }

    /**
     * Get instance
     * 
     * @param Request $request
     * @param string $team
     * @param string $document
     * @param string $instance
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Team $team, Document $document, Instance $instance)
    {
        $user = $request->user();

        Log::info('Document instance show called', ['user' => $user->id, 'team' => $team->id, 'document' => $document->id, 'instance' => $instance->id]);

        // return response

        return response()->json(['instance' => $instance], 200);
    }

    /**
     * Update instance
     * 
     * @param Request $request
     * @param string $team
     * @param string $document
     * @param string $instance
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Document $document, Instance $instance)
    {
        $user = $request->user();

        Log::info('Document instance update called', ['user' => $user->id, 'team' => $team->id, 'document' => $document->id, 'instance' => $instance->id]);

        $request->validate([
            'input_id' => 'string',
            'value' => 'string',
        ]);

        // check if the input exists

        $input = $instance->input;

        if (!$input) {
            Log::info('Input does not exist', ['user' => $user->id, 'input' => $request->input_id]);
            return response()->json(['message' => 'Input does not exist'], 404);
        }

        // check if the input belongs to the template of the document

        $template = $document->template;

        if (!$template->inputs->contains($input)) {
            Log::info('Input does not belong to the template of the document', ['user' => $user->id, 'input' => $request->input_id, 'template' => $template->id, 'document' => $document->id]);
            return response()->json(['message' => 'Input does not belong to the template of the document'], 403);
        }


        // check if the instance belongs to the document

        if (!$instance->document->is($document)) {
            Log::info('Instance does not belong to the document', ['user' => $user->id, 'instance' => $instance->id, 'document' => $document->id]);
            return response()->json(['message' => 'Instance does not belong to the document'], 403);
        }

        // update instance value

        $instance->value = $request->value;
        $instance->save();

        // return response

        return response()->json(['instance' => $instance], 200);

    }

    /**
     * Delete instance
     * 
     * @param Request $request
     * @param string $team
     * @param string $document
     * @param string $instance
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team, Document $document, Instance $instance)
    {
        // not allowed
        return response()->json(['message' => 'Instance deletion is not allowed.'], 403);
    }
}