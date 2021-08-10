### How to run the application

To run this application you need to do these steps:

1 - Clone the repository

2 - Build docker image
`docker build -t vending-machine .`

3 - Run the container
`docker run -it --rm --name vending-machine-run vending-machine`

### Requirements
You only need to have git to clone the project and docker to build and run the image.

If you want to run the tests inside the container, you can run `docker exec -it vending-machine-run vendor/bin/phpunit tests` in another terminal while the container is running.

For running tests outside you will need php 7.4 and composer to install dependencies. Then need to run `vendor/bin/phpunit tests`

### Additional Notes
The application starts with each item having a quantity of 10. 
In this exercise it would be possible to add an application layer with every use case, but I've focused on Vending Machine logic.
