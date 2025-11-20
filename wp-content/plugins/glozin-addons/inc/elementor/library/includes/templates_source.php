<?php
namespace Glozin\Addons\Elementor\Library;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Base;

class Templates_Source extends Source_Base {
	public function get_id(){
		return 'glozin_ext';
	}
	public function get_title(){
		return esc_html__('Glozin Elementor Library','glozin-addons');
	}
	public function register_data(){}

	public function get_items($args=[]){
		$library_data = \Glozin\Addons\Elementor\Library\Templates::get_library_data();

		$templates=[];
		$pro_status='inactive';
		if(!empty($library_data['templates'])){
			foreach( $library_data['templates'] as $template_data ){
				$data = $this->prepare_template($template_data);
				$data['proStatus']=$pro_status;

				if ( empty( $data['thumbnail'] ) ) {
					$data['thumbnail'] = \Glozin\Addons\Elementor\Library::dir_path() . 'thumbnails/' . $template_data['id'] . '.jpg';
				}

				if ( empty( $data['url'] ) ) {
					$ext = '.jpg';
					if( $template_data['tags'] == 'pages' ) {
						$ext = '-full.jpg';
					} else if( $template_data['tags'] == 'footer-mobile' ) {
						$ext = '-full.jpg';
					}
					$data['url'] = \Glozin\Addons\Elementor\Library::dir_path() . 'thumbnails/' . $template_data['id'] . $ext;
				}

				$templates[]=$data;
			}
		}
		return $templates;
	}
	public function get_item($template_id){
		$templates=$this->get_items();
		return $templates[$template_id];
	}
	public function save_item($template_data){
		return new \WP_Error('invalid_request', esc_html__('Cannot save template to a remote source', 'glozin-addons') );
	}
	public function update_item($new_data){
		return new \WP_Error('invalid_request', esc_html__('Cannot update template to a remote source', 'glozin-addons') );
	}
	public function delete_template($template_id){
		return new \WP_Error('invalid_request', esc_html__('Cannot delete template from a remote source', 'glozin-addons') );
	}
	public function export_template($template_id){
		return new \WP_Error('invalid_request', esc_html__('Cannot export template from a remote source', 'glozin-addons') );
	}
	public function get_data(array $args,$context='display') {
		do_action('glozin_addons_elementor_library_before_import');
		$data = \Glozin\Addons\Elementor\Library\Templates::get_template_content($args['template_name']);
		$data=(array) $data;
		$data['content'] = $this->replace_elements_ids($data['content']);
		$data['content'] = $this->process_export_import_content($data['content'],'on_import');
		$post_id = $args['editor_post_id'];
		$document = Plugin::$instance->documents->get($post_id);
		if($document){
			$data['content'] = $document->get_elements_raw_data($data['content'],true);
		}
		do_action('glozin_addons_elementor_library_after_import');
		return $data;
	}
	private function prepare_template(array $template_data){
		$favorite_templates = $this->get_user_meta('favorites');
		return[
			'template_id'=>$template_data['id'],
			'post_id'=> get_the_ID(),
			'source'=>$this->get_id(),
			'type'=>$template_data['type'],
			'subtype'=>$template_data['subtype'],
			'title'=>$template_data['title'],
			'thumbnail'=>$template_data['thumbnail'],
			'date'=>$template_data['tmpl_created'],
			'author'=>$template_data['author'],
			'tags'=>json_decode($template_data['tags']),
			'isPro'=>$template_data['is_pro'],
			'url'=>$template_data['url'],
			'favorite'=>!empty($favorite_templates[$template_data['id']]),
		];
	}
}
