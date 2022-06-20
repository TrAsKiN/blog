# Blog

The project has been developed and optimized for use with Docker.

### Requirements

 * **The project requires PHP 8.1, MySQL 8.0 and Node.js 18.4**

## Install, build and run with PHP and MySQL CLI

*It is necessary that PHP must be configured with the PDO extension enabled!*

 1. `mysql -u root -p blog < blog.sql`
    * Import the database structure

 2. `npm ci` & `npm run build`
    * Install and build node dependencies

 3. `composer install`
    * Install php dependencies

 4. `php -S localhost:8000 -t public/`
    * Start the internal php server
    * Access the blog at: http://localhost:8000

## Install, build and run with Docker

 1. `docker run -it --rm -v ${PWD}:/app composer install`
    * Install php dependencies

 2. `docker run -it --rm -v ${PWD}:/app -w /app node npm ci`
    * Install node dependencies

 3. `docker run -it --rm -v ${PWD}:/app -w /app node npm run build`
    * Build node dependencies

 4. `docker-compose up -d --build`
    * Start the containers
    * Access the blog at: http://localhost

## Configuration

If you need to modify configuration items such as database access or mail gateway, just edit the `config.php` file at the root of the project.

If this file has not been created, you can use the `config.php.dist` template by duplicating it and renaming it `config.php`.

### Code quality

[![SymfonyInsight](https://insight.symfony.com/projects/a4b9c445-a35c-4a8f-b74f-e2bd66ca711f/big.svg)](https://insight.symfony.com/projects/a4b9c445-a35c-4a8f-b74f-e2bd66ca711f)
