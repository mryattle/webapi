# Web APIs

In microservice architectures developers have to deal with distributed systems composed of several nodes that work together. Communication between these nodes is an important issue that should be taken care of. The aim of this project is to analyze the possible paradigms for the design of APIs, evaluating strength, maturity level, possible use cases and related best practices. 

## Prerequisites

- Docker, version >=19.03.1
- docker-compose, version >=1.24.1
- Git utility
- Product source code
- Dialogflow account and API key
- A running postersql instance that will allow postgresql user to execute SQL and DDL

## Fullfill prerequisites

### Install docker

You can find docker install documentation on [docker website](https://docs.docker.com/)

### Download source code

To start a development environment please download product source code using GIT utility

```bash
git clone https://github.com/mryattle/webapi.git
```

### Dialogflow

WebSocket chat interaction need a **Dialogflow agent** to work properly. Please read [Google documentation](https://developers.google.com/assistant/actions/dialogflow/project-agent) to create a new agent.

File express/BladeRunner.zip contains default agent intents and should be used to import intents into your Dialogflow agent service account to fulfill WebSocket chat basic interactions.

Moreover a **google API credentials key** should be generated to allow dialogflow agent be used through API. The key will be used by express microservice to forward questions to Dialogflow service.

The key must be copied inside *webapi/express/app/* folder using this name *google-key.json*

```javascript
// Instantiates a session client
this.sessionClient = new dialogflow.SessionsClient({
  keyFilename: "/app/google-key.json"});     
```

Also you must declare google project name in *webapi/express/app/config.js*

```javascript
const config = {
  google: {
    project: '$google-project-name'
  },
  ....
};
module.exports = config;
```

### PostgreSQL

REST, RPC and GraphQL APIs are using a postgresql RDBMS to read and write data. Yo can use [Heroku Hobby Dev free plan to register a new postgresql instance](https://elements.heroku.com/addons/heroku-postgresql). You need to provide connection params using *webapi/laravel/.env* file, those params will be used to create a new schema and perform read, write operations on it. 

## Stack

The product is built on top of five components, we will explore them to better understand product deployment environments.

The product components/microservices are:

- **UI** - developed using Docusaurus v2 (a tool designed to make it easy for teams to publish documentation using PRPL pattern)
- **Request-Response API** - developed using laravel (development of web applications following the model–view–controller (MVC) architectural pattern)
- **Event-Driven API** - developed using express and socket.io (a JavaScript library for realtime web applications. It enables realtime, bi-directional communication  between web clients and servers using Websockets)
- **PostgreSQL** - to store and retrieve sample data
- **Nginx** - to proxy APIs requests and publish static HTML contents in a non-blocking way

## Deploy

Deployment process for development, test and production environments is described.

### Development 

Main docker-compose.yml should be used to build, modify and run the product in development environment.

We will use docker-compose.yml file to run the product and create data for development purpose 

```bash
cd webapi
# build docker images
docker-compose build
# initialize postgresql data
docker-compose run laravel php artisan migrate
docker-compose run laravel php artisan db:seed --class=DatabaseSeeder
# start development instance
docker-compose up
```

Now you can access web UI using your favorite web browser on address http://localhost

### Test

Test environment should be used to run application and test static HTML contents. Before to start test instance you need to publish HTML static content. Docusaurus will provide tools to perform static HTML files building.

```bash
# build docker images
#docker-compose -f docker-compose.yml -f docker-compose.test.yml build
# initialize postgresql data
#docker-compose -f docker-compose.yml -f docker-compose.test.yml run laravel php artisan migrate
#docker-compose -f docker-compose.yml -f docker-compose.test.yml run laravel php artisan db:seed --class=DatabaseSeeder
# build static HTML contents
docker-compose run docusaurus npm run build
# start test instance
docker-compose -f docker-compose.yml -f docker-compose.test.yml up
```

Now you can access web UI using your favorite web browser on address http://localhost

### Production

To deploy you application you can easily create an Heroku free account and register all microservices to expose you application to the world.

After [registering](https://signup.heroku.com/) to Heroku you need to create in your [Heroku dashboard](https://dashboard.heroku.com/apps):

- Request-Response API app to publish laravel microservice
- Event-Driven API app to publish express microservice
- Nginx proxy app to publish HTML contents and forward APIs requests to Request-Response and Event-Driven microservice

During app creating process you will obtain three unique application names that will be used to publish th product. (eg. webapi-rr.herokuapp.com, webapi-ed.herokuapp.com, webapi-static.herokuapp.com).

You also need to [create a PostgreSQL instance](https://elements.heroku.com/addons/heroku-postgresql) to initialize sample data, doing so you will obtain PostgreSQL connection params:

- Host
- Database
- User
- Port
- Password

Those parameters must be used to define production database settings in *webapi/laravel/.env*

```
DB_HOST=${heroku-database-hostname}
DB_PORT=${heroku-database-port}
DB_DATABASE=${heroku-database-database}
DB_USERNAME=${heroku-database-username}
DB_PASSWORD=${heroku-database-password}
```

To initialize PostgreSQL data on your "remote" RDBMS instance you must use laravel production instance.

```bash
# remote postgres sample data initialization
docker-compose -f docker-compose.yml -f docker-compose.heroku.yml run laravel php artisan migrate
docker-compose -f docker-compose.yml -f docker-compose.heroku.yml run laravel php artisan db:seed --class=DatabaseSeeder
```

Now we will Heroku Container Registry deployment features, doing so we will build locally docker images and we will push them to Heroku.

```bash
# laravel publishing
cd webapi/laravel
heroku login
heroku container:login
heroku container:push web --recursive  -a webapi-rr
heroku container:release web -a webapi-rr
heroku ps:scale web=1 -a webapi-rr
```
To ensure that you application is up and running you must visit https://webapi-rr.herokuapp.com 

```bash
# express publishing
cd webapi/express
heroku login
heroku container:login
heroku container:push web --recursive  -a webapi-ed
heroku container:release web -a webapi-ed
heroku ps:scale web=1 -a webapi-ed
```
To ensure that you application is up and running you must visit https://webapi-ed.herokuapp.com 

After both APIs microservice are up and running you must configure nginx using production configuration file *webapi/nginx/prod.conf* to define proxied hosts for request-response and event-driven APIs.

```nginx

  # ....
  
  # Express
	location /chat/ {
		proxy_pass https://webapi-ed.herokuapp.com/;
	}

    location /socket.io/ {
  			# ....
        proxy_set_header Host "webapi-ed.herokuapp.com";
  			# ....
        proxy_pass https://webapi-static.herokuapp.com/socket.io/;
  			# ....
    }   

    # Express - Webhook
    location /api/webhook {
        proxy_pass https://webapi-ed.herokuapp.com/webhook;
    }
	
	location /api/ {
		proxy_pass https://webapi-rr.herokuapp.com/api/;
	}
```

Now you are ready to publish static HTML pages

```bash
# nginx publishing
cd webapi
heroku login
heroku container:login
heroku container:push web --recursive  -a webapi-static
heroku container:release web -a webapi-static
heroku ps:scale web=1 -a webapi-static
```

To ensure that you application is up and running you must visit https://webapi-static.herokuapp.com 

## Demo

- [Checkout demo](https://webapi-analysis.herokuapp.com/)

## License

MIT