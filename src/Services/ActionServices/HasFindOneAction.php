<?php

namespace Thereline\CrudMaster\Services\ActionServices;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasFindOneAction
{
    private ?string $findActionModel = null;

    /**
     * Retrieve a single model instance by its ID with optional relationships and callback function.
     */
    public function findOneAction(int|array $data): array
    {

        // Check if the retrieval was successful
        if (is_array($data)) {
            return $this->successFindOneResponse($data);
        }

        return $this->errorFindOneResponse($data);
    }

    /**
     * Generate a success response.
     */
    private function successFindOneResponse(array $data): array
    {
        return [
            'success' => true,
            'error' => false,
            'status' => ResponseAlias::HTTP_FOUND,
            'message' => trans('crud-master::translations.getOne.success', ['model' => $this->getFindActionModel()]),
            'data' => $data,
        ];
    }

    /**
     * Generate an error response based on the error code.
     */
    private function errorFindOneResponse(int $data): array
    {
        switch ($data) {
            case ExceptionCodes::MODEL_NOT_FOUND:
                $status = ResponseAlias::HTTP_NOT_FOUND;
                $message = trans('crud-master::translations.getOne.error', ['model' => $this->getFindActionModel()]);
                break;
            case ExceptionCodes::DB_QUERY_ERROR:
                $status = ResponseAlias::HTTP_BAD_REQUEST;
                $message = trans('crud-master::translations.error.query', ['actioning' => 'getting', 'name' => $this->getFindActionModel()]);
                break;
            default:
                $status = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
                $message = trans('crud-master::translations.error.500', ['actioning' => 'getting', 'name' => $this->getFindActionModel()]);
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

    public function setFindActionModel(?string $model = null): void
    {
        $this->findActionModel = basename(str_replace('\\', '/', $model));
    }

    private function getFindActionModel(): string
    {
        return is_null($this->findActionModel) ?
            $this->modelName :
            $this->findActionModel;
    }
}
