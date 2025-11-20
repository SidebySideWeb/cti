<?php
namespace Glozin\Addons\Elementor\Library;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\User;

class Templates {
	public static function init(){
		add_action('elementor/init',[__CLASS__,'register_source']);
		add_action('elementor/editor/after_enqueue_scripts',[__CLASS__,'enqueue_editor_scripts']);
		add_action('elementor/ajax/register_actions',[__CLASS__,'register_ajax_actions']);
		add_action('elementor/editor/footer',[__CLASS__,'render_template']);
		add_action( 'glozin_addons_elementor_library_before_import', array( __CLASS__,'enable_svg_upload') );
		add_action( 'glozin_addons_elementor_library_after_import', array( __CLASS__,'disable_svg_upload') );
	}
	public static function logo(){
		return GLOZIN_ADDONS_URL . 'inc/elementor/library/assets/images/logo.svg';
	}
	public static function register_source(){
		Plugin::$instance->templates_manager->register_source( '\Glozin\Addons\Elementor\Library\Templates_Source');
	}
	public static function enqueue_editor_scripts(){
		$min = '';
		wp_enqueue_style('glozin-templates-style', GLOZIN_ADDONS_URL . 'inc/elementor/library/assets/css/style.css',[], GLOZIN_ADDONS_VER);
		wp_enqueue_script('glozin-elementor-ext-templates-lib', GLOZIN_ADDONS_URL . 'inc/elementor/library/assets/js/scripts' . $min . '.js',['jquery','backbone-marionette','backbone-radio','elementor-common-modules','elementor-dialog'], GLOZIN_ADDONS_VER,true);
		wp_localize_script('glozin-elementor-ext-templates-lib','glozin_ext_templates_lib',array('logoUrl'=>self::logo()));
	}
	public static function register_ajax_actions(Ajax $ajax){
		$library_ajax_requests=['glozin_ext_get_library_data',];
		foreach($library_ajax_requests as $ajax_request){
			$ajax->register_ajax_action($ajax_request,function($data)use($ajax_request){
				return self::handle_ajax_request($ajax_request,$data);
			});
		}
	}
	private static function handle_ajax_request($ajax_request,array $data){
		if(!User::is_current_user_can_edit_post_type(Source_Local::CPT)){
			throw new \Exception(__('Access Denied','glozin-addons'));
		}
		if(!empty($data['editor_post_id'])){
			$editor_post_id=absint($data['editor_post_id']);
			if(!get_post($editor_post_id)){
				throw new \Exception(__('Post not found.','glozin-addons'));
			}
			Plugin::$instance->db->switch_to_post($editor_post_id);
		}
		$result=call_user_func([__CLASS__,$ajax_request],$data);
		if(is_wp_error($result)){
			throw new \Exception($result->get_error_message());
		}
		return $result;
	}
	public static function glozin_ext_get_library_data(array $args){
		$library_data=self::get_library_data(!empty($args['sync']));
		Plugin::$instance->documents->get_document_types();
		return[
			'templates'=>self::get_templates(),
			'config'=>$library_data['types_data'],
		];
	}
	public static function get_library_data($force_update=false){
		return self::get_templates_data($force_update);
	}
	private static function get_templates_data($force_update=false){
		$templates=[];

		$import_file =  GLOZIN_ADDONS_DIR . 'inc/elementor/library/' . 'templates.json';

		if ( file_exists($import_file) && self::is_theme_registered() ) {
			$raw_data = @file_get_contents( $import_file );
			$info_data = json_decode( $raw_data, true );
			$templates = $info_data['library'];
		}

		return $templates;
	}
	public static function get_templates(){
		$source = Plugin::$instance->templates_manager->get_source('glozin_ext');
		return $source->get_items();

	}
	public static function get_template_content($template_name){
		$data = [];
		$import_file = \Glozin\Addons\Elementor\Library::dir_path() . 'templates/' . $template_name . '.json' ;
		$response = wp_remote_get($import_file, array(
		'headers' => array(
			'Accept' => 'application/json',
		)
		));
		if ( ( !is_wp_error($response)) && (200 === wp_remote_retrieve_response_code( $response ) ) ) {
			$raw_data = $response['body'];
			$data = json_decode( $raw_data, true );
		}
		return $data;
	}
	public static function is_theme_registered() {
		return ( 'import' );
	}

