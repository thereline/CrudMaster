<?php

namespace Thereline\CrudMaster\Services\ActionServices;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasCreateAction
{
    private ?string $createActionModel = null;

    /**
     * Create a new model instance with the given input and relationships.
     */
    public function createAction(array|int $data): array
    {

        // Check if the creation was successful
        if (is_array($data)) {
            return $this->successCreateResponse($data);
        }

        return $this->errorCreateResponse($data);
    }

    /**
     * Generate a success response.
     */
    private function successCreateResponse(array $data): array
    {
        return [
            'success' => true,
            'error' => false,
            'status' => ResponseAlias::HTTP_CREATED,
            'message' => trans('crud-master::translations.create.success', ['model' => $this->getCreateActionModel()]),
            'data' => $data,
        ];
    }

    /**
     * Generate an error response based on the error code.
     */
    private function errorCreateResponse(int $data): array
    {
        switch ($data) {
            case ExceptionCodes::MODEL_RELATIONSHIP_NOTFOUND:
                $status = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
                $message = trans('crud-master::translations.create.error', ['model' => $this->getCreateActionModel()]);
                break;
            case ExceptionCodes::DB_UNIQUE_VIOLATION_ERROR:
                $status = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
                $message = trans('crud-master::translations.error.unique', ['model' => $this->getCreateActionModel()]);
                break;
            case ExceptionCodes::DB_NOT_NULL_VIOLATION:
                $status = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
                $message = trans('crud-master::translations.error.notnull', ['model' => $this->getCreateActionModel()]);
                break;
            case ExceptionCodes::DB_QUERY_ERROR:
                $status = ResponseAlias::HTTP_BAD_REQUEST;
                $message = trans('crud-master::translations.error.query', ['actioning' => 'creating', 'name' => $this->getCreateActionModel()]);
                break;
            default:
                $status = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
                $message = trans('crud-master::translations.error.500', ['actioning' => 'creating', 'name' => $this->getCreateActionModel()]);
                break;
        }

        return [
            'data' => [],
            'status' => $status,
            'message' => $message,
            'error' => true,
            'success' => false,
        ];
    }

    public function setCreateActionModel(?string $model = null): void
    {
        $this->createActionModel = basename(str_replace('\\', '/', $model));
    }

    private function getCreateActionModel(): string
    {
        return is_null($this->createActionModel) ?
            $this->modelName :
            $this->createActionModel;
    }
}
