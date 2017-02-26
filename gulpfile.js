var gulp = require('gulp');
var browserSync = require('browser-sync');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');

function swallowError (error) {
    console.log(error.toString());
    this.emit('end')
}

// Static Server + watching scss/html files
gulp.task('serve', ['sass'], function() {
    browserSync.init({
        proxy:  "127.0.0.1:8000"
    });

    gulp.watch(["resources/scss/*.scss", "resources/scss/**/*scss"], ['sass']);
    gulp.watch(["resources/views/*.php", "resources/views/**/*.php"]).on('change', browserSync.reload);
    gulp.watch(["resources/lang/**/*.php"]).on('change', browserSync.reload);
});

// Compile sass into CSS & auto-inject into browsers
gulp.task('sass', function() {
    return gulp.src("resources/scss/styles.scss")
        .pipe(sass())
        .on('error', swallowError)
        .pipe(autoprefixer({
            browsers: ['> 0%', 'ie 6-8'],
            cascade: false,
            remove: false
        }))
        .pipe(gulp.dest("public/css"))
        .pipe(browserSync.stream());
});