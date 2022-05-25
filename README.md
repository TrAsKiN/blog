# Install and run with Docker

1. `docker run -it --rm -v ${PWD}:/app composer install`
   * Install dependencies
2. `docker-compose up -d --build`
   * Start the containers
