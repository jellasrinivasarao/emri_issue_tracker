<?php

namespace App\Services;

use App\Models\ProjectSupportConfiguration;
use Illuminate\Support\Facades\DB;

class ProjectSupportConfigurationService
{
    public function getAll()
    {
        return ProjectSupportConfiguration::query()
            ->orderBy('config_name')
            ->get();
    }

    public function create(array $data): ProjectSupportConfiguration
    {
        return DB::transaction(function () use ($data) {

            return ProjectSupportConfiguration::create([
                'project_id' => $data['project_id'] ?? null,
                'config_code' => strtoupper(trim($data['config_code'])),
                'config_name' => trim($data['config_name']),
                'description' => $data['description'] ?? null,
                'default_priority' => $data['default_priority'] ?? 'MEDIUM',
                'auto_routing_enabled' => $data['auto_routing_enabled'] ?? true,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function update(
        ProjectSupportConfiguration $configuration,
        array $data
    ): ProjectSupportConfiguration {

        $configuration->update([
            'project_id' => $data['project_id'] ?? null,
            'config_code' => strtoupper(trim($data['config_code'])),
            'config_name' => trim($data['config_name']),
            'description' => $data['description'] ?? null,
            'default_priority' => $data['default_priority'] ?? 'MEDIUM',
            'auto_routing_enabled' => $data['auto_routing_enabled'] ?? true,
            'updated_by' => auth()->id(),
        ]);

        return $configuration->refresh();
    }

    public function toggle(ProjectSupportConfiguration $configuration): bool
    {
        $configuration->is_active = ! $configuration->is_active;
        $configuration->updated_by = auth()->id();

        return $configuration->save();
    }
}