<?php

namespace Thereline\CrudMaster\Services\DataServices;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Thereline\CrudMaster\Exceptions\DBExceptions;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasCreateEntity
{
    private ?Model $createEntityClass = null;

    /**
     * Create a new record
     */
    public function createEntity(array $data, array $relationships = []): array|int
    {
        try {

            $model = $this->getCreateEntityClass();

            // Begin transaction to ensure data integrity
            DB::beginTransaction();

            // Fill and save the primary Entity
            $model->fill($data)->saveOrFail();

            // Update relationships if relationship data is provided
            if (! empty($relationships)) {
                $this->createModelRelationships($model, $relationships);
                $model->refresh();
            }

            // Commit transaction after all successful saves
            DB::commit();

            // load relationships to model as array
            $result = $model->load(array_keys($relationships))->toArray();

            Log::info(class_basename($model).' and relationships created successfully');

            return $result;

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
            if (DBExceptions::isNotNullViolation($e)) {
                return ExceptionCodes::DB_NOT_NULL_VIOLATION;
            }

            return ExceptionCodes::DB_QUERY_ERROR;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            Log::error($e);

            return ExceptionCodes::FATAL_ERROR;
        }
    }

    private function createModelRelationships(Model $model, array $relationships): void
    {
        // Define relationship handlers
        $relationshipHandlers = [
            Relations\HasOne::class => 'createHasOneOrMorphOne',
            Relations\MorphOne::class => 'createHasOneOrMorphOne',
            Relations\HasMany::class => 'createHasManyOrMorphMany',
            Relations\MorphMany::class => 'createHasManyOrMorphMany',
            Relations\BelongsTo::class => 'createBelongsTo',
            Relations\BelongsToMany::class => 'createBelongsToManyOrMorphToMany',
            Relations\MorphToMany::class => 'createBelongsToManyOrMorphToMany',
            Relations\MorphTo::class => 'createMorphTo',
        ];

        // Process relationships if any
        foreach ($relationships as $relationName => $relationData) {

            if (! method_exists($model, $relationName)) {
                throw new RelationNotFoundException("Undefined relationship method: $relationName");
            }
            if (! is_array($relationData)) {
                throw new Exception('Invalid data format', ExceptionCodes::INVALID_DATA_FORMAT);
            }

            $relationModel = $model->$relationName();
            $relationClass = get_class($relationModel);

            // Check if the relationship class has a handler method defined
            if (isset($relationshipHandlers[$relationClass])) {
                $handlerMethod = $relationshipHandlers[$relationClass];
                $this->$handlerMethod($model, $relationModel, $relationName, $relationData);
            } else {
                throw new RelationNotFoundException;
            }
        }
    }

    private function getCreateEntityClass(): ?Model
    {
        return is_null($this->createEntityClass) ? $this->modelClass : $this->createEntityClass;
    }

    public function setCreateEntityClass(?Model $model): void
    {

        $this->createEntityClass = $model;
    }

    private function createHasOneOrMorphOne($model, $relation, $relationName, $relationData): void
    {
        // Create a related model instance for HasOne or MorphOne relationship
        $relation->create($relationData);
    }

    private function createHasManyOrMorphMany($model, $relation, $relationName, $relationData): void
    {
        // Create multiple related model instances for HasMany or MorphMany relationship
        foreach ($relationData as $singleRelationData) {
            $relation->create($singleRelationData);
        }
    }

    private function createBelongsTo($model, $relation, $relationName, $relationData): void
    {
        // Create a related model instance for BelongsTo relationship and associate it
        $relatedModelClass = $relation->getModel()::class;
        $relatedModel = $relatedModelClass::create($relationData);
        $model->$relationName()->associate($relatedModel);
        $model->save();
    }

    private function createBelongsToManyOrMorphToMany($model, $relation, $relationName, $relationData): void
    {

        // Create related model instances for BelongsToMany or MorphToMany relationship and attach them with pivot data if provided
        foreach ($relationData as $singleRelationData) {
            if (is_array($singleRelationData) && isset($singleRelationData['pivot'])) {
                // Separate the related model data and pivot data
                $relatedModelData = $singleRelationData;
                $pivotData = $relatedModelData['pivot'];
                unset($relatedModelData['pivot']);

                // Create the related model and attach with pivot data
                $relatedModelClass = $relation->getModel()::class;
                $relatedModel = $relatedModelClass::create($relatedModelData);
                $relation->attach($relatedModel->id, $pivotData);
            } elseif (is_array($singleRelationData)) {
                // Handle without pivot data
                $relatedModelClass = $relation->getModel()::class;
                $relatedModel = $relatedModelClass::create($singleRelationData);
                $relation->attach($relatedModel->id);
            } else {
                // Attach existing related model id without additional data
                $relation->attach($singleRelationData);
            }
        }

    }

    private function createMorphTo($model, $relation, $relationName, $relationData): void
    {
        // Create a related model instance for MorphTo relationship and associate it
        $relatedModelClass = $relationData['model'];
        unset($relationData['model']);
        $relatedModel = $relatedModelClass::create($relationData);
        $model->$relationName()->associate($relatedModel);
        $model->save();
    }
}
