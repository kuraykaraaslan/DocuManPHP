<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use soft delete and uuids
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;

class Input extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /* This model will describe the input fields of a template
    - name: the name of the field
    - type: the type of the field
    - default: the default value of the field
    - required: if the field is required
    - options: the options of the field (if the field is a select)
    - placeholder: the placeholder of the field
    - description: the description of the field
    - validation: the validation of the field
    - validation_message: the validation message of the field */


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'template_id', // added
        'name',
        'type',
        'default',
        'required',
        'options',
        'placeholder',
        'description',
        'validation',
        'validation_message'
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

    // get the template of the input
    function template()
    {
        return $this->belongsTo(Template::class);
    }

    // get the instances of the input
    function instances()
    {
        return $this->hasMany(Instance::class);
    }

    // custom delete function
    function delete()
    {
        // delete all instances of the input
        $this->instances()->delete();

        // delete the input
        return parent::delete();
    }

}
