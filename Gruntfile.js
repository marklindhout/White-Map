module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		appcache: {
			options: {
				basePath: '.'
			},
			all: {
				dest: '<%= pkg.name %>.appcache',
				cache: {
					patterns: [
						'style.css',
						'library/css/**/*',
						'library/font/**/*',
						'library/img/**/*',
						'library/js/**/*',
					],
					literals: '/',
					network: '*',
					fallback: '/ /offline.html',
				},
			},
		},

		// Javascript Linting
		jshint: {
			options: {
				loopfunc: true,
			},
			files: ['library/js/*.js', '!library/js/*.min.js'],
		},

		// Less compilation and compression
		less: {
			app: {
				options: {
					cleancss: true,
					optimization: 9,
					strictImports: true,
					strictMath: true,
					report: 'gzip',
					sourceMap: true,
				},
				files: {
					'library/css/<%= pkg.name %>.min.css': ['library/less/_package.less'],
				},
			},
		},

		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
				sourceMap: true,
			},
			build: {
				src: '<%= jshint.files %>',
				dest: 'library/js/<%= pkg.name %>.min.js'
			},
		},

		watch: {
			js: {
				files: ['<%= jshint.files %>'],
				tasks: ['uglify', 'jshint'],
			},
			less: {
				files: ['library/less/**/*.less'],
				tasks: ['less'],
			},
		},

	});

	// Load modules
	grunt.loadNpmTasks('grunt-appcache');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-jshint');

	// Define tasks
	grunt.registerTask('default', ['watch']);
	grunt.registerTask('buildall', ['build-all']);
	grunt.registerTask('build-all', ['appcache', 'less', 'uglify', 'jshint']);

};
