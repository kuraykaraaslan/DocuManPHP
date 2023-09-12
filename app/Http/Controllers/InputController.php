<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Document;
use App\Models\Ownership;
use App\Models\Template;

class InputController extends Controller
{

    /*
    protected $fillable = [
        'template_id', // the template id of the field
        'name',
        'type',
        'value',
        'required',
        'options',
        'placeholder',
        'label',
        'description',
        'validation',
        'validation_message',
        'data'
    ];
    */

    /**
     * Get all inputs of template
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, Team $team, Template $template)
    {
        // get inputs
        $inputs = $template->inputs()->get();

        //check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // return inputs
        return response()->json(['inputs' => $inputs], 200);
    }

    /**
     * Create a new input
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Team $team, Template $template)
    {
        // validate request
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'value' => 'string',
            'required' => 'boolean',
            'options' => 'string',
            'placeholder' => 'string',
            'label' => 'string',
            'description' => 'string',
            'validation' => 'string',
            'validation_message' => 'string',
            'data' => 'string'
        ]);

        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // create input
        $input = $template->inputs()->create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'required' => $request->required,
            'options' => $request->options,
            'placeholder' => $request->placeholder,
            'label' => $request->label,
            'description' => $request->description,
            'validation' => $request->validation,
            'validation_message' => $request->validation_message,
            'data' => $request->data
        ]);

        // return input
        return response()->json(['input' => $input], 200);
    }

    /**
     * Get input
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @param Input $input
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Team $team, Template $template, Input $input)
    {
        // get input
        $input = $template->inputs()->find($input->id);

        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        if ($accessLevel < 1) {
            return response()->json(['message' => 'You do not have access to this team'], 403);
        }

        // return input
        return response()->json(['input' => $input], 200);
    }
}
