<h1 align="center">Magento 2 OrderFlow Integration</h1>

## Installation

```bash
composer require realtimedespatch/magento2-orderflow
php bin/magento module:enable RealtimeDespatch_OrderFlow
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

## Compatibility
- Magento 2.4, 2.5, 2.6, 2.7
- PHP 8.0-8.4

## Documentation

The official documentation can be found at the following location:

<a href="https://documentation.realtimedespatch.co.uk/html/Magento2IntegrationGuide/">Documentation</a>

## Contributing

PRs should satisfy the following tests:

### Unit Tests

```bash
vendor/bin/phpunit -c vendor/realtimedespatch/magento2-orderflow/phpunit.xml --coverage-text vendor/realtimedespatch/magento2-orderflow
```

### PHP Compatibility

```bash
vendor/bin/phpcs -p vendor/realtimedespatch/ --standard=PHPCompatibility --runtime-set testVersion 8.0-8.4
```