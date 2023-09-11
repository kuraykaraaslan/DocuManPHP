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

use App\Models\Input;


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
     * Check Access Level
     * 
     * @param User $user
     * @param Team $team
     * @return int // 0 = no access, 1 = read access, 2 = write access
     */

    public function template()
    {
        Log::info('Document template called', ['document' => $this->id]);
        return $this->belongsTo(Template::class);
    }

    public function releationships()
    {
        Log::info('Document releationships called', ['document' => $this->id]);
        return $this->hasMany(Releationship::class);
    }

    public function teams()
    {
        Log::info('Document teams called', ['document' => $this->id]);
        return $this->belongsToMany(Team::class, 'releationships');
    }


    public function set_vaule(Input $input, $data)
    {
        $vaules = $this->vaules;

        //if vaules is not an array, make it an array
        if (!is_array($vaules)) {
            $vaules = [];
        }

        $input_type = $input->type;

        //check if input type matches data type
        if (gettype($data) != $input_type) {
            return false;
        }

        // set vaule
        $vaules[$input->id] = $data;

        // update vaules
        $this->update([
            'vaules' => $vaules
        ]);

        return true;

    }

    public function get_vaule(Input $input)
    {
        $vaules = $this->vaules;

        //if vaules is not an array, make it an array
        if (!is_array($vaules)) {
            $vaules = [];
        }

        // get vaule
        $vaule = $vaules[$input->id];

        //if vaule is not set, return null
        if (!isset($vaule)) {
            return null;
        }

        return $vaule;

    }

    public function delete_vaule(Input $input)
    {
        $vaules = $this->vaules;

        //if vaules is not an array, make it an array
        if (!is_array($vaules)) {
            $vaules = [];
        }

        // delete vaule
        unset($vaules[$input->id]);

        // update vaules
        $this->update([
            'vaules' => $vaules
        ]);

        return true;

    }

}
        








