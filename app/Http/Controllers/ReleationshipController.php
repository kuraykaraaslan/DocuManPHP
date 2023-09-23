<?php

namespace App\Http\Controllers;

use App\Models\Releationship;
use App\Models\Document;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReleationshipController extends Controller
{
    /*
        protected $fillable = [
        'related_documents' //json
    ];
    */

    /**
     * Get all releationships of document
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request, Team $team, Document $document)
    {
        // Log request
        Log::info('Releationship index called', ['team' => $team->id, 'document' => $document->id]);

        // get releationships
        $releationships = $document->releationships();
        return response()->json(['releationships' => $releationships], 200);
    }


    public function store(Request $request, Team $team, Document $document)
    {
        // validate request
        $this->validate($request, [
            'related_documents' => 'array|nullable',
        ]);

        // validate related documents
        $relatedDocuments = json_decode($request->related_documents);


        if (!$relatedDocuments) {
            $relatedDocuments = [];
        }

        // check if related documents has valid documents
        foreach ($relatedDocuments as $relatedDocument) {
            $document = Document::find($relatedDocument);

            if (!$document) {
                return response()->json(['message' => 'Related document not found'], 403);
            }
        }

        //check if releatedDocuments has not current document gently add it
        if (!in_array($document->id, $relatedDocuments)) {
            array_push($relatedDocuments, $document->id);
        }



        // create releationship
        $releationship = new Releationship([
            'team_id' => $team->id,
            'related_documents' => json_encode($relatedDocuments),
        ]);

        $releationship->save();

        // return response
        return response()->json(['releationship' => $releationship], 200);
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\Response
     */



    public function show(Request $request, Team $team, Document $document, Releationship $releationship)
    {
        return response()->json(['releationship' => $releationship], 200);
    }

    /**
     * Add a document to a releationship
     *
     * @param Request $request
     * @param Team $team
     * @param Releationship $releationship
     * @return \Illuminate\Http\Response
     */

    public function document_add(Request $request, Team $team, Releationship $releationship)
    {
        // validate request
        $this->validate($request, [
            'document' => 'required|string|max:255',
        ]);

        // validate document
        $document = Document::find($request->document);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 403);
        }

        // add document to releationship
        $releationship->related_documents = json_decode($releationship->related_documents);
        array_push($releationship->related_documents, $request->document);
        $releationship->related_documents = json_encode($releationship->related_documents);
        $releationship->save();

        // return response
        return response()->json(['releationship' => $releationship], 200);
    }

    /**
     * Remove a document from a releationship
     * 
     * @param Request $request
     * @param Team $team
     * @param Releationship $releationship
     * @return \Illuminate\Http\Response
     */

    public function document_remove(Request $request, Team $team, Releationship $releationship)
    {
        // validate request
        $this->validate($request, [
            'document' => 'required|string|max:255',
        ]);

        // validate document
        $document = Document::find($request->document);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 403);
        }

        // remove document from releationship
        $releationship->related_documents = json_decode($releationship->related_documents);
        $releationship->related_documents = array_diff($releationship->related_documents, [$request->document]);
        $releationship->related_documents = json_encode($releationship->related_documents);
        $releationship->save();

        // return response
        return response()->json(['releationship' => $releationship], 200);
    }

    /**
     * Update a releationship
     * 
     * @param Request $request
     * @param Team $team
     * @param Releationship $releationship
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Team $team, Releationship $releationship)
    {
        // not implemented
        return response()->json(['message' => 'Not implemented'], 403);
    }

    /**
     * Delete a releationship
     * 
     * @param Request $request
     * @param Team $team
     * @param Releationship $releationship
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, Team $team, Releationship $releationship)
    {
        // delete releationship
        $releationship->delete();

        // return response
        return response()->json(['message' => 'Releationship deleted'], 200);
    }
}
