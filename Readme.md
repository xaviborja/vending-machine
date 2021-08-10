### How to run the application

To run this application you need to do these steps:

1 - Clone the repository

2 - Build docker image
`docker build -t vending-machine .`

3 - Run the container
`docker run -it --rm --name vending-machine-run vending-machine`

### Requirements
You only need to have git to clone the project and docker to build and run the image.

### Additional Notes
The application starts with each item having a quantity of 10. 
In this exercise it would be possible to add an application layer with every use case, but I've focused on Vending Machine logic.
