# PHP Login API with Slim Framework 4 and JWT Auth 

This template should help get you started developing with this API.
We recomend run this project in Docker.

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/)

## Customize configuration

See [Vite Configuration Reference](https://vitejs.dev/config/).

## Project Setup

```sh
docker-compose up
```

### Install Composer Dependencies

```sh
docker run --rm --interactive --tty \
  --volume $PWD:/app \
  composer install
```

## Set Enviroment Variables

See [PHP DotEnv Configuration Reference](https://github.com/vlucas/phpdotenv) and .env.example file.

### Conecting to Database

The HOSTNAME in .env file should be the same of docker-compose file db:container_name
