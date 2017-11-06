"use strict";

let gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    plumber = require('gulp-plumber'),
    notify = require('gulp-notify'),
    minifyCSS = require('gulp-minify-css'),
    minify = require('gulp-minify'),
    gutil = require('gulp-util'),
    babel = require('gulp-babel'),
    exec = require('child_process').exec;

const config = {
    production: !!gutil.env.production,
    srcCSS : 'scss/**/*.scss',
    srcJS : 'js/src/*.js',
    destJS : 'js/',
    destCSS : 'css/',
    bundles :
        [
            'src/AppBundle/Resources/public/'
        ]
};


gulp.task('updateAssets', () => {
    exec('php bin/console assets:install', (err, stdin, stdout) => {
        // console.log(stdin);
    });
});

gulp.task('dumpAssets', () => {
    exec('php bin/console assetic:dump', (err, stdin, stdout) => {
        // console.log(stdin);
    });
})

gulp.task('clearCache', () => {
    console.log("Please wait for cache to clear..");
    exec('php bin/console cache:clear --no-warmup', (err, stdin, stdout) => {
       // console.log(stdin);
    });
    exec('php bin/console cache:warmup', (err, stdin, stdout) => {
       // console.log(stdin);
    });
});

gulp.task('sass', () => {
    for (let i = 0; i < config.bundles.length; i++) {
        gulp.src(`${config.bundles[i]}${config.srcCSS}`)
            .pipe(plumber({
                errorHandler: notify.onError("Error: \n <%= error.message %>")
            }
            ))
            .pipe(sass())
            .pipe(autoprefixer({
                browsers: [
                    "last 10 versions",
                    "Android 2.3",
                    "Android >= 4",
                    "Chrome >= 20",
                    "safari < 6",
                    "firefox < 49",
                    "opera < 12.1",
                    "explorer > 11",
                    "iOS >= 6"
                ]
            }))
            .pipe(config.production ? minifyCSS() : gutil.noop())
            .pipe(gulp.dest(`${config.bundles[i]}${config.destCSS}`));
    }
});

gulp.task('scripts', () => {
    for (let i = 0; i < config.bundles.length; i++) {
        gulp.src(`${config.bundles[i]}${config.srcJS}`)
            .pipe(plumber({
                errorHandler: notify.onError("Error: \n <%= error.message %>")
            }))
            .pipe(babel({
                presets: ['es2015']
            }))
            .pipe(config.production ? minify({
                ext:{
                    src:'.js',
                    min:'.js'
                }
            }) : gutil.noop())
            .pipe(gulp.dest(`${config.bundles[i]}${config.destJS}`));
    }

});

// Watch, apply only on Dev Environment
gulp.task('watch', () => {
    for (let i = 0; i < config.bundles.length; i++) {
        gulp.watch(`${config.bundles[i]}${config.srcCSS}`, ['sass','dumpAssets']);
        gulp.watch(`${config.bundles[i]}${config.srcJS}`, ['scripts']);
    }
});

// PLEASE ADD --production for deployment.
gulp.task('default', config.production ? ['sass', 'scripts'] : ['sass', 'scripts','watch', 'dumpAssets', 'dumpAssets']);
