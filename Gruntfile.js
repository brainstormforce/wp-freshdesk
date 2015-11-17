module.exports = function(grunt) {
	grunt.initConfig({
		copy: {
			main: {
				options: {
					mode: true
				},
				src: [
				'**',
				'!node_modules/**',
				'!build/**',
				'!.git/**',
				'!bin/**',
				'!tests/**',
				'!Gruntfile.js',
				'!package.json',
				'!.gitignore',
				'!phpunit.xml'
				],
				dest: 'wp-freshdesk/'
			}
		}
	});

	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.registerTask( 'release', [ 'copy' ] );
};