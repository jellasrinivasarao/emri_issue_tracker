<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClarificationReply extends Model
{
    use HasFactory;

    protected $fillable = [

        'clarification_id',

        'user_id',

        'message',
    ];


    public function clarification()
    {
        return $this->belongsTo(
            Clarification::class
        );
    }


    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}
