<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Log;


/*
this model will describe the template of the document that a documents will be based on
input fields, data types, etc and this will be used to generate the document
*/

class Template extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'team_id',
        'title',
        'description',
        'orders'
    ];

    // hide these attributes when serializing
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'data'
    ];

    function inputs()
    {
        return $this->hasMany(Input::class);
    }

    function documents()
    {
        return $this->hasMany(Document::class);
    }

    function orders()
    {
        // orders are stored in the data field of the template
        // the data field is an array
        // the orders are stored in the orders array

        try {
            $orders = $this->data['orders'];
        } catch (\Throwable $th) {
            $orders = [];
        }
        
        /* orders is an array of inputs ids
        the inputs ids are the ids of the inputs of the template

        */ 

        $inputs = $this->inputs;

        for ($i=0; $i < count($orders); $i++) { 
            $orders[$i] = $inputs->find($orders[$i]);

            if ($orders[$i] == null) {
                unset($orders[$i]);
            }

        }

        return $orders;

    }

    function setOrder($order, $input)
    {
        // orders are stored in the data field of the template
        // the data field is an array
        // the orders are stored in the orders array

        try {
            $orders = $this->data['orders'];
        } catch (\Throwable $th) {
            $orders = [];
        }

        /* orders is an array of inputs ids
        the inputs ids are the ids of the inputs of the template

        */ 

        $inputs = $this->inputs;

        $orders[$order] = $input->id;

        $this->data['orders'] = $orders;

        $this->save();

        return $orders;

    }

}


    

    
