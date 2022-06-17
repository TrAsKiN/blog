const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    mode: 'production',
    entry: './assets/main.js',
    output: {
        filename: './js/app.js',
        path: path.resolve(__dirname, 'public'),
    },
    module: {
        rules: [{
            test: /\.(scss)$/,
            use: [{
                loader: MiniCssExtractPlugin.loader,
                options: {
                    publicPath: '../images/',
                },
            },
                'css-loader',
                'sass-loader'
            ],
        }, {
            test: /\.(png|svg|jpg|jpeg|gif)$/i,
            type: 'asset/resource',
            generator: {
                outputPath: 'images/',
            },
        }]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: './css/app.css'
        }),
    ],
}
