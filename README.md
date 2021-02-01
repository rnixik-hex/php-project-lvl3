### Hexlet tests and linter status:
[![Actions Status](https://github.com/rnixik-hex/php-project-lvl3/workflows/hexlet-check/badge.svg)](https://github.com/rnixik-hex/php-project-lvl3/actions)
[![Linter](https://github.com/rnixik-hex/php-project-lvl3/workflows/Linter/badge.svg)](https://github.com/rnixik-hex/php-project-lvl3/actions)
[![Tests](https://github.com/rnixik-hex/php-project-lvl3/workflows/Tests/badge.svg)](https://github.com/rnixik-hex/php-project-lvl3/actions)
[![Maintainability](https://api.codeclimate.com/v1/badges/c2e18a0c491c978a878f/maintainability)](https://codeclimate.com/github/rnixik-hex/php-project-lvl3/maintainability)

## Heroku url

https://aqueous-oasis-21676.herokuapp.com/

## How to run locally

1. `make install` to install composer dependencies
2. `./sail up -d` to start docker containers
3. `./sail artisan key:generate --ansi` to generate app key
4. Open default address http://127.0.0.1:9505

To run artisan command use `./sail artisan`: `./sail artisan list`

## How to test:

1. Run locally
2. Run `make test`
