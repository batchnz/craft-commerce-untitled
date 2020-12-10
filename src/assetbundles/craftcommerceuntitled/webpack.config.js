const { VueLoaderPlugin } = require("vue-loader");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const ManifestPlugin = require("webpack-manifest-plugin");
const webpack = require("webpack");
const path = require("path");

const configurePlugins = (options) => {
  const plugins = [
    new VueLoaderPlugin(),
    new ManifestPlugin(configureManifest("manifest.json")),
  ];

  if (options.mode === "development") {
    plugins.push(new webpack.HotModuleReplacementPlugin());
  }

  if (options.mode === "production") {
    plugins.push(
      new CleanWebpackPlugin({
        cleanOnceBeforeBuildPatterns: [
          path.resolve(__dirname, "dist/js/"),
          path.resolve(__dirname, "dist/manifest.json"),
        ],
      })
    );
  }

  return plugins;
};

const configureManifest = (fileName) => {
  return {
    fileName: fileName,
    basePath: "",
    map: (file) => {
      file.name = file.name.replace(/(\.[a-f0-9]{32})(\..*)$/, "$2");
      return file;
    },
  };
};

const configurePublicPath = (options) => {
  if (options.mode === "development") {
    return (process.env.DEVSERVER_PUBLIC || "http://localhost:8080") + "/";
  }

  return process.env.PUBLIC_PATH || "/dist/";
};

module.exports = (env, options) => {
  return {
    entry: {
      app: path.resolve(__dirname, "src/js/app.js"),
    },
    output: {
      filename: path.join(
        "./js",
        `[name]${options.mode === "development" ? `.[hash]` : ``}.js`
      ),
      publicPath: configurePublicPath(options),
      path: path.resolve(__dirname, "dist/"),
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
