<?php

namespace Thereline\CrudMaster\Services\EntityServices;

use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Thereline\CrudMaster\ExceptionCodes;
use Thereline\CrudMaster\HasCrudMastery;

trait HasCreateEntity
{
    use HasCrudMastery;


    /**
     * Create a new record
     * @param array $data
     * @param array $relationships
     * @return array|int
     */
    public function createEntity(array $data, array $relationships = []): array|int
    {
        try {

            $model = $this->model;

            // Begin transaction to ensure data integrity
            DB::beginTransaction();


            // Fill and save the primary model
            $model->fill($data)->saveOrFail();

            // Handle relationships if provided
            if ($relationships){
                foreach ($relationships as $relation => $relationData) {
                    //check relation exist
                    if (method_exists($model, $relation)) {
                        if (is_array($relationData) && $this->is_assoc($relationData)) {
                            // Single relationship data (associative array)
                            // Associate single relationship for "belongsTo" or "hasOne"
                            $relatedModel = $model->$relation()->create($relationData);
                        } elseif (is_array($relationData) && !$this->is_assoc($relationData)) {
                            // Multiple relationship data (array of associative arrays)
                            // Create related models if relationship is a "hasMany" or "morphMany"
                            $relatedModels = collect($relationData)->map(function ($data) use ($model, $relation) {
                                return $model->$relation()->create($data);
                            });
                        }
                    }
                }

            }

            // Commit transaction after all successful saves
            DB::commit();

            // load relationships to model as array
            $result = $model->load(array_keys($relationships))->toArray();

        } catch (RelationNotFoundException $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            Log::error($e);
            return ExceptionCodes::MODEL_RELATIONSHIP_NOTFOUND;

        } catch (UniqueConstraintViolationException $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            Log::error($e);
            return ExceptionCodes::DB_UNIQUE_VIOLATION_ERROR;

        } catch (QueryException $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            Log::error($e);

            return ExceptionCodes::DB_QUERY_ERROR;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            Log::error($e);
            return ExceptionCodes::FATAL_ERROR;
        }

        Log::info(class_basename($model) .' and relationships created successfully');

        return $result;
    }

    // Helper function to check if an array is associative
    function is_assoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

}
