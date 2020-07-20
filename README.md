## SimpleQueryBuilderInterface task

Implement SimpleQueryBuilderInterface

## Prerequisites

Install Docker and optionally Make utility.

Commands from Makefile could be executed manually in case Make utility is not installed.

## Explanations and additions

- as it's not clearly explained in the interface how to deal with parametrized queries, for simplification I put parameters values just inside the SQL, but not the placeholders for parametrized queries.
- in real life implementation it should be whether placeholders instead of values in SQL, and parameters to be added with the database parametrized query, or in case if database does not support parametrized queries, we should filter SQL injection on the builder itself.

## Clone the repo
    git clone https://github.com/aleksandrkravchukos/SimpleQueryBuilder.git
    cd SimpleQueryBuilder

## Build container and install composer dependencies

    Make build

## Copy dist files

If dist files are not copied to actual destination, then
    
    Make copy-dist-configs
        
## Up docker container

Up containers.

    Make up   
    
## Check docker container

Check if all good.

    Make check           

## Run unit tests

Runs container and executes unit tests.

    Make unit-tests

## Run functional tests

Runs container and executes functional tests.

    Make functional-tests
        
## Fix code style

    Make cs-fix