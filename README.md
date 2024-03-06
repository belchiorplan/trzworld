## TRZWorld

### Technologies
The following technologies are being used to make this project work.

- *PHP* - version 8.3.3
- *MySQL (or MariaDB)* - Always latest version
- *NGINX* - Always latest version stable

### Setup

You can install Docker following the instructions on the link [get Docker](https://docs.docker.com/engine/install/).

### Run it locally
The sections below describe how to set up the development environment.

#### Start up
To startup the application you can run the command shown below.

```bash
docker-compose up --build -d
```

Install dependencies:

```bash
docker exec -it trzworld_php composer install
```

Copy .env.example:

```bash
cp .env.example .env
```

Generate Laravel key:

```bash
docker exec -it trzworld_php php artisan key:generate
```

Run migrations and seed:

```bash
docker exec -it trzworld_php php artisan migrate --seed
```

Optionally, you can run test with command below:

```bash
docker exec -it trzworld_php php artisan test
```

Now, you can access the API.

For see to API routes see [this page](http://localhost/api/documentation).
