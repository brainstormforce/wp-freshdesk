copy: {module.exports = function (grunt) {
   // Project configuration.
   grunt.initConfig( {
       pkg: grunt.file.readJSON( 'package.json' ),
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
                   '!Gruntfile.js',
                   '!package.json',
                   '!.gitignore'
               ],
               dest: 'wp-freshdesk/'
           }
       }
   });

  grunt.loadNpmTasks( 'grunt-contrib-copy' );
  grunt.registerTask( 'release', [ 'copy' ] );

};