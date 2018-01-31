/**
 * Created by User on 01/06/2017.
 */

module.exports = function (grunt) {
    grunt.initConfig({
        babel: {
            options: {
                sourceMap: true,
                presets: ['babel-preset-es2015']
            },
            dist: {
                files: {
                    'script.js': 'views/js/script.js'
                }
            }
        },
        jshint: {
            files: ["views/js/*.js"],
            options: {
                esnext: true,
                globals: {
                    jQuery: true
                }
            }
        },
        autoprefixer: {
            single_file: {
                src: "style.css",
                dest: "style.css"
            }
        },
        browserify: {
            client: {
                src: [
                    "views/js/script.js"
                ],
                dest: "script.js"
            }
        },
        sass_import: {
            options: {
                basePath: 'sass/'
            },
            files: {
                'style.scss': ['node_modules/*']
            },
        },
        sass: {                              // Task
            dist: {                            // Target
                options: {                       // Target options
                    style: 'compressed',
                },
                files: {                         // Dictionary of files
                    'style.css': 'views/css/style.scss'
                }
            }
        },
        watch: {
            css: {
                files: ["views/css/*.scss","views/css/mini.css/src/*/*.scss"],
                tasks: ["css"]
            },
            scripts: {
                files: ["views/js/*.js"],
                tasks: ["babel", "browserify"],
                options: {
                    spawn: false,
                    livereload: true
                }
            }
        }
    });

    grunt.loadNpmTasks("grunt-contrib-jshint");
    grunt.loadNpmTasks("grunt-autoprefixer");
    grunt.loadNpmTasks("grunt-browserify");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-sass-import');
    grunt.loadNpmTasks('grunt-babel');

    grunt.registerTask("css", ["sass", "autoprefixer"]);
    grunt.registerTask("js", ["babel","browserify"]);

    grunt.registerTask("default", ["css", "js"]);
};
