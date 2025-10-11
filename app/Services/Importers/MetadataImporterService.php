<?php

namespace App\Services\Importers;

class MetadataImporterService
{
    public function import(string $metaClass, array $data): void
    {
        $newRows = [];
        $existedSlugs = $metaClass::whereIn('slug', array_keys($data))
            ->select('slug')
            ->pluck('slug')
            ->toArray();

        $now = now();
        foreach ($data as $slug => $name) {
            if (!in_array($slug, $existedSlugs)) {
                $newRows[] = [
                    'slug' => $slug,
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        $metaClass::insert($newRows);
    }
}
