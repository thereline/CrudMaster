<?php

namespace Thereline\CrudMaster\Services\DataServices;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasFindAllEntities
{
    private ?Model $findEntitiesClass = null;

    private array $filterBy = [];

    private array $relationsAndFilterBy = [];

    /**
     * Retrieve all records
     */
    public function findAllEntities(
        array $filters = [],
        string $orderBy = 'created_at',
        string $orderDirection = 'desc',
        int $perPage = 15,
        array $withRelations = [],
        ?callable $throughFunction = null
    ): array|int {
        try {
            $model = $this->getFindEntitiesClass();
            $query = $model->newQuery(); // Start the query

            // Eager load relationships if provided
            if (! empty($withRelations)) {
                $query->with($withRelations);
            }

            //Apply filter if given
            if (! empty($filters)) {
                $this->applyFilter($filters, $query);
            }

            // Apply ordering
            $query->orderBy($orderBy, $orderDirection);

            // Paginate results
            $results = $query->paginate($perPage)->withQueryString();

            // Apply additional transformation if a function is provided
            if ($throughFunction) {
                $results->setCollection($throughFunction($results->getCollection()));
            }

            Log::info(class_basename($model).' models retrieved successfully');

            return $results->toArray(); // Return the paginated results as an array
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            Log::error($e);

            return ExceptionCodes::DB_QUERY_ERROR; // Return a specific error code for database query errors
        } catch (\Throwable $e) {
            Log::warning($e->getMessage());
            Log::error($e);

            return ExceptionCodes::FATAL_ERROR; // Return a specific error code for general errors
        }
    }

    private function applyFilter(array $filters, Builder $query): void
    {

        $search = $filters['search'] ?? null;
        $trashed = $filters['trashed'] ?? null;
        $active = $filters['active'] ?? null;
        $filterBy = $this->getFilterBy(); // Get fields to filter by
        $relationsAndFilterBy = $this->getRelationsAndFilterBy(); // Get relationship fields to filter by

        // Apply filters to the query
        $query->where(function (Builder $query) use ($filterBy, $search, $active) {
            if (isset($active)) {
                $query->where('active', $active)
                    ->WhereAny($filterBy, 'like', '%'.$search.'%');
                //$query->ddRawSql();
            } else {
                $query->orWhereAny($filterBy, 'like', '%'.$search.'%');
            }
        });

        // Apply relationship filters if initial query returns no results
        if ($query->get()->isEmpty() && ! empty($relationsAndFilterBy)) {
            foreach ($relationsAndFilterBy as $relation => $fields) {
                $query->orWhereHas($relation, function (Builder $query) use ($fields, $search) {
                    $query->whereAny($fields, 'like', '%'.$search.'%');
                });
            }
        }

        // Apply trashed filter
        if ($filters['trashed'] ?? false) {
            switch ($filters['trashed']) {
                case 'with':
                    $query->withTrashed();
                    break;
                case 'only':
                    $query->onlyTrashed();
                    break;
            }
        }

    }

    private function getFilterBy(): array
    {
        return $this->filterBy;
    }

    private function getFindEntitiesClass(): ?Model
    {
        return is_null($this->findEntitiesClass) ? $this->modelClass : $this->findEntitiesClass;
    }

    public function setFilterBy(array $filterBy): void
    {
        $this->filterBy = $filterBy;
    }

    private function getRelationsAndFilterBy(): array
    {
        return $this->relationsAndFilterBy;
    }

    public function setRelationsAndFilterBy(array $relationsAndFilterBy): void
    {
        $this->relationsAndFilterBy = $relationsAndFilterBy;
    }

    public function setFindEntitiesClass(?Model $findEntitiesClass): void
    {
        $this->findEntitiesClass = $findEntitiesClass;
    }
}
