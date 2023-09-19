const webpack = require('webpack');
const fs = require('fs');
const path = require("path");
const glob = require('glob-all');
const pathDistribution = "dist/";
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const { PurgeCSSPlugin } = require('purgecss-webpack-plugin');

const htmlPlugins = [];

// function generateHtmlPlugins (srcDir, destDir) {
//     const templateFiles = fs.readdirSync(path.resolve(__dirname, srcDir));
//     let htmlPlugins = [];
//     if (templateFiles.length > 0) {
//         templateFiles.forEach(item => {
//             const parts = item.split('.');
//             if (parts.length == 3) {
//                 htmlPlugins.push(new HtmlWebpackPlugin({
//                     minify: false,
//                     inject: false,
//                     hash: true,
//                     template: srcDir + item,
//                     filename: destDir + item
//                 }));
//             }
//         });
//     }
//     return htmlPlugins;
// };

// const htmlPlugins = generateHtmlPlugins('../views/', '../../resources/views/compiled/');

module.exports = {
    entry: path.resolve(__dirname, "./entry.js"),
    output: {
        filename: "bundle.js",
        chunkFilename: '[name].[chunkhash:8].chunk.js',
        path: path.resolve(__dirname, "../../public/" + pathDistribution),
        publicPath: pathDistribution,
    },
    mode: "production",
    module: {
        rules: [
            {
                test: /\.css$/,
                use: [
                    { loader: MiniCssExtractPlugin.loader, options: { publicPath: "" } },
                    { loader: "css-loader", options: { importLoaders: 1 } },
                    { loader: "postcss-loader", options: { postcssOptions: {
                        plugins: [
                            require('autoprefixer')
                        ]
                    } } }
                ],
            },
            {
                test: /\.scss$/,
                use: [
                    { loader: MiniCssExtractPlugin.loader, options: { publicPath: "" } },
                    { loader: "css-loader", options: { importLoaders: 1 } },
                    { loader: "postcss-loader", options: { postcssOptions: {
                        plugins: [
                            require('autoprefixer')
                        ]
                    } } },
                    { loader: "sass-loader" }
                ],
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: "babel-loader",
                        options: {
                            presets: ["@babel/env"]
                        },
                    },
                ],
            },
        ],
    },
    optimization: {
        minimize: true,
        minimizer: [
            new CssMinimizerPlugin(),
            new TerserPlugin()
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "style.css",
        }),
        new CleanWebpackPlugin({
            dry: false,
            cleanOnceBeforeBuildPatterns: ["**/*"],
            dangerouslyAllowCleanPatternsOutsideProject: true
        }),
        new PurgeCSSPlugin({
            paths: glob.sync([
                path.resolve(__dirname, '../../resources/views') + '/**/*',
                path.resolve(__dirname, './components') + '/**/*',
                path.resolve(__dirname, './mixins') + '/**/*',
                path.resolve(__dirname, './utilities') + '/**/*',
            ], { nodir: true }),
            
            safelist: {
                standard: [/swiper$/, /swiper/]
            },
            
            defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || []
        }),
    ].concat(htmlPlugins),
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js'
        }
    },
    externals: {
        Stripe: "Stripe",
        paypal: "paypal",
    },
};