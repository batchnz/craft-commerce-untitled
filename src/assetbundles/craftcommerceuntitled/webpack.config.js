const { VueLoaderPlugin } = require("vue-loader");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const ManifestPlugin = require("webpack-manifest-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const webpack = require("webpack");
const path = require("path");

const configurePlugins = (mode) => {
  const plugins = [
    new VueLoaderPlugin(),
    new ManifestPlugin(configureManifest("manifest.json")),
  ];

  if (mode === "development") {
    plugins.push(new webpack.HotModuleReplacementPlugin());
  }

  if (mode === "production") {
    plugins.push(
      new CleanWebpackPlugin({
        cleanOnceBeforeBuildPatterns: [
          path.resolve(__dirname, "dist/js/"),
          path.resolve(__dirname, "dist/css/"),
          path.resolve(__dirname, "dist/manifest.json"),
        ],
      })
    );
    plugins.push(
      new MiniCssExtractPlugin({
        filename: path.join(
          "./css",
          mode === "development" ? "[name].[hash].css" : "[name].css"
        ),
        ignoreOrder: true,
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

const configurePublicPath = (mode) => {
  if (mode === "development") {
    return (process.env.DEVSERVER_PUBLIC || "http://localhost:8080") + "/";
  }

  return process.env.PUBLIC_PATH || "/dist/";
};

module.exports = (env, { mode = "development" }) => {
  return {
    entry: {
      app: path.resolve(__dirname, "src/js/app.js"),
    },
    output: {
      filename: path.join(
        "./js",
        mode === "development" ? "[name].[hash].js" : "[name].js"
      ),
      publicPath: configurePublicPath(mode),
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
        {
          test: /\.s[ac]ss$/i,
          use: [
            mode === "development"
              ? "style-loader"
              : MiniCssExtractPlugin.loader,
            "css-loader",
            "sass-loader",
          ],
        },
      ],
    },
    plugins: configurePlugins(mode),
    resolve: {
      alias: {
        vue$:
          mode === "development"
            ? "vue/dist/vue.esm.js"
            : "vue/dist/vue.runtime.esm.js",
      },
      extensions: ["*", ".js", ".vue", ".json"],
    },
    optimization:
      mode === "development"
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
