const process = require('process')
const gulp = require('gulp')
const {series, parallel, src, dest} = gulp
const del = require('del')
const minimist = require('minimist')
const pump = require('pump')
const usage = require('gulp-help-doc')
const sass = require('gulp-sass')
const rename = require('gulp-rename')
const sourcemaps = require('gulp-sourcemaps')

const options = minimist(process.argv, {
	string: [
		'baseDir',
		'buildDir',
		'local',
	],
	bools: [
		'q', // Quiet
		'l', // Local
	],
	default: {
		baseDir: __dirname,
		buildDir: `${__dirname}/build`,
		q: false,
		l: false,
	},
})


// --------------------------------------------------------------------
// FUNCTIONS
// --------------------------------------------------------------------

/**
 * Runs tasks in the list, and executes a callback once they are all finished
 *
 * @param {Function<Function>[]} tasks The tasks to chain.
 * @param {Function} callback The callback that should run at the end of the chain.
 */
let chain = function (tasks, callback) {
	let task = tasks.shift()

	return task((error) => {
		if (error || !tasks.length) {
			return callback(error)
		}

		return chain(tasks, callback)
	})
}

// --------------------------------------------------------------------
// TASKS
// --------------------------------------------------------------------

function _help() {
	return function help(done) {
		return usage(gulp)
	}
}

function _clean({baseDir, buildDir}) {
	return function clean(done) {
		del.sync([buildDir], {force: true, cwd: baseDir})
		done()
	}
}

function _copy({baseDir, buildDir}) {
	return function copy(done) {
		pump(
			src([
				// All files with all extensions
				`**/*`,
				`**/*.*`,

				// Not these though
				`!${buildDir}/**/*`,
				'!.git/**/*',
				'!vendor/**/*',
				'!node_modules/**/*',
				// Not these because they need to be generated
				'!public/{css,css/**}',
				// Although vendor is totally ignored above, without the next line a similar error is thrown:
				// https://github.com/amphp/amp/issues/227
				// Presumably, a problem with symlinks, but not sure why
				'!vendor/amphp/**/asset',
			], {base: baseDir, cwd: baseDir, dot: true}),
			dest(buildDir),
			done
		)
	}
}

function _processCss({baseDir, buildDir, l: local}) {
	/**
	 * Convert Scss Files Into Css.
	 *
	 * This will also generate source maps.
	 * Anything in `resources` will to to `public/css`.
	 */
	return function processCss(done) {
		const workDir = local ? baseDir : buildDir;

		pump(
			src([
				'resources/scss/*.scss',
			], {
				base: workDir,
				cwd: workDir,
				dot: true,
			}),
			sourcemaps.init(),
			sass({
				outputStyle: 'compressed',
				precision: 3,
			}),
			sourcemaps.write('./'),
			rename(path => {
				let newPath = {
					dirname: path.dirname,
					extname: path.extname,
					// Suffix doesn't work: https://github.com/hparra/gulp-rename/issues/95
					basename: `${path.basename}.min`,
				}
				newPath.dirname = 'public/css'

				return newPath
			}),
			dest('.', {cwd: workDir, base: workDir}),
			done
		)
	}
}

// --------------------------------------------------------------------
// TARGETS
// --------------------------------------------------------------------
/**
 * Prints the usage doc.
 *
 * You can always use global options `buildDir` and `distDir` to control where the build happens.
 * The `q` flag will suppress all but error output.
 * The `l` flag will, for some commands, result in modifications to the working directory.
 *
 * @task {help}
 * @order {0}
 */
exports.help = series(
	_help(options),
)

/**
 * Cleans the build directory.
 *
 * @task {clean}
 * @arg {buildDir} The directory to use for building.
 */
exports.clean = series(
	_clean(options),
)

/**
 * Copies project files to build directory.
 *
 * Will skip Git files, as well as Composer and Node packages.
 *
 * @arg {buildDir} The directory to use for building.
 *
 * @task {copy}
 */
exports.copy = series(
	_copy(options),
)

/**
 * Processes CSS files.
 *
 * @task {processCss}
 * @args {local} Use this flag to put the results into the working directory instead of build dir.
 */
exports.processCss = series(
	_processCss(options),
)

/**
 * Processes assets.
 *
 * @task {processAsserts}
 * @order {4}
 * @args {local} Use this flag process in the working dir instead of build dir.
 */
exports.processAssets = parallel(
	exports.processCss,
)

/**
 * Process the source and other files.
 *
 * @task {process}
 */
exports.process = parallel(
	exports.processAssets,
)

/**
 * Create a build in the corresponding directory.
 *
 * @task {build}
 * @order {1}
 */
exports.build = series(
	exports.clean,
	exports.copy,
	exports.process,
)

exports.default = series(
	exports.build,
)
