<?php

if (!function_exists('aesop_map_shortcode')) {
	function aesop_map_shortcode($atts, $content = null) {

		$defaults = array(
			'height' 				=> 500,
		);

		wp_enqueue_script('aesop-map-script',AI_CORE_URL.'/public/includes/libs/leaflet/leaflet.js');
		wp_enqueue_style('aesop-map-style',AI_CORE_URL.'/public/includes/libs/leaflet/leaflet.css', AI_CORE_VERSION, true);

		$atts = apply_filters('aesop_map_defaults',shortcode_atts($defaults, $atts));

		$hash = rand();

		// actions
		$actiontop = do_action('aesop_parallax_component_before');
		$actionbottom = do_action('aesop_parallax_component_after');


		$out = sprintf('%s<section id="aesop-map-component" class="aesop-component aesop-map-component" style="height:%spx"></section>%s',$actiontop, $atts['height'], $actionbottom);

		return apply_filters('aesop_map_output',$out);
	}

}

class AesopMapComponent {

	function __construct(){
		add_action('wp_footer', array($this,'aesop_map_loader'),20);
	}

	function aesop_map_loader(){

		global $post;

		//$markers = get_post_meta($post->ID,'aesop_map_component_locations', false);
		//$start = get_post_meta($post->ID,'aesop_map_start', true);

		if( isset($post) && is_single() && has_shortcode( $post->post_content, 'aesop_map') )  { ?>
			<script>

				var map = L.map('aesop-map-component',{
					scrollWheelZoom: false,
					zoom:12,
					center: [51.5, -0.09]
				});

				L.tileLayer('http://{s}.tile.cloudmade.com/4595fbb0139f4a8b9ccbd1b150016109/997/256/{z}/{x}/{y}.png', {
					maxZoom: 18,
					attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>'
				}).addTo(map);

				<?php 

					foreach($markers as $marker):

						$lat = $marker['lat'];
						$long = $marker['long'];
						$text = $marker['content'];

						$loc = $lat.','.$long;

						?> L.marker([<?php echo $loc;?>]).addTo(map).bindPopup("<?php echo $text;?>").openPopup(); <?php

					endforeach;
				?>
			</script>

		<?php }
	}

}
new AesopMapComponent;