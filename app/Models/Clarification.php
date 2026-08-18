<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clarification extends Model
{
    use HasFactory;

    protected $fillable = [

        'requirement_id',

        'user_id',

        'message',

        'status',

        'closed_at',

        'closed_by',
    ];


    protected $casts = [
        'closed_at' => 'datetime',
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
            User::class
        );
    }


    public function replies()
    {
        return $this->hasMany(
            ClarificationReply::class
        )->oldest();
    }


    public function closedBy()
    {
        return $this->belongsTo(
            User::class,
            'closed_by'
        );
    }

}
