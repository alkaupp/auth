[![Build Status](https://travis-ci.com/alkaupp/auth.svg?branch=master)](https://travis-ci.com/alkaupp/auth)

Auth
====

Provides authentication service.

Running the application
-----------------------

```
cp ./app/.env.example ./app/.env
docker-compose up -d
```

Auth application will run in http://localhost:8000.

Usage
-----

First authorize your application, so "Auth" can start providing authentication services for your application.

### POST /authorize

```json
{
  "appName": "myApp",
  "siteUrl": "https://example.com",
  "secretKey": "mySecret"
}
```

In response you will receive an `appId` for your application that your users will have to
provide when registering from your site. The response will look like this (obviously uuid will be different):

```json
{
  "appId": "54036de4-652a-11e9-8888-c5d1c66dcec3",
  "appName": "myApp",
  "siteUrl": "https://example.com",
  "secretKey": "mySecret"
}
```

Now, you're users can start registering through "Auth" application. Notice that `userName` must be an email address.

### POST /register

```json
{
  "userName": "me@example.com",
  "password": "password123",
  "appId": "54036de4-652a-11e9-8888-c5d1c66dcec3"
}
```

Successful registration will return a plain HTTP 200 response.

After registration, users can sign in through "Auth" application.

### POST /signin

```json
{
  "userName": "me@example.com",
  "password": "password123",
  "appId": "54036de4-652a-11e9-8888-c5d1c66dcec3"
}
```

Successful authentication will return a HTTP 200 response accompanied with a JWT-token.

### POST /changepassword

```json
{
  "userName": "me@example.com",
  "oldPassword": "oldpassword",
  "newPassword": "newpassword"
}
```

Successful changing of password will return a plain HTTP 200.

**NOTE**: Api documentation can be found in http://localhost:8001

Common development operations
-----------------------------

Update dependencies
```
docker-compose run composer update --ignore-platform-reqs
```

Add new dependencies
```
docker-compose run composer require somevendor/somelib --ignore-platform-reqs
```

Run unit tests
```
docker exec -it auth-php-server bash -c 'vendor/bin/phpunit'
```

Run acceptance tests
```
docker exec -it auth-php-server bash -c 'vendor/bin/codecept run Api'
```

Run static analysis with phpstan
```
docker exec -it auth-php-server bash -c 'vendor/bin/phpstan analyse -l 5'
```

Run code style check
```
docker exec -it auth-php-server bash -c 'vendor/bin/phpcs'
```

Fix code style violations (that can be automatically fixed)
```
docker exec -it auth-php-server bash -c 'vendor/bin/phpcbf'
```
