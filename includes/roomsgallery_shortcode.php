<?php
function gen_roomsgallery_shortcode($atts){
	global $table_prefix,$wpdb;
	$sql_rooms = "select * from wp_posts p inner join wp_postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and p.post_type='gen_custom_booking' and pm.meta_key='_room_image'";
	$rooms = $wpdb->get_results($sql_rooms);
	$output = '<div>';
	foreach($rooms as $room){
		global $table_prefix,$wpdb;
		$postid = $room->post_id;
		$sqlprice = "select * from ".$table_prefix."postmeta where post_id=".$postid." and meta_key='_room_price'";
		$room_price = $wpdb->get_results($sqlprice);
		$image = $room->meta_value;
		$img_arr = explode('/',$image);
		$imgname = "";
		if(isset($img_arr[8])){
			$imgname = $img_arr[8];
		}
		$cssfix_front = get_option('cssfix_front');
		$output .= '<style type="text/css">
						'.$cssfix_front.'
					</style>';
    $output .= '<div style="float:left;padding:5px;"><a href="'.get_option('siteurl').'/?page_id='.GENBOOKINGCALENDAR_PAGEID.'&room_id='.$postid.'"><img src="'.$image.'" style="height:180px;width:250px;" /></a>
		<div>'.$room->post_title.'</div><div>'.$room_price[0]->meta_value.' BDT/Day</div>
		</div>';
	}
	$output .= '</div>';
	$output .= '<script type="text/javascript">jQuery(document).ready(function(){jQuery(".entry-header").remove();});</script>';
	return $output;
}
add_shortcode('gen_roomsgallery','gen_roomsgallery_shortcode');