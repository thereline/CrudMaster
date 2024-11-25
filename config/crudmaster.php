<?php

// config for Thereline/CrudMaster
return [

    'useValidation' => true,
    'useTranslation' => true,
    'folder' => [
        1 => 'Services|-UserService|-UserDataService
                                 |-USerActionService',
        2 => 'Services|-DataServices/UserDataService
                    |-ActionServices/UserActionService',
        3 => '|-Repository/UserRepository
            |-Actions/UserActions',
    ],

    'generate' => [
        'controller' => true,
        'model_service' => true,
        'routes' => [
            'type' => 'both', //or api or web
            'structure' => 'merge', // or separate
        ],
        'views' => 'vue', // or blade
        'migration' => true,
        'factory' => true,
        'seeder' => true,
    ],
];
