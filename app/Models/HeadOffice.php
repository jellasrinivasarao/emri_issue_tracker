<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeadOffice extends Model
{
    protected $table = 'mst_head_office';

    protected $primaryKey = 'head_office_id';

    public $timestamps = false;

    protected $fillable = [
        'organisation_id',
        'head_office_code',
        'head_office_name',
        'address',
        'contact_number',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class,'organisation_id','organisation_id');
    }
}