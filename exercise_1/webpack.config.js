const webpack = require('webpack');
const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin');

module.exports = {
    entry: {
        ['reverse_binary']: './src/modules/reverse_binary/index.js',
    },
    output: {
        path: path.resolve(__dirname, 'build'),
        filename: '[name].js',
    },
    resolveLoader: {
        modules: [path.join(__dirname, 'node_modules')],
    },
    module: {
        rules: [
            {
                test: /src[\\/].*\.js$/,
                exclude: /node_modules/,
                use: [
                    { loader: 'babel-loader' },
                    {
                        loader: 'eslint-loader',
                        options: {
                            emitError: false,
                            emitWarning: false,
                            failOnWarning: false,
                            failOnError: true,
                        },
                    },
                ],
            },
        ],
    },
    plugins: [
        new CleanWebpackPlugin(
            ['build'],
            {
                root: __dirname,
                verbose: true,
                dry: false,
                exclude: [],
                watch: false,
            }
        ),
        new webpack.LoaderOptionsPlugin({
            debug: false,
        }),
    ],
};
