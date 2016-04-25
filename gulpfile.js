var gulp = require('gulp');
var cssmin = require('gulp-cssmin');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var del = require('del');

gulp.task('nuke', function () {
  return del([
    'assets/styles/combined',
    'assets/scripts/combined',
    'public/css',
    'public/js'
  ]);
});

gulp.task('concat_styles', ['nuke'], function() {
  return gulp.src(['assets/styles/*.css', 'assets/lib/dropzone/dist/dropzone.css'])
    .pipe(concat('main.css'))
    .pipe(gulp.dest('assets/styles/combined'));
});

gulp.task('concat_scripts', ['nuke'], function() {
  return gulp.src(['assets/lib/dropzone/dist/dropzone.js', 'assets/lib/jquery/dist/jquery.js',
      'assets/scripts/main.js'])
    .pipe(concat('main.js'))
    .pipe(gulp.dest('assets/scripts/combined'));
});

gulp.task('min_css', ['concat_styles'], function(){
  gulp.src('assets/styles/combined/main.css')
      .pipe(cssmin())
      .pipe(gulp.dest('public/css'))
});

gulp.task('min_js', ['concat_scripts'], function(){
  gulp.src('assets/scripts/combined/main.js')
    .pipe(uglify())
    .pipe(gulp.dest('public/js'))
});


gulp.task('min', ['nuke', 'concat_styles', 'concat_scripts', 'min_css', 'min_js']);