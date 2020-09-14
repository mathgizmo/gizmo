# Math Gizmo

### What is this repository for?

We try to create the app that allow people to learn math.

### How do I get set up?

#### Server
- Install [Composer](https://getcomposer.org/).
- Go to `laravel` folder and run `composer install` to install dependencies.
- Create *.env* file (copy it from *.env.example*) and set your DB credentials: *DB_DATABASE*, *DB_PORT*, *DB_USERNAME*, *DB_PASSWORD*. 
- To update your DB to current version run `php artisan migrate --seed`. If you get any error run `composer update`, then rerun command.
- For email sending make sure you have in your *.env* file next keys set: *MAIL_HOST*, *MAIL_PORT*, *MAIL_USERNAME*, *MAIL_PASSWORD*. 
- For Google ReCaptcha set next keys in your *.env* file: *RECAPTCHA_KEY*, *RECAPTCHA_SECRET*.
- For preview questions in the admin you need *PREVIEW_URL* with a link to your client base preview path.
- To add admin ability to login as student you need *CLIENT_LOGIN_URL* with a link to your client login path.
- For email verification you need *CLIENT_VERIFY_EMAIL_URL* with a link to your client email verification path.
- Fou production build change environment to production in your *.env* file: *APP_ENV=production*.
- Run `php artisan key:generate` to generate app key. If you get any error on key generation, check if line *APP_KEY=* exists in *.env*, then rerun command.
- Run `php artisan jwt:generate` to generate secret for API.
- Make sure apache has access to write into `laravel/bootstrap/cache` and `laravel/storage` folders.
- To clear your cache run `php artisan config:clear`, `php artisan view:clear`, `php artisan route:clear`, `php artisan cache:clear`

#### Client
- Globally install [Angular CLI](https://angular.io/guide/quickstart) using command `npm install -g @angular/cli@latest`
- Run `npm install` into `ci` folder to install required npm modules
- Add URL to your local server API and captchaKey to `/ci/src/environments/environment.ts` (copy it from `environment.example.ts`).
- Run `npm start` for a dev server. The app will automatically reload if you change any of the source files.
- Use `ng build --prod` into `ci` folder to build the angular app and see your changes under apache (production build uses variables from `environment.prod.ts`, so set your globals there)
- If you want to disable captcha just set `ignoreCaptcha: true` in `/ci/src/environments/environment.ts`.
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
