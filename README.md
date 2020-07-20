## SimpleQueryBuilderInterface task

Implement SimpleQueryBuilderInterface

## Prerequisites

Install Docker and optionally Make utility.

Commands from Makefile could be executed manually in case Make utility is not installed.

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