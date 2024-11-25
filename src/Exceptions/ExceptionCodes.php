<?php

namespace Thereline\CrudMaster\Exceptions;

class ExceptionCodes
{
    const FATAL_ERROR = 100;

    const DUPLICATE_NAME_ERROR = 101;

    const ACTION_ERROR = 200;

    const ACTION_NOT_AUTHORIZED = 201;

    const DB_ERROR = 400;

    const DB_TABLE_ERROR = 401;

    const DB_UNIQUE_VIOLATION_ERROR = 402;

    const DB_QUERY_ERROR = 403;

    const DB_NOT_NULL_VIOLATION = 404;

    const TRANSLATION_FIELD_ERROR = 500;

    const TRANSLATION_FIELD_UNDEFINED = 501;

    const REQUEST_INPUT_ERROR = 600;

    const REQUEST_INPUT_NOTFOUND = 601;

    const REQUEST_INPUT_INVALID = 602;

    const REQUEST_INPUT_EMPTY = 603;

    const MODEL_ERROR = 700;

    const MODEL_NOT_FOUND = 701;

    const MODEL_RELATIONSHIP_NOTFOUND = 702;

    const INVALID_DATA = 800;

    const INVALID_DATA_FORMAT = 801;
}
