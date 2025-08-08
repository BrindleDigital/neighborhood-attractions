const mix = require('laravel-mix');
const globImporter = require('node-sass-glob-importer');

mix.sass('assets/css/neighborhood-attractions.scss', 'assets/css', {
	sassOptions: {
		importer: globImporter(),
	},
}).options({
	processCssUrls: false,
});
