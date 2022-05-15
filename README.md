# Install and run with Docker

1. `docker-compose up -d --build`
    * Start the containers
2. `docker run -it --rm -v ${PWD}:/app composer install`
    * Install dependencies
