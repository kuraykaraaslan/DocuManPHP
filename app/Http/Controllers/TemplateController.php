<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Template;
use Illuminate\Support\Facades\Log;


class TemplateController extends Controller
{
    /*
        protected $fillable = [
        'team_id',
        'title',
        'description',
        'orders'
    ];
    */


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, Team $team)
    {
        return response()->json(['templates' => $team->templates], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, Team $team)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        // create template

        $template = new Template([
            'title' => $request->title,
            'description' => $request->description,
            'team_id' => $team->id
        ]);

        $template->save();

        return response()->json(['template' => $template], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Team $team, Template $template)
    {
        return response()->json(['template' => $template], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Template $template)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        // update template

        $template->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json(['template' => $template], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team, Template $template)
    {
        $template->delete();

        return response()->json(['message' => 'Template deleted'], 200);
    }
}

