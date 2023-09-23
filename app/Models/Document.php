<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Log;

/*
Thic model can have multiple owner teams
and can have multiple releationships that has multiple related documents
*/

use App\Models\Input;


class Document extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /* This model will describe the documents of a team
    - title: the title of the document
    - description: the description of the document
    - template_id: the template id of the document
    - instances: the instances of the document
    - teams: the teams of the document
    - inputs: the inputs of the document's template
    */


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'title',
        'description',
        'template_id',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    // get the template of the document
    function template()
    {
        Log::info('Document template called', ['document' => $this->id]);
        return $this->belongsTo(Template::class);
    }

    // get the teams of the document
    function teams()
    {
        Log::info('Document teams called', ['document' => $this->id]);
        return $this->belongsToMany(Team::class, 'ownerships');
    }

    // get the instances of the document
    function instances()
    {
        $template = $this->template;
        $inputs = $template->inputs;

        $instances = Instance::where('document_id', $this->id)->get();
        $inputs = Input::where('template_id', $template->id)->get();

        // create instances for the inputs that does not have instances
        foreach ($inputs as $input) {
            $instance = Instance::where('document_id', $this->id)
                ->where('input_id', $input->id)
                ->first();

            if (!$instance) {
                $instance = new Instance();
                $instance->document_id = $this->id;
                $instance->input_id = $input->id;
                $instance->value = '';
                $instance->save();
            }
        }

        // delete instances for the inputs that does not have inputs
        foreach ($instances as $instance) {
            $input = $inputs->where('id', $instance->input_id)->first();

            if (!$input) {
                $instance->delete();
            }

        }

        // add the inputs to the instances

        foreach ($instances as $instance) {
            $input = $inputs->where('id', $instance->input_id)->first();
            $instance->input = $input;
        }


        return $instances;

    }

    // get the inputs of the document
    function inputs()
    {
        Log::info('Document inputs called', ['document' => $this->id]);
        return $this->hasManyThrough(Input::class, Template::class,
            'id', 'template_id', 'template_id', 'id');
    }


    // get the releationships of the document
    function releationships()
    {
        // find the releationships models that has releated documents that has the document id
        Log::info('Document releationships called', ['document' => $this->id]);

        $releationships = DB::table('releationships')
            ->whereJsonContains('related_documents', $this->id)
            ->get();

        return $releationships;
    }
}

