<?php

beforeEach(function () {
    $this->entityHeleperUser = new class
    {
        use \Thereline\CrudMaster\Traits\HasEntityHelpers;
    };

    $this->actionHelperUser = new class
    {
        use \Thereline\CrudMaster\Traits\HasActionHelpers;
    };

});

it(' sets and gets model class', function () {
    $this->entityHeleperUser->setModelClass(new \Workbench\App\Models\User);
    expect($this->entityHeleperUser->getModelClass())->toBeInstanceOf(\Workbench\App\Models\User::class);
});

it(' sets and gets model name', function () {
    $this->actionHelperUser->setModelName(\Workbench\App\Models\User::class);
    expect($this->actionHelperUser->getModelName())->toBe('User');
});
