<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Log;

/*
Thic model can have multiple owner teams
and can have multiple releationships that has multiple related documents
*/


class Document extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'title',
        'description',
        'template_id',
        'vaules'
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


    /** 
     * Has relationships with other documents
     */


    public function relationships()
    {
        Log::info('Document relationships called', ['document' => $this->id]);
        return $this->hasMany(Relationship::class);
    }


    function template()
    {
        Log::info('Document template called', ['document' => $this->id]);
        return $this->belongsTo(Template::class);
    }


    function owners()
    {
        Log::info('Document owners called', ['document' => $this->id]);
        return $this->hasManyThrough(User::class, Ownership::class);
    }

    function reversedRelationships()
    {
        Log::info('Document reversedRelationships called', ['document' => $this->id]);
        // in releationship model has an array of related documents
        // this function will return all the releationships that has this document id in the related documents array

        return Relationship::whereJsonContains('related_documents', $this->id)->get();

    }

    function teams()
    {
        Log::info('Document teams called', ['document' => $this->id]);
        return $this->hasManyThrough(Team::class, Ownership::class);
    }

    function values()
    {
        Log::info('Document values called', ['document' => $this->id]);
        // values are stored in the data field of the document
        // the data field is an array
        // the values are stored in the values array

        /*
         item = [
             'input_id' => UUID, // string // name
             'type' => 'string', // string // type
             'data' => '35kcd32' // string // value
            ]

        */

        /*
        input id refers to the input id in the template
        */

        // cheeck if values is an array

        if (!is_array($this->values)) {
            Log::info('Document values is not an array repaireing', ['document' => $this->id]);
            $this->values = [];
            $this->save();
        }

        // for each value check if the input id is in the template

        $templateInputs = $this->template->inputs;

        // for each value 

        foreach ($this->values as $key => $item) {
            // check if the input id is in the template
            $input = $templateInputs->where('id', $key)->first();

            if (!$input) {
                // remove the value
                unset($this->values[$key]);
            }


            // check if the input type is match the value type
            if ($input->type != $item['type']) {
                
                // if the type is not match then convert the value to the type of the input
                try {
                    $this->values[$key]['data'] = $this->convertValue($item['data'], $item['type'], $input->type);
                    Log::info('Document value converted', ['document' => $this->id, 'value' => $item]);
                } catch (\Throwable $th) {
                    // if the value can not be converted , empty the data
                    $this->values[$key]['data'] = '';
                    Log::info('Document value can not be converted', ['document' => $this->id, 'value' => $item]);
                }

            }

        }

        $this->save();

        return $this->values;

    }

}
        