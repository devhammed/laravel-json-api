<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesSlug
{
    /**
     * Generate a slug for the model.
     */
    public function generateSlug(
        string $field = 'title',
        string $slugField = 'slug',
    ): string {
        $count = 0;
        $value = $this->{$field};
        $slug  = Str::slug($value);

        while ($this->slugExists($slugField, $slug)) {
            $count++;
            $slug = Str::slug($value) . '-' . $count;
        }

        return $slug;
    }

    /**
     * Determine if the given slug exists.
     */
    protected function slugExists(
        string $field,
        string $value,
    ): bool {
        $ignoreId = $this->getKey();
        $idField  = $this->getKeyName();
        $query    = static::where($field, $value);

        if ($ignoreId !== null) {
            $query->where($idField, '!=', $ignoreId);
        }

        return $query->exists();
    }
}
