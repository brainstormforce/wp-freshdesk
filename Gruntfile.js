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
		},
		compress: {
			main: {
				options: {
					archive: 'wp-freshdesk/wp-freshdesk.zip',
					mode: 'zip'
				},
				files: [
			      // Each of the files in the src/ folder will be output to
			      // the dist/ folder each with the extension .gz.js
			      {expand: true, src: ['**/*'], dest: 'wp-freshdesk/', ext: '.zip'}
			      ]
			  }
			}
		});

	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.registerTask( 'release', [ 'copy', 'compress' ] );
};