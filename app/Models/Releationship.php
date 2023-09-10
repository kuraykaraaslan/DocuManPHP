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
        'document_id',
        'title',
        'related_documents' //json
    ];

    // get the document that owns the relationship

    public function owner()
    {
        return $this->belongsTo(Document::class);
    }

    // releated documents is an array of document ids

    function releatedDocuments()
    {
        // check if the releated_documents is an array
        if (!is_array($this->related_documents)) {
            Log::info('Releationship related is not an array repaireing', ['releationship' => $this->id]);
            $this->related_documents = [];
            $this->save();
        }

        $documents = Document::findMany($this->related_documents);

        return $documents;

    }

    function addReleatedDocument($document)
    {
        // check if the releated_documents is an array
        if (!is_array($this->related_documents)) {
            Log::info('Releationship related is not an array repaireing', ['releationship' => $this->id]);
            $this->related_documents = [];
            $this->save();
        }

        // add the document id to the array
        $this->related_documents[] = $document->id;
        $this->save();

        return $this->related_documents;
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

}


    


    



