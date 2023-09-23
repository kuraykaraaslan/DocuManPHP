<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Template;
use App\Models\Input;

class InputController extends Controller
{

    /*
    - name: the name of the field
    - type: the type of the field
    - default: the default value of the field
    - required: if the field is required
    - options: the options of the field (if the field is a select)
    - placeholder: the placeholder of the field
    - description: the description of the field
    - validation: the validation of the field
    - validation_message: the validation message of the field
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
        return response()->json(['inputs' => $template->inputs], 200);
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
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'default' => 'string|max:255|nullable',
            'required' => 'boolean|nullable|nullable',
            'options' => 'string|max:255|nullable',
            'placeholder' => 'string|max:255|nullable',
            'description' => 'string|max:255|nullable',
            'validation' => 'string|max:255|nullable',
            'validation_message' => 'string|max:255|nullable',
        ]);

        // create input

        $input = new Input([
            'name' => $request->name,
            'type' => $request->type,
            'template_id' => $template->id,
        ]);

        $input->save();

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
        return response()->json(['input' => $input], 200);
    }

    /**
     * Update input
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @param Input $input
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Template $template, Input $input)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'default' => 'string|max:255|nullable',
            'required' => 'boolean|nullable|nullable',
            'options' => 'string|max:255|nullable',
            'placeholder' => 'string|max:255|nullable',
            'description' => 'string|max:255|nullable',
            'validation' => 'string|max:255|nullable',
            'validation_message' => 'string|max:255|nullable',
        ]);

        // update input

        $input->update([
            'name' => $request->name,
            'type' => $request->type,
            'default' => $request->default,
            'required' => $request->required,
            'options' => $request->options,
            'placeholder' => $request->placeholder,
            'description' => $request->description,
            'validation' => $request->validation,
            'validation_message' => $request->validation_message,
        ]);

        return response()->json(['input' => $input], 200);
    }

    /**
     * Delete input
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @param Input $input
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team, Template $template, Input $input)
    {
        $input->delete();

        return response()->json(['message' => 'Input deleted'], 200);
    }
}