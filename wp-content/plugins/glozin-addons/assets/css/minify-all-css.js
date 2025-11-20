const shell = require('shelljs');
const path = require('path');

const folders = [
    'assets/css/elementor',
    'modules/checkout-limit/assets',
    'modules/customer-reviews/assets',
    'modules/free-shipping-bar/assets',
    'modules/linked-variant/assets',
    'modules/live-sales-notification/assets',
    'modules/model-sizing/assets',
    'modules/multi-color-swatches/assets',
    'modules/people-view-fake/assets',
    'modules/popup/assets',
    'modules/pre-order/assets',
    'modules/product-3d-viewer/assets',
    'modules/product-360/assets',
    'modules/product-bought-together/assets',
    'modules/product-video/assets',
    'modules/products-stock-progress-bar/assets',
    'modules/sticky-add-to-cart/assets',
    'modules/variation-compare/assets',

  ];

  const cssFiles = folders.flatMap(folder =>
    shell.ls(`${folder}/*.css`).filter(file => !file.endsWith('.min.css'))
  );

cssFiles.forEach(file => {
  const minFile = file.replace(/\.css$/, '.min.css');
  const result = shell.exec(`npx cleancss -o "${minFile}" "${file}"`);

  if (result.code !== 0) {
    console.error(`❌ Failed to minify ${file}`);
    process.exit(1);
  } else {
    console.log(`✅ Minified: ${file} → ${minFile}`);
  }
});
