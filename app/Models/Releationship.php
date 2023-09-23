<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use soft delete and uuids
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Log;


/*
This model can have only one owner document and have multiple releated documents
*/


class Releationship extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /* This model will describe the releationships of a document
    - related_documents: the related documents of the releationship
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'related_documents', //json
        'name',
    
    ];

    //hide these attributes when serializing
    protected $hidden = [
        'related_documents',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    // get the documents with the ids in the related_documents
    function documents()
    {
        Log::info('Releationship documents called', ['releationship' => $this->id]);
        return Document::whereIn('id', $this->related_documents)->get();
    }

    /// array to
    function toArray()
    {
        $array = parent::toArray();
        $array['related_documents'] = json_decode($this->related_documents);
        return $array;
    }
}




    



