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
        $template_inputs = $this->template->inputs;
        $document_instances = $this->hasMany(Instance::class)->get();

        // create instances for the inputs that does not have instances
        foreach ($template_inputs as $template_input) {
            $document_instance = $document_instances->where('input_id', $template_input->id)->first();
            if (!$document_instance) {
                $document_instance = new Instance([
                    'input_id' => $template_input->id,
                    'document_id' => $this->id,
                    'value' => $template_input->default,
                ]);
                
                $document_instance->save();
                $document_instances->push($document_instance);
            }
        }

        // if the instance exist but the input does not exist in the template, delete the instance
        foreach ($document_instances as $document_instance) {
            $template_input = $template_inputs->where('id', $document_instance->input_id)->first();
            if (!$template_input) {
                $document_instance->delete();
            }
        }
        

        // add the inputs to the instances

        foreach ($document_instances as $instance) {
            $input = $template_inputs->where('id', $instance->input_id)->first();
            $instance->input = $input;
        }


        return $document_instances;

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

    // custom array of the document
    public function toArray()
    {
        $array = parent::toArray();
        $array['template'] = $this->template;
        //human readable date
        $array['created_at'] = $this->created_at->diffForHumans();
        return $array;
    }
}

