var gulp = require('gulp');
var clean = require('gulp-clean');
var copy = require('gulp-copy');
var zip = require('gulp-zip');
var composer = require('gulp-composer');
var exec = require('child_process').exec;
var phpcs = require('gulp-phpcs');
var config = require('./package.json');

// Clean the target directory.
gulp.task('clean', function () {
	console.log('Cleaning up target directory  ...');
	return gulp.src('dist', {read: false})
		.pipe(clean());
});

// Prepare composer.
gulp.task('compose', function () {
	console.log('Preparing Composer ...');
	return composer('install');
});

// Execute PHP Code Sniffer.
gulp.task('test-cs', function (cb) {
	return exec('./vendor/bin/phpcs --config-set installed_paths vendor/wimg/php-compatibility,vendor/wp-coding-standards/wpcs', function (err, stdout, stderr) {
		console.log(stdout);
		console.log(stderr);
		if (null === err) {
			console.log('Running PHP Code Sniffer tests ...');
			gulp.src(['*.php', 'inc/**/*.php'])
				.pipe(phpcs({bin: './vendor/bin/phpcs', standard: 'phpcs.xml'}))
				.pipe(phpcs.reporter('log'));
		}
		cb(err);
	});
});

// Bundle files as required for plugin distribution.
gulp.task('bundle', ['clean'], function () {
	console.log('Collecting files for package dist/' + config.name + config.version + ' ...');
	return gulp.src(['**/*.php', '!test/**', '!vendor/**', 'README.md', 'LICENSE.txt', 'lang/*'], {base: './'})
		.pipe(copy('./dist/' + config.name + '.' + config.version));
});

// Create a ZIP package of the relevant files for plugin distribution.
gulp.task('package', ['bundle'], function () {
	console.log('Building package dist/' + config.name + config.version + '.zip ...');
	return gulp.src('./dist/' + config.name + '.' + config.version + '/**')
		.pipe(zip(config.name + '.' + config.version + '.zip'))
		.pipe(gulp.dest('./dist'));
});

gulp.task('default', ['clean', 'compose', 'test-cs', 'bundle', 'package']);
