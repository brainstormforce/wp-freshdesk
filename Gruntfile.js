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
					archive: 'wp-freshdesk.zip',
					mode: 'zip'
				},
				files: [
			      { 
			      	src: [
			      	'./wp-freshdesk/**'
			      	]

			      }
			    ]
			  }
			}
		});

	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.registerTask( 'release', [ 'copy', 'compress' ] );
};