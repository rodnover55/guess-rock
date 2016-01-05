//var elixir = require('laravel-elixir');
//
///*
// |--------------------------------------------------------------------------
// | Elixir Asset Management
// |--------------------------------------------------------------------------
// |
// | Elixir provides a clean, fluent API for defining some basic Gulp tasks
// | for your Laravel application. By default, we are compiling the Sass
// | file for our application, as well as publishing vendor resources.
// |
// */
//
//elixir(function(mix) {
//    mix.sass('app.scss');
//});

var gulp = require('gulp');
var gutil = require('gulp-util');
var gulpif = require('gulp-if');
var stylus = require('gulp-stylus');
var csso = require('gulp-csso');
var uglify = require('gulp-uglify');
var prefix = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var webserver = require('gulp-webserver');
var ngAnnotate = require('gulp-ng-annotate');
var angularFilesort = require('gulp-angular-filesort');
var notify = require('gulp-notify');
var ngHtml2Js = require("gulp-ng-html2js");
var minifyHtml = require("gulp-minify-html");

// Set some defaults
var isDev = true;
var isProd = false;

// If "production" is passed from the command line then update the defaults
if (gutil.env.type === 'production') {
    isDev = false;
    isProd = true;
}

gulp.task('stylus', function() {
    gulp.src(['public/src/stylus/main.styl'])
        .pipe(stylus({
            'include css': true,
            'url': {
                'name': 'embedurl',
                'paths': ['.', 'public/src/img/embed'], //искать сначала в текущей папке, если нет то в общей папке
                'limit': false
            }
        }))
        .pipe(prefix())
        .pipe(gulpif(isProd, csso()))
        .pipe(gulp.dest('./public/css/'));
});

//gulp.task('js', function() {
//    gulp.src(['public/src/js/**/*.js'])
//        .pipe(angularFilesort())
//        .on('error', notify.onError("Error: <%= error.message %>"))
//        .pipe(ngAnnotate())
//        .pipe(gulpif(isProd, uglify({
//            compress: {
//                drop_console: true
//            }
//        })))
//        .pipe(concat('app.js'))
//        .pipe(gulp.dest('public/js'));
//});

//gulp.task('vendor', function() {
//    gulp.src([
//            'public/vendor/Polyfills-for-IE8/getComputedStyle.js',
//            'public/vendor/es5-shim/es5-shim.js'
//        ])
//        .pipe(concat('vendor.js'))
//        .pipe(gulpif(isProd, uglify({
//            mangle: false,
//            compress: {
//                drop_console: true
//            }
//        })))
//        .pipe(gulp.dest('public/js'));
//});

//gulp.task('vendor-style', function() {
//    gulp.src([
//            'public/vendor/angular-tooltips/src/css/angular-tooltips.css'
//        ])
//        .pipe(concat('vendor.css'))
//        .pipe(gulpif(isProd, csso()))
//        .pipe(gulp.dest('./public/css/'));
//});

//gulp.task('templates', function () {
//    gulp.src("public/views/*.html")
//        .pipe(gulpif(isProd, minifyHtml({
//            empty: true,
//            spare: true,
//            quotes: true
//        })))
//        .pipe(ngHtml2Js({
//            moduleName: "labora",
//            prefix: "/views/"
//        }))
//        .pipe(gulpif(isProd, uglify({
//            mangle: false,
//            compress: {
//                drop_console: true
//            }
//        })))
//        .pipe(concat("templates.js"))
//        .pipe(gulp.dest("public/js"));
//})

gulp.task('watch', function() {

    //gulp.watch('public/src/js/**/*.js', ['js']);
    gulp.watch('public/src/stylus/**/*.styl', ['stylus']);
    //gulp.watch('public/views/*.html', ['templates']);
    //gulp.watch('public/vendor/**/*.js', ['vendor']);
    //gulp.watch('public/vendor/**/css/*.css', ['vendor-style']);
});

gulp.task('build', ['stylus']);
//gulp.task('build', ['stylus', 'vendor', 'js', 'templates', 'vendor-style']);
gulp.task('default', ['build', 'watch']);

