<?php

return [

    'error' => [
        'inertia-missing' => 'Inertia is not installed',
        '500' => 'Something went wrong',
        'result-format' => 'Unexpected result format',
        'unique' => 'One or more :model values failed to be unique',
        'notnull' => 'One or more :model required value(s) is/are empty',
        'query' => 'Query error while :actioning :attribute.',
    ],
    'create' => [
        'success' => 'New :model added successfully',
        'error' => 'Failed to add :model',
    ],
    'update' => [
        'success' => 'The :model with :attribute :value has been updated',
        'error' => 'Failed to update the :model with :attribute :value',
    ],
    'getAll' => [
        'success' => ':model records retrieved successfully',
        'error' => 'Failed to retrieved :model records',
    ],
    'getOne' => [
        'success' => 'The :model record retrieved successfully',
        'error' => 'Failed to retrieved the :model record',
    ],

    'delete' => [
        'success' => 'You are about removing the :model with :attribute :value',
        'error' => 'Failed to remove the :model with :attribute :value',
    ],

    'translatable' => 'translation',
];
