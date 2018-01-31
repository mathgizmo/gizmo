# README #

This is Gizmo project. It consist of 3 main modules

### What is this repository for? ###

We try to create app that allow people to learn math.
It consist of 3 main modules admin, API, and hybrid app

### How do I get set up? ###

For admin part we use composer, so go to laravel folder and run composer install
Also change copy config/global/dbconf.php file into config/local folder and set you db credential there. Do not save real credential in global folders. 
To update your DB to current version go to laravel folder and run "php artisan migrate"
Make sure that apache has access to write into laravel/bootstrap/cache and laravel/storage folders. Run php artisan key:generate to generate app key. If you get eny error on key generation, create file '.env' and put 'APP_KEY=' in it, then rerun command. Check .env file if key actually bin generated.Run php artisan jwt:generate to generate secret for API.

For Client App we use Angular2 which require nodejs version 6 at least. Verify that you are running at least node 6.9.x and npm 3.x.x by running node -v and npm -v in a terminal/console window. Older versions produce errors, but newer versions are fine. https://nodejs.org/
npm -v
3.10.10
node -v
v6.11.3

Also install angular cli https://angular.io/guide/quickstart
Run *npm install* into ci folder to install required npm modules
Copy ci/src/app/globals.ts.distr to ci/src/app/globals.ts and set you variables there
Use *ng build --prod* into ci folder to build angular app and see your changes under apache

###To set up backups to dropbox
Go to backup folder. Copy .dropbox_uploader.distr into .dropbox_uploader
 1) Open the following URL in your Browser, and log in using your account: https://www.dropbox.com/developers/apps
 2) Click on "Create App", then select "Dropbox API app"
 3) Now go on with the configuration, choosing the app permissions and access restrictions to your DropBox folder
 4) Enter the "App Name" that you prefer (e.g. MyUploader3196922415454)

 Now, click on the "Create App" button.

 When your new App is successfully created, please click on the Generate button under the 'Generated access token' section, then copy and paste the new access token into corresponding variable in .dropbox_uploader file. Define your dropbox backups folder there.
 Add backups/.do_backup.sh to your daily cron