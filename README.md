Auth
====

Provides authentication service.

Running the application
-----------------------

```
docker-compose up
```

Update dependencies
```
docker-compose run composer update --ignore-platform-reqs
```

Usage
-----

First authorize, so "Auth" can provide authentication services for your application.

POST /authorize

```json
{
  "appName": "myApp",
  "siteUrl": "https://example.com",
  "secretKey": "mySecret"
}
```

In response you will receive an `appId` for your application that your users will have to
provide when registering to your site. The response will look like this (obviously uuid will be different):

```json
{
  "appId": "54036de4-652a-11e9-8888-c5d1c66dcec3",
  "appName": "myApp",
  "siteUrl": "https://example.com",
  "secretKey": "mySecret"
}
```

Now, you're users can start registering through "Auth" application. Notice that `userName` must be an email address.

POST /register

```json
{
  "userName": "me@example.com",
  "password": "password123",
  "appId": "54036de4-652a-11e9-8888-c5d1c66dcec3"
}
```

Successful registration will return a plain HTTP 200 response.

After registration users can sign in through "Auth" application.

POST /signin

```json
{
  "userName": "me@example.com",
  "password": "password123",
}
```

Successful authentication will return a HTTP 200 response accompanied with a JWT-token.
