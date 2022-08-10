# Crowd base build

This version of the Crowd Base Build includes Gulp version 4.
This means you must be using the latest version of Node (or at lease version 10).

---
## Setup

1. Install [Node + NPM](https://www.npmjs.com/get-npm).
2. Run `npm install -g gulp` to install gulp globally.
3. `cd` into your themes directory.
4. Run `npm install`.
5. Have a kitkat 🍫

---
## Crowd's standard tasks

`gulp` Will run and then watch sass, js, and images tasks.

`gulp package` will product a package in the directory above that contains
whitelisted files. This is used to generate a 'package' version of the theme
that can be deployed to developement environments.

If you've made any changes to `gulpfile.js`, or added some new sas or js paths, then you'll need to restart the `gulp` task.

#### NPM scripts

Npm scripts are also setup, if you want to use them.

`npm start` basically just runs `gulp` (the standard dev / watch task).

`npm run build` runs `gulp package`.

### Flags

`--production` will run tasks configured to output for production. This often
means output files will be compressed.

`gulp package --production` will run the packaging task while also compressing
outputted files. The generated package should be deployed to development
environments and live environments.

`--pipeline` is for use only if the task is being run from a pipeline. All this does is change the name of the packaged folder to 'pipeline' so that bitbucket can save it as an artifact for automated deployment.

---
## File and folder structure

### `functions.php`

The engine room of any WordPress install. This is where Timber is initialised, and any modifications to the default WordPress behaviour are made.

We also use hooks to include any extras, like new Custom Post Types, Taxonomies, ACF Options Pages, and Page Blocks.

#### `includes/`

If you've got a lot of things added in `functions.php`, you might want to split it off into a separate file. Use the `includes` folder to add new .php files and then use `require_once` in `functions.php` to include them.

### `templates/` and `components/`

In these folders we put our `.twig` templates. The `templates` folder is best used for page and post templates. Basically anything that receives data directly from a .php template like `single.php` or `page.php`.

The `components` folder is where reusable element templates are. Things like `image.twig` or `menu.twig` that get passed data and can be used anywhere in the site. You might also want to save your block templates here too.

### `src/`

This is where all our raw static assets are saved for development. Use `gulp` to generate built files from `js` or `sass` which get saved in `dist`, ready to use in the browser.
There are also tasks set up for `images` and `fonts` folders within `src` that minify and serve image and font files into `dist`.

### Extra Directories

You might want to create or include many extra directories in the theme - that's great!
But remember to add anything you'll need in your packaged theme into the `packageWhitelist` constant in `gulpfile.js`, otherwise it'll be ignored in the build process.

---
## Setting up CI (Bitbucket Pipelines) for your project

The Crowd base build now comes with a `bitbucket-pipelines.yml` file which is a
YAML config for Continuous Integration. It has some sensible defaults, and some
areas which will need to be uncommented + configured for your repository.

By default you'll need to add the following environment variables to your
bitbucket repo in the repository settings.

The environment variable `LFTP_infrastructure_user_string` holds the SFTP
connection details to your destination server. __It is very important to click
the 'padlock' icon when adding this environment variable to your repository.__
This will mask and encrypt the sensitive information detailed below (SFTP
usernames/passwords).


For TSO, this could look like the following:
```
sftp://sftp-username:sftp-password@shell.gridhost.co.uk
```

For SiteGround, LFTP does not quite work, so we use rsync.
This means using the THEME_DIR environment variable like so:
```
u278-z85k9umvk5y4@thisiscrowdlab.com:/home/customer/www/{ SITE FOLDER }/public_html/wp-content/themes/{ PACKAGE NAME }
```
Make sure that your Pipeline's SSH key (Settings -> SSH Keys) is imported to Siteground in SSH Keys Manager.

For Flywheel, it would look similar to the following:
```
sftp://crowdinfrastructure:crowd-infra-pass@sftp.flywheelsites.com
```

The flywheel deployment uses our `crowdinfrastructure` Flywheel account. You'll
need to contact Nathan to get the password for this account.

### Known Hosts

To run successful deployments, you'll need to add your destination SFTP servers
to the repo's "known hosts" area.

1. Go to your repo in bitbucket
2. Find the repo settings (cog icon)
3. Go to 'SSH Keys'
4. Enter the 'host address' (e.g. `sftp.flywheelsites.com` or
   `shell.gridhost.co.uk`) and click `Fetch`.
