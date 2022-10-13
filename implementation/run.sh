#!/bin/bash

docker build -f ./Dockerfile -t sowiso-php-api-sdk ../
docker run -d -p 8081:80 -v "$PWD/..":/var/www/html --env-file .env --name sowiso-php-api-sdk sowiso-php-api-sdk
