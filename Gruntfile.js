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
		},
		postcss: {
			main: {
				options: {
					map: true,
					processors: [
						require('pixrem')(), // add fallbacks for rem units
						require('autoprefixer')({
							browsers: [
							'Android >= 2.1',
							'Chrome >= 21',
							'Edge >= 12',
							'Explorer >= 7',
							'Firefox >= 17',
							'Opera >= 12.1',
							'Safari >= 6.0'
							],
							cascade: false
				        }), // add vendor prefixes
						]
					},
					dist: {
						src: 'css/*.css'
					}
				}
			});

	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.registerTask( 'release', [ 'copy', 'compress' ] );
	grunt.registerTask( 'css', [ 'postcss' ] );
};