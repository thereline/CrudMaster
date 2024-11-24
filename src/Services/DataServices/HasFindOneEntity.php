<?php

namespace Thereline\CrudMaster\Services\DataServices;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasFindOneEntity
{
    private ?Model $findEntityClass = null;

    /**
     * Get a single record by its
     **/
    public function findOneEntity(
        int $id,
        array $withRelations = [],
        ?callable $throughFunction = null
    ): array|int {
        try {
            $model = $this->getFindEntityClass();

            // Start the query and eager load relationships if provided
            $query = $model->newQuery();
            if (! empty($withRelations)) {
                $query->with($withRelations);
            }

            // Find the entity by ID or fail
            $entity = $query->findOrFail($id);

            // Apply additional transformation if a function is provided
            if ($throughFunction) {
                $entity = $throughFunction($entity);
            }

            Log::info(class_basename($model).' model retrieved successfully');

            return $entity->toArray();
        } catch (ModelNotFoundException $e) {
            Log::warning($e->getMessage());

            return ExceptionCodes::MODEL_NOT_FOUND; // Return a specific error code for not found
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

    private function getFindEntityClass(): ?Model
    {
        return is_null($this->findEntityClass) ? $this->modelClass : $this->findEntityClass;
    }

    public function setFindEntityClass(?Model $findEntityClass): void
    {
        $this->findEntityClass = $findEntityClass;
    }
}
