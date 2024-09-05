<?php
/*
Plugin Name: Azur Post Geotag Map
Plugin URI: https://github.com/sinky/azur-post-geotag-map
Version: 1.1
Author: Marco Krage
Author URI: https://my-azur.de
Description: Displays a little map with a marker below content of each post, zoomable and with link to google maps
*/

function azur_append_map_scripts() {
	wp_enqueue_style('leaflet', '//cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css');
	wp_enqueue_script('leaflet-js', '//cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js');
}
add_action('wp_enqueue_scripts', 'azur_append_map_scripts');


add_filter('the_content', 'azur_append_map');
function azur_append_map( $content ) {
  //if(!is_single()) return $content;
  $lat = get_post_custom_values('geo_latitude');
  $lng = get_post_custom_values('geo_longitude');

  if ( !empty($lat[0]) && !empty($lng[0]) ){
    $content .= "<div class='azur-post-geotag-map' style='height:200px; width:100%' data-center='".$lat[0].",".$lng[0]."'></div>";
  }
  return $content;
}


add_action('wp_footer', 'azur_append_map_script');
function azur_append_map_script(){ ?>
<script>
document.addEventListener("DOMContentLoaded", function(event) {
	var maps = document.querySelectorAll('.azur-post-geotag-map');
	var addMap = function($map) {
		var latlng = $map.getAttribute("data-center").split(',');
		var center = [latlng[0],latlng[1]];
		var zoomA = 9;
		var zoomB = 15;
		var map = L.map($map, {
			center: center,
			zoom: zoomA,
			zoomControl: false,
			dragging: false,
			doubleClickZoom: false,
			scrollWheelZoom: false,
			boxZoom: false,
			keyboard: false,
			tap: false
		});
		
		L.tileLayer('//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);

		var marker = L.marker(center).addTo(map);
		marker.on('click', function() {
			var url = "https://www.google.com/maps/search/?api=1&query=" + latlng[0] + "," + latlng[1];
			window.open(url);
		});
		marker.bindTooltip("In Maps öffnen");
		var options_flyto = {
			duration: 0.5,
			animate: false
		}
		map.on('click', function() {
			var zoomTo = (map.getZoom() == zoomA) ? zoomB : zoomA;
			map.flyTo(center, zoomTo, options_flyto);
		});
	}

	for (let i = 0; i < maps.length; i++) {
		addMap(maps[i]);
	}
});
</script>
<?php };
