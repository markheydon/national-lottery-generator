const webpack = require('webpack');
const WebpackDevServer = require('webpack-dev-server');

/**
 * Start Laravel Mix hot reload using the webpack-dev-server API.
 *
 * This avoids a webpack-cli/dev-server v5 incompatibility where
 * non-devServer flags leak into devServer options.
 */
async function startHotServer() {
    // Ensure Mix builds a hot/dev-server config.
    if (!process.argv.includes('--hot')) {
        process.argv.push('--hot');
    }

    const configFactory = require('../node_modules/laravel-mix/setup/webpack.config');
    const config =
        typeof configFactory === 'function'
            ? await configFactory({}, {})
            : configFactory;

    const compiler = webpack(config);

    const supportedKeys = new Set([
        'allowedHosts',
        'bonjour',
        'client',
        'compress',
        'devMiddleware',
        'headers',
        'historyApiFallback',
        'host',
        'hot',
        'ipc',
        'liveReload',
        'onListening',
        'open',
        'port',
        'proxy',
        'server',
        'app',
        'setupExitSignals',
        'setupMiddlewares',
        'static',
        'watchFiles',
        'webSocketServer',
    ]);

    const options = Object.entries(config.devServer || {}).reduce(
        (result, [key, value]) => {
            if (supportedKeys.has(key)) {
                result[key] = value;
            }

            return result;
        },
        { hot: true }
    );

    const server = new WebpackDevServer(options, compiler);
    await server.start();
}

startHotServer().catch((error) => {
    console.error(error);
    process.exit(1);
});
