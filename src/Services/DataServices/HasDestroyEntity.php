<?php

namespace Thereline\CrudMaster\Services\DataServices;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Thereline\CrudMaster\CrudMaster;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasDestroyEntity
{
    private ?Model $destroyEntityClass = null;

    public function DestroyEntity(
        int $id,
        array $withRelations = [],
        ?callable $throughFunction = null
    ): array|int {

        try {
            $model = $this->getDestroyEntityClass();

            DB::beginTransaction();
            // Start the query and eager load relationships if provided
            $query = $model->newQuery();
            if (! empty($withRelations)) {
                $query->with($withRelations);
            }

            // Find the entity by ID or fail
            $entity = $query->findOrFail($id);

            // Update the entity with the provided data
            if (! empty($entity)) {

                $entity->Delete();
            }

            // Update relationships if relationship data is provided
            if (! empty($relationshipData)) {
                $this->destroyEntityRelationships($entity, $relationshipData);
                $entity->refresh();
            }

            // Reload the entity with relationships if provided
            if (! empty($withRelations)) {
                $entity->load($withRelations);
            }

            // Apply additional transformation if a function is provided
            if ($throughFunction) {
                $entity = $throughFunction($entity);
            }

            DB::commit();

            Log::info(class_basename($model).'  model updated successfully');

            return $entity->toArray();

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning($e->getMessage());

            return ExceptionCodes::MODEL_NOT_FOUND; // Return a specific error code for not found
        } catch (QueryException $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            Log::error($e);

            return ExceptionCodes::DB_QUERY_ERROR; // Return a specific error code for database query errors
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            Log::error($e);

            return ExceptionCodes::FATAL_ERROR; // Return a specific error code for general errors
        }
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    private function destroyEntityRelationships(Model $model, array $relationshipData): void
    {
        $relationshipHandlers = [
            HasOne::class => 'destroyHasOneRelationship',
            MorphOne::class => 'destroyHasOneRelationship',
            HasMany::class => 'destroyHasManyRelationship',
            MorphMany::class => 'destroyHasManyRelationship',
            BelongsToMany::class => 'destroyBelongsToManyRelationship',
            MorphToMany::class => 'destroyBelongsToManyRelationship',
            BelongsTo::class => 'destroyBelongsToRelationship',
            MorphTo::class => 'destroyBelongsToRelationship',
        ];

        foreach ($relationshipData as $relationName => $relationData) {
            // Check if the relationship method exists on the model
            if (! method_exists($model, $relationName)) {
                throw new Exception("Undefined relationship method: $relationName");
            }
            if (is_array($relationData)) {
                // Get the relationship object and its type
                $relatedModel = $model->{$relationName}();
                $relationClass = get_class($relatedModel);

                // Handle the relationship update if a handler exists
                if (isset($relationshipHandlers[$relationClass])) {
                    $handlerMethod = $relationshipHandlers[$relationClass];
                    //dd($handler());
                    $this->$handlerMethod($relatedModel, $relationData);
                } else {
                    throw new Exception("Unsupported relationship type: $relationClass");
                }
            } else {
                throw new Exception('Invalid data format:'.$relationData.' Should be array');
            }

        }
    }

    // Function to handle HasOne relationships
    private function getDestroyEntityClass(): ?Model
    {
        return is_null($this->destroyEntityClass) ? $this->modelClass : $this->destroyEntityClass;
    }

    /**
     * @throws \Throwable
     */
    private function destroyHasOneRelationship(Relation $relatedModel, array $relationData): void
    {

        $relatedInstance = $relatedModel->firstOrFail();
        if ($relatedInstance) {
            $relatedInstance->updateOrFail($relationData);
        } else {
            $relatedModel->create($relationData);
        }
    }

    // Function to handle HasMany relationships
    /**
     * @throws \Throwable
     */
    private function destroyHasManyRelationship(Relation $relatedModel, array $relationData): void
    {
        foreach ($relationData as $item) {
            $relatedModel->updateOrCreate(['id' => $item['id'] ?? null], $item);
        }

        //$relatedModel->ddRawSql();
        // One-to-Many or Morph Many
        /*foreach ($relationData as $relatedData) {
            if (isset($relatedData['id'])) {
                $relatedModel = $relatedModel->findOrFail($relatedData['id']);
                if ($relatedModel) {
                    $relatedModel->updateOrFail($relatedData);
                }
            } else {
                $relatedModel->create($relatedData);
            }
        }*/

    }

    // Function to handle BelongsToMany relationships
    private function destroyBelongsToManyRelationship(Relation $relatedModel, array $relationData): void
    {

        //Get the relationship parent
        $parentModelClass = get_class($relatedModel->getRelated());

        //check if data array kind
        if (CrudMaster::isAssocArray($relationData)) {
            $parentModel = $parentModelClass::updateOrCreate(
                ['id' => $relationData['id'] ?? null],
                $relationData
            );
            // Sync the relationship
            $relatedModel->syncWithoutDetaching([$parentModel->id]);
        } else {

            foreach ($relationData as $item) {
                $parentModel = $parentModelClass::updateOrCreate(
                    ['id' => $item['id'] ?? null],
                    $item
                );

                // Sync the relationship
                $relatedModel->syncWithoutDetaching([$parentModel->id]);
            }
        }
    }

    // Function to handle BelongsTo relationships
    private function destroyBelongsToRelationship(Relation $relatedModel, array $relationData): void
    {

        if (CrudMaster::isAssocArray($relationData)) {
            $updatedModel = $relatedModel->updateOrCreate(['id' => $relationData['id'] ?? null], $relationData);
        } else {
            //Update the relation model
            foreach ($relationData as $item) {
                $updatedModel = $relatedModel->updateOrCreate(['id' => $item['id'] ?? null], $item);
            }
        }

        //associate the updated parent model with child
        $relatedModel->associate($updatedModel);
        $relatedModel->getParent()->save();
    }

    public function setDestroyEntityClass(?Model $destroyEntityClass): void
    {
        $this->destroyEntityClass = $destroyEntityClass;
    }
}
