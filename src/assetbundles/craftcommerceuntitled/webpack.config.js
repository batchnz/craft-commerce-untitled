const { VueLoaderPlugin } = require("vue-loader");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const ManifestPlugin = require("webpack-manifest-plugin");
const webpack = require("webpack");
const path = require("path");

const configurePlugins = (options) => {
  const plugins = [
    new VueLoaderPlugin(),
    new CleanWebpackPlugin(),
    new ManifestPlugin(),
  ];

  if (options.mode === "development") {
    plugins.push(new webpack.HotModuleReplacementPlugin());
  }

  return plugins;
};

module.exports = (env, options) => {
  return {
    entry: {
      app: path.resolve(__dirname, "src/js/app.js"),
    },
    output: {
      filename: path.join("./js", "[name].[hash].js"),
      publicPath:
        (process.env.DEVSERVER_PUBLIC || "http://localhost:8080") + "/",
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader",
          },
        },
        {
          test: /\.vue$/,
          loader: "vue-loader",
        },
      ],
    },
    plugins: configurePlugins(options),
    resolve: {
      alias: {
        vue$:
          options.mode === "development"
            ? "vue/dist/vue.esm.js"
            : "vue/dist/vue.runtime.esm.js",
      },
      extensions: ["*", ".js", ".vue", ".json"],
    },
    optimization:
      options.mode === "development"
        ? {}
        : {
            moduleIds: "hashed",
            runtimeChunk: "single",
            splitChunks: {
              cacheGroups: {
                vendor: {
                  test: /[\\/]node_modules[\\/]/,
                  name: "vendors",
                  priority: -10,
                  chunks: "all",
                },
              },
            },
          },
    devServer: {
      public: process.env.DEVSERVER_PUBLIC || "http://localhost:8080",
      contentBase: path.resolve(process.cwd(), "/src/"),
      host: process.env.DEVSERVER_HOST || "localhost",
      port: process.env.DEVSERVER_PORT || 8080,
      https: !!parseInt(process.env.DEVSERVER_HTTPS || false),
      disableHostCheck: true,
      hot: true,
      overlay: true,
      watchContentBase: true,
      watchOptions: {
        poll: !!parseInt(process.env.DEVSERVER_POLL || false),
        ignored: /node_modules/,
      },
      headers: {
        "Access-Control-Allow-Origin": "*",
      },
    },
  };
};
