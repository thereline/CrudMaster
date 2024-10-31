<?php

namespace Thereline\CrudMaster\Services\EntityServices;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Thereline\CrudMaster\ExceptionCodes;
use Thereline\CrudMaster\HasCrudMastery;


trait HasGetEntities
{

    use HasCrudMastery;

    /**
     * @var array
     */
    private array $filterBy = [];
    /**
     * @var array
     */
    private array $filterableRelations = [];





    /**
     * Retrieve all records
     * @param array $filters
     * @param string $orderBy
     * @param string $orderDirection
     * @param int $perPage
     * @param array $withRelations
     * @param callable|null $throughFunction
     * @return array|int
     */
    public function getEntities(array $filters = [], string $orderBy = 'created_at', string $orderDirection = 'desc', int $perPage = 15, array $withRelations = [], ?callable $throughFunction = null): array|int
    {
        try {

            $model = $this->model;

            // Start the query
             $query = $model;

            $search = $filters['search'] ?? null;
            $trashed = $filters['trashed'] ?? null;
            $active = $filters['active'] ?? true;

            $filterBy = $this->getFilterBy();
            $relations = $this->getFilterableRelations();

            // Eager load relationships if provided
            if (! empty($withRelations)) {
                $query = $query->with($withRelations);
            }

            // Apply filters if any
            if (! empty($filters)) {

                // Apply relation filters if any
                if (! empty($relations)) {
                    $query = $query->where(function (Builder $query) use ($filterBy, $search, $active) {
                        $query->where('active', $active)
                            ->orWhereAny($filterBy, 'like', '%'.$search.'%');
                    });
                    foreach ($relations as $relation => $fields) {
                        $query->whereHas($relation, function (Builder $query) use ($fields, $search) {
                            $query->whereAny($fields, 'like', '%'.$search.'%');
                        });
                    }

                } else {
                    $query = $query->where(function (Builder $query) use ($filterBy, $search, $active) {
                        $query->where('active', $active)
                            ->whereAny($filterBy, 'like', '%'.$search.'%');
                    });
                }
            }

            // Apply trashed filter
            if ($trashed) {
                if ($trashed === 'with') {
                    $query->withTrashed();
                } elseif ($trashed === 'only') {
                    $query->onlyTrashed();
                }
            }

            // Apply ordering
            $query = $query->orderBy($orderBy, $orderDirection);

            // Paginate results
            $results = $query->paginate($perPage)->withQueryString();

            // Apply additional transformation if a function is provided
            if ($throughFunction) {
                $results->setCollection($throughFunction($results->getCollection()));
            }

        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            Log::error($e);

            return ExceptionCodes::DB_QUERY_ERROR;
        } catch (\Throwable $e) {
            dd($e);
            Log::warning($e->getMessage());
            Log::error($e);

            return ExceptionCodes::FATAL_ERROR;
        }

        Log::info(class_basename($model).' models retrieved successfully');

        return $results->toArray(); // Return the paginated results as an array
    }

    /**
     * @return array
     */
    public function getFilterBy(): array
    {
        return $this->filterBy;
    }

    public function setFilterBy(array $filterBy): void
    {
        $this->filterBy = $filterBy;
    }

    public function getFilterableRelations(): array
    {
        return $this->filterableRelations;
    }

    public function setFilterableRelations(array $filterableRelations): void
    {
        $this->filterableRelations = $filterableRelations;
    }
}
