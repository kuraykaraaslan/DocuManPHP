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



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'related_documents' //json
    ];

    //hide these attributes when serializing
    protected $hidden = [
        'related_documents',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    // get the document that owns the relationship

    public function owner()
    {
        return $this->belongsTo(Document::class);
    }

    // releated documents is an array of document ids

    function releatedDocuments()
    {
        $rel = $this->related_documents;

        $rel = json_decode($rel);

        return $rel;

    }

    function addDocument($document)
    {

        // get the releated_documents and convert it to an array
        $rel = $this->related_documents;

        $rel = json_decode($rel);       

        // check if $rel is an array
        if (!is_array($rel)) {
            Log::info('Releationship related is not an array repaireing', ['releationship' => $this->id]);
            $rel = [];
        }

        // check if the document is already in the array
        if (in_array($document->id, $rel)) {
            return false;
        }

        // add the document id to the array
        array_push($rel, $document->id);

        // save the array
        $this->related_documents = $rel;
        $this->save();

        return true;
    }

    function removeReleatedDocument($document)
    {
        // check if the releated_documents is an array
        if (!is_array($this->related_documents)) {
            Log::info('Releationship related is not an array repaireing', ['releationship' => $this->id]);
            $this->related_documents = [];
            $this->save();
        }

        // remove the document id from the array
        $this->related_documents = array_diff($this->related_documents, [$document->id]);
        $this->save();

        return $this->related_documents;
    }

    // costum serialize
    public function toArray()
    {
        $array = parent::toArray();

        $array['related_documents'] = $this->releatedDocuments();

        //if owner is present
        if ($this->owner) {
            $owner = $this->owner->toArray();
            $array['title'] = $owner['title'];
            $array['description'] = $owner['description'];
        } else {
            $array['title'] = 'Owner not found';
            $array['description'] = 'Owner not found';
        }

        return $array;
    }

}


    


    



