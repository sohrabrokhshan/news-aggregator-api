<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Support\Facades\DB;

class AuthorService
{
    public function bulkInsert(array $data): void
    {
        DB::transaction(function () use ($data) {
            $newRows = [];
            $existedSlugs = Author::whereIn('slug', array_keys($data))
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

            Author::insert($newRows);
        });
    }
}
