# Acceptance Tests

This document defines the package acceptance-test scope and how to run it.

## Goal

Acceptance tests verify that the package works as installed in a Laravel app, with real service providers, routes, migrations, and Eloquent models.

## Runtime

- Framework: Laravel Testbench (`orchestra/testbench`)
- Test runner: PHPUnit 11
- Database: in-memory SQLite
- Package provider under test: `MetaFramework\Dictionnaries\DictionnariesServiceProvider`

Base setup is in `tests/TestCase.php`.

## Covered Acceptance Scenarios

- Route registration with default package config.
- Route registration with custom route prefix and name prefix.
- Dictionary creation flow in multilang mode (`mfw.translatable.multilang=true`).
- Dictionary creation flow in single-locale mode (`mfw.translatable.multilang=false`).
- Accessor behavior for `simple` dictionaries (`selectValues` flat map).
- Accessor behavior for `meta` dictionaries (nested parent/child output and `entry()` lookup).
- Entry subclass resolution from configured namespace for custom dictionaries.
- Legacy JSON value fallback when multilang is disabled.

Current test files:

- `tests/Feature/RouteRegistrationTest.php`
- `tests/Feature/CustomRouteConfigurationTest.php`
- `tests/Feature/DictionnaryControllerStoreTest.php`
- `tests/Integration/DictionnariesAccessorTest.php`
- `tests/Integration/ModelBehaviorTest.php`

## Run Acceptance Tests

Run the full suite:

```bash
php vendor/bin/phpunit
```

Run only acceptance-oriented suites:

```bash
php vendor/bin/phpunit --testsuite Feature,Integration
```

Run coverage report (PowerShell):

```powershell
$env:XDEBUG_MODE='coverage'; php vendor/bin/phpunit --coverage-clover coverage.xml
```

## Writing New Acceptance Tests

- Put route/request/UI behavior tests in `tests/Feature`.
- Put data access/model integration behavior in `tests/Integration`.
- Extend `Tests\TestCase` to use package provider bootstrapping and in-memory DB.
- Prefer real HTTP route calls (`get`, `post`, etc.) and DB assertions over heavy mocking.
- If a test changes `mfw.translatable.multilang`, reset the cached value (`mfw.multilang`) before asserting behavior.
