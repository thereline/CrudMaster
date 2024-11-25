<?php

namespace Thereline\CrudMaster\Services\ActionServices;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasRemoveAction
{
    private ?string $removeActionModel = null;

    /**
     * Remove a model instance by its ID.
     */
    public function removeAction(int|array $data): array
    {

        // Check the result and return appropriate response
        if (is_array($data)) {
            return $this->successRemoveResponse();
        }

        return $this->errorRemoveResponse($data);
    }

    /**
     * Generate a success response for successful removal.
     */
    private function successRemoveResponse(): array
    {
        return [
            'success' => true,
            'error' => false,
            'status' => ResponseAlias::HTTP_OK,
            'message' => trans('crud-master::translations.delete.success', ['model' => $this->getRemoveActionModel()]),
            'data' => [],
        ];
    }

    /**
     * Generate an error response based on the error code.
     */
    private function errorRemoveResponse(int $data): array
    {
        switch ($data) {
            case ExceptionCodes::MODEL_NOT_FOUND:
                $status = ResponseAlias::HTTP_NOT_FOUND;
                $message = trans('crud-master::translations.delete.error', ['model' => $this->getRemoveActionModel()]);
                break;
            case ExceptionCodes::DB_QUERY_ERROR:
                $status = ResponseAlias::HTTP_BAD_REQUEST;
                $message = trans('crud-master::translations.error.query', ['actioning' => 'deleting', 'name' => $this->getRemoveActionModel()]);
                break;
            default:
                $status = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
                $message = trans('crud-master::translations.error.500', ['actioning' => 'deleting', 'name' => $this->getRemoveActionModel()]);
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

    public function setRemoveActionModel(?string $model = null): void
    {
        $this->removeActionModel = basename(str_replace('\\', '/', $model));
    }

    private function getRemoveActionModel(): string
    {

        return is_null($this->removeActionModel) ?
            $this->modelName :
            $this->removeActionModel;
    }
}
