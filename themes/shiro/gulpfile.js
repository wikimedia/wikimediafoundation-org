var gulp         = require( 'gulp' );
var sass         = require( 'gulp-sass' );
var rtlcss       = require( 'gulp-rtlcss' );
var footer       = require('gulp-footer');
var rename       = require( 'gulp-rename' );
var autoprefixer = require( 'gulp-autoprefixer' );
var sourcemaps   = require( 'gulp-sourcemaps' );

var concat       = require( 'gulp-concat' );
var uglify       = require( 'gulp-uglify' );
var eslint       = require( 'gulp-eslint' );

var svgsprite    = require( 'gulp-svg-sprite' );
var wppot        = require( 'gulp-wp-pot' );
var themeConfig  = require( './package.json' ).themeConfig;

var paths = {
	sassSrc: 'assets/src/sass/style.scss',
	sassRoot: 'assets/src/sass',
	sassFiles: ['assets/src/sass/**/*.scss', '!assets/src/sass/base/**/*.scss', '!assets/src/sass/_vars.scss'],
	jsFiles: 'assets/src/js/**/*.js',
	jsLintFiles: ['assets/src/js/**/*.js', '!assets/src/js/mule-js/**/*.js'],
	phpFiles: [ '*.php', 'inc/**/*.php', 'template-parts/**/*.php' ],
	svgFiles: 'assets/src/svg/individual/*.svg'
}

var svgConfig = {
	mode: {
		symbol: {
			sprite: 'icons.svg',
			dest: '.'
		}
	}
}

gulp.task( 'sass', gulp.series(function() {
	return gulp.src( paths.sassSrc )
			   .pipe( sourcemaps.init() )
			   .pipe( sass.sync( { outputStyle: 'compressed' } ).on( 'error', sass.logError ) )
			   .pipe( sourcemaps.write( 'map', {
					includeContent: false,
					sourceRoot: './'
			   }))
			   .pipe( gulp.dest( './' ) );
} ) );

gulp.task( 'rtl', gulp.series(function () {
	return gulp.src( 'style.css' )
		.pipe( rtlcss() )
		.pipe( footer( 'body{direction:rtl}' ) )
		.pipe( rename( 'rtl.css' ) )
		.pipe( gulp.dest( './' ) );
} ) );

gulp.task( 'svg', gulp.series(function() {
	return gulp.src( paths.svgFiles )
			   .pipe( svgsprite( svgConfig ) )
			   .pipe( gulp.dest( 'assets/dist') );
} ) );

gulp.task( 'concat', gulp.series(function() {
	return gulp.src( paths.jsFiles )
			   .pipe( concat( 'scripts.min.js' ) )
			   .pipe( sourcemaps.init() )
			   .pipe( uglify() )
			   .pipe( sourcemaps.write( 'maps' ) )
			   .pipe( gulp.dest( 'assets/dist' ) )
} ) );

gulp.task( 'jslint', gulp.series(function() {
	return gulp.src( paths.jsLintFiles )
			   .pipe( eslint() )
			   .pipe( eslint.format() )
} ) );

gulp.task( 'pot', gulp.series(function() {
	if ( typeof themeConfig === 'undefined' ) {
		return;
	}

	var text_domain;
	if ( typeof themeConfig.rename === 'object' ) {
		text_domain = themeConfig.rename.text_domain;
	}

	if ( typeof themeConfig.rename === 'string' ) {
		text_domain = themeConfig.rename;
	}

	return gulp.src( paths.phpFiles )
			   .pipe( wppot( { domain: text_domain } ) )
			   .pipe( gulp.dest( 'languages/' + text_domain + '.pot' ) );
} ) );

gulp.task( 'watch', gulp.series(function() {
	gulp.watch( gulp.series(paths.sassFiles, ['styles'] ) );
	gulp.watch( gulp.series(paths.jsFiles, ['scripts'] ) );
} ) );




gulp.task( 'styles', gulp.series( [ 'sass', 'rtl' ] ) );
gulp.task( 'scripts', gulp.series( [ 'jslint', 'concat' ] ) );
gulp.task( 'lint', gulp.series( [ 'jslint' ] ) );
gulp.task( 'build', gulp.series( [ 'svg', 'styles', 'scripts' ] ) );
gulp.task( 'default', gulp.series( [ 'build', 'watch' ] ) );