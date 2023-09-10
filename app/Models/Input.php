<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;

    /* This model will describe the input fields of a template
    - name: the name of the field
    - type: the type of the field
    - value: the value of the field
    - required: if the field is required
    - options: the options of the field (if the field is a select)
    - placeholder: the placeholder of the field
    - label: the label of the field
    - description: the description of the field
    - validation: the validation of the field
    - validation_message: the validation message of the field */


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'template_id', // the template id of the field
        'name',
        'type',
        'value',
        'required',
        'options',
        'placeholder',
        'label',
        'description',
        'validation',
        'validation_message',
        'data'
    ];

    function template()
    {
        return $this->belongsTo(Template::class);
    }


    public $availableTypes = [
        'string',
        'number',
        'date',
        'select',
        'checkbox',
    ];

}

    