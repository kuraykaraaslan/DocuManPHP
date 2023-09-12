<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Template;

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
        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        // if user has no access to team, return error
        if ($accessLevel < 1) {
            return response()->json([
                'message' => 'You have no access to this team.'
            ], 403);
        }

        // get all teamplates of team
        $templates = $team->templates()->get();

        // return templates
        return response()->json([
            'templates' => $templates
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *  @param Team $team
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, Team $team)
    {
        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        // if user has no write access to team, return error
        if ($accessLevel < 2) {
            return response()->json([
                'message' => 'You have no write access to this team.'
            ], 403);
        }

        // validate request 
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'orders' => 'nullable|array',
            'orders.*' => 'nullable|integer|exists:inputs,id'
        ]);

        // create template
        $template = $team->templates()->create([
            'title' => $request->title,
            'description' => $request->description,
            'data' => [
                'orders' => $request->orders
            ]
        ]);

        // return template
        return response()->json([
            'template' => $template
        ], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Template  $template
     * @param Team $team
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Team $team, Template $template)
    {
        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        // if user has no read access to team, return error
        if ($accessLevel < 1) {
            return response()->json([
                'message' => 'You have no read access to this team.'
            ], 403);
        }

        // check if template belongs to team
        if ($template->team_id != $team->id) {
            return response()->json([
                'message' => 'This template does not belong to this team.'
            ], 403);
        }

        // return template
        return response()->json([
            'template' => $template
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *  @param Team $team
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Template $template)
    {
        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        // if user has no write access to team, return error
        if ($accessLevel < 2) {
            return response()->json([
                'message' => 'You have no write access to this team.'
            ], 403);
        }

        // validate request 
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        // if orders are in request, return error 

        if ($request->has('orders')) {
            return response()->json([
                'message' => 'You can not update the orders of a template this way.'
            ], 403);
        }

        // update template
        $template->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        // return template

        return response()->json([
            'template' => $template
        ], 200);


    }

    /**
     * order the inputs of the template
     * 
     * @param Request $request
     * @param Team $team
     * @param Template $template
     * @param Input $input
     * @return \Illuminate\Http\Response
     */

    public function order(Request $request, Team $team, Template $template, Input $input)
    {
        // check if user has access to team
        $accessLevel = $team->checkAccessLevel($request->user());

        // if user has no write access to team, return error
        if ($accessLevel < 2) {
            return response()->json([
                'message' => 'You have no write access to this team.'
            ], 403);
        }

        // check if template belongs to team
        if ($template->team_id != $team->id) {
            return response()->json([
                'message' => 'This template does not belong to this team.'
            ], 403);
        }

        // check if input belongs to template
        if ($input->template_id != $template->id) {
            return response()->json([
                'message' => 'This input does not belong to this template.'
            ], 403);
        }

        // validate request 
        $request->validate([
            'order' => 'required|integer'
        ]);

        // get orders of template
        $orders = $template->data['orders'];

        // check if order is already in orders
        if (in_array($request->order, $orders)) {
            return response()->json([
                'message' => 'This order is already in use.'
            ], 403);
        }

        // add order to orders
        $orders[] = $request->order;

        // update template
        $template->update([
            'data' => [
                'orders' => $orders
            ]
        ]);

        // return template
        return response()->json([
            'template' => $template
        ], 200);

    }
}