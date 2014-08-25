module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        folder: {
            components: 'bower_components',
            src: 'app/Resources',
            dist: 'web'
        },
        concat:
        {
            site: {
                src: [
                    '<%=folder.components%>/jquery/jquery.js',

                    // Twitter bootstrap
                    '<%=folder.components%>/bootstrap/js/affix.js',
                    '<%=folder.components%>/bootstrap/js/alert.js',
                    '<%=folder.components%>/bootstrap/js/button.js',
                    '<%=folder.components%>/bootstrap/js/carousel.js',
                    '<%=folder.components%>/bootstrap/js/collapse.js',
                    '<%=folder.components%>/bootstrap/js/dropdown.js',
                    '<%=folder.components%>/bootstrap/js/modal.js',
                    '<%=folder.components%>/bootstrap/js/tooltip.js',
                    '<%=folder.components%>/bootstrap/js/popover.js',
                    '<%=folder.components%>/bootstrap/js/scrollspy.js',
                    '<%=folder.components%>/bootstrap/js/tab.js',
                    '<%=folder.components%>/bootstrap/js/transition.js',

                    // Others
                    '<%=folder.src%>/js/*.js'

                ],
                dest: '<%=folder.dist%>/js/site.js'
            },
            modernizr: {
                src: [
                    '<%=folder.components%>/modernizr/modernizr.js'
                ],
                dest: '<%=folder.dist%>/js/modernizr.js'
            }
        },

        uglify: {
            site: {
                src: ['<%=folder.dist%>/js/site.js'],
                dest: '<%=folder.dist%>/js/site.js'
            },
            modernizr: {
                src: ['<%=folder.dist%>/js/modernizr.js'],
                dest: '<%=folder.dist%>/js/modernizr.js'
            }
        },
        less: {
            application: {
                options: {
                    paths: [
                        "<%=folder.components%>",
                        "<%=folder.src%>/less"
                    ],
                    yuicompress: false
                },
                files: {
                    "<%=folder.dist%>/css/screen.css": "<%=folder.src%>/less/css-screen.less"
                }
            }
        },
		watch: {
			gruntfile: {
				files: ['Gruntfile.js']
			},
			js: {
				files: '<%=folder.src%>/js/**/*.js',
				tasks: ['js']
			},
			css: {
				files: '<%=folder.src%>/less/**/*.less',
				tasks: ['css']
			}
		}
    });

    // Load tasks from "grunt-sample" grunt plugin installed via Npm.
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('js', ['concat']);
    grunt.registerTask('css', 'less');

    // Default task.
    grunt.registerTask('default', ['css', 'js']);
    grunt.registerTask('deploy', ['default', 'uglify']);

};