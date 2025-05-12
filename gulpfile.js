const gulp = require( 'gulp' );
const { pipeline } = require( 'stream' );
const sass = require( 'gulp-sass' )( require( 'sass' ) );
const sourcemaps = require( 'gulp-sourcemaps' );
const csso = require( 'gulp-csso' );
const uglify = require( 'gulp-uglify' );
const concat = require( 'gulp-concat' );


async function sass_compile(cb) {
	const autoprefixer = (await import('gulp-autoprefixer')).default; // Используем import()

	return pipeline(
		gulp.src('./assets/scss/**/style.scss'),
		sourcemaps.init(),
		sass().on('error', sass.logError),
		autoprefixer(),
		csso({
			restructure: true,
			sourceMap: true,
			debug: true,
			comments: 'exclamation',
		}),
		sourcemaps.write('./'),
		gulp.dest('./'),
		(err) => {
			if (err) {
				console.error('Таск sass_compile выдал ошибку.', err.toString());
			} else {
				console.log('Таск sass_compile завершён');
			}
		});
}

function minify_js ( cb ) {
	return pipeline(
		gulp.src( './assets/js/*' ),
		sourcemaps.init(),
		uglify().on( 'error', function ( err ) {
			console.log( 'Таск minify_js выдал ошибку: \n ' + err )
		} ),
		concat( 'main.js' ),
		sourcemaps.write( './' ),
		gulp.dest( './' ),
		( err ) => {
		}
	);
}

gulp.task( 'watch', function () {
	gulp.watch( './assets/scss/**/*.scss', gulp.series( sass_compile ) );
	gulp.watch( './assets/js/*', gulp.series( minify_js ) );
} );