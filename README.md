# Credit Service
Credit Service app developed in pure PHP.

[Running](#running)


## Running
This project is dockerized, so you'll have to install docker first. After installing docker, run the following commands in the project directory:

```shell
docker build -t credit-service-app:latest .
```
Then make a `.env` file from `.env.example` and fill all the values.
Then you can run the setup by the following command:
```shell
docker compose up -d
```
It will create `.data` folder in the project root, which holds mysql and redis data and runs 3 containers. Now you must install dependencies by running:
```shell
docker exec credit-service-app composer install
```
Now you must build database tables with:
```shell
docker exec credit-service-app php public/index.php setup:database
```

## Running Commands
You can run any of project commands by this:
```shell
docker exec credit-service-app php public/index.php <COMMAND>
```

You can get list of project's commands by:
```shell
docker exec credit-service-app php public/index.php list
```

## Running Tests
```shell
docker exec credit-service-app ./vendor/bin/phpunit --configuration phpunit.xml
```

## Database Seed
To create many users as you want, use this command:
```shell
docker exec credit-service-app php public/index.php setup:generate:users <NUM>
```
