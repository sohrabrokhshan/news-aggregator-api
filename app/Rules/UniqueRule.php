<?php

namespace App\Rules;

use App\Exceptions\ServerException;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;

class UniqueRule implements ValidationRule
{
    private Builder $query;
    private ?string $ignoreId;
    private ?string $column;
    private ?string $ignoreColumn;

    public function __construct(
        string | Builder $modelOrQuery,
        ?string $ignoreId = null,
        ?string $column = null,
        ?string $ignoreColumn = null,
    ) {
        $isBuilder = $modelOrQuery instanceof Builder;

        if ($isBuilder === false && class_exists($modelOrQuery) === false) {
            throw new ServerException('The model should be a valid class.');
        }

        $this->query = $isBuilder ? $modelOrQuery : $modelOrQuery::query();
        $this->query->withoutGlobalScopes();

        $this->ignoreId = $ignoreId;
        $this->column = $column;
        $this->ignoreColumn = $ignoreColumn;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->ignoreId) {
            $this->query->where($this->ignoreColumn ?? 'id', '<>', $this->ignoreId);
        }

        if ($this->query->where($this->column ?? $attribute, $value)->exists()) {
            $fail('The :attribute is already taken.');
        }

        $this->query = $this->query->getModel()->query()->withoutGlobalScopes();
    }
}
