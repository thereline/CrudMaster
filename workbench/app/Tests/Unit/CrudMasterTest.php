<?php

namespace Thereline\CrudMaster\Tests\Unit;

use Thereline\CrudMaster\CrudMaster;

test('string to pascal case', function () {
    $testCases = [
        'person-manager',
        'person_manager',
        'person manager',
        'personmanager',
        'personManager',
        'PERSON-MANAGER',
        'PERSON_MANAGER',
        'PERSON MANAGER',
        'Person-manager',
        'Person_Manager',
    ];
    foreach ($testCases as $testCase) {
        $out = CrudMaster::stringToPascalCase($testCase);
        expect($out)->toBe('PersonManager');
    }

});
