<?php
/**
 * Hooks for importer
 *
 * @package Glozin
 */

namespace Glozin\Addons;


/**
 * Class Importter
 */
class Importer {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
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
		add_filter( 'glozin_demo_packages', array( $this, 'importer' ), 20 );
		add_action( 'glozin_before_import_content', array( $this,'import_product_attributes') );
		add_action( 'glozin_before_import_content', array( $this,'enable_svg_upload') );
		add_action( 'glozin_after_setup_pages', array( $this,'disable_svg_upload') );
		add_action('glozin_after_setup_pages', array( $this,'update_page_option') );

		add_filter('glozin_before_select_demo_page', array( $this, 'check_elementor_container_grid' ));
	}


	/**
	 * Importer the demo content
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function importer() {
		return array(
			array(
				'name'       => 'Home - Main Demo',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-main-demo/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-main-demo/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-main-demo/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-main-demo/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Main Demo',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 780,
						'height' => 1040,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 533,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 173,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Chic Boutique',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-chic-boutique/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-chic-boutique/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-chic-boutique/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-chic-boutique/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Chic Boutique',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 780,
						'height' => 1040,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 533,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 173,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - EchoZone',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-echozone/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-echozone/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-echozone/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-echozone/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - EchoZone',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => '1:1',
					'woocommerce_thumbnail_cropping_custom_width' => 1,
					'woocommerce_thumbnail_cropping_custom_height' => 1,
					'shop_catalog_image_size'   => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 130,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Modern Wardrobe',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-modern-wardrobe/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-modern-wardrobe/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-modern-wardrobe/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-modern-wardrobe/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Modern Wardrobe',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 780,
						'height' => 1040,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 533,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 173,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Urban Living',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-urban-living/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-urban-living/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-urban-living/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-urban-living/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Urban Living',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => '1:1',
					'woocommerce_thumbnail_cropping_custom_width' => 1,
					'woocommerce_thumbnail_cropping_custom_height' => 1,
					'shop_catalog_image_size'   => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 130,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Single Product',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-single-product/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-single-product/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-single-product/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-single-product/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Single Product',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => '1:1',
					'woocommerce_thumbnail_cropping_custom_width' => 1,
					'woocommerce_thumbnail_cropping_custom_height' => 1,
					'shop_catalog_image_size'   => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 130,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Cosmetic',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-cosmetic/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-cosmetic/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-cosmetic/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-cosmetic/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Cosmetic',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 337,
					'woocommerce_thumbnail_cropping_custom_height' => 450,
					'shop_catalog_image_size'   => array(
						'width'  => 600,
						'height' => 798,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 534,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 173,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Activewear',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-activewear/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-activewear/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-activewear/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-activewear/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Activewear',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 600,
						'height' => 800,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 533,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 173,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Decor',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-decor/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-decor/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-decor/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-decor/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Decor',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 600,
						'height' => 762,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 509,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 165,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Elegant Style',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-elegant-style/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-elegant-style/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-elegant-style/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-elegant-style/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Elegant Style',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 780,
						'height' => 1040,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 533,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 173,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Gleam Luxe',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-gleam-luxe/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-gleam-luxe/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-gleam-luxe/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-gleam-luxe/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Gleam Luxe',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 700,
						'height' => 937,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 536,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Jewelry',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-jewelry/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-jewelry/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-jewelry/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-jewelry/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Jewelry',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => '1:1',
					'woocommerce_thumbnail_cropping_custom_width' => 1,
					'woocommerce_thumbnail_cropping_custom_height' => 1,
					'shop_catalog_image_size'   => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 400,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 130,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Tiny Outfits',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-tiny-outfits/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-tiny-outfits/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-tiny-outfits/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-tiny-outfits/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Tiny Outfits',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => '1:1',
					'woocommerce_thumbnail_cropping_custom_width' => 1,
					'woocommerce_thumbnail_cropping_custom_height' => 1,
					'shop_catalog_image_size'   => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 400,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 130,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Healthy Haven',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-healthy-haven/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-healthy-haven/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-healthy-haven/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-healthy-haven/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Organic',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 700,
						'height' => 937,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 536,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Paw Paradise',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-paw-paradise/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-paw-paradise/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-paw-paradise/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-paw-paradise/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Paw Paradise',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 700,
						'height' => 937,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 536,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Echelon Watches',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-echelon-watches/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-echelon-watches/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-echelon-watches/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-echelon-watches/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Echelon Watches',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 700,
						'height' => 937,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 400,
						'height' => 536,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Wigs',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-wigs/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-wigs/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-wigs/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-wigs/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Wigs',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 338,
						'height' => 449,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 799,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Kids Toys',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-kids-toys/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-kids-toys/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-kids-toys/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-kids-toys/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Kid Toys',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 338,
						'height' => 449,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 799,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Underwear',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-underwear/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-underwear/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-underwear/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-underwear/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Underwear',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 338,
						'height' => 449,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 799,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Socks',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-socks/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-socks/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-socks/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-socks/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Socks',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 338,
						'height' => 449,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 799,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Celeste Charm',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-celeste-charm/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-celeste-charm/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-celeste-charm/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-celeste-charm/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Celeste Charm',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 1,
					'woocommerce_thumbnail_cropping_custom_height' => 1,
					'shop_catalog_image_size'   => array(
						'width'  => 400,
						'height' => 400,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 130,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Footwear',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-footwear/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-footwear/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-footwear/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-footwear/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Footwear',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 1,
					'woocommerce_thumbnail_cropping_custom_height' => 1,
					'shop_catalog_image_size'   => array(
						'width'  => 400,
						'height' => 400,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 130,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Electro Pulse',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-electro-pulse/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-electro-pulse/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-electro-pulse/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-electro-pulse/preview.jpg',
				'pages'      => array(
					'front_page' => 'electro',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
					'category-menu' 	=> 'category-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 1,
					'woocommerce_thumbnail_cropping_custom_height' => 1,
					'shop_catalog_image_size'   => array(
						'width'  => 400,
						'height' => 400,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 600,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 130,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Sari Couture',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-sari-couture/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-sari-couture/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-sari-couture/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-sari-couture/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Sari Couture',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
					'category-menu' 	=> 'category-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 338,
						'height' => 449,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 799,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Campster',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-campster/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-campster/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-campster/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-campster/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Campster',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
					'category-menu' 	=> 'category-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 338,
						'height' => 449,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 799,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
			array(
				'name'       => 'Home - Coffee',
				'content'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-coffee/demo-content.xml',
				'widgets'     => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-coffee/widgets.wie',
				'customizer' => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-coffee/customizer.dat',
				'preview'   => 'https://raw.githubusercontent.com/uixthemeswp/glozin/refs/heads/main/importer/demo-content/home-coffee/preview.jpg',
				'pages'      => array(
					'front_page' => 'Home - Coffee',
					'blog'       => 'Blog',
					'cart'		 => 'Shopping Cart',
					'checkout'	 => 'checkouts',
				),
				'menus'      => array(
					'primary-menu' 		=> 'primary-menu',
					'category-menu' 	=> 'category-menu',
				),
				'options'    => array(
					'woocommerce_thumbnail_cropping' => 'custom',
					'woocommerce_thumbnail_cropping_custom_width' => 3,
					'woocommerce_thumbnail_cropping_custom_height' => 4,
					'shop_catalog_image_size'   => array(
						'width'  => 338,
						'height' => 449,
						'crop'   => 1,
					),
					'shop_single_image_size'    => array(
						'width'  => 600,
						'height' => 799,
						'crop'   => 1,
					),
					'shop_thumbnail_image_size' => array(
						'width'  => 130,
						'height' => 174,
						'crop'   => 1,
					),
				),
			),
		);
	}

	/**
	 * Prepare product attributes before import demo content
	 *
	 * @param $file
	 */
	function import_product_attributes( $file ) {
		global $wpdb;

		if ( ! class_exists( 'WXR_Parser' ) ) {
			if ( ! file_exists( WP_PLUGIN_DIR . '/soo-demo-importer/includes/parsers.php' ) ) {
				return;
			}

			require_once WP_PLUGIN_DIR . '/soo-demo-importer/includes/parsers.php';
		}

		$parser      = new \WXR_Parser();
		$import_data = $parser->parse( $file );

		if ( empty( $import_data ) || is_wp_error( $import_data ) ) {
			return;
		}

		if ( isset( $import_data['posts'] ) ) {
			$posts = $import_data['posts'];

			if ( $posts && sizeof( $posts ) > 0 ) {
				foreach ( $posts as $post ) {
					if ( 'product' === $post['post_type'] ) {
						if ( ! empty( $post['terms'] ) ) {
							foreach ( $post['terms'] as $term ) {
								if ( strstr( $term['domain'], 'pa_' ) ) {
									if ( ! taxonomy_exists( $term['domain'] ) ) {
										$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $term['domain'] ) );

										// Create the taxonomy
										if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
											$attribute = array(
												'attribute_label'   => $attribute_name,
												'attribute_name'    => $attribute_name,
												'attribute_type'    => 'select',
												'attribute_orderby' => 'menu_order',
												'attribute_public'  => 0
											);
											$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
											delete_transient( 'wc_attribute_taxonomies' );
										}

										// Register the taxonomy now so that the import works!
										register_taxonomy(
											$term['domain'],
											apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ),
											apply_filters( 'woocommerce_taxonomy_args_' . $term['domain'], array(
												'hierarchical' => true,
												'show_ui'      => false,
												'query_var'    => true,
												'rewrite'      => false,
											) )
										);
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Enable svg upload
	 *
	 * @param $file
	 */
	function enable_svg_upload() {
		add_filter('upload_mimes', array($this, 'svg_upload_types'));
	}

	/**
	 * Enable svg upload
	 *
	 * @param $file
	 */
	function svg_upload_types($file_types) {
		$new_filetypes = array();
		$new_filetypes['svg'] = 'image/svg+xml';
		$new_filetypes['webp'] = 'image/webp';
		$file_types = array_merge($file_types, $new_filetypes );
		return $file_types;
	}

	/**
	 * Enable svg upload
	 *
	 * @param $file
	 */
	function disable_svg_upload() {
		remove_filter('upload_mimes', array($this, 'svg_upload_types'));
	}

	/**
	 * Update page option
	 *
	 * @param $file
	 */
	function update_page_option($demo) {
		if ( isset( $demo['help_center_page'] ) ) {
			$page = $this->get_page_by_slug( $demo['help_center_page'] );
			if ( $page ) {
				update_option( 'help_center_page_id', $page->ID );
			}
		}

		if ( isset( $demo['order_tracking_page'] ) ) {
			$page = $this->get_page_by_slug( $demo['order_tracking_page'] );
			if ( $page ) {
				update_option( 'order_tracking_page_id', $page->ID );
			}
		}
	}


	/**
	 * Get page by slug
	 *
	 * @param $page_slug
	 */
	public function get_page_by_slug($page_slug) {
		$args = array(
			'name'           => $page_slug,
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1
		);
		$posts = get_posts( $args );
		$post = $posts ? $posts[0] : '';
		wp_reset_postdata();

		return $post;
	}

	public function check_elementor_container_grid($data_tabs) {
		if (class_exists('\Elementor\Plugin') && ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			echo sprintf('<h4>%s</h4>', esc_html('In order to use Glozin demo, first you need to active Container in Elementor. Go to Elementor > Settings > Features > Container to select active option.', 'glozin-addons'));
			echo sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=elementor-settings#tab-experiments')), esc_html('Active Elementor Container', 'glozin-addons'));
			$data_tabs = array();

		}
		return $data_tabs;

	}
}