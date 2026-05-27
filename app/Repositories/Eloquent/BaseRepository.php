<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    public function query(bool $onlyActive = true): Builder
    {
        $query = $this->model->newQuery();

        if ($onlyActive && $this->supportsSoftDeleteFlag()) {
            $query->where('delete_status', false);
        }

        return $query;
    }

    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->withRelations($this->query(true), $relations)->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->withRelations($this->query(true), $relations)->paginate($perPage, $columns);
    }

    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->withRelations($this->query(true), $relations)->find($id, $columns);
    }

    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->withRelations($this->query(true), $relations)->findOrFail($id, $columns);
    }

    public function findBy(string $column, mixed $value, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->withRelations($this->query(true), $relations)->where($column, $value)->first($columns);
    }

    public function getBy(string $column, mixed $value, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->withRelations($this->query(true), $relations)->where($column, $value)->get($columns);
    }

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int|string|Model $model, array $data): Model
    {
        $entity = $this->resolveModel($model, false);
        $entity->fill($data);
        $entity->save();

        return $entity->refresh();
    }

    public function delete(int|string|Model $model, ?int $deletedBy = null): bool
    {
        $entity = $this->resolveModel($model, false);

        if (!$this->supportsSoftDeleteFlag()) {
            return (bool) $entity->delete();
        }

        $entity->setAttribute('delete_status', true);

        if ($this->hasFillable('deleted_at')) {
            $entity->setAttribute('deleted_at', now());
        }

        if ($deletedBy !== null && $this->hasFillable('deleted_by')) {
            $entity->setAttribute('deleted_by', $deletedBy);
        }

        return $entity->save();
    }

    public function restore(int|string|Model $model): bool
    {
        if (!$this->supportsSoftDeleteFlag()) {
            return false;
        }

        $entity = $this->resolveModel($model, false);
        $entity->setAttribute('delete_status', false);

        if ($this->hasFillable('deleted_at')) {
            $entity->setAttribute('deleted_at', null);
        }

        if ($this->hasFillable('deleted_by')) {
            $entity->setAttribute('deleted_by', null);
        }

        return $entity->save();
    }

    protected function resolveModel(int|string|Model $model, bool $onlyActive = false): Model
    {
        if ($model instanceof Model) {
            return $model;
        }

        return $this->query($onlyActive)->findOrFail($model);
    }

    protected function withRelations(Builder $query, array $relations = []): Builder
    {
        if (empty($relations)) {
            return $query;
        }

        return $query->with($relations);
    }

    protected function supportsSoftDeleteFlag(): bool
    {
        return $this->hasFillable('delete_status');
    }

    protected function hasFillable(string $attribute): bool
    {
        return in_array($attribute, $this->model->getFillable(), true);
    }
}
