# Quoter

Quoter is an application that is responsible to convert quotes of famous people into its shouting version


## Dependencies

- Docker

## Installation

To install and run this application you need to run the following command from the root folder.

```
make dev
```

## Usage

If for any reason you want to "turn off" your services. Run the following command from the root folder. 

```
make down
```

## Architecture

High-level and solutions architecture with the data caching logic is documented here -  [link](https://excalidraw.com/#room=9ea957ec4190bf778cbb,mEdlWUcapIieeimujinjcw)


## Tests

Tests are covered using PHPSpec for the unit and integration tests and Behat for the E2E.

## Docker Containers

- Redis - 1st layer caching
- Mysql DB
- RabbitMQ
- PHP-FPM
- NGINX
- 2x RabbitMQ consumers to write to Redis
