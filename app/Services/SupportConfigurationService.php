<?php

namespace App\Services;

use App\Models\ProjectSupportConfiguration;
use Illuminate\Support\Facades\DB;

class SupportConfigurationService
{
    public function create(array $data): ProjectSupportConfiguration
    {
        return DB::transaction(function () use ($data) {

            $data['is_active'] = $data['is_active'] ?? 1;
            $data['created_by'] = auth()->id();

            return ProjectSupportConfiguration::create($data);
        });
    }

    public function update(ProjectSupportConfiguration $configuration,array $data): ProjectSupportConfiguration {

        return DB::transaction(function () use ($configuration, $data) {

            $data['updated_by'] = auth()->id();

            $configuration->update($data);

            return $configuration->refresh();
        });
    }

    public function toggle(ProjectSupportConfiguration $configuration): ProjectSupportConfiguration {

        $configuration->update([
            'is_active' => ! $configuration->is_active,
            'updated_by' => auth()->id(),
        ]);

        return $configuration->refresh();
    }
}