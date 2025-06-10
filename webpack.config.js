const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
module.exports = {
  mode: 'development',
  entry: {
    'js/app' : './src/js/app.js',
    'js/inicio' : './src/js/inicio.js',
    'js/registro/index' : './src/js/registro/index.js',
    'js/login/index' : './src/js/login/index.js',
    'js/aplicaciones/index' : './src/js/aplicaciones/index.js',
    'js/permisos/index' : './src/js/permisos/index.js',
    'js/clientes/index' : './src/js/clientes/index.js',
    'js/marcas/index' : './src/js/marcas/index.js',
    'js/inventario/index' : './src/js/inventario/index.js',
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'public/build')
  },
  plugins: [
    new MiniCssExtractPlugin({
        filename: 'styles.css'
    })
  ],
  module: {
    rules: [
      {
        test: /\.(c|sc|sa)ss$/,
        use: [
            {
                loader: MiniCssExtractPlugin.loader
            },
            'css-loader',
            'sass-loader'
        ]
      },
      {
        test: /\.(png|svg|jpe?g|gif)$/,
        type: 'asset/resource',
      },
    ]
  }
};