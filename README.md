# CrudMaster
A Laravel package for easy CRUD generation. Generate models, repositories, services, controllers, and views effortlessly with customizable options for Blade or Vue.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thereline/crudmaster.svg?style=flat-square)](https://packagist.org/packages/thereline/crudmaster)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/thereline/crudmaster/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/thereline/crudmaster/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/thereline/crudmaster/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/thereline/crudmaster/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/thereline/crudmaster.svg?style=flat-square)](https://packagist.org/packages/thereline/crudmaster)

CrudMaster is a powerful and flexible Laravel package designed to simplify the process of generating CRUD (Create, Read, Update, Delete) operations for any model. With CrudMaster, developers can quickly scaffold complete CRUD functionality, including models, repositories, action services, controllers, and views, saving time and reducing repetitive tasks.

## Key Features:
**Artisan Command:** Easily generate CRUD operations using a single Artisan command.

**Customizable Views:** Choose between Blade or Vue.js for your front-end views.

**Repository Pattern:** Implements the repository pattern for clean and maintainable code.

**Action Services:** Encapsulate business logic in service classes for better code organization.

**Automatic Migrations:** Automatically generate and run migrations for your models.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/CrudMaster.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/CrudMaster)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require thereline/crudmaster
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="crudmaster-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="crudmaster-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="crudmaster-views"
```

## Usage

```php
$crudMaster = new Thereline\CrudMaster();
echo $crudMaster->echoPhrase('Hello, Thereline!');
```
1. Generate CRUD:
```php
php artisan crudmaster:generate ModelName --columns=name,email,password --views=blade
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [elcomware](https://github.com/elcomnware)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
