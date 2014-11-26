<?php
	function gen_programmatically_create_page($title,$slug,$page_content,$post_type='page') {
		global $wpdb;
		// Initialize the page ID to -1. This indicates no action has been taken.
		$page_id = -1;
		// Setup the author, slug, and title for the post
		$slug_original = $slug;
		$current_user = wp_get_current_user();
		$author_id = $current_user->ID;
		//get page by slug
		$page_data_by_slug = '';
		//$page_data_by_slug = get_page_by_slug($slug);
		$isGenerate = true;
		$count = 0;
		while($isGenerate){
			$count++;
			$sql = $wpdb->prepare( "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_name = %s AND post_type= %s AND post_status = 'publish'", $slug, $post_type );
			$page = $wpdb->get_var($sql);
			if ( $page ){ 
				$page_data_by_slug = get_post($page, ARRAY_N ); 
			}	
			else{ 
				$page_data_by_slug = null; 
			}
			// If the page doesn't already exist, then create it
			if($page_data_by_slug == NULL){
				$page_id = wp_insert_post(
				array(
						'comment_status'	=>	'closed',
						'ping_status'		=>	'closed',
						'post_author'		=>	$author_id,
						'post_name'		=>	$slug,
						'post_title'		=>	$title,
						'post_status'		=>	'publish',
						'post_type'		=>	$post_type,
						'post_content' => $page_content,
						'post_parent'=>0
					)
				);
				update_option($slug_original, $title);	
				$isGenerate = false;
			}
			// Otherwise, we'll change the slug and page title
			else{
				$title = $title.$count;
				$slug = $slug.$count;
				$page_id = -2;
			}
		}
		return $page_id;
	} // end programmatically_create_post