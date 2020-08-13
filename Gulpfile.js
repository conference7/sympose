'use strict';

const gulp = require('gulp');
const babel = require('gulp-babel');
const sass = require('gulp-sass');
const cssnano = require('gulp-cssnano');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify-es').default;
const rename = require('gulp-rename');
const notify = require('gulp-notify');
const autoprefixer = require('gulp-autoprefixer');
const checktextdomain = require('gulp-checktextdomain');
const wpPot = require('gulp-wp-pot');
const concat = require('gulp-concat');
const plumber = require('gulp-plumber');
const browserSync = require('browser-sync');
const reload = browserSync.reload;
const fs = require('fs');
const path = require('path');
const merge = require('merge-stream');
const del = require('del');
const { doesNotMatch } = require('assert');
const { src } = require('gulp');
const gutil = require("gulp-util");
const zip = require("gulp-zip");
const exec = require('child_process').exec;

sass.compiler = require('node-sass');

/* Sass task */
gulp.task('css', () => {

    const types = getFolders('./css/src/');

    const tasks = types.map((dir) => {

        return gulp.src('./css/src/' + dir + '/*.scss')
            .pipe(plumber())
            .pipe(sourcemaps.init())
            .pipe(sass().on('error', sass.logError))
            .pipe(autoprefixer('last 2 version', 'ie 9', 'ios 6', 'android 4'))
            .pipe(sourcemaps.write())
            .pipe(gulp.dest('./css/dist/' + dir + '/'))
            .pipe(rename({ suffix: '.min' }))
            .pipe(cssnano({
                zindex: false
            }))
            .pipe(gulp.dest('./css/dist/' + dir + '/'))
            .pipe(reload({ stream: true }))
    });

    return merge(tasks);
});

/* Admin Scripts task */
gulp.task('js', function () {

    const types = getFolders('./js/src/');
    const tasks = types.map((dir) => {

        return gulp.src('./js/src/' + dir + '/*.js')
            .pipe(babel({
                presets: ['@babel/env']
            }))
            .pipe(concat('sympose.js'))
            .pipe(gulp.dest('./js/dist/' + dir + '/'))
            .pipe(rename({ suffix: '.min' }))
            .pipe(uglify().on('error', handleErrors))
            .pipe(gulp.dest('./js/dist/' + dir + '/'));
        });

    return merge(tasks);
});


const i18n_config = {
    'text_domain': 'sympose',
    'package': 'sympose',
    'php_files': ['./app/**/*.php'],
    'cacheFolder': './app/languages/cache',
    'destFolder': './app/languages',
    'keepCache': false
};


gulp.task('checktextdomain', function () {
    return gulp
        .src(['admin/', 'public/'])
        .pipe(checktextdomain({
            text_domain: 'sympose',
            keywords: [
                '__:1,2d',
                '_e:1,2d',
                '_x:1,2c,3d',
                'esc_html__:1,2d',
                'esc_html_e:1,2d',
                'esc_html_x:1,2c,3d',
                'esc_attr__:1,2d',
                'esc_attr_e:1,2d',
                'esc_attr_x:1,2c,3d',
                '_ex:1,2c,3d',
                '_n:1,2,4d',
                '_nx:1,2,4c,5d',
                '_n_noop:1,2,3d',
                '_nx_noop:1,2,3c,4d'
            ]
        }));
});

gulp.task('generate-pot', function () {
    return gulp.src(i18n_config.php_files)
        .pipe(wpPot({
            domain: i18n_config.text_domain,
            package: i18n_config.package,
            src: i18n_config.php_files,
        }))
        .pipe(gulp.dest(i18n_config.destFolder + '/' + i18n_config.text_domain + '.pot'))
});

gulp.task('watch', function () {
    gulp.watch('css/src/*/**.scss', gulp.series(['css']));
    gulp.watch('js/src/*.js', gulp.series(['js']));
});

/* Watch scss, js and html files, doing different things with each. */
gulp.task('default', gulp.series(['css', 'js', 'watch']), function () {
    //
});

gulp.task('dist:clean', function() {
    gutil.log(gutil.colors.green('Cleaning build folder..'));
    return del([
        'dist/*',
        'sympose.zip'
    ]);
});

gulp.task('composer-install-no-dev', function(cb) {
    exec('composer install --no-dev', function (err, stdout, stderr) {
        cb(err);
      });
});

gulp.task('dist:sync-to-svn', function(cb) {
    exec('cp -r dist/ ../sympose-svn/trunk', function (err, stdout, stderr) {
        cb(err);
      });
});

gulp.task('composer-install', function(cb) {
    exec('composer install', function (err, stdout, stderr) {
        cb(err);
      });
});

gulp.task('dist:build', function() {
    gutil.log(gutil.colors.green('Copying contents to ./dist'));
    return gulp.src([
        '**', 
        '!css/src/**', 
        '!js/src/**', 
        '!node_modules/**',
        '!scripts/**',
        '!.circleci',
        '!.github',
        '!.git',
        '!.gitattributes',
        '!.gitignore',
        '!.DS_Store',
        '!package.json',
        '!package-lock.json',
        '!composer.json',
        '!composer.lock',
        '!Gulpfile.js',
        '!phpcs.xml',
        '!README.md',
        ])
        .pipe(gulp.dest('./dist/'))
});

gulp.task('dist:clean-build', function() {
    gutil.log(gutil.colors.green('Removing unnecessary files..'));
    return del(['./dist/.DS_Store'])
});

gulp.task('dist:build-zip', function() {
    gutil.log(gutil.colors.green('Creating ZIP file'));
    return gulp.src(['./dist/**'])
        .pipe(zip('sympose.zip'))
        .pipe(gulp.dest('./'));
    
});

// Build
gulp.task('build', gulp.series(['dist:clean', 'composer-install-no-dev', 'dist:build', 'dist:clean-build', 'dist:sync-to-svn', 'dist:build-zip', 'composer-install']), function(cb) {
    gutil.log(gutil.colors.green('🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉'));
    gutil.log(gutil.colors.green('Done building!'));
    return cb(null);
});

function handleErrors() {
    const args = Array.prototype.slice.call(arguments);

    // Send error to notification center with gulp-notify
    notify.onError({
        title: "Compile Error",
        message: "<%= error.message %>"
    }).apply(this, args);

    this.emit('end');
}

function getFolders(dir) {
    return fs.readdirSync(dir)
        .filter(function (file) {
            return fs.statSync(path.join(dir, file)).isDirectory();
        });
}