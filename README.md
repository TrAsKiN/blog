# Blog [![SymfonyInsight](https://insight.symfony.com/projects/a4b9c445-a35c-4a8f-b74f-e2bd66ca711f/mini.svg)](https://insight.symfony.com/projects/a4b9c445-a35c-4a8f-b74f-e2bd66ca711f)

The project has been developed and optimized for use with Docker.

### Requirements

 * PHP 8.1 (with PDO & Intl extensions)
 * MySQL 8.0
 * Composer 2.3
 * Node.js 18.4

## Install, build and run

 1. Clone or download the source code and extract it.

 2. In the root folder of the project, run the following commands to install and build the necessary dependencies:
    1. `npm ci` & `npm run build`
       * Allows to install and build Node.js dependencies

    2. `composer install`
       * Allows to install PHP dependencies

 3. Import the database structure contained in the `blog.sql` file.

 4. Launch the local internal PHP server: `php -S localhost:8000 -t public/`

 5. Access the blog at: http://localhost:8000

### Configuration

If you need to modify configuration items such as database access or mail gateway, just edit the `config.php` file at the root of the project.

If this file has not been created, you can use the `config.php.dist` template by duplicating it and renaming it `config.php`.

### Fake default dataset

You can load fake datasets (users and blog posts) by running the command:

 * `php bin/console fixtures:load`

### Alternative installation with Docker

 1. `docker run -it --rm -v ${PWD}:/app composer install`
    * Install php dependencies

 2. `docker run -it --rm -v ${PWD}:/app -w /app node npm ci`
    * Install node dependencies

 3. `docker run -it --rm -v ${PWD}:/app -w /app node npm run build`
    * Build node dependencies

 4. `docker-compose up -d --build`
    * Start the containers
    * Access the blog at: http://localhost
 
 5. `docker-compose exec app php bin/console fixtures:load`
    * Add fake default datasets
