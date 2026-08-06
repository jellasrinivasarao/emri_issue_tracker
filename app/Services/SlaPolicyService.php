<?php

namespace App\Services;

use App\Models\SlaPolicy;

class SlaPolicyService
{

    public function getAll()
    {
        return SlaPolicy::query()
            ->with([
                'project',
                'application',
                'service',
                'priority',
                'calendar'
            ])
            ->latest()
            ->paginate(15);
    }

    public function create(array $data)
    {
        return SlaPolicy::create($data);
    }

    public function update(SlaPolicy $policy,array $data)
    {
        $policy->update($data);

        return $policy;
    }

    public function delete(SlaPolicy $policy)
    {
        return $policy->delete();
    }

    public function findPolicy(
        int $project,
        int $application,
        int $service,
        int $priority
    )
    {
        return SlaPolicy::whereProjectId($project)

            ->whereApplicationId($application)

            ->whereServiceId($service)

            ->wherePriorityId($priority)

            ->whereIsActive(1)

            ->first();
    }

}