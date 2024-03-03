# Docker
Basic settings for development with docker

## Technologies
The following technologies are being used to make this project work.

- *PHP* - version 8.1
- *MySQL (or MariaDB)* - Allways latest version
- *NGINX* - Allways latest version stable

## Setup

You can install Docker following the instructions on the link [get Docker](https://docs.docker.com/engine/install/).

Clone this repository for into your project with command below

```bash
https://github.com/carlosbelchior/docker.git
```

### Run it locally
The sections below describe how to set up the development environment.

#### Start up
To startup the application you can run the command shown below.

```bash
docker-compose up --build -d
```

Docker command to access the php service:

```bash
docker-compose exec php /bin/bash
```

It will start the application using some docker containers (nginx, mysql and PHP).

#### Settings files

NOTE: The default credentials for database are:
- host: mysql
- database: default
- user: root
- password: admin

### Access your application:

```json
localhost:8080
```