# Math Gizmo

### What is this repository for?

We try to create app that allow people to learn math.
It consist of 3 main modules admin, API, and hybrid app

### How do I get set up?

#### Server
- For admin part we use [Composer](https://getcomposer.org/), so go to laravel folder and run `composer install`
- Also change copy `laravel/config/global/dbconf.php` file into `laravel/config/local` folder and set you db credential there. Do not save real credential in global folders. 
- To update your DB to current version go to laravel folder and run `php artisan migrate`
- Make sure that apache has access to write into `laravel/bootstrap/cache` and `laravel/storage` folders. 
- Create file `.env` and put `APP_KEY=` in it. For email sending make sure that you have in your `.env` file next keys set: `MAIL_DRIVER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`. You can just rename `example.env` to `.env`.
- Run `php artisan key:generate` to generate app key. Check `.env` file if key actually been generated.
- Run `php artisan jwt:generate` to generate secret for API.
- 

#### Client
- For Client App we use [Angular5](https://angular.io/) which require [NodeJS](https://nodejs.org/) version 6 at least. Verify that you are running at least node 6.9.x and npm 3.x.x by running `node -v` and `npm -v` in a terminal/console window. Older versions produce errors, but newer versions are fine. 
`npm -v
3.10.10
node -v
v6.11.3`
- Globally install [Angular CLI](https://angular.io/guide/quickstart) using command `npm install -g @angular/cli@latest`
- Run `npm install` into `ci` folder to install required npm modules
- Add URL to your local server API to `/ci/src/environments/environment.ts` 
- Run `npm start` or `ng serve  --open --base-href /gizmo/` for a dev server. The app will automatically reload if you change any of the source files.
- Use `ng build --prod` into `ci` folder to build angular app and see your changes under apache (production build uses variables from `environment.prod.ts`, so set your globals there)
- If you want to generate a new component run `ng generate component component-name`. You can also use `ng generate directive|pipe|service|class|guard|interface|enum|module`.

### To set up backups to dropbox
- Go to backup folder. Copy `.dropbox_uploader.distr` into `.dropbox_uploader`
- Open [the following URL](https://www.dropbox.com/developers/apps) in your Browser, and log in using your account
- Click on `Create App`, then select `Dropbox API app`
- Now go on with the configuration, choosing the app permissions and access restrictions to your DropBox folder
- Enter the `App Name` that you prefer (e.g. MyUploader3196922415454)
- Now, click on the `Create App` button.
- When your new App is successfully created, please click on the `Generate` button under the `Generated access token` section, then copy and paste the new access token into corresponding variable in `.dropbox_uploader` file. Define your dropbox backups folder there.
- Add `backups/.do_backup.sh` to your daily cron

### License: [MIT](./LICENSE.MD)