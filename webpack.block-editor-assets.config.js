const path                 = require( 'path' );
const getBaseWebpackConfig = require( '@automattic/calypso-build/webpack.config.js' );

function getWebpackConfig( env, argv ) {
	const webpackConfig = getBaseWebpackConfig( env, argv );

	return {
		...webpackConfig,
		entry: {
			'course-shortcode-block': './assets/block-editor/course-shortcode-block',
			'messages-block': './assets/block-editor/messages-block',
			'messages-block-frontend': './assets/block-editor/messages-block/frontend.jsx',
		},
		output: {
			path: path.resolve( __dirname, 'assets/block-editor/build' ),
		},
		node: {
			crypto: 'empty',
		},
	};
}

module.exports = getWebpackConfig;
