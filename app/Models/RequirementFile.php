<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementFile extends Model
{
    use HasFactory;

    protected $fillable = [

        'requirement_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];


    public function requirement()
    {
        return $this->belongsTo(
            Requirement::class
        );
    }


    public function uploader()
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }
}
