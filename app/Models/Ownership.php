<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Log;


/* 
This model can have only one owner team and have only one document
*/


class Ownership extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'team_id',
        'document_id',
        'data'

    ];

    function team()
    {
        Log::info('Ownership team called', ['ownership' => $this->id]);
        return $this->belongsTo(Team::class);
    }

    function document()
    {
        Log::info('Ownership document called', ['ownership' => $this->id]);
        return $this->belongsTo(Document::class);
    }

}

