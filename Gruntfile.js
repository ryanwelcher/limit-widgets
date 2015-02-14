module.exports = function(grunt) {

	// Load all grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			my_target: {
				files: {
					'assets/script.min.js': ['assets/script.js']
				}
			}
		},
		jshint: {
			all: ['assets/script.js']
		},
		watch: {
			files: ['assets/script.js'],
			tasks: ['jshint', 'uglify']
		}
	});

	// Default task(s).
	grunt.registerTask( 'default', ['jshint', 'uglify'] );
};
