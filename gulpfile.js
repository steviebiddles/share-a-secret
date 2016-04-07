'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    minifyCSS = require('gulp-minify-css'),
    autoprefixer = require('gulp-autoprefixer'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify');

var app = {};

var config = {
    bowerDir: 'bower_components',
    assetsDir: 'app/Resources/assets',
    sassPattern: 'scss/**/*.scss'
};

app.addStyle = function (paths, outputFilename) {
    gulp.src(paths)
        .pipe(sass({
            includePaths: [
                config.bowerDir + '/bootstrap-sass/assets/stylesheets'
            ]
        }))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(concat(outputFilename))
        .pipe(minifyCSS())
        .pipe(gulp.dest('web/css'))
    ;
};

app.addScript = function (paths, outputFilename) {
    gulp.src(paths)
        .pipe(concat(outputFilename))
        .pipe(uglify())
        .pipe(gulp.dest('web/js'))
    ;
};

app.copy = function (srcFiles, outputDir) {
    gulp.src(srcFiles)
        .pipe(gulp.dest(outputDir));
};

gulp.task('styles', function () {
    app.addStyle([
        config.bowerDir + '/bootstrap-sass/assets/stylesheets',
        config.assetsDir + '/' + config.sassPattern
    ], 'site.css');
});

gulp.task('scripts', function () {
    app.addScript([
        config.bowerDir + '/jquery/dist/jquery.js',
        config.bowerDir + '/bootstrap-sass/assets/javascripts/bootstrap.js',
        config.assetsDir + '/js/site.js'
    ], 'site.js');
});

gulp.task('fonts', function () {
    app.copy(
        config.bowerDir + '/bootstrap-sass/assets/fonts/**/*',
        'web/fonts'
    );
});

gulp.task('watch', function () {
    gulp.watch(config.assetsDir + '/' + config.sassPattern, ['styles']);
    gulp.watch(config.assetsDir + '/js/**/*.js', ['scripts']);
});

gulp.task('default', ['styles', 'scripts', 'watch']);