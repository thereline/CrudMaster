<?php

namespace Thereline\CrudMaster\Services\ActionServices;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasUpdateAction
{
    private ?string $updateActionModel = null;

    /**
     * Update a model instance with the given data and relationships.
     */
    public function updateAction(int|array $data): array
    {

        // Check if the update was successful
        if (is_array($data)) {
            return $this->successUpdateResponse($data);
        }

        return $this->errorUpdateResponse($data);
    }

    /**
     * Generate a success response.
     */
    private function successUpdateResponse(array $data): array
    {
        return [
            'success' => true,
            'error' => false,
            'status' => ResponseAlias::HTTP_OK,
            'message' => trans('crud-master::translations.update.success', ['model' => $this->getUpdateActionModel()]),
            'data' => $data,
        ];
    }

    /**
     * Generate an error response based on the error code.
     */
    private function errorUpdateResponse(int $data): array
    {
        switch ($data) {
            case ExceptionCodes::MODEL_NOT_FOUND:
                $status = ResponseAlias::HTTP_NOT_FOUND;
                $message = trans('crud-master::translations.update.error', ['model' => $this->getUpdateActionModel()]);
                break;
            case ExceptionCodes::DB_UNIQUE_VIOLATION_ERROR:
                $status = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
                $message = trans('crud-master::translations.error.unique', ['model' => $this->getUpdateActionModel()]);
                break;
            case ExceptionCodes::DB_QUERY_ERROR:
                $status = ResponseAlias::HTTP_BAD_REQUEST;
                $message = trans('crud-master::translations.error.query', ['actioning' => 'updating', 'name' => $this->getUpdateActionModel()]);
                break;
            default:
                $status = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
                $message = trans('crud-master::translations.error.500', ['actioning' => 'updating', 'name' => $this->getUpdateActionModel()]);
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

    public function setUpdateActionModel(?string $model = null): void
    {
        $this->updateActionModel = basename(str_replace('\\', '/', $model));
    }

    private function getUpdateActionModel(): string
    {
        return is_null($this->updateActionModel) ?
            $this->modelName :
            $this->updateActionModel;
    }
}
