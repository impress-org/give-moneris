const mix = require( 'laravel-mix' );
const wpPot = require( 'wp-pot' );

mix
	.setPublicPath( 'assets/dist' )
	.sourceMaps( false, 'eval-source-map' )

	.js( 'assets/src/js/admin/give-moneris-admin.js', 'assets/dist/js' )
	.js( 'assets/src/js/frontend/give-moneris.js', 'assets/dist/js' )

	.sass( 'assets/src/css/admin/give-moneris-admin.scss', 'assets/dist/css' )
	.sass( 'assets/src/css/frontend/give-moneris-frontend.scss', 'assets/dist/css/give-moneris.css' );

mix.webpackConfig( {
	externals: {
		$: 'jQuery',
		jquery: 'jQuery',
	},
} );

if ( mix.inProduction() ) {
	wpPot( {
		package: 'Give-Moneris',
		domain: 'give-moneris',
		destFile: 'languages/give-moneris.pot',
		relativeTo: './',
		bugReport: 'https://github.com/impress-org/give-moneris/issues/new',
		team: 'GiveWP <info@givewp.com>',
	} );
}
