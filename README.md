# README #

This is Gizmo project. It consist of 3 main modules

### What is this repository for? ###

We try to create app that allow people to learn math.
It consist of 3 main modules admin, API, and hybrid app

### How do I get set up? ###

For admin part we use composer, so go to admin folder and run composer install
Also change copy config/global/dbconf.php file into config/local folder and set you db credential there. Do not save real credential in global folders.

For Client App we use Angular2 which require nodejs version 6 at least. Verify that you are running at least node 6.9.x and npm 3.x.x by running node -v and npm -v in a terminal/console window. Older versions produce errors, but newer versions are fine. https://nodejs.org/
npm -v
3.10.10
node -v
v6.11.3

Also install angular cli https://angular.io/guide/quickstart
Run *npm install* into ci folder to install required npm modules
Use *ng build --prod* into ci folder to build angular app and see your changes under apache