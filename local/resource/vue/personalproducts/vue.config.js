const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
//const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

module.exports = {
    //mode: 'production',
    configureWebpack: {
        output: {
            filename: 'app.js',
            chunkFilename: 'vendors.js'
        },
        plugins: [
            //new BundleAnalyzerPlugin(),
        ],
    },
};