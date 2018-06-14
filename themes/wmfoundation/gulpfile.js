var gulp         = require( 'gulp' );
var sass         = require( 'gulp-sass' );
var rtlcss       = require( 'gulp-rtlcss' );
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

gulp.task( 'sass', function() {
	return gulp.src( paths.sassSrc )
			   .pipe( sourcemaps.init() )
			   .pipe( sass.sync( { outputStyle: 'compressed' } ).on( 'error', sass.logError ) )
			   .pipe( autoprefixer( { browsers: [ '> 5%', 'Last 2 versions', 'IE 10' ] } ) )
			   .pipe( sourcemaps.write( 'map', {
					includeContent: false,
					sourceRoot: './'
			   }))
			   .pipe( gulp.dest( './' ) );
} );

gulp.task( 'rtl', function () {
	return gulp.src( 'style.css' )
		.pipe( rtlcss() )
		.pipe( rename( 'rtl.css' ) )
		.pipe( gulp.dest( './' ) );
} );

gulp.task( 'svg', function() {
	return gulp.src( paths.svgFiles )
			   .pipe( svgsprite( svgConfig ) )
			   .pipe( gulp.dest( 'assets/dist') );
} );

gulp.task( 'concat', function() {
	return gulp.src( paths.jsFiles )
			   .pipe( concat( 'scripts.min.js' ) )
			   .pipe( sourcemaps.init() )
			   .pipe( uglify() )
			   .pipe( sourcemaps.write( 'maps' ) )
			   .pipe( gulp.dest( 'assets/dist' ) )
} );

gulp.task( 'jslint', function() {
	return gulp.src( paths.jsLintFiles )
			   .pipe( eslint() )
			   .pipe( eslint.format() )
} );

gulp.task( 'pot', function() {
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
} );

gulp.task( 'watch', function() {
	gulp.watch( paths.sassFiles, ['styles'] );
	gulp.watch( paths.jsFiles, ['scripts'] );
} );




gulp.task( 'styles', [ 'sass' ] );
gulp.task( 'scripts', [ 'jslint', 'concat' ] );
gulp.task( 'lint', [ 'jslint' ] );
gulp.task( 'build', [ 'svg', 'styles', 'scripts' ] );
gulp.task( 'default', [ 'build', 'watch' ] );