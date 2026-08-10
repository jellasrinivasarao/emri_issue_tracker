<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class IssueEscalation extends Model
{
    protected $table = 'txn_issue_escalation';

    protected $primaryKey = 'escalation_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [

        'issue_id',

        'escalation_level',

        'escalation_type',

        'escalated_from_group_id',

        'escalated_to_group_id',

        'escalated_by',

        'escalated_at',

        'acknowledged_at',

        'acknowledged_by',

        'escalation_status',
         'reason',

        'remarks',

        'sla_type',

        'sla_due_at',

        'sla_breached_at',

        'resolved_at',

        'resolved_by',

    ];

    protected $casts = [

        'escalated_at' =>
            'datetime',

        'acknowledged_at' =>
            'datetime',

        'sla_due_at' =>
            'datetime',

        'sla_breached_at' =>
            'datetime',

        'resolved_at' =>
        'datetime',

    ];


    public function issue(): BelongsTo
    {
        return $this->belongsTo(
            Issue::class,
            'issue_id',
            'issue_id'
        );
    }

        public function fromGroup(): BelongsTo
        {
            return $this->belongsTo(
                SupportGroup::class,
                'escalated_from_group_id',
                'support_group_id'
            );
        }

        public function toGroup(): BelongsTo
        {
            return $this->belongsTo(
                SupportGroup::class,
                'escalated_to_group_id',
                'support_group_id'
            );
        }

         public function escalatedBy(): BelongsTo
        {
            return $this->belongsTo(
                User::class,
                'escalated_by',
                'user_id'
            );
        }

        public function acknowledgedBy(): BelongsTo
        {
            return $this->belongsTo(
                User::class,
                'acknowledged_by',
                'user_id'
            );
        }
}   