const shell = require('shelljs');
const path = require('path');

// Danh sách folder cần quét
const folders = [
    'assets/js/elementor',
    'modules/add-to-cart-ajax/assets',
    'modules/advanced-search/assets',
    'modules/buy-now/assets',
    'modules/checkout-limit/assets',
    'modules/customer-reviews/assets',
    'modules/free-shipping-bar/assets',
    'modules/linked-variant/assets',
    'modules/live-sales-notification/assets',
    'modules/people-view-fake/assets',
    'modules/popup/assets',
    'modules/pre-order/assets',
    'modules/product-3d-viewer/assets',
    'modules/product-360/assets',
    'modules/product-bought-together/assets',
    'modules/product-video/assets',
    'modules/products-filter/assets',
    'modules/products-stock-progress-bar/assets',
    'modules/sticky-add-to-cart/assets',
    'modules/variation-compare/assets',
    'modules/variation-images/assets',
    
  ];
  
  const files = folders.flatMap(folder =>
    shell.ls(`${folder}/*.js`).filter(file => !file.endsWith('.min.js'))
  );

files.forEach(file => {
  const minifiedFile = file.replace(/\.js$/, '.min.js');
  const result = shell.exec(`npx uglifyjs "${file}" -m -o "${minifiedFile}"`);

  if (result.code !== 0) {
    console.error(`❌ Failed to minify ${file}`);
    process.exit(1);
  } else {
    console.log(`✅ Minified: ${file} → ${minifiedFile}`);
  }
});
