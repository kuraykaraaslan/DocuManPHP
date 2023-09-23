<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use soft delete and uuids
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;

class Instance extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /* This model will describe the instances of a template input field of a document
    - document_id: the document id of the instance
    - input_id: the input id of the instance
    - value: the value of the instance
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'document_id',
        'input_id',
        'value'
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


    /* 
    Get the document of the instance
    */

    function document()
    {
        return $this->belongsTo(Document::class);
    }

    /*
    Get the input of the instance
    */

    function input()
    {
        return $this->belongsTo(Input::class);
    }

    /*
    Get the template of the instance
    */

    function template()
    {
        return $this->belongsTo(Template::class);
    }
}

