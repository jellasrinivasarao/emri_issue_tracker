<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'requirement_id',
        'from_status',
        'to_status',
        'remarks',
        'changed_by',
    ];


    public function requirement()
    {
        return $this->belongsTo(
            Requirement::class
        );
    }


    public function user()
    {
        return $this->belongsTo(
            User::class,
            'changed_by'
        );
    }
}
