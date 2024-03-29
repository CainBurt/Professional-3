'use strict';

/**
 * Dependencies and other variables should be listed out here.
 */
const gulp = require('gulp'),
    sass = require('gulp-sass')(require('sass')),
    sourcemaps = require('gulp-sourcemaps'),
    imagemin = require('gulp-imagemin'),
    args = require('yargs').argv,
    gulpif = require('gulp-if'),
    webpack = require('webpack-stream'),
    autoprefixer = require('autoprefixer'),
    postcss = require('gulp-postcss'),
    entryPlus = require('webpack-entry-plus'),
    glob = require('glob'),
    rename = require('gulp-rename'),
    changed = require('gulp-changed'),
    debug = require('gulp-debug'),
    replace = require('gulp-replace'),
    theme = 'employee-hub', // Define the theme name for packaging
    paths = {
        sass: {
            essential: {
                src: 'src/sass/essential/**/*.scss',
                name: 'main.css',
                dist: 'dist/styles'
            },
            deferred: {
                src: 'src/sass/deferred/**/*.scss',
                name: 'deferred.css',
                dist: 'dist/styles'
            },
            footer: {
                src: 'src/sass/footer/**/*.scss',
                name: 'footer.css',
                dist: 'dist/styles'
            },
            header: {
                src: 'src/sass/header/**/*.scss',
                name: 'header.css',
                dist: 'dist/styles'
            },
            nav_grid: {
                src: 'src/sass/blocks/landing-page/nav-grid.scss',
                name: 'nav-grid.css',
                dist: 'dist/styles'
            },
            latest_updates: {
                src: 'src/sass/blocks/landing-page/latest-updates.scss',
                name: 'latest-updates.css',
                dist: 'dist/styles'
            },
            resource: {
                src: 'src/sass/blocks/resource.scss',
                name: 'resource.css',
                dist: 'dist/styles'
            },
            team: {
                src: 'src/sass/blocks/team.scss',
                name: 'team.css',
                dist: 'dist/styles'
            },
            hero: {
                src: 'src/sass/blocks/hero.scss',
                name: 'hero.css',
                dist: 'dist/styles'
            },
            start: {
                src: 'src/sass/blocks/start.scss',
                name: 'start.css',
                dist: 'dist/styles'
            },
            start_form: {
                src: 'src/sass/new-starter-form.scss',
                name: 'start-form.css',
                dist: 'dist/styles'
            },
            form: {
                src: 'src/sass/forms.scss',
                name: 'forms.css',
                dist: 'dist/styles'
            },
            handbook_form: {
                src: 'src/sass/handbook-form.scss',
                name: 'handbook-form.css',
                dist: 'dist/styles'
            },
            download_list: {
                src: 'src/sass/blocks/download-list.scss',
                name: 'download-list.css',
                dist: 'dist/styles'
            },
            tracking_table: {
                src: 'src/sass/tracking-table.scss',
                name: 'tracking-table.css',
                dist: 'dist/styles'
            },
            download_list: {
                src: 'src/sass/blocks/free-text.scss',
                name: 'free-text.css',
                dist: 'dist/styles'
            },

        
        },
        js: {
            src: [
                'src/js/**/*.js', // Place js here that is essential to the site, will be returned in the <head>.
            ],
            dist: 'dist/js',
            entries: [
                {
                    entryFiles: glob.sync('./src/js/essential/**/*.js'),
                    outputName: 'essential'
                },
                {
                    entryFiles: glob.sync('./src/js/deferred/**/*.js'),
                    outputName: 'deferred'
                },
                {
                    entryFiles: glob.sync('./src/js/google-analytics.js'),
                    outputName: 'google-analytics'
                },
                {
                    entryFiles: glob.sync('./src/js/google-tag-manager.js'),
                    outputName: 'google-tag-manager'
                },
                {
                    entryFiles: glob.sync('./src/js/partytown.js'),
                    outputName: 'partytown'
                },
                {
                    entryFiles: glob.sync('./src/js/header/header.js'),
                    outputName: 'header'
                },
                {
                    entryFiles: glob.sync('./src/js/blocks/landing-page/nav-grid.js'),
                    outputName: 'nav-grid'
                },
                {
                    entryFiles: glob.sync('./src/js/blocks/team.js'),
                    outputName: 'team'
                },
                {
                    entryFiles: glob.sync('./src/js/blocks/new-starter.js'),
                    outputName: 'new-starter'
                },
                {
                    entryFiles: glob.sync('./src/js/blocks/handbook.js'),
                    outputName: 'handbook'
                },
                {
                    entryFiles: glob.sync('./src/js/qsm.js'),
                    outputName: 'qsm'
                },
                {
                    entryFiles: glob.sync('./src/js/incident-admin.js'),
                    outputName: 'incident-admin'
                },
                
            ]
        },
        images: {
            src: 'src/images/**/*',
            dist: 'dist/images'
        },
        fonts: {
            src: 'src/fonts/**/*',
            dist: 'dist/fonts'
        },
        cache: {
            src: './includes/cache_bust.php',
            dest: './includes/'
        },
        packageWhitelist: [ //Customise to your own folder structure
            '*.{php,png,css,zip}',
            'acf-json/**/*.json',
            'includes/**/*.php',
            'includes/plugins/advanced-custom-fields-pro.zip',
            'dist/**/*',
            'components/**/*.twig',
            'templates/**/*.twig',
            'login/**/*',
            'includes/edit-strings/**/*',
        ],
        acf: {
            src: 'includes/toggle_acf_edit.php',
            dest: 'includes/'
        }
    };

