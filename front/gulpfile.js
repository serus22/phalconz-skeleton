'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var minifyCss = require('gulp-minify-css');
var watch = require('gulp-watch');
var livereload = require('gulp-livereload');

//sass
gulp.task('sass', function () {
  gulp.src(['../modules/**/*.scss', '../public/**/*.scss'])
    .pipe(sass({outputStyle: 'compressed'}))
    .pipe(concat('style.css'))
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(gulp.dest('../public/dist'))
    .pipe(livereload());
});

//watch
gulp.task('watch', function() {
  livereload.listen();
  gulp.watch(['../**/*.scss', '!../public/dist/style.css'], ['sass']);
  gulp.watch('../**/*.phtml').on('change', function(file) {
    livereload.changed(file.path);
  });
});

// Default task
gulp.task('default', ['watch']);

