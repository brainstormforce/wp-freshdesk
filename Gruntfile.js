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
				'!css/sourcemap/**',
				'!.git/**',
				'!bin/**',
				'!tests/**',
				'!Gruntfile.js',
				'!package.json',
				'!.gitignore',
				'!phpunit.xml',
				'!README.md'
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
					map: {
						inline: false, 
						annotation: 'css/sourcemap' //sourcemap for autoprefixr
					},
					processors: [
					require('autoprefixer')({
						browsers: [
						'Android >= 2.1',
						'Chrome >= 21',
						'Edge >= 12',
						'Explorer >= 7',
						'Firefox >= 17',
						'Opera >= 10',
						'Safari >= 6.0'
						]
				        }), // add vendor prefixes
					]
				},
				src: [
				'css/*.css'
				]
			}
		},
		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt'
				}
			},
		},

	});

grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
grunt.loadNpmTasks( 'grunt-contrib-copy' );
grunt.loadNpmTasks( 'grunt-contrib-compress' );
grunt.loadNpmTasks( 'grunt-postcss' );

grunt.registerTask( 'release', [ 'copy', 'compress' ] );
grunt.registerTask( 'css', [ 'postcss' ] );
grunt.registerTask( 'readme', ['wp_readme_to_markdown']);

grunt.util.linefeed = '\n';
};