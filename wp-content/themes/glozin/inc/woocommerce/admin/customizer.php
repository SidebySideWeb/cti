<?php
/**
 * WooCommerce Customizer functions and definitions.
 *
 * @package glozin
 */

namespace Glozin\WooCommerce\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The glozin WooCommerce Customizer class
 */
class Customizer {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'glozin_customize_panels', array( $this, 'get_customize_panels' ) );
		add_filter( 'glozin_customize_sections', array( $this, 'get_customize_sections' ) );
		add_filter( 'glozin_customize_settings', array( $this, 'get_customize_settings' ) );
	}

	/**
	 * Adds theme options panels of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $panels Theme options panels.
	 *
	 * @return array
	 */
	public function get_customize_panels( $panels ) {
		$panels['woocommerce'] = array(
			'priority' => 50,
			'title'    => esc_html__( 'Woocommerce', 'glozin' ),
		);

		$panels['shop'] = array(
			'priority' => 55,
			'title'    => esc_html__( 'Shop', 'glozin' ),
		);

		if( apply_filters('glozin_get_single_product_settings', true ) ) {
			$panels['single_product'] = array(
				'priority' => 60,
				'title'    => esc_html__( 'Single Product', 'glozin' ),
			);
		}

		$panels['vendors'] = array(
			'priority' => 60,
			'title'    => esc_html__( 'Vendors', 'glozin' ),
		);

		return $panels;
	}

	/**
	 * Adds theme options sections of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections Theme options sections.
	 *
	 * @return array
	 */
	public function get_customize_sections( $sections ) {
		// Typography
		$sections['typo_catalog'] = array(
			'title'    => esc_html__( 'Product Catalog', 'glozin' ),
			'panel'    => 'typography',
		);
		$sections['typo_product'] = array(
			'title'    => esc_html__( 'Single Product', 'glozin' ),
			'panel'    => 'typography',
		);

		// Cart Page
		$sections['woocommerce_cart'] = array(
			'title'    => esc_html__( 'Cart Page', 'glozin' ),
			'panel'    => 'woocommerce',
		);

		// Mini Cart
		$sections['mini_cart'] = array(
			'title'    => esc_html__( 'Mini Cart', 'glozin' ),
			'panel'    => 'woocommerce',
		);

		// Compare Page
		$sections['compare_page'] = array(
			'title'    => esc_html__( 'Compare Page', 'glozin' ),
			'panel'    => 'woocommerce',
		);

		if( apply_filters( 'glozin_shop_header_elementor', true ) ) {
			// Page Header
			$sections['shop_header'] = array(
				'title'    => esc_html__( 'Page Header', 'glozin' ),
				'panel'    => 'shop',
			);
		}

		if( apply_filters( 'glozin_taxonomy_description_elementor', true ) ) {
			// Taxonomy Description
			$sections['taxonomy_description'] = array(
				'title'    => esc_html__( 'Taxonomy Description', 'glozin' ),
				'panel'    => 'shop',
			);
		}

		if( apply_filters( 'glozin_top_categories_elementor', true ) ) {
			// Top Categories
			$sections['shop_top_categories'] = array(
				'title'    => esc_html__( 'Top Categories', 'glozin' ),
				'panel'    => 'shop',
			);
		}

		if( apply_filters( 'glozin_catalog_toolbar_elementor', true ) ) {
			// Catalog Toolbar
			$sections['shop_catalog_toolbar'] = array(
				'title'    => esc_html__( 'Catalog Toolbar', 'glozin' ),
				'panel'    => 'shop',
			);
		}

		if( apply_filters( 'glozin_product_catalog_elementor', true ) ) {
			// Product Catalog
			$sections['product_catalog'] = array(
				'title'    => esc_html__( 'Product Catalog', 'glozin' ),
				'panel'    => 'shop',
			);
		}

		$sections['product_grid_banner'] = array(
			'title'    => esc_html__( 'Product Grid Banner', 'glozin' ),
			'panel'    => 'shop',
		);

		// Product Card
		$sections['product_card'] = array(
			'title'    => esc_html__( 'Product Card', 'glozin' ),
			'panel'    => 'shop',
		);

		// Product Notifications
		$sections['product_notifications'] = array(
			'title'    => esc_html__( 'Product Notifications', 'glozin' ),
			'panel'    => 'shop',
		);

		// Badges
		$sections['badges'] = array(
			'title'    => esc_html__( 'Badges', 'glozin' ),
			'panel'    => 'shop',
		);

		$sections['product_gallery'] = array(
			'title'    => esc_html__( 'Product Gallery', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Single Product
		$sections['product'] = array(
			'title'    => esc_html__( 'Product Summary', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Single Badges
		$sections['product_badges'] = array(
			'title'    => esc_html__( 'Badges', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Shipping & Promotions Information
		$sections['product_shipping_promotions'] = array(
			'title'    => esc_html__( 'Shipping & Promotions Information', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Guarantee Safe Checkout
		$sections['product_guarantee_safe_checkout'] = array(
			'title'    => esc_html__( 'Guarantee Safe Checkout', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Product Highlights
		$sections['product_highlights'] = array(
			'title'    => esc_html__( 'Product Highlights', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Product tabs
		$sections['product_tabs'] = array(
			'title'    => esc_html__( 'Product Tabs', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Upsells Product
		$sections['upsells_products'] = array(
			'title'    => esc_html__( 'Up-Sells Products', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Related Product
		$sections['related_products'] = array(
			'title'    => esc_html__( 'Related Products', 'glozin' ),
			'panel'    => 'single_product',
		);

		// Recently Viewed Product
		$sections['recently_viewed_products'] = array(
			'title'    => esc_html__( 'Recently Viewed Products', 'glozin' ),
			'panel'    => 'single_product',
		);

		$sections['vendors_store_style'] = array(
			'title'    => esc_html__( 'Store Style', 'glozin' ),
			'panel'    => 'vendors',
		);

		return $sections;
	}

	/**
	 * Adds theme options of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Theme options fields.
	 *
	 * @return array
	 */
	public function get_customize_settings( $settings ) {
		// Product Compare Page
		if ( function_exists('wcboost_products_compare') ) {
			$columns = [
				'remove'      => esc_html__( 'Remove', 'glozin' ),
				'badge'       => esc_html__( 'Badge', 'glozin' ),
				'thumbnail'   => esc_html__( 'Thumbnail', 'glozin' ),
				'name'        => esc_html__( 'Name', 'glozin' ),
				'rating'      => esc_html__( 'Rating', 'glozin' ),
				'price'       => esc_html__( 'Price', 'glozin' ),
				'stock'       => esc_html__( 'Availability', 'glozin' ),
				'sku'         => esc_html__( 'SKU', 'glozin' ),
				'dimensions'  => esc_html__( 'Dimensions', 'glozin' ),
				'weight'      => esc_html__( 'Weight', 'glozin' ),
				'add-to-cart' => esc_html__( 'Add To Cart', 'glozin' ),
			];

			$columns = array_merge( $columns, $this->get_product_attributes() );
			if( isset( $columns[''] ) ) {
				unset($columns['']);
			}
			$settings['compare_page'] = array(
				'compare_page_columns'                => array(
					'type'     => 'multicheck',
					'label'    => esc_html__('Table Columns', 'glozin'),
					'default'  => array('remove', 'badge', 'thumbnail', 'name', 'rating', 'price', 'stock', 'sku', 'dimensions', 'weight', 'add-to-cart'),
					'choices'  => $columns,
				)
			);
		}

		// Typography - catalog.
		$settings['typo_catalog'] = array(
			'typo_catalog_page_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Page Header Title', 'glozin' ),
				'description' => esc_html__( 'Customize the font of page header title', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => '600',
					'font-size'      => '36px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '-1.224px',
				),
				'choices'   => \Glozin\Options::customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header--shop .page-header__title',
					),
				),
			),
			'typo_catalog_page_description'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Page Header Description', 'glozin' ),
				'description' => esc_html__( 'Customize the font of page header description', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => 'regular',
					'font-size'      => '15px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#444',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => \Glozin\Options::customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header--shop .page-header__description',
					),
				),
			),
			'typo_catalog_product_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Product Name', 'glozin' ),
				'description' => esc_html__( 'Customize the font of product name', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => '600',
					'font-size'      => '15px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => \Glozin\Options::customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'ul.products li.product h2.woocommerce-loop-product__title a',
					),
				),
			),
		);

		// Typography - product.
		$settings['typo_product'] = array(
			'typo_product_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Product Name', 'glozin' ),
				'description' => esc_html__( 'Customize the font of product name', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => '600',
					'font-size'      => '26px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '-0.884px',
				),
				'choices'   => \Glozin\Options::customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.single-product div.product .product-gallery-summary h1.product_title',
					),
				),
			),
		);

		// Product Catalog
		$settings['product_catalog'] = array(
			'product_filter_type' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Product Filter', 'glozin' ),
				'description'     => esc_html__( 'Go to appearance > widgets find to catalog filters sidebar to edit your sidebar', 'glozin' ),
				'default'         => 'no-filter',
				'choices'         => array(
					'content-sidebar' 	=> esc_html__( 'Right Sidebar', 'glozin' ),
					'sidebar-content' 	=> esc_html__( 'Left Sidebar', 'glozin' ),
					'horizontal'      	=> esc_html__( 'Horizontal', 'glozin' ),
					'popup'      		=> esc_html__( 'Popup', 'glozin' ),
					'no-filter'      	=> esc_html__( 'No Filter', 'glozin' ),
				),
				'priority'        => 10,
			),

			'product_catalog_hr'  => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_catalog_pagination' => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Pagination Type', 'glozin' ),
				'default' => 'numeric',
				'choices' => array(
					'numeric'  => esc_attr__( 'Numeric', 'glozin' ),
					'infinite' => esc_attr__( 'Infinite Scroll', 'glozin' ),
					'loadmore' => esc_attr__( 'Load More', 'glozin' ),
				),
				'priority'        => 40,
			),
			'product_catalog_pagination_ajax_url_change' => array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Change the URL after page loaded', 'glozin' ),
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'product_catalog_pagination',
						'operator' => '!=',
						'value'    => 'numeric',
					),
				),
				'priority'        => 50,
			),
		);

		// Product Banner
		$settings['product_grid_banner'] = array(
			'product_grid_banner' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Grid Banner', 'glozin' ),
				'description' => esc_html__( 'Enable this option to display the product banner on the first page of the product grid loop on the shop page', 'glozin' ),
				'default'     => false,
			),
			'category_product_grid_banner_fallback' => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Category Product Banner Fallback', 'glozin' ),
				'description' => esc_html__( 'Choose what happens if the category has no product banner.', 'glozin' ),
				'default'     => 'none',
				'choices'     => array(
					'none'  => esc_html__( 'Do not display banner', 'glozin' ),
					'shop'  => esc_html__( 'Show shop banner', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_grid_banner',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_grid_banner_hr'  => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'product_grid_banner',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_grid_banner_position'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Position', 'glozin' ),
				'default'         => '6',
				'active_callback' => array(
					array(
						'setting'  => 'product_grid_banner',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_grid_banner_image'       => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Image', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'product_grid_banner',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_grid_banner_link'       => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Link', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'product_grid_banner',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Product Card
		$settings['product_card'] = array(
			'image_rounded_shape_product_card'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Image Corner Radius', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'glozin' ),
					'round'  	=> esc_html__( 'Round', 'glozin' ),
					'custom'  	=> esc_html__( 'Custom', 'glozin' ),
				),
			),
			'image_rounded_number_product_card'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Number(px)', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape_product_card',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'product_card_images_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_layout' => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Product Layout', 'glozin' ),
				'default' => '1',
				'choices' => array(
					'1' => esc_html__( 'Layout v1', 'glozin' ),
					'2' => esc_html__( 'Layout v2', 'glozin' ),
				),
			),
			'product_card_hover' => array(
				'type'              => 'select',
				'label'             => esc_html__( 'Product Hover', 'glozin' ),
				'description'       => esc_html__( 'Product hover animation.', 'glozin' ),
				'default'           => '',
				'choices'           => array(
					''                 => esc_html__( 'Standard', 'glozin' ),
					'fadein'           => esc_html__( 'Fadein', 'glozin' ),
				),
				'priority'    => 10,
			),
			'product_card_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_quickadd' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Quick Add button', 'glozin' ),
				'description' => esc_html__( 'Disable this setting to return to the default button', 'glozin' ),
				'default'     => true,
			),
			'product_card_wishlist' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Wishlist button', 'glozin' ),
				'default' => true,
			),
			'product_card_wishlist_display'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Wishlist Display', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Show on Hover', 'glozin' ),
					'always'  	=> esc_html__( 'Always Show', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_card_wishlist',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_card_layout',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'product_card_compare' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Compare button', 'glozin' ),
				'default' => true,
			),
			'product_card_quick_view' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Quick view button', 'glozin' ),
				'default' => true,
			),
			'featured_button_rounded_shape_product_card'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Featured Button Corner Radius', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'glozin' ),
					'square'  	=> esc_html__( 'Square', 'glozin' ),
					'round'  	=> esc_html__( 'Round', 'glozin' ),
					'custom'  	=> esc_html__( 'Custom', 'glozin' ),
				),
			),
			'featured_button_rounded_number_product_card'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Number(px)', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'featured_button_rounded_shape_product_card',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'product_sale_coundown_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),

			'sale_display_type'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sale Display Type', 'glozin' ),
				'default'         => 'countdown',
				'choices'         => array(
					'' 			=> esc_html__( 'None', 'glozin' ),
					'countdown' => esc_html__( 'Countdown Timer', 'glozin' ),
					'marquee' 	=> esc_html__( 'Flash Sale Marquee', 'glozin' ),
				),
			),

			'sale_display_marquee_speed' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Speed', 'glozin' ),
				'description'     => esc_html__( 'Customize marquee speed (Example: 0.25)', 'glozin' ),
				'default'         => 0.1,
				'choices'  => [
					'min'  => 0,
					'max'  => 1,
					'step' => 0.1,
				],
				'active_callback' => array(
					array(
						'setting'  => 'sale_display_type',
						'operator' => '==',
						'value'    => 'marquee',
					),
				),
			),

			'product_card_taxonomy_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_taxonomy'               => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Product Taxonomy', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					''   => esc_html__( 'None', 'glozin' ),
					'product_cat'   => esc_html__( 'Category', 'glozin' ),
					'product_brand' => esc_html__( 'Brand', 'glozin' ),
				),
			),
			'product_card_rating_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_rating'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Show Rating', 'glozin' ),
				'default'     => true,
			),
			'product_card_empty_rating'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Hide Empty Rating', 'glozin' ),
				'default'     => false,
			),
			'product_card_title_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_title_heading_tag' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Product Title HTML Tag', 'glozin' ),
				'default'            => 'h2',
				'choices'            => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				),
			),
			'product_card_title_lines' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Product Title in', 'glozin' ),
				'default'            => '',
				'choices'            => array(
					''  => esc_html__( 'Default', 'glozin' ),
					'1' => esc_html__( '1 line', 'glozin' ),
					'2' => esc_html__( '2 lines', 'glozin' ),
					'3' => esc_html__( '3 lines', 'glozin' ),
					'4' => esc_html__( '4 lines', 'glozin' ),
				),
			),
			'product_card_summary_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'product_card_layout',
						'operator' => 'in',
						'value'    => array('1', '2'),
					),
				),
			),
			'product_card_summary' => array(
				'type'              => 'select',
				'label'             => esc_html__( 'Product Summary Alignment', 'glozin' ),
				'default'           => 'center',
				'choices'           => array(
					'flex-start' => esc_html__( 'Left', 'glozin' ),
					'center'     => esc_html__( 'Center', 'glozin' ),
					'flex-end'   => esc_html__( 'Right', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_card_layout',
						'operator' => 'in',
						'value'    => array('1', '2'),
					),
				),
			),
			'product_card_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_card_attribute' => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Primary Product Attribute', 'glozin' ),
				'default'     => 'none',
				'choices'     => $this->get_product_attributes(),
				'description' => esc_html__( 'Show primary product attribute in the product card', 'glozin' ),
			),
			'product_card_attribute_number' => array(
				'type'            => 'number',
				'description'     => esc_html__( 'Primary Product Attribute Number', 'glozin' ),
				'default'         => 4,
				'choices'  => array(
					'min'  => 1,
				),
			),
			'product_card_attribute_variation_swatches'                => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Variation Swatches Style', 'glozin' ),
				'default'     => 'default',
				'choices'     => array(
					'default'  => esc_html__( 'By the Theme', 'glozin' ),
					'swatches'  => esc_html__( 'By Variation Swatches plugin', 'glozin' ),
				),
			),
			'product_card_attribute_image_swap_hover' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable Image Swap on Hover', 'glozin' ),
				'default'     => 1,
			),
			'product_card_hr_30' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'product_list_short_description_line_limit' => array(
				'type'            => 'number',
				'label'           => esc_html__('Short Description Line Limit', 'glozin'),
				'description'     => esc_html__( 'Lines of short description in product list and catalog page', 'glozin' ),
				'responsive'      => true,
				'choices'     => [
					'min'  => 1,
					'max'  => 10,
					'step' => 1,
				],
				'default'    => [
					'desktop' => 3,
                    'tablet'  => 2,
                    'mobile'  => 3,
				],
			),
		);

		// WCFM
		if ( class_exists( 'WCFMmp' ) ) {
			$settings['product_card']['product_card_vendor_name_custom'] = array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'priority'    => 42,
			);
			$settings['product_card']['product_card_vendor_name'] = array(
				'type'            => 'select',
				'label'           => esc_html__( 'Vendor Name', 'glozin' ),
				'default'         => 'avatar',
				'choices'         => array(
					'none' => esc_html__( 'None', 'glozin' ),
					'avatar' => esc_html__( 'Avatar - Vendor Name', 'glozin' ),
					'text' => esc_html__( 'By - Vendor Name', 'glozin' ),
				),
				'priority'    => 42,
			);
			$settings['product_card']['product_card_vendor_position'] = array(
				'type'            => 'select',
				'label'           => esc_html__( 'Vendor Position', 'glozin' ),
				'default'         => 'after-price',
				'choices'         => array(
					'after-price' => esc_html__( 'After Price', 'glozin' ),
					'after-thumbnail' => esc_html__( 'After Thumbnail', 'glozin' ),
				),
				'priority'    => 42,
			);
			$settings['vendors_store_style']['vendor_store_style_theme'] = array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Enable Style From Theme', 'glozin' ),
				'description' => esc_html__( 'Enable the store list and store page style from theme.', 'glozin' ),
				'default' => true,
			);
		}

		// Vendor
		if ( class_exists( 'WeDevs_Dokan' ) ) {
			$settings['product_card'] = array_merge(
				$settings['product_card'],
				array(
					'product_card_vendor_custom'      => array(
						'type'     => 'custom',
						'default'  => '<hr/>',
					),
					'product_card_vendor_name'     => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Vendor Name', 'glozin' ),
						'default'         => 'avatar',
						'choices'         => array(
							'none' => esc_html__( 'None', 'glozin' ),
							'avatar' => esc_html__( 'Avatar - Vendor Name', 'glozin' ),
							'text' => esc_html__( 'By - Vendor Name', 'glozin' ),
						),
					),
					'product_card_vendor_position'     => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Vendor Position', 'glozin' ),
						'default'         => 'after-price',
						'choices'         => array(
							'after-price' => esc_html__( 'After Price', 'glozin' ),
							'after-thumbnail' => esc_html__( 'After Thumbnail', 'glozin' ),
						),
					),
				)
			);
		};

		// Product Notifications
		$settings['product_notifications'] = array(
			'added_to_cart_notice'                => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Added to Cart Notice', 'glozin' ),
				'description' => esc_html__( 'Display a notification when a product is added to cart.', 'glozin' ),
				'default'     => 'mini',
				'choices'     => array(
					'mini'  => esc_html__( 'Open mini cart', 'glozin' ),
					'none'  => esc_html__( 'None', 'glozin' ),
				),
			),
			'added_to_wishlist_custom'                 => array(
				'type'     => 'custom',
				'default'  => '<hr/>',
			),
			'added_to_wishlist_notice' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Added to Wishlist Notification', 'glozin' ),
				'description' => esc_html__( 'Display a notification when a product is added to wishlist', 'glozin' ),
				'section'     => 'product_notifications',
				'default'     => 0,
			),

			'wishlist_notice_auto_hide'   => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Wishlist Notification Auto Hide', 'glozin' ),
				'description'     => esc_html__( 'How many seconds you want to hide the notification.', 'glozin' ),
				'section'         => 'product_notifications',
				'active_callback' => array(
					array(
						'setting'  => 'added_to_wishlist_notice',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'default'         => 3,
			),
			'added_to_compare_custom'                 => array(
				'type'     => 'custom',
				'default'  => '<hr/>',
			),
			'added_to_compare_notice' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Added to Compare Notification', 'glozin' ),
				'description' => esc_html__( 'Display a notification when a product is added to compare', 'glozin' ),
				'section'     => 'product_notifications',
				'default'     => 0,
			),

			'compare_notice_auto_hide'   => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Compare Notification Auto Hide', 'glozin' ),
				'description'     => esc_html__( 'How many seconds you want to hide the notification.', 'glozin' ),
				'section'         => 'product_notifications',
				'active_callback' => array(
					array(
						'setting'  => 'added_to_compare_notice',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'default'         => 3,
			),
		);

		// Badges
		$settings['badges'] = array(
			'badges_sale'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Sale Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for sale products.', 'glozin' ),
				'default'     => true,
			),
			'badges_sale_type'     => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Type', 'glozin' ),
				'default'         => 'percent',
				'choices'         => array(
					'percent'        => esc_html__( 'Percentage', 'glozin' ),
					'text'           => esc_html__( 'Text', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
			),
			'badges_sale_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .onsale',
						'property' => 'background-color',
					),
				),
			),
			'badges_sale_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .onsale',
						'property' => 'color',
					),
				),
			),
			'badges_hr_2'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'badges_new'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'New Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for new products.', 'glozin' ),
				'default'     => true,
			),
			'badges_newness'       => array(
				'type'            => 'number',
				'description'     => esc_html__( 'Display the "New" badge for how many days?', 'glozin' ),
				'tooltip'         => esc_html__( 'You can also add the NEW badge to each product in the Advanced setting tab of them.', 'glozin' ),
				'default'         => 3,
				'active_callback' => array(
					array(
						'setting'  => 'badges_new',
						'operator' => '=',
						'value'    => true,
					),
				),
			),
			'badges_new_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_new',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .new',
						'property' => 'background-color',
					),
				),
			),
			'badges_new_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_new',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .new',
						'property' => 'color',
					),
				),
			),
			'badges_hr_3'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'badges_featured'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Featured Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for featured products.', 'glozin' ),
				'default'     => true,
			),
			'badges_featured_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_featured',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .featured',
						'property' => 'background-color',
					),
				),
			),
			'badges_featured_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_featured',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .featured',
						'property' => 'color',
					),
				),
			),
			'badges_hr_4'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'badges_soldout'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Sold Out Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for out of stock products.', 'glozin' ),
				'default'     => true,
			),
			'badges_soldout_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_soldout',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .sold-out, .woocommerce-badges.woocommerce-badges.sold-out--center.sold-out',
						'property' => 'background-color',
					),
				),
			),
			'badges_soldout_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_soldout',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .sold-out',
						'property' => 'color',
					),
				),
			),
			'badges_hr_5'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'badges_preorder'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Preorder Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for preorder products.', 'glozin' ),
				'default'     => true,
			),
			'badges_preorder_bg'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_preorder',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .pre-order',
						'property' => 'background-color',
					),
				),
			),
			'badges_preorder_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'badges_preorder',
						'operator' => '=',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .pre-order',
						'property' => 'color',
					),
				),
			),
			'badges_custom_badge'       => array(
				'type'    => 'custom',
				'default' => '<hr/><h3>' . esc_html__( 'Custom Badge', 'glozin' ) . '</h3>',
			),

			'badges_custom_bg'    => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background', 'glozin' ),
				'default'         => '',
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .custom',
						'property' => 'background-color',
					),
				),
			),

			'badges_custom_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'glozin' ),
				'default'         => '',
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.woocommerce-badges .custom ',
						'property' => 'color',
					),
				),
			),

		);

		// Page Header.
		$settings['shop_header'] = array(
			'shop_header' => array(
				'type'        => 'toggle',
				'default'     => true,
				'label'       => esc_html__('Enable Page Header', 'glozin'),
				'description' => esc_html__('Enable to show a shop header for the shop below the site header', 'glozin'),
			),
			'shop_header_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'shop_header_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Elements', 'glozin'),
				'default'  => array( 'title', 'breadcrumb', 'description' ),
				'choices'  => array(
					'title'      => esc_html__('Title', 'glozin'),
					'breadcrumb' => esc_html__('BreadCrumb', 'glozin'),
					'description' => esc_html__('Description', 'glozin'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'glozin'),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'shop_header_number_lines'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Description Number Lines', 'glozin'),
				'default'         => 5,
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'description',
					),
				),
			),
			'shop_header_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/><h3>' . esc_html__('Custom', 'glozin') . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'shop_header_background_image'          => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Background Image', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'shop_header_background_overlay' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Overlay', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--shop::before',
						'property' => 'background-color',
					),
				),
			),
			'shop_header_title_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Title Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--shop .page-header__title',
						'property' => 'color',
					),
				),
			),
			'shop_header_breadcrumb_link_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Link Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header--shop .site-breadcrumb',
						'property' => '--gz-site-breadcrumb-link-color',
					),
				),
			),
			'shop_header_breadcrumb_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header--shop .site-breadcrumb',
						'property' => '--gz-site-breadcrumb-color',
					),
				),
			),
			'shop_header_description_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Description Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'shop_header_els',
						'operator' => 'in',
						'value'    => 'description',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--shop .page-header__description',
						'property' => 'color',
					),
				),
			),
			'shop_header_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Top', 'glozin'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 80,
                    'tablet'  => 80,
                    'mobile'  => 60,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'output'         => array(
					array(
						'element'  => '.page-header.page-header--shop',
						'property' => 'padding-top',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'shop_header_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Bottom', 'glozin'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 10,
                    'tablet'  => 10,
                    'mobile'  => 10,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'output'         => array(
					array(
						'element'  => '.page-header.page-header--shop',
						'property' => 'padding-bottom',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'shop_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		);

		// Top Categories.
		$settings['shop_top_categories'] = array(
			'top_categories'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Top Categories', 'glozin' ),
				'default' => false,
			),
			'show_brand_page'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Show on Brand Page', 'glozin' ),
				'default' => true,
			),
			'top_categories_limit' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Limit', 'glozin' ),
				'description'     => esc_html__( 'Enter 0 to get all categories. Enter a number to get limit number of top categories.', 'glozin' ),
				'default'         => 0,
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'top_categories_order' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Order By', 'glozin' ),
				'default'         => 'order',
				'choices'         => array(
					'order' => esc_html__( 'Category Order', 'glozin' ),
					'name'  => esc_html__( 'Category Name', 'glozin' ),
					'id'    => esc_html__( 'Category ID', 'glozin' ),
					'count' => esc_html__( 'Product Counts', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'top_categories_columns' => array(
				'type'      => 'number',
				'label'     => esc_html__('Columns', 'glozin'),
				'default'    => [
					'desktop' => 6,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 1,
					'max' => 10,
				),
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'top_categories_title_html_tag' => array(
				'type'      => 'select',
				'label'     => esc_html__('Title HTML Tag', 'glozin'),
				'default'    => 'div',
				'choices'   => array(
					'div' => esc_html__( 'Div', 'glozin' ),
					'h2'  => esc_html__( 'H2', 'glozin' ),
					'h3'  => esc_html__( 'H3', 'glozin' ),
					'h4'  => esc_html__( 'H4', 'glozin' ),
					'h5'  => esc_html__( 'H5', 'glozin' ),
					'h6'  => esc_html__( 'H6', 'glozin' ),
					'span'  => esc_html__( 'Span', 'glozin' ),
					'p'  => esc_html__( 'P', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'top_categories',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Catalog toolbar.
		$settings['taxonomy_description'] = array(
			'taxonomy_description_enable'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Taxonomy Description Below the Products', 'glozin' ),
				'description' => esc_html__('Enable this option to show the taxonomy description below the products', 'glozin'),
				'default' => false,
			),
			'taxonomy_description_html'               => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Taxonomy Description HTML', 'glozin' ),
				'description' => esc_html__('Enable this option to allow HTML in the Taxonomy Description', 'glozin'),
				'default' => false,
				'active_callback' => array(
					array(
						'setting'  => 'taxonomy_description_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'taxonomy_description_number_lines'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Description Number Lines', 'glozin'),
				'default'         => 5,
				'active_callback' => array(
					array(
						'setting'  => 'taxonomy_description_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'taxonomy_description_alignment'      => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Description Alignment', 'glozin' ),
				'default' => 'left',
				'section' => 'taxonomy_description',
				'choices' => array(
					'left' => esc_html__( 'Left', 'glozin' ),
					'center' => esc_html__( 'Center', 'glozin' ),
					'right' => esc_html__( 'Right', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'taxonomy_description_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Catalog toolbar.
		$settings['shop_catalog_toolbar'] = array(
			'catalog_toolbar'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Catalog Toolbar', 'glozin' ),
				'default' => true,
			),
			'catalog_toolbar_list_hr'  => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
			),
			'catalog_toolbar_els'         => array(
				'type'            => 'multicheck',
				'label'           => esc_html__( 'Elements', 'glozin' ),
				'default'         => array( 'total', 'sortby', 'view' ),
				'choices'         => array(
					'total'    => esc_html__( 'Total Products', 'glozin' ),
					'sortby'    => esc_html__( 'Sort By', 'glozin' ),
					'view'  	=> esc_html__( 'View', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_toolbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'catalog_toolbar_views' => array(
				'type'               => 'multicheck',
				'label'              => esc_html__( 'View', 'glozin' ),
				'default'            => array( '1', '2', '3', '4' ),
				'choices'            => array(
					'2'       => esc_html__( 'Grid 2 Columns', 'glozin' ),
					'3'       => esc_html__( 'Grid 3 Columns', 'glozin' ),
					'4'       => esc_html__( 'Grid 4 Columns', 'glozin' ),
					'1'       => esc_html__( 'List', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'catalog_toolbar_els',
						'operator' => 'in',
						'value'    => 'view',
					),
				),
			),
			'catalog_toolbar_default_view' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Default View', 'glozin' ),
				'default'            => 'grid',
				'choices'            => array(
					'list'       => esc_html__( 'List', 'glozin' ),
					'grid'       => esc_html__( 'Grid', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_toolbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'catalog_toolbar_els',
						'operator' => 'in',
						'value'    => 'view',
					),
				),
			),
		);

		// Single Product
		$settings['product'] = array(
			'product_taxonomy'               => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Product Taxonomy', 'glozin' ),
				'default'         => 'product_cat',
				'choices'         => array(
					'product_cat'   => esc_html__( 'Category', 'glozin' ),
					''              => esc_html__( 'None', 'glozin' ),
					'product_brand' => esc_html__( 'Brand', 'glozin' ),
				),
				'description' => esc_html__( 'Show a product taxonomy above the product title on the product page.', 'glozin' ),
			),
			'product_description_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_description'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Description', 'glozin' ),
				'default'     => false,
			),
			'product_description_lines'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Product Description Lines', 'glozin' ),
				'default'         => 4,
				'active_callback' => array(
					array(
						'setting'  => 'product_description',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'product_countdown_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_countdown_layout'               => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Product Countdown Layout', 'glozin' ),
				'default'         => 'v1',
				'choices'         => array(
					'v1' => esc_html__( 'Layout v1', 'glozin' ),
					'v2' => esc_html__( 'Layout v2', 'glozin' ),
				),
			),
			'product_share_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_share'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Share', 'glozin' ),
				'default'     => false,
			),
			'product_ask_question'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Ask a Question', 'glozin' ),
				'default'     => false,
			),
			'product_ask_question_form'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Contact Form', 'glozin'),
				'description' => esc_html__('Please enter the contact form shortcode', 'glozin'),
				'default'     => '',
				'input_attrs' => array(
					'placeholder' => '[contact-form-7 id="11" title="Contact form 1"]',
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_ask_question',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_meta_heading' => array(
				'type'    => 'custom',
				'label'   => '<h3>' . esc_html__( 'Product Meta', 'glozin' ) . '</h3>',
			),
			'product_sku' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product SKU', 'glozin' ),
				'default' => true,
			),
			'product_stock' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product Stock', 'glozin' ),
				'default' => true,
			),
			'product_categtories' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product Categories', 'glozin' ),
				'default' => true,
			),
			'product_brands' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product Brands', 'glozin' ),
				'default' => false,
			),
			'product_tags' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Product Tags', 'glozin' ),
				'default' => true,
			),
			'product_countdown_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_countdown_layout'               => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Product Countdown Layout', 'glozin' ),
				'default'         => 'v1',
				'choices'         => array(
					'v1' => esc_html__( 'Layout v1', 'glozin' ),
					'v2' => esc_html__( 'Layout v2', 'glozin' ),
				),
			),
			'product_clickable_outofstock_variations_hr' => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_clickable_outofstock_variations' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Clickable Out of Stock Variations', 'glozin' ),
				'default'     => false,
			),
		);

		// Vendor
		if ( class_exists( 'WeDevs_Dokan' ) ) {
			$settings['product'] = array_merge(
				$settings['product'],
				array(
					'single_product_vendor_name_custom' => array(
						'type'        => 'custom',
						'default'  => '<hr/>',
						'priority' => 95,
					),
					'single_product_vendor_name'     => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Vendor Name', 'glozin' ),
						'default'         => 'avatar',
						'choices'         => array(
							'none' => esc_html__( 'None', 'glozin' ),
							'avatar' => esc_html__( 'Avatar - Vendor Name', 'glozin' ),
							'text' => esc_html__( 'By - Vendor Name', 'glozin' ),
						),
					),
				)
			);
		};

		$settings['product_gallery'] = array(
			'product_gallery_layout' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Layout', 'glozin' ),
				'default'            => '',
				'choices'            => array(
					''                  => esc_html__( 'Left thumbnails', 'glozin' ),
					'bottom-thumbnails' => esc_html__( 'Bottom thumbnails', 'glozin' ),
					'grid-1'            => esc_html__( 'Grid 1', 'glozin' ),
					'grid-2'            => esc_html__( 'Grid 2', 'glozin' ),
					'stacked'           => esc_html__( 'Stacked', 'glozin' ),
					'hidden-thumbnails' => esc_html__( 'Hidden thumbnails', 'glozin' ),
				),
			),
			'product_image_zoom' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Zoom', 'glozin' ),
				'default'            => 'bounding',
				'choices'            => array(
					'none'  	=> esc_html__( 'None', 'glozin' ),
					'bounding'  => esc_html__( 'External zoom', 'glozin' ),
					'inner'     => esc_html__( 'Inner zoom square', 'glozin' ),
					'magnifier' => esc_html__( 'Inner zoom circle', 'glozin' ),
				),
				'description' => esc_html__( 'Zooms in where your cursor is on the image', 'glozin' ),
			),
			'product_image_lightbox_hr'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_image_lightbox'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Lightbox', 'glozin' ),
				'description' => esc_html__( 'Opens your images against a dark backdrop', 'glozin' ),
				'default'     => true,
			),
		);

		// Single Badges
		$settings['product_badges'] = array(
			'product_badges_sale'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Sale Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for sale products.', 'glozin' ),
				'default'     => true,
			),
			'product_badges_sale_type'     => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Type', 'glozin' ),
				'default'         => 'percent',
				'choices'         => array(
					'percent'        => esc_html__( 'Percentage', 'glozin' ),
					'text'           => esc_html__( 'Text', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_badges_sale',
						'operator' => '=',
						'value'    => true,
					),
				),
			),
			'product_badges_hr_2'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_badges_new'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'New Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for new product.', 'glozin' ),
				'default'     => false,
			),
			'product_badges_hr_3'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_badges_featured'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Featured Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for featured product.', 'glozin' ),
				'default'     => false,
			),
			'product_badges_hr_4'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_badges_stock'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Stock Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for stock product.', 'glozin' ),
				'default'     => true,
			),
			'product_badges_hr_5'          => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'product_badges_preorder'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Preorder Badge', 'glozin' ),
				'description' => esc_html__( 'Display a badge for preorder product.', 'glozin' ),
				'default'     => true,
			),
		);

		// Guarantee Safe Checkout
		$settings['product_guarantee_safe_checkout'] = array(
			'product_guarantee_safe_checkout'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Guarantee Safe Checkout', 'glozin' ),
				'description'		=> esc_html__( 'Enable this option to show this section below the product meta', 'glozin' ),
				'default'     => false,
			),
			'product_guarantee_safe_checkout_html' => array(
				'type'        => 'textarea',
				'label'       => esc_html__( 'Insert HTML', 'glozin' ),
				'default'     => '',
				'active_callback' => array(
					array(
						'setting'  => 'product_guarantee_safe_checkout',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Shipping & Promotions Information
		$settings['product_shipping_promotions'] = array(
			'product_shipping_promotions'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Shipping & Promotions Information', 'glozin' ),
				'description'		=> esc_html__( 'Enable this option to show this section(like delivery times, discount codes...) below the product description', 'glozin' ),
				'default'     => false,
			),
			'product_shipping_promotions_position' => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Position', 'glozin' ),
				'default'     => 'description',
				'choices'     => array(
					'description' => esc_html__( 'Below the description', 'glozin' ),
					'add_to_cart' => esc_html__( 'Below the add to cart', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_shipping_promotions',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_shipping_promotions_type' => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Type', 'glozin' ),
				'default'     => 'list',
				'choices'     => array(
					'list' => esc_html__( 'List', 'glozin' ),
					'grid' => esc_html__( 'Grid', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_shipping_promotions',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_shipping_promotions_hr' => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'product_shipping_promotions',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_shipping_promotions_list' => array(
				'type'        => 'repeater',
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
				),
				'default'  => [],
				'fields'      => array(
					'image' => array(
						'type'        => 'image',
						'label'       => esc_html__( 'Image', 'glozin' ),
					),
					'description' => array(
						'type'        => 'textarea',
						'label'       => esc_html__( 'Description', 'glozin' ),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_shipping_promotions',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Product Highlights
		$settings['product_highlights'] = array(
			'product_highlights'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Product Highlights', 'glozin' ),
				'description'		=> esc_html__( 'Enable this option to show this section(like free returns and delivery options) below the product gallery', 'glozin' ),
				'default'     => false,
			),
			'product_highlights_image' => array(
				'type'        => 'image',
				'label'       => esc_html__( 'Image', 'glozin' ),
				'active_callback' => array(
					array(
						'setting'  => 'product_highlights',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_highlights_image_dimension' => array(
				'type'            => 'dimensions',
				'label'           => esc_html__( 'Image Dimension', 'glozin' ),
				'default'         => array(
					'width'  => '12',
					'height' => '12',
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_highlights',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'product_highlights_image',
						'operator' => '!=',
						'value'    => '',
					),
				),
			),
			'product_highlights_list' => array(
				'type'        => 'repeater',
				'label'    => esc_html__( 'Items', 'glozin' ),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
				),
				'fields'      => array(
					'text' => array(
						'type'        => 'textarea',
						'label'       => esc_html__( 'Text', 'glozin' ),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_highlights',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_highlights_speed' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Speed', 'glozin' ),
				'description'     => esc_html__( 'Customize marquee speed (Example: 0.25)', 'glozin' ),
				'default'         => 0.25,
				'choices'  => [
					'min'  => 0,
					'max'  => 1,
					'step' => 0.1,
				],
				'active_callback' => array(
					array(
						'setting'  => 'product_highlights',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Product tabs
		$settings['product_tabs'] = array(
			'product_tabs_layout' => array(
				'type'               => 'select',
				'label'              => esc_html__( 'Layout', 'glozin' ),
				'default'            => '',
				'choices'            => array(
					''          => esc_html__( 'Tabs', 'glozin' ),
					'accordion' => esc_html__( 'Accordion', 'glozin' ),
				),
			),
			'product_tabs_status' => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Product Tabs Status', 'glozin' ),
				'default' => 'close',
				'choices' => array(
					'close' => esc_html__( 'Close all tabs', 'glozin' ),
					'first' => esc_html__( 'Open first tab', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_tabs_layout',
						'operator' => '==',
						'value'    => 'accordion',
					),
				),
			),
			'product_tabs_position' => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Product Tabs Position', 'glozin' ),
				'default' => '',
				'choices' => array(
					''              => esc_html__( 'Default', 'glozin' ),
					'under-summary' => esc_html__( 'Under Summary', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'product_tabs_layout',
						'operator' => '==',
						'value'    => 'accordion',
					),
				),
			),
		);

		$settings['upsells_products'] = array(
			'upsells_products'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Upsells Products', 'glozin' ),
				'default'     => true,
			),
			'upsells_products_numbers' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Numbers', 'glozin' ),
				'default'         => 10,
				'active_callback' => array(
					array(
						'setting'  => 'upsells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'upsells_products_columns' => array(
				'type'        	=> 'number',
				'label' 		=> esc_html__('Columns', 'glozin'),
				'default'     	=> [
					'desktop' => 4,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive'  => true,
				'choices'     => array(
					'min' => 1,
					'max' => 6,
				),
				'active_callback' => array(
					array(
						'setting'  => 'upsells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'upsells_products_description'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Description', 'glozin'),
				'description' => esc_html__('Please enter the description', 'glozin'),
				'default'     => '',
				'active_callback' => array(
					array(
						'setting'  => 'upsells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['related_products']= array(
			'related_products'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Related Products', 'glozin' ),
				'default'     => true,
			),
			'related_products_by_cats'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'By Categories', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'related_products_by_tags'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'By Tags', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'related_products_numbers' => array(
				'type'        	 => 'number',
				'label' 	 => esc_html__( 'Numbers', 'glozin' ),
				'default'     	 => 10,
				'choices'     	 => array(
					'min' => 1,
				),
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'related_products_columns' => array(
				'type'        	=> 'number',
				'label' 		=> esc_html__('Columns', 'glozin'),
				'default'     	=> [
					'desktop' => 4,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive'  => true,
				'choices'     => array(
					'min' => 1,
					'max' => 6,
				),
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'related_products_description'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Description', 'glozin'),
				'description' => esc_html__('Please enter the description', 'glozin'),
				'default'     => '',
				'active_callback' => array(
					array(
						'setting'  => 'related_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['recently_viewed_products']= array(
			'recently_viewed_products'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Recently Viewed Products', 'glozin' ),
				'default'     => true,
			),
			'recently_viewed_products_ajax' => array(
				'type'    => 'toggle',
				'label'   => esc_html__('Load With Ajax', 'glozin'),
				'default' => false,
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'recently_viewed_products_numbers' => array(
				'type'           => 'number',
				'description'    => esc_html__( 'Numbers', 'glozin' ),
				'default'        => 10,
				'choices'     	 => array(
					'min' => 1,
				),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'recently_viewed_products_columns' => array(
				'type'        	=> 'number',
				'label' 		=> esc_html__('Columns', 'glozin'),
				'default'     	=> [
					'desktop' => 4,
					'tablet'  => 3,
					'mobile'  => 2,
				],
				'responsive'  => true,
				'choices'     => array(
					'min' => 1,
					'max' => 6,
				),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'recently_viewed_products_description'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Description', 'glozin'),
				'description' => esc_html__('Please enter the description', 'glozin'),
				'default'     => '',
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['wcboost_variation_swatches'] = array(
			'wcboost_variation_swatches_label_hr' => array(
				'type' => 'custom',
				'default' => '<hr>',
				'priority' => 50,
			),
			'wcboost_variation_swatches_label_shape' => array(
				'type' => 'select',
				'label' => esc_html__( 'Label Swatches Shape', 'glozin' ),
				'default' => '',
				'choices' => array(
					'' => esc_html__( 'Default', 'glozin' ),
					'round'   => esc_html__( 'Circle', 'glozin' ),
					'rounded' => esc_html__( 'Rounded corners', 'glozin' ),
					'square'  => esc_html__( 'Square', 'glozin' ),
				),
				'priority' => 55,
			),
		);

		$settings['woocommerce_cart'] = array(
			'update_cart_page_auto'       => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Update Cart Automatically', 'glozin' ),
				'description' => esc_html__( 'Check this option to update cart page automatically', 'glozin' ),
				'default'     => 0,
			),
			'product_hr_1'                => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'cross_sells_products'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Cross-Sells Products', 'glozin' ),
				'default'     => true,
			),
			'cross_sells_empty_type' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Cross-Sells Empty Products', 'glozin' ),
				'description'     => esc_html__( 'If cross-sells are empty, the display style for the product will be selected.', 'glozin' ),
				'default'         => 'recent_products',
				'choices'         => array(
					'recent_products' 			=> esc_html__( 'Recent Products', 'glozin' ),
					'top_rated_products' 		=> esc_html__( 'Top Rated Products', 'glozin' ),
					'sale_products'      		=> esc_html__( 'Sale Products', 'glozin' ),
					'featured_products'      	=> esc_html__( 'Featured Products', 'glozin' ),
				),
			),
			'cross_sells_products_numbers' => array(
				'type'            => 'number',
				'label'     	  => esc_html__( 'Numbers', 'glozin' ),
				'default'         => 4,
				'active_callback' => array(
					array(
						'setting'  => 'cross_sells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'cross_sells_products_columns' => array(
				'type'        	=> 'number',
				'label' 		=> esc_html__('Columns', 'glozin'),
				'default'     	=> [
					'desktop' => 2,
					'tablet'  => 2,
					'mobile'  => 1,
				],
				'responsive'  => true,
				'choices'     => array(
					'min' => 1,
					'max' => 6,
				),
				'active_callback' => array(
					array(
						'setting'  => 'cross_sells_products',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'product_hr_2'                => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'cart_service_highlight'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Service Highlight', 'glozin' ),
				'description' => esc_html__( 'Check this option to display the service highlight below the cart content in the cart page', 'glozin' ),
				'default'     => false,
				'priority' => 30,
			),
			'cart_service_highlight_content'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Service Highlight Content', 'glozin' ),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'text',
				),
				'fields'          => array(
					'icon' => array(
						'type'    => 'textarea',
						'label'   => esc_html__( 'Icon', 'glozin' ),
						'sanitize_callback' => '\Glozin\Icon::sanitize_svg',
					),
					'title'          => array(
						'type'    => 'text',
						'label'   => esc_html__( 'Title', 'glozin' ),
					),
					'description'          => array(
						'type'    => 'textarea',
						'label'   => esc_html__( 'Description', 'glozin' ),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'cart_service_highlight',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 35,
			),
			'product_hr_3'                => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'priority' => 40,
			),
			'cart_information_box'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Order Information Box', 'glozin' ),
				'description' => esc_html__( 'Check this option to display the order information box below the cart totals in the cart page', 'glozin' ),
				'default'     => false,
				'priority' => 45,
			),
			'cart_information_box_content' => array(
				'type' => 'textarea',
				'label' => esc_html__('Order Information Box Content', 'glozin'),
				'default' => '',
				'active_callback' => array(
					array(
						'setting'  => 'cart_information_box',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 50,
			),
		);

		$settings['woocommerce_checkout'] = array(
			'checkout_information_box'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Order Information Box', 'glozin' ),
				'description' => esc_html__( 'Check this option to display the order information box below the cart totals in the checkout page', 'glozin' ),
				'default'     => false,
			),
			'checkout_information_box_content' => array(
				'type' => 'textarea',
				'label' => esc_html__('Order Information Box Content', 'glozin'),
				'default' => '',
				'active_callback' => array(
					array(
						'setting'  => 'checkout_information_box',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		return $settings;
	}

	/**
	* Get product attributes
	*
	* @return string
	*/
	public function get_product_attributes() {
		$output = array();
		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			$attributes_tax = wc_get_attribute_taxonomies();
			if ( $attributes_tax ) {
				$output[''] = esc_html__( 'None', 'glozin' );

				foreach ( $attributes_tax as $attribute ) {
					$output[$attribute->attribute_name] = $attribute->attribute_label;
				}

			}
		}

		return $output;
	}
}
