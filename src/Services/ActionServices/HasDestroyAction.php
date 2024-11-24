<?php

namespace Thereline\CrudMaster\Services\ActionServices;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasDestroyAction
{
    private ?string $destroyActionModel = null;

    /**
     * Destroy a model instance by its ID.
     */
    public function destroyAction(int|array $data): array
    {

        // Check the result and return appropriate response
        if (is_array($data)) {
            return $this->successDestroyResponse();
        }

        return $this->errorDestroyResponse($data);
    }

    /**
     * Generate a success response for successful destroy.
     */
    private function successDestroyResponse(): array
    {
        return [
            'success' => true,
            'error' => false,
            'status' => ResponseAlias::HTTP_OK,
            'message' => trans('crud-master::translations.delete.success', ['model' => $this->getDestroyActionModel()]),
            'data' => [],
        ];
    }

    /**
     * Generate an error response based on the error code.
     */
    private function errorDestroyResponse(int $errorCode): array
    {
        switch ($errorCode) {
            case ExceptionCodes::MODEL_NOT_FOUND:
                $status = ResponseAlias::HTTP_NOT_FOUND;
                $message = trans('crud-master::translations.delete.error', ['model' => $this->getDestroyActionModel()]);
                break;
            case ExceptionCodes::DB_QUERY_ERROR:
                $status = ResponseAlias::HTTP_BAD_REQUEST;
                $message = trans('crud-master::translations.error.query', ['actioning' => 'deleting', 'name' => $this->getDestroyActionModel()]);
                break;
            default:
                $status = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
                $message = trans('crud-master::translations.error.500', ['actioning' => 'deleting', 'name' => $this->getDestroyActionModel()]);
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

    public function setDestroyActionModel(?string $model = null): void
    {
        $this->destroyActionModel = basename(str_replace('\\', '/', $model));
    }

    private function getDestroyActionModel(): string
    {

        return is_null($this->destroyActionModel) ?
            $this->modelName :
            $this->destroyActionModel;
    }
}
