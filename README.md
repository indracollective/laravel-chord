# This is my package chord

[![Latest Version on Packagist](https://img.shields.io/packagist/v/livesource/chord.svg?style=flat-square)](https://packagist.org/packages/livesource/chord)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/livesource/chord/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/livesource/chord/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/livesource/chord/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/livesource/chord/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/livesource/chord.svg?style=flat-square)](https://packagist.org/packages/livesource/chord)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require livesource/chord
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="chord-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="chord-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="chord-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$chord = new LiveSource\Chord();
echo $chord->echoPhrase('Hello, LiveSource!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Shea Dawson](https://github.com/livesource)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
