## SimpleQueryBuilderInterface task

Implement SimpleQueryBuilderInterface

## Prerequisites

Install Docker and optionally Make utility.

Commands from Makefile could be executed manually in case Make utility is not installed.

## Build container and install composer dependencies

    Make build

## Build container and install composer dependencies

If dist files are not copied to actual destination, then
    
    Make copy-dist-configs
        
## Up docker container

Up containers.

    Make up   
    
## Check docker container

Check if all good.

    Make check           
        
## Run application

Runs container and executes console application.

    Make run
    

## Run functional tests

Runs container and executes functional tests.

    Make functional-tests
    
## Fix code style

    Make cs-fix