<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailConfiguration extends Model
{
    use HasFactory;

    protected $table = 'mst_mail_configuration';
    protected $primaryKey = 'mail_configuration_id';

    public $timestamps = true;

    protected $fillable = [
        'state_id',
        'state_name',
        'project_id',
        'application_id',
        'application_ids',
        'to_emails',
        'cc_emails',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'mail_configuration_id' => 'integer',
        'state_id' => 'integer',
        'project_id' => 'integer',
        'application_id' => 'integer',
        'application_ids' => 'array',
        'to_emails' => 'array',
        'cc_emails' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function updateTimestamps(): static
    {
        if (! $this->exists) {
            if (! $this->isDirty($this->getCreatedAtColumn())) {
                $this->setCreatedAt($this->freshTimestamp());
            }

            return $this;
        }

        return parent::updateTimestamps();
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'application_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