	/**
	 * Enable svg upload
	 *
	 * @param $file
	 */
	public static function  enable_svg_upload() {
		add_filter('upload_mimes', array(__CLASS__, 'svg_upload_types'));
	}
	/**
	 * Enable svg upload
	 *
	 * @param $file
	 */
	public static function svg_upload_types($file_types) {
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
	public static function disable_svg_upload() {
		remove_filter('upload_mimes', array(__CLASS__, 'svg_upload_types'));
	}
	public static function render_template(){
	?>
	<script type="text/template" id="tmpl-elementor-template-library-header-actions-glozin-ext">
		<div id="elementor-template-library-header-sync" class="elementor-templates-modal__header__item">
			<i class="eicon-sync" aria-hidden="true" title="<?php esc_attr_e('Sync Templates','glozin-addons'); ?>"></i>
			<span class="elementor-screen-only"><?php echo esc_html__('Sync Templates','glozin-addons'); ?></span>
		</div>
	</script>
	<script type="text/template" id="tmpl-elementor-templates-modal__header__logo-glozin-ext">
		<span class="elementor-templates-modal__header__logo__icon-wrapper">
			<img src="<?php echo esc_url(self::logo()); ?>" style="height: 30px;" />
		</span>
		<span class="elementor-templates-modal__header__logo__title">{{{ title }}}</span>
	</script>
	<script type="text/template" id="tmpl-elementor-template-library-header-preview-glozin-ext">
		<div id="elementor-template-library-header-preview-insert-wrapper" class="elementor-templates-modal__header__item">
			{{{ glozin_ext_templates_lib.templates.layout.getTemplateActionButton( obj ) }}}
		</div>
	</script>
	<script type="text/template" id="tmpl-elementor-template-library-templates-glozin-ext">
		<#
			var activeSource = glozin_ext_templates_lib.templates.getFilter('source');
		#>
		<div id="elementor-template-library-toolbar">
			<# if ( 'glozin_ext' === activeSource ) {
				var activeType = glozin_ext_templates_lib.templates.getFilter('type');
				#>
				<div id="elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar">
					<# if ( 'new_page' === activeType ) { #>
						<div id="elementor-template-library-order">
							<input type="radio" id="elementor-template-library-order-new" class="elementor-template-library-order-input" name="elementor-template-library-order" value="date">
							<label for="elementor-template-library-order-new" class="elementor-template-library-order-label"><?php echo esc_html__('New','glozin-addons'); ?></label>
							<input type="radio" id="elementor-template-library-order-trend" class="elementor-template-library-order-input" name="elementor-template-library-order" value="trendIndex">
							<label for="elementor-template-library-order-trend" class="elementor-template-library-order-label"><?php echo esc_html__('Trend','glozin-addons'); ?></label>
							<input type="radio" id="elementor-template-library-order-popular" class="elementor-template-library-order-input" name="elementor-template-library-order" value="popularityIndex">
							<label for="elementor-template-library-order-popular" class="elementor-template-library-order-label"><?php echo esc_html__('Popular','glozin-addons'); ?></label>
						</div>
					<# } else {
						var config = glozin_ext_templates_lib.templates.getConfig( activeType );
						if ( config.categories ) { #>
							<div id="elementor-template-library-filter">
								<select id="elementor-template-library-filter-subtype" class="elementor-template-library-filter-select" data-elementor-filter="subtype">
									<option></option>
									<# config.categories.forEach( function( category ) {
										var selected = category === glozin_ext_templates_lib.templates.getFilter( 'subtype' ) ? ' selected' : '';
										#>
										<option value="{{ category }}"{{{ selected }}}>{{{ category }}}</option>
									<# } ); #>
								</select>
							</div>
						<# }
					} #>
					<div id="elementor-template-library-my-favorites">
						<# var checked = glozin_ext_templates_lib.templates.getFilter( 'favorite' ) ? ' checked' : ''; #>
						<input id="elementor-template-library-filter-my-favorites" type="checkbox"{{{ checked }}}>
						<label id="elementor-template-library-filter-my-favorites-label" for="elementor-template-library-filter-my-favorites">
							<i class="eicon" aria-hidden="true"></i>
							<?php echo esc_html__('My Favorites','glozin-addons'); ?>
						</label>
					</div>
				</div>
			<# } #>
			<div id="elementor-template-library-filter-text-wrapper">
				<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php echo esc_html__('Search Templates:','glozin-addons'); ?></label>
				<input id="elementor-template-library-filter-text" placeholder="<?php echo esc_attr__('Search','glozin-addons'); ?>">
				<i class="eicon-search"></i>
			</div>
		</div>
		<div id="elementor-template-library-templates-container"></div>
	</script>
	<script type="text/template" id="tmpl-elementor-template-library-template-glozin-ext">
		<div class="elementor-template-library-template-body">
			<# if ( 'page' === type || 'homepage' === type || 'woocommerce' === type ) { #>
				<div class="elementor-template-library-template-screenshot" style="background-image: url({{ thumbnail }});"></div>
			<# } else { #>
				<img src="{{ thumbnail }}">
			<# } #>
			<# if ( '' !== url ) { #>
				<div class="elementor-template-library-template-preview">
					<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
				</div>
			<# } #>
		</div>
		<div class="elementor-template-library-template-footer">
			{{{ glozin_ext_templates_lib.templates.layout.getTemplateActionButton( obj ) }}}
			<div class="elementor-template-library-template-name">{{{ title }}} - {{{ type }}}</div>
			<div class="elementor-template-library-favorite">
				<input id="elementor-template-library-template-{{ template_id }}-favorite-input" class="elementor-template-library-template-favorite-input" type="checkbox"{{ favorite ? " checked" : "" }}>
				<label for="elementor-template-library-template-{{ template_id }}-favorite-input" class="elementor-template-library-template-favorite-label">
					<i class="eicon-heart-o" aria-hidden="true"></i>
					<span class="elementor-screen-only"><?php echo esc_html__('Favorite','glozin-addons'); ?></span>
				</label>
			</div>
		</div>
	</script>
	<?php
	}
}