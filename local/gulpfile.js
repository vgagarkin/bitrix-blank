// Подключаем Gulp
var gulp = require('gulp');
less = require('gulp-less');
path = require('path');
//autoprefixer= require('gulp-autoprefixer');

gulp.task('less', function () {
    return gulp.src('src/less/template_styles.less')
        .pipe(less({
            paths: [ path.join(__dirname, 'less', 'includes') ]
        }))
        /*.pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))*/
        .pipe(gulp.dest('build/css'));
});
gulp.task("watch", function() {
    gulp.watch("src/less/*.less", gulp.series('less'));
});

// Запуск тасков по умолчанию
gulp.task("default", gulp.series('less', 'watch'));