const styleFunctions = [];

/*
 * For each item in paths.sass, creates a function that runs all the gulp pipelines
 */
for (let [key, value] of Object.entries(paths.sass)) {
    value.function = () => {
        return gulp.src(value.src)
            .pipe(gulpif(!args.production, sourcemaps.init()))
            .pipe(sass())
            .on('error', sass.logError)
            .pipe(postcss([autoprefixer()]))
            .pipe(gulpif(!args.production, sourcemaps.write('.')))
            .pipe(rename(value.name))
            .pipe(gulp.dest(value.dist));
    }
    Object.defineProperty(value.function, 'name', {value: key, writable: false});
}

/*
*   Styles
*
*   Runs all style functions
*/
function styles(done) {
    for (let [key, value] of Object.entries(paths.sass)) {
        value.function();
    }
    done();
}

/**
 *  JavaScript
 * 
 *  Runs the JS bundler.
 *  To separate JS bundles, add paths to the 'entries' array in paths->js
 */
function scripts() {
    let buildMode = 'development';
    if (args.production) {
        buildMode = 'production';
    }
    return gulp.src(paths.js.src)
        .pipe(sourcemaps.init())
        .pipe(webpack({
            mode: buildMode,
            entry: entryPlus(paths.js.entries),
            output: {
                filename: '[name].js'
            },
            module: {
                rules: [
                    {
                        test: /\.js$/,
                        exclude: /(node_modules|bower_components)/,
                        use: {
                            loader: "babel-loader",
                            options: {
                                presets: ["@babel/preset-env"],
                            }
                        }
                    },
                ]
            }
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.js.dist));
}

/**
 * `gulp images`
 *
 * Pipes changed/new images.
 * Optimises files for filesize, including SVGs.
 */
function images() {
    return gulp.src(paths.images.src)
        .pipe(changed(paths.images.dist))
        .pipe(debug({title: 'images'}))
        .pipe(imagemin([
            imagemin.gifsicle(),
            imagemin.jpegtran(),
            imagemin.optipng(),
            imagemin.svgo({plugins: [
                {mergePaths: false},
                {removeAttrs: false},
                {convertShapeToPath: false},
                {sortAttrs: true}
            ]})
        ]))
        .pipe(gulp.dest(paths.images.dist));
}

/**
 * `gulp fonts`
 *
 * Pipes changed source fonts -> dist
 */
function fonts() {
    return gulp.src(paths.fonts.src)
        .pipe(changed(paths.fonts.dist))
        .pipe(debug({title: 'fonts'}))
        .pipe(gulp.dest(paths.fonts.dist));
}

/**
 *  Cache Bust
 * 
 *  Changes the php variable for cache versions to the current timestamp
 */
function cacheBust() {
    let cbString = new Date().getTime();
    return gulp.src(paths.cache.src)
        .pipe(replace(/\$cache_ver=\d+/g, () => {
            return '\$cache_ver=' + cbString;
        }))
        .pipe(gulp.dest(paths.cache.dest));
}

function watch() {
    // Watch each entry in paths.sass separately
    for (let [key, value] of Object.entries(paths.sass)) {
        gulp.watch(value.src, gulp.series(value.function, cacheBust));
    }
    gulp.watch(paths.js.src, gulp.series(scripts, cacheBust));
    gulp.watch(paths.images.src, gulp.series(images));
}

/**
 *  Deploy
 * 
 *  Runs the build tasks, with minification. Then moves eveything into a package folder.
 */
function deploy() {
    return gulp.src(paths.packageWhitelist, { base: './' })
        .pipe(gulpif(args.pipeline, gulp.dest('pipeline/'), gulp.dest('../' + theme + '-package/')));
}

function disableAcf() {
    let src = '';
    let dest = '';
    if (args.pipeline) {
        src = `pipeline/${paths.acf.src}`;
        dest = `pipeline/${paths.acf.dest}`;
    } else {
        src = `../${theme}-package/${paths.acf.src}`
        dest = `../${theme}-package/${paths.acf.dest}`;
    }
    return gulp.src(src)
        .pipe(replace(/\$showacf=\w+/g, () => {
            return '\$showacf=false';
        })).pipe(gulp.dest(dest));
}

gulp.task('default', gulp.series(fonts, images, styles, scripts, watch));

gulp.task('package', gulp.series(fonts, images, styles, scripts, deploy));