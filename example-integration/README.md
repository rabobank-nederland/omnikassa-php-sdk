# Rabo Smart Pay PHP SDK Example integration

This example integration demonstrates the usage of the underlying Smartpay SDK. 
It uses a (standard) Symfony Framework setup with docker.

## Setup

###
1. In your terminal, make sure you are in the correct folder/path: `example-integration`
2. run the following two commands to create and spin up the docker container (with php,nginx,valkey): 
```
    docker compose build
    docker compose up -d
```
3. Now we need to run `composer install` from within the docker container. 
e.g. open a terminal in docker from a service container OR use the following command: 
`docker compose exec php bash`

4. Make sure you paste the right credentials in the `.env.local` file. The `env.local` is excluded from git so credentials will not be shared.
5. The example-integration shop will now be accessible from:
` http://localhost:1234 `
Good luck!


