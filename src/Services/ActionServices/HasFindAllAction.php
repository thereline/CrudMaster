<?php

namespace Thereline\CrudMaster\Services\ActionServices;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;

trait HasFindAllAction
{
    private ?string $findAllActionModel = null;

    public function findAllAction(array|int $data): array
    {

        if (is_array($data)) {
            return $this->successFindAllResponse($data);
        }

        return $this->errorFindAllResponse($data);
    }

    private function successFindAllResponse($data): array
    {
        return [
            'success' => true,
            'error' => false,
            'status' => ResponseAlias::HTTP_OK,
            'message' => trans('crud-master::translations.getAll.success', ['model' => $this->getFindAllActionModel()]),
            'data' => $data,
        ];
    }

    private function errorFindAllResponse(int $data): array
    {
        switch ($data) {
            case ExceptionCodes::FATAL_ERROR:
                $status = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
                $message = trans('crud-master::translations.getAll.error', ['model' => $this->getFindAllActionModel()]);
                break;
            case ExceptionCodes::DB_QUERY_ERROR:
                $status = ResponseAlias::HTTP_BAD_REQUEST;
                $message = trans('crud-master::translations.error.query', ['actioning' => 'getting', 'name' => $this->getFindAllActionModel()]);
                break;
            default:
                $status = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
                $message = trans('crud-master::translations.error.500', ['actioning' => 'getting', 'name' => $this->getFindAllActionModel()]);
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

    public function setFindAllActionModel(?string $model = null): void
    {
        $this->findAllActionModel = basename(str_replace('\\', '/', $model));
    }

    protected function getFindAllActionModel(): string
    {
        return is_null($this->findAllActionModel) ?
            $this->modelName :
            $this->findAllActionModel;
    }
}
