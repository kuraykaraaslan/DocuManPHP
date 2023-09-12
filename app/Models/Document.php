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
        'values'
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
        return $this->belongsToMany(Team::class, 'ownerships');
    }


    public function set_value(Input $input, $data)
    {
        $values = $this->values;

        //if values is not an array, make it an array
        if (!is_array($values)) {
            $values = [];
        }

        $input_type = $input->type;

        //check if input type matches data type
        if (gettype($data) != $input_type) {
            return false;
        }

        // set vaule
        $values[$input->id] = $data;

        // update values
        $this->update([
            'values' => $values
        ]);

        return true;

    }

    public function get_value(Input $input)
    {
        $values = $this->values;

        //if values is not an array, make it an array
        if (!is_array($values)) {
            $values = [];
        }

        // check if vaule exists
        if (!array_key_exists($input->id, $values)) {
            return false;
        }

        // get vaule
        $vaule = $values[$input->id];

        return $vaule;

    }

    public function delete_value(Input $input)
    {
        $values = $this->values;

        //if values is not an array, make it an array
        if (!is_array($values)) {
            $values = [];
        }

        // delete vaule
        unset($values[$input->id]);

        // update values
        $this->update([
            'values' => $values
        ]);

        return true;

    }

    public function get_inputs()
    {
        $inputs = $this->template->inputs;

        //if inputs is not an array, make it an array
        if (!is_array($inputs)) {
            $inputs = [];
        }

        return $inputs;

    }
} 








