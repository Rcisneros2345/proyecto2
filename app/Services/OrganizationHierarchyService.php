<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Area;
use App\Models\Employee;
use Illuminate\Support\Collection;

class OrganizationHierarchyService
{
    public function getAncestors(Area $area): Collection
    {
        $ancestors = collect();
        $current = $area;

        while ($current->parent) {
            $current = $current->parent;
            $ancestors->push($current);
        }

        return $ancestors->reverse();
    }

    public function getDirectHead(Area $area): ?Employee
    {
        return $area->head()->first();
    }

    public function getParentArea(Area $area): ?Area
    {
        return $area->parent()->first();
    }

    public function getHierarchyPath(Area $area): array
    {
        $path = $this->getAncestors($area)->pluck('id')->all();
        $path[] = $area->id;

        return $path;
    }
}
