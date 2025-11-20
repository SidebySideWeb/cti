<?php
namespace Glozin\Addons\Elementor\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Utils;

trait Video_Base {
	/**
	 * Register controls
	 *
	 * @param array $controls
	 */
	protected function register_video_repeater_controls( $repeater, $condition = [] ) {
		$repeater->add_control(
			'video_source',
			[
				'label' => esc_html__( 'Video Source', 'glozin-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'media' 	 => esc_html__( 'Media', 'glozin-addons' ),
					'hosted'  	 => esc_html__( 'Self Hosted', 'glozin-addons' ),
					'youtube'    => esc_html__( 'Youtube', 'glozin-addons' ),
					'vimeo' 	 => esc_html__( 'Vimeo', 'glozin-addons' ),
				],
				'default' => 'media',
				'condition' => $condition,
			]
		);

		$repeater->add_control(
			'media_url',
			[
				'label'    => __( 'Video', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'video' ],
				'condition' => array_merge( $condition, [ 'video_source' => 'media' ] ),
			]
		);

		$repeater->add_control(
			'hosted_url',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your URL', 'glozin-addons' ) . ' (mp4)',
				'label_block' => true,
				'condition' => array_merge( $condition, [ 'video_source' => 'hosted' ] ),
			]
		);

		$repeater->add_control(
			'poster_url',
			[
				'label' => esc_html__( 'Poster', 'glozin-addons' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'image' ],
				'condition' => array_merge( $condition, [ 'video_source' => [ 'media', 'hosted' ] ] ),
			]
		);

		$repeater->add_control(
			'youtube_url',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your URL', 'glozin-addons' ) . ' (YouTube)',
				'default' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				'label_block' => true,
				'condition' => array_merge( $condition, [ 'video_source' => 'youtube' ] ),
			]
		);

		$repeater->add_control(
			'vimeo_url',
			[
				'label' => esc_html__( 'Link', 'glozin-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your URL', 'glozin-addons' ) . ' (Vimeo)',
				'default' => 'https://vimeo.com/235215203',
				'label_block' => true,
				'condition' => array_merge( $condition, [ 'video_source' => 'vimeo' ] ),
			]
		);

		$repeater->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'glozin-addons' ),
				'description' => sprintf(
					esc_html__( 'Note: Autoplay is affected by %1$s Google’s Autoplay policy %2$s on Chrome browsers.', 'glozin-addons' ),
					'<a href="https://developers.google.com/web/updates/2017/09/autoplay-policy-changes" target="_blank">',
					'</a>'
				),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $condition,
			]
		);

		$repeater->add_control(
			'mute',
			[
				'label' => esc_html__( 'Mute', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $condition,
			]
		);

		$repeater->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $condition,
			]
		);

		$repeater->add_control(
			'controls',
			[
				'label' => esc_html__( 'Player Controls', 'glozin-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'glozin-addons' ),
				'label_on' => esc_html__( 'Show', 'glozin-addons' ),
				'default' => '',
				'condition' => $condition,
			]
		);
	}

	/**
	 * Render video for shortcode.
	 *
	 */
	protected function render_video( $settings = '' ) {
		$settings 	= ! empty( $settings ) ? $settings : $this->get_settings_for_display();

		if( $settings['video_source'] == 'youtube' ) {
			self::get_video_youtube( $settings );
		} elseif( $settings['video_source'] == 'vimeo' ) {
			self::get_video_vimeo( $settings );
		} else {
			self::get_video_selfhosted( $settings );
		}
	}

	/**
	 * Get video youtube
	 *
	 * @param array $settings
	 * @return void
	 */
	protected function get_video_youtube( $settings = '' ) {
		if( empty( $settings ) ) {
			return;
		}

		$video_url = $settings['youtube_url'];
		$class = '';

		$embed_params = array(
			'enablejsapi' => '1',
			'playsinline' => '1',
			'html5' => '1',
			'showinfo' => '0',
			'modestbranding' => '0',
			'rel' => '0',
			'fs' => '0',
			'playerapiid' => 'ytplayer',
			'wmode' => 'opaque',
		);

		$video_properties = \Elementor\Embed::get_video_properties( $settings['youtube_url'] );

		foreach ( [ 'autoplay', 'loop', 'controls', 'mute' ] as $option_name ) {
			if ( $settings[ $option_name ] ) {
				$embed_params[ $option_name ] = '1';
				if( 'loop' == $option_name ) {
					$embed_params['playlist'] = $video_properties['video_id'];
				}
			} else {
				$embed_params[ $option_name ] = '0';
			}
		}

		if ( '0' === $embed_params['controls'] ) {
			$class = 'class=pe-none';
		}

		$video_url = \Elementor\Embed::get_embed_url( $video_url, $embed_params );
		
		echo '<iframe ' . esc_attr( $class ) . ' src="' . esc_url( $video_url ) . '" width="1200" height="500" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen playsinline></iframe>';
	}

	/**
	 * Get video vimeo
	 *
	 * @param array $settings
	 * @return void
	 */
	protected function get_video_vimeo( $settings = '' ) {
		if( empty( $settings ) ) {
			return;
		}
		
		$video_url = $settings['vimeo_url'];

		$embed_params = array(
			'api' => '1',
		);

		foreach ( [ 'autoplay', 'loop', 'mute' ] as $option_name ) {
			if ( $settings[ $option_name ] ) {
				$embed_params[ $option_name ] = '1';
			} else {
				$embed_params[ $option_name ] = '0';
			}
		}

		if( $settings['controls'] ) {
			$embed_params['background'] = '0';
		} else {
			$embed_params['background'] = '1';
		}

		$video_url = \Elementor\Embed::get_embed_url( $video_url, $embed_params );
		
		echo '<iframe src="' . esc_url( $video_url ) . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen playsinline></iframe>';
	}
	
	/**
	 * Get video selfhosted
	 *
	 * @param array $settings
	 * @return void
	 */
	protected function get_video_selfhosted( $settings = '' ) {
		if( empty( $settings ) ) {
			return;
		}

		$video_params = [];

		foreach ( [ 'autoplay', 'loop', 'controls' ] as $option_name ) {
			if ( $settings[ $option_name ] ) {
				$video_params[ $option_name ] = '';
			}
		}

		if ( $settings['mute'] ) {
			$video_params['muted'] = 'muted';
		}

		$video_url = $settings['video_source'] == 'media' ? $settings['media_url']['url'] : $settings['hosted_url'];
		$poster = ! empty( $settings['poster_url']['url'] ) ? 'poster="' . esc_url( $settings['poster_url']['url'] ) . '"' : '';
		?>
		<video src="<?php echo esc_url( $video_url ); ?>" <?php echo $poster; ?> <?php Utils::print_html_attributes( $video_params ); ?> playsinline preload="metadata"></video>
		<?php
	}

	/**
	 * Get video url
	 *
	 * @param array $settings
	 * @return void
	 */
	protected function has_video( $settings = '' ) {
		$settings 	= ! empty( $settings ) ? $settings : $this->get_settings_for_display();

		if( $settings['video_source'] == 'youtube' && ! empty( $settings['youtube_url'] ) ) {
			return true;
		}
		
		if( $settings['video_source'] == 'vimeo' && ! empty( $settings['vimeo_url'] ) ) {
			return true;
		}
		
		if( $settings['video_source'] == 'media' && ! empty( $settings['media_url']['url'] ) ) {
			return true;
		}

		if( $settings['video_source'] == 'hosted' && ! empty( $settings['hosted_url'] ) ) {
			return true;
		}

		return false;
	}
}
