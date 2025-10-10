<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function bulkInsert(array $data): void
    {
        DB::transaction(function () use ($data) {
            $newRows = [];
            $existedSlugs = Category::whereIn('slug', array_keys($data))
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

            Category::insert($newRows);
        });
    }
}
