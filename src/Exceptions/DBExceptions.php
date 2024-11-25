<?php

namespace Thereline\CrudMaster\Exceptions;

use Illuminate\Database\QueryException;

class DBExceptions
{
    public static function isNotNullViolation(QueryException $e): bool
    {
        // SQLSTATE error codes for constraint violations
        $constraintViolationCodes = [
            '23000', // Generic integrity constraint violation
            '23502', // PostgreSQL: Not null violation
            '23503', // PostgreSQL: Foreign key violation
            '23505', // PostgreSQL: Unique violation
            '1452',  // MySQL: Cannot add or update a child row: a foreign key constraint fails
            '1062',  // MySQL: Duplicate entry for unique constraint
        ];

        // Extract the SQLSTATE code
        $sqlState = $e->errorInfo[0] ?? null;

        // Extract the error code for MySQL-specific cases
        $errorCode = $e->errorInfo[1] ?? null;

        return in_array($sqlState, $constraintViolationCodes) || in_array($errorCode, $constraintViolationCodes);
    }
}
