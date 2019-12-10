// Подключаем Gulp
let gulp = require('gulp');
less = require('gulp-less');
rename = require('gulp-rename');
path = require('path');
//autoprefixer= require('gulp-autoprefixer');

gulp.task('less', function () {
    return gulp.src('src/less/main.less')
        .pipe(less({
            paths: [ path.join(__dirname, 'less', 'includes') ]
        }))
        /*.pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))*/
        .pipe(rename('build.css'))
        .pipe(gulp.dest('build/css'));
});
gulp.task("watch", function() {
    gulp.watch("src/less/*.less", gulp.series('less'));
});

// Запуск тасков по умолчанию
gulp.task("default", gulp.series('less', 'watch'));