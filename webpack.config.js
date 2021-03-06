var webpack             = require('webpack');
var ExtractTextPlugin   = require('extract-text-webpack-plugin');

var theme = 'slickboard';

module.exports = {
    entry: [
        './themes/' + theme + '/_assets/src/js/index.js'
    ],
    output: {
        path: __dirname + '/themes/' + theme + '/_assets/dist',
        filename: 'slickboard.min.js'
    },
    module: {
        loaders: [
            {
                test: /\.scss$/,
                //loader: 'style-loader!css-loader!sass-loader!font-loader?format[]=truetype&format[]=woff&format[]=embedded-opentype',
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: ['css-loader?minimize&sourceMap', 'sass-loader']
                })
            },
            {
                test: /\.(jpe?g|gif|png)$/,
                loader: 'file-loader?outputPath=images/'
            },
            {
                test: require.resolve('jquery'),
                loader: 'expose-loader?jQuery!expose-loader?$'
            }
        ]
    },
    plugins: [
        new ExtractTextPlugin('slickboard.min.css'),
        new webpack.optimize.UglifyJsPlugin({
            compress: { warnings: false }
        })
    ]
};