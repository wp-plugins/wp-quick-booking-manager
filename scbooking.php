<?php
/*
Plugin Name: WP Quick Booking Manager
Plugin URI: http://www.codeinterest.com/
Description: General Hotel/Resort Booking Management System
Version: 1.1
Author: wpproducts
Author URI: http://www.solvercircle.com
*/
define('GEN_SCBOOKING_PLUGIN_URL', plugins_url('',__FILE__));
define("GEN_SC_BASE_URL", WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)));
define('CCB_PROCESSING_BG_COLOR','7FCA27');
define('CCB_BOOKED_BG_COLOR','138219');

$booking_calendar_title = get_option('gen-booking-calendar');
$genrooms_title = get_option('gen-rooms');

$genbooking_calendar_page = get_page_by_title($booking_calendar_title);
$genrooms_page =  get_page_by_title($genrooms_title);

$genbooking_calendar_page_id = 0;
$genrooms_page_id = 0;
if($genbooking_calendar_page){
	$genbooking_calendar_page_id = $genbooking_calendar_page->ID;
}
if($genrooms_page){
	$genrooms_page_id = $genrooms_page->ID;	
}


define('GENBOOKINGCALENDAR_PAGEID',$genbooking_calendar_page_id);
define('GENROOM_PAGEID',$genrooms_page_id);
//include_once('includes/calendar_shortcode.php');
//include_once('includes/managebooking_shortcode.php');
//include_once('includes/user_add_booking_shortcode.php');


include_once('includes/fullcalendar_shortcode.php');
//include_once('front-login/frontLoginForm.php');
include_once('includes/roomsgallery_shortcode.php');
include_once('includes/create_page.php');


function gen_create_custom_post_type() {
	register_post_type( 'gen_custom_booking',
		array(
			'labels' => array(
				'name' => __( 'Rooms' ),
				'singular_name' => __( 'Room' ),
				'menu_name'=>__('Quick Booking Manager'),
				'all_items'=>__('Rooms'),
				'add_new_item'=>__('Add New Room'),
				'add_new'=> __('Add New Room'),
				'not_found'=>__('No rooms found.'),
				'search_items'=>__('Search Rooms'),
				'edit_item'=>__('Edit Room'),
				'view_item'=>__('View Room'),
				'not_found_in_trash'=>__('No Rooms found in trash')
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'custom_bookings'),
      'supports' => array('title','thumbnail')
		)
	);
}

//add_action( 'init', 'gen_create_book_taxonomy' );   // commented to disable room category

function gen_create_book_taxonomy() {
	register_taxonomy(
		'gen_custom_category',
		'gen_custom_booking',
		array(
			'label' => __( 'Category' ),
			'rewrite' => array( 'slug' => 'gen_custom_category' ),
			'hierarchical' => true,
		)
	);
}

function  gen_add_metabox_for_room(){
add_meta_box(
		'room_attribute_metabox', // ID, should be a string
		'Room Attribute Settings', // Meta Box Title
		'gen_room_meta_box_content', // Your call back function, this is where your form field will go
		'gen_custom_booking', // The post type you want this to edit screen section (�post�, �page�, �dashboard�, �link�, �attachment� or �custom_post_type� where custom_post_type is the custom post type slug)
		'normal', // The placement of your meta box, can be �normal�, �advanced�or side
		'high' // The priority in which this will be displayed
		);
}
function gen_room_meta_box_content($post){
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	 
	 jQuery('#upload_roomimage_button').click(function() {
			formfield = jQuery('#roommetabox_image').attr('name');
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
			window.send_to_editor = function(html) {
				imgurl = jQuery('img',html).attr('src');
				jQuery('#roommetabox_image').val(imgurl);
				tb_remove();
			}
			return false;
	 });
	 
});
</script>
<?php
$room_noofbed = get_post_meta($post->ID, '_room_noofbed', true);
$room_bathroom = get_post_meta($post->ID, '_room_bathroom', true);
$room_price = get_post_meta($post->ID, '_room_price', true);
$room_capacity = get_post_meta($post->ID, '_room_capacity', true);
$room_description = get_post_meta($post->ID, '_room_description', true);
$room_image = get_post_meta($post->ID, '_room_image', true);

?>
<table >
  <tbody>
    <tr>
    	<th scope="row">No of Bed:</th>
      <td><input type="text" name="roommetabox_noofbed" id="roommetabox_noofbed" value="<?php if(isset($room_noofbed)) echo $room_noofbed;?>" style="width:300px;" /></td>
    </tr>
    <tr>
    	<th scope="row">Bath Room:</th>
    	<td>
      	<select id="bathroom" name="bathroom">
        	<option value="ensuite" <?php if($room_bathroom == 'ensuite') echo 'selected'; ?> >Ensuite</option>
          <option value="shared" <?php if($room_bathroom == 'shared') echo 'selected'; ?> >Shared</option>
        </select>
      </td>
    </tr>
    <tr>
    	<th scope="row">Price:</th>
      <td><input type="text" name="roommetabox_price" id="roommetabox_price" value="<?php if(isset($room_price)) echo $room_price;?>" style="width:300px;" /></td>
    </tr>
    <tr>
    	<th scope="row">Capacity:</th>
      <td><input type="text" name="roommetabox_capacity" id="roommetabox_capacity" value="<?php if(isset($room_capacity)) echo $room_capacity;?>" style="width:300px;" /></td>
    </tr>
    <tr>
    	<th scope="row">Description:</th>
      <td>
      <textarea name="roommetabox_Description" id="roommetabox_Description" rows="5" cols="46"><?php if(isset($room_description)) echo $room_description;?></textarea>
      </td>
    </tr>
    <tr>
    	<th scope="row">Image:</th>
      <td>
      	<input type="text" class="code"  name="roommetabox_image" id="roommetabox_image" value="<?php if(isset($room_image)) echo $room_image;?>" style="width:300px;" />
        <input  id="upload_roomimage_button" class="button" type="button" value="Upload Image" />
      </td>
    </tr>
    
  </tbody>
</table>
<?php
}
function gen_save_room_metabox( $post_id){
	$post = get_post($post_id);
	// Get our form field
	if($_POST){
		//$room_roomtype = esc_attr( $_POST['roomtype'] );
		$room_noofbed = 0;
		if(isset($_POST['roommetabox_noofbed'])){	
			$room_noofbed = esc_attr( $_POST['roommetabox_noofbed'] );
		}
		$room_bathroom = "";
		if(isset($_POST['bathroom'])){
			$room_bathroom = esc_attr( $_POST['bathroom'] );
		}
		$room_price = 0;
		if(isset($_POST['roommetabox_price'])){
			$room_price = esc_attr( $_POST['roommetabox_price'] );
		}
		$room_capacity = 0;
		if(isset($_POST['roommetabox_capacity'])){
			$room_capacity = esc_attr( $_POST['roommetabox_capacity'] );
		}
		$room_description = "";
		if(isset($_POST['roommetabox_Description'])){
			$room_description = esc_attr( $_POST['roommetabox_Description'] );
		}
		$room_image = "";
		if(isset($_POST['roommetabox_image'])){
			$room_image = esc_attr( $_POST['roommetabox_image'] );
		}
		// Update post meta
		//update_post_meta($post->ID, '_room_roomtype', $room_roomtype);
		update_post_meta($post->ID, '_room_noofbed', $room_noofbed);
		update_post_meta($post->ID, '_room_bathroom', $room_bathroom);
		update_post_meta($post->ID, '_room_price', $room_price);
		update_post_meta($post->ID, '_room_capacity', $room_capacity);
		update_post_meta($post->ID, '_room_description', $room_description);
		update_post_meta($post->ID, '_room_image', $room_image);
	}
}

add_action( 'save_post', 'gen_save_room_metabox' );
add_action('add_meta_boxes','gen_add_metabox_for_room');
/*---------------------*/
function gen_custom_manage_booking_menu(){
	//add_submenu_page( 'add.php?post_type=custom_booking', 'Bookings', 'Bookings', 'manage_options', 'hotel-bookings-menu', 'manage_booking_settings' );
	add_submenu_page( 'edit.php?post_type=gen_custom_booking', 'Manage Booking', 'Manage Booking', 'manage_options', 'manage-booking-menu', 'gen_manage_booking_settings');
}

function gen_custom_add_booking_menu(){
    add_submenu_page( 'edit.php?post_type=gen_custom_booking', 'Add Booking', 'Add Booking', 'manage_options', 'add-hotel-booking-menu', 'gen_add_booking_settings' );
}
function gen_booking_calendar_menu(){
    add_submenu_page( 'edit.php?post_type=gen_custom_booking', 'Booking Calendar', 'Booking Calendar', 'manage_options', 'booking-calendar-menu', 'gen_booking_calendar' );
}
function gen_cssfix_front(){
	add_submenu_page( 'edit.php?post_type=gen_custom_booking', 'FrontEnd CSS Fix', 'FrontEnd CSS Fix', 'manage_options', 'css-fix-menu', 'gen_cssfix_front_setting' );
}
function gen_pro_version(){
	add_submenu_page( 'edit.php?post_type=gen_custom_booking', 'Booking Pro Version', 'BOOKING PRO VERSION', 'manage_options', 'booking-pro-version', 'gen_booking_pro_version_setting' );
}
//-------------Booking Settings-----------------------
function gen_scbooking_get_opt_val($opt_name,$default_val){
	if(get_option($opt_name)!=''){
		return $value = get_option($opt_name);
	}else{
		return $value =$default_val;
	}
}
//
function gen_booking_calendar(){
	include_once('calendar-fullcalendar.php');
}
function gen_manage_booking_settings(){
	include_once('includes/manage_booking.php');
}
function gen_add_booking_settings(){
	include_once('includes/add_booking_backend.php');
}
function gen_cssfix_front_setting(){
	include_once('includes/add_cssfix_front.php');	
}
function gen_booking_pro_version_setting(){
  include_once('includes/booking_pro_version.php');
}

add_action( 'admin_menu', 'gen_custom_add_booking_menu' );
add_action( 'admin_menu', 'gen_custom_manage_booking_menu' );
add_action( 'admin_menu', 'gen_booking_calendar_menu' );
add_action('admin_menu','gen_cssfix_front');
add_action('admin_menu','gen_pro_version');

include_once('operations/scbooking_init.php');

function gen_booking_uninstall(){
}

register_activation_hook( __FILE__, 'gen_scbooking_install' );
register_deactivation_hook( __FILE__, 'gen_booking_uninstall');
add_action( 'init', 'gen_create_custom_post_type' );
//====== session start =================================
add_action('init', 'gen_bookingStartSession', 1);
function gen_bookingStartSession() {
    if(!session_id()) {
        session_start();
    }
}

	function gen_fullcalendarincludejs(){

		wp_register_script( 'fullcalendar',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.js',__FILE__));
    wp_register_script( 'jquery.multiple.select',plugins_url('/multiselect/multiple-select/jquery.multiple.select.js',__FILE__), array( 'jquery' ));
    
		wp_enqueue_script( 'fullcalendar');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-datepicker');
    
    wp_enqueue_script( 'jquery.multiple.select' );
	}
	function 	gen_fullcalendarincludecss(){
			wp_register_style( 'smoothness_jqueryui',plugins_url('/assets/css/jquery/jquery-ui.css',__FILE__));
			wp_register_style( 'fullcalendarcss',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.css',__FILE__));
			wp_register_style( 'fullcalendarprintcss',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.print.css',__FILE__));
			wp_register_style( 'multiple_selectcss',plugins_url('/multiselect/multiple-select/multiple-select.css',__FILE__));
      wp_register_style( 'addbooking_back_popup_css',plugins_url('/assets/css/add_booking.css',__FILE__));
      wp_register_style( 'addbooking_backend_css',plugins_url('/assets/css/add_booking_backend.css',__FILE__));
      
			wp_enqueue_style( 'smoothness_jqueryui');
 
      wp_enqueue_style( 'fullcalendarcss');
			wp_enqueue_style( 'fullcalendarprintcss');
      wp_enqueue_style( 'multiple_selectcss');
      wp_enqueue_style( 'addbooking_back_popup_css');
      wp_enqueue_style( 'addbooking_backend_css');
			
	}
	function gen_fullcalendarincludejs_front(){
    wp_register_script( 'jquery.multiple.select',plugins_url('/multiselect/multiple-select/jquery.multiple.select.js',__FILE__), array( 'jquery' ));
    wp_register_script( 'fullcalendar',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.js',__FILE__), array( 'jquery' ));
    
		wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery.multiple.select' );
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-datepicker');
    
    wp_enqueue_script( 'fullcalendar' );
    
	}
	function gen_fullcalendarincludecss_front(){
		
		wp_register_style( 'add_booking_front_popupcss',plugins_url('/assets/css/add_booking_front_popup.css',__FILE__));
		wp_register_style( 'multiple_selectcss_front',plugins_url('/multiselect/multiple-select/multiple-select.css',__FILE__));
    wp_register_style( 'fullcalendarcss_front',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.css',__FILE__));
    wp_register_style( 'jqueryuicss_front',plugins_url('/assets/css/jquery/jquery-ui.css',__FILE__));
    
		wp_enqueue_style( 'add_booking_front_popupcss');
    wp_enqueue_style( 'fullcalendarcss_front');
    wp_enqueue_style( 'jqueryuicss_front');
		wp_enqueue_style( 'multiple_selectcss_front');
	} 
	
	add_action('admin_enqueue_scripts','gen_fullcalendarincludejs');
	add_action('admin_enqueue_scripts','gen_fullcalendarincludecss');
	
	add_action('wp_enqueue_scripts','gen_fullcalendarincludejs_front');
	add_action('wp_enqueue_scripts','gen_fullcalendarincludecss_front');
  //
  //======================= admin ajax calls ===============================================================
  function gen_get_roomprice_by_custompost(){
      global $table_prefix,$wpdb;
      $post_ids_arr = $_REQUEST['post_ids_arr'];
      $fromdate = $_REQUEST['from_date'];
      $todate = $_REQUEST['to_date'];
      $days = gen_howManyDays($fromdate,$todate);

      $price = 0;
      foreach($post_ids_arr as $post_id){
        $sql_room_price = "select * from ".$table_prefix."postmeta where meta_key='_room_price' and post_id=".$post_id;	
        $result = $wpdb->get_results($sql_room_price);
        $price = $price + ($result[0]->meta_value*$days);
      }
      
      echo $price;
      exit;
  }
  function gen_howManyDays($startDate,$endDate) {
      $date1  = strtotime($startDate." 0:00:00");
      $date2  = strtotime($endDate." 23:59:59");
      $res    =  (int)(($date2-$date1)/86400);        
      return $res+1;
  } 
  add_action( 'wp_ajax_nopriv_gen_get_roomprice_by_custompost','gen_get_roomprice_by_custompost' );
  add_action( 'wp_ajax_gen_get_roomprice_by_custompost', 'gen_get_roomprice_by_custompost' );
  
  function gen_get_room_bycat(){
    global $table_prefix,$wpdb;
    $term_id = $_REQUEST['term_id'];
    $sql_room = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and tt.term_id=".$term_id." and pm.meta_key='_room_price'";
    $result = $wpdb->get_results($sql_room);
    echo json_encode($result);
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_get_room_bycat','gen_get_room_bycat' );
  add_action( 'wp_ajax_gen_get_room_bycat', 'gen_get_room_bycat' );
  
  function gen_check_booking(){
      global $table_prefix,$wpdb;
      $hdnbookingid = $_REQUEST['hdnbookingid'];
      $allroom = $_REQUEST['room'];
      $rooms = explode(",",$allroom);
      $room_cond = "";
      $count = 1;
      foreach($rooms as $rm){
        if($count==1){
          $room_cond .= "room like '%".$rm."%'";  
        }
        else{
          $room_cond .= " or room like '%".$rm."%'";  
        }
        $count++;
      }
      $from_date = $_REQUEST['from_date'];
      $to_date = $_REQUEST['to_date'];

      $sql = "";
      if($hdnbookingid != '' || $hdnbookingid != NULL ){
        $sql = "select * from ".$table_prefix."gen_scbooking where (".$room_cond.") and (from_date between '".$from_date."' and '".$to_date."' or to_date between '".$from_date."' and '".$to_date."' or (from_date < '".$from_date."' and to_date > '".$to_date."') ) and booking_id!=".$hdnbookingid;
      }
      else{
        $sql = "select * from ".$table_prefix."gen_scbooking where (".$room_cond.") and (from_date between '".$from_date."' and '".$to_date."' or to_date between '".$from_date."' and '".$to_date."' or (from_date < '".$from_date."' and to_date > '".$to_date."') )";
      }
      $result = $wpdb->get_results($sql);
      $yesno = "";
      if(count($result)>0){
        $yesno .= "yes";	
      }
      else{
        $yesno .= "no";
      }
      echo $yesno;
      exit;
  }
  add_action( 'wp_ajax_nopriv_gen_check_booking','gen_check_booking' );
  add_action( 'wp_ajax_gen_check_booking', 'gen_check_booking' );
  
  function gen_save_booking(){
     if ( count($_POST) > 0 ){ 
        global $table_prefix,$wpdb;

        $hdnbookingid = $_REQUEST['hdnbookingid'];
        $room_type = $_REQUEST['room_type'];
        $roomid = $_REQUEST['roomid'];
        $room = $_REQUEST['room'];
        $from_date = $_REQUEST['from_date'];
        $to_date = $_REQUEST['to_date'];
        $first_name = $_REQUEST['first_name'];
        $last_name = $_REQUEST['last_name'];
        $email = $_REQUEST['email'];
        $phone = $_REQUEST['phone'];
        $details = $_REQUEST['details'];
        $bookingby = $_REQUEST['bookingby'];
        $guest_type = $_REQUEST['guest_type'];
        $price = $_REQUEST['price'];
        $paid = $_REQUEST['paid'];
        $due = $_REQUEST['due'];
        $payment_method = $_REQUEST['payment_method'];
        $tracking_no = $_REQUEST['tracking_no'];

        $values = array(
          'room_type'=>$room_type,
          'room_id'=>$roomid,
          'room'=>$room,
          'from_date'=>$from_date, 
          'to_date'=>$to_date, 
          'first_name'=>$first_name, 
          'last_name'=>$last_name, 
          'email'=>$email, 
          'phone'=>$phone, 
          'details'=>$details, 
          'booking_by'=>$bookingby, 
          'guest_type'=>$guest_type, 
          'custom_price'=>$price, 
          'paid'=>$paid, 
          'due'=>$due,
          'payment_method'=>$payment_method,
          'tracking_no'=> $tracking_no
        );
        if($hdnbookingid == "" || $hdnbookingid == NULL){
          $wpdb->insert($table_prefix.'gen_scbooking',$values );	
          $inserted_id = $wpdb->insert_id;
          echo $inserted_id;
        }
        else{
          $wpdb->update(
             $table_prefix.'gen_scbooking',
             $values,
             array('booking_id' =>$hdnbookingid)
           );
           echo $hdnbookingid;
        }

      }
      exit;
  }
  add_action( 'wp_ajax_nopriv_gen_save_booking','gen_save_booking' );
  add_action( 'wp_ajax_gen_save_booking', 'gen_save_booking' );
  
  function gen_get_bookings(){
    global $table_prefix,$wpdb;
    $booking_id = $_REQUEST['booking_id'];
    $sql = "select * from ".$table_prefix."gen_scbooking where booking_id=".$booking_id;
    $result = $wpdb->get_results($sql);
    echo json_encode($result);
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_get_bookings','gen_get_bookings' );
  add_action( 'wp_ajax_gen_get_bookings', 'gen_get_bookings' );

  function gen_load_managebooking_data_front(){
    if($_POST['page'])
    {
      $page = $_POST['page'];
      $cur_page = $page;
      $page -= 1;
      $per_page = 10;
      $previous_btn = true;
      $next_btn = true;
      $first_btn = true;
      $last_btn = true;
      $start = $page * $per_page;
      global $table_prefix,$wpdb;
      $sql = "select * from ".$table_prefix."gen_scbooking ";
      $result_count = $wpdb->get_results($sql);
      $count = count($result_count);
      $sql = $sql.' LIMIT '.$start.', '.$per_page.'';
      //echo $sql;die();
      $result_page_data = $wpdb->get_results($sql); 
      $msg = "
      <style type='text/css'>
        /*-----paginations------*/
        #loading{
            width: 50px;
            position: absolute;
            /*top: 100px;
            left: 100px;
            margin-top:200px;*/
            height:50px;
        }
        #inner_content{
           padding: 0 20px 0 0!important;
        }
        #inner_content .pagination ul li.inactive,
        #inner_content .pagination ul li.inactive:hover{
            background-color:#ededed;
            color:#bababa;
            border:1px solid #bababa;
            cursor: default;
        }
        #inner_content .data ul li{
            list-style: none;
            font-family: verdana;
            margin: 5px 0 5px 0;
            color: #000;
            font-size: 13px;
        }

        #inner_content .pagination{
            width: 80%;/*800px;*/
            height: 45px;
        }
        #inner_content .pagination ul li{
            list-style: none;
            float: left;
            border: 1px solid #006699;
            padding: 2px 6px 2px 6px;
            margin: 0 3px 0 3px;
            font-family: arial;
            font-size: 14px;
            color: #006699;
            font-weight: bold;
            background-color: #f2f2f2;

            /*display:inline;
            cursor:pointer;*/
        }
        #inner_content .pagination ul li:hover{
            color: #fff;
            background-color: #006699;
            cursor: pointer;
        }
        .go_button
        {
          background-color:#f2f2f2;
          border:1px solid #006699;
          color:#cc0000;
          padding:2px 6px 2px 6px;
          cursor:pointer;
          position:absolute;
          /*margin-top:-1px;*/
          width:50px;
        }
        .total
        {
          float:right;
          font-family:arial;
          color:#999;
          padding-right:150px;
        }
        #namediv input {
          width:5%!important;
        }
      </style>
      ";
      $msg .= "<div id='content_top'></div>";
      if(count($result_page_data)){
            $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
                        <thead>
                          <tr>
                            <th>Room</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tr>';
                    foreach($result_page_data as $booking){
                      $msg .= '<tr class="alternate">
                                  <td>'.$booking->room.'</td>
                                  <td>'.$booking->from_date.'</td>
                                  <td>'.$booking->to_date.'</td>
                                  <td>'.$booking->email.'</td>
                                  <td>'.$booking->phone.'</td>

                                  <td>
                                    ';
                      if(!$booking->confirmed){
                          $msg .= '<a id="lnkapprove" href="" > Approve </a>&nbsp;&nbsp;&nbsp;';
                      }
                      else {
                          $msg .= '<span id="" > <b>Approved </b></span>&nbsp;&nbsp;&nbsp;';
                      }
                      $msg .= '<a onclick="gen_open_edit_popup('.$booking->booking_id.')" style="cursor:pointer;text-decoration:none;" >edit</a>
                                    &nbsp;&nbsp;&nbsp;<a id="delete_booking" href="#" >delete</a>
                                    <input type="hidden" id="hdnbookingid"  name="hdnbookingid" value="'.$booking->booking_id.'" />

                                  </td>
                              </tr>';
                    }
                    $msg .= '</tr>
                              <tfoot>
                                <tr>
                                  <th>Room</th>
                                  <th>From Date</th>
                                  <th>To Date</th>
                                  <th>Email</th>
                                  <th>Phone</th>
                                  <th></th>
                                </tr>
                              </tfoot>
                            </table>';	
        //}
      }
      else{
        $msg .= '<div style="padding:80px;color:red;">Sorry! No Data Found!</div>';
      }	
      $msg = "<div class='data'>" . $msg . "</div>"; // Content for Data


      /* --------------------------------------------- */
      $no_of_paginations = ceil($count / $per_page);

      /* ---------------Calculating the starting and endign values for the loop----------------------------------- */
      if ($cur_page >= 7) {
          $start_loop = $cur_page - 3;
          if ($no_of_paginations > $cur_page + 3)
              $end_loop = $cur_page + 3;
          else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
              $start_loop = $no_of_paginations - 6;
              $end_loop = $no_of_paginations;
          } else {
              $end_loop = $no_of_paginations;
          }
      } else {
          $start_loop = 1;
          if ($no_of_paginations > 7)
              $end_loop = 7;
          else
              $end_loop = $no_of_paginations;
      }
      /* ----------------------------------------------------------------------------------------------------------- */
      $msg .= "<div class='pagination'><ul>";

      // FOR ENABLING THE FIRST BUTTON
      if ($first_btn && $cur_page > 1) {
          $msg .= "<li p='1' class='active'>First</li>";
      } else if ($first_btn) {
          $msg .= "<li p='1' class='inactive'>First</li>";
      }

      // FOR ENABLING THE PREVIOUS BUTTON
      if ($previous_btn && $cur_page > 1) {
          $pre = $cur_page - 1;
          $msg .= "<li p='$pre' class='active'>Previous</li>";
      } else if ($previous_btn) {
          $msg .= "<li class='inactive'>Previous</li>";
      }
      for ($i = $start_loop; $i <= $end_loop; $i++) {

          if ($cur_page == $i)
              $msg .= "<li p='$i' style='color:#fff;background-color:#006699;' class='active'>{$i}</li>";
          else
              $msg .= "<li p='$i' class='active'>{$i}</li>";
      }

      // TO ENABLE THE NEXT BUTTON
      if ($next_btn && $cur_page < $no_of_paginations) {
          $nex = $cur_page + 1;
          $msg .= "<li p='$nex' class='active'>Next</li>";
      } else if ($next_btn) {
          $msg .= "<li class='inactive'>Next</li>";
      }

      // TO ENABLE THE END BUTTON
      if ($last_btn && $cur_page < $no_of_paginations) {
          $msg .= "<li p='$no_of_paginations' class='active'>Last</li>";
      } else if ($last_btn) {
          $msg .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
      }
      $goto = "<input type='text' class='goto' size='1' style='margin-left:30px;height:24px;'/><input type='button' id='go_btn' class='go_button' value='Go'/>";
      $total_string = "<span class='total' a='$no_of_paginations'>Page <b>" . $cur_page . "</b> of <b>$no_of_paginations</b></span>";
      $img_loading = "<span ><div id='loading'></div></span>";
      $msg = $msg . "" . $goto . $total_string . $img_loading . "</ul></div>";  // Content for pagination
      echo $msg;
    }
    exit;
  }
  
  add_action( 'wp_ajax_nopriv_gen_load_managebooking_data_front','gen_load_managebooking_data_front' );
  add_action( 'wp_ajax_gen_load_managebooking_data_front', 'gen_load_managebooking_data_front' );
  
  function gen_save_cssfixfront(){
    if ( count($_POST) > 0 ){ 
      global $table_prefix,$wpdb;

      $cssfix = $_REQUEST['cssfix'];
      $css = $_REQUEST['css'];
      $isupdate ="";
      if($cssfix == "front"){
        $isupdate = update_option('cssfix_front',$css);
      }
      if($isupdate){
        echo "added";
      }

    }
    exit;
  }
  
  add_action( 'wp_ajax_nopriv_gen_save_cssfixfront','gen_save_cssfixfront' );
  add_action( 'wp_ajax_gen_save_cssfixfront', 'gen_save_cssfixfront' );
  
  function gen_set_ajax_room_cookie(){
    if($_POST){
      $calltype = $_REQUEST['calltype'];
      if($calltype = 'setcookie'){
        $_SESSION['roomid'] = $_REQUEST['room']; 
      }
      else if($calltype = 'setcookie_front' ){
        $_SESSION['roomid_front'] = $_REQUEST['room']; 
      }
    }
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_set_ajax_room_cookie','gen_set_ajax_room_cookie' );
  add_action( 'wp_ajax_gen_set_ajax_room_cookie', 'gen_set_ajax_room_cookie' );
  
  function gen_search_booking(){
    global $table_prefix,$wpdb;
    $search_text = $_REQUEST['searchtext'];
    $sql = "select * from ".$table_prefix."gen_scbooking where email='".$search_text."' or phone='".$search_text."' or tracking_no='".$search_text."'";
    $result = $wpdb->get_results($sql);

    $msg = "<div id='content_top'></div>";
    if(count($result)){
          $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
                      <thead>
                        <tr>
                          <th>Room</th>
                          <th>From Date</th>
                          <th>To Date</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tr>';
                  foreach($result as $booking){
                    $msg .= '<tr class="alternate">
                                <td>'.$booking->room.'</td>
                                <td>'.$booking->from_date.'</td>
                                <td>'.$booking->to_date.'</td>
                                <td>'.$booking->email.'</td>
                                <td>'.$booking->phone.'</td>

                                <td>
                                  ';
                    $msg .= '<a href="'.site_url().'/wp-admin/edit.php?post_type=gen_custom_booking&page=add-hotel-booking-menu&calltype=editbooking&id='.$booking->booking_id.'">edit</a>
                                  &nbsp;&nbsp;&nbsp;<a style="cursor:pointer;" id="delete_booking">delete</a>
                                  <input type="hidden" id="hdnbookingid"  name="hdnbookingid" value="'.$booking->booking_id.'" />
                                </td>
                            </tr>';
                  }
                  $msg .= '</tr>
                            <tfoot>
                              <tr>
                                <th>Room</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th></th>
                              </tr>
                            </tfoot>
                          </table>';	
    }
    else{
      $msg .= '<div style="padding:80px;color:red;">Sorry! No Data Found!</div>';
    }
    $msg = "<div class='data'>" . $msg . "</div>";
    echo $msg;
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_search_booking','gen_search_booking' );
  add_action( 'wp_ajax_gen_search_booking', 'gen_search_booking' );
  
  function gen_load_managebooking_data(){
    if($_POST['page'])
    {
      $page = $_POST['page'];
      $cur_page = $page;
      $page -= 1;
      $per_page = 15;
      $previous_btn = true;
      $next_btn = true;
      $first_btn = true;
      $last_btn = true;
      $start = $page * $per_page;
      global $table_prefix,$wpdb;
      $sql = "select * from ".$table_prefix."gen_scbooking ";
      $result_count = $wpdb->get_results($sql);
      $count = count($result_count);
      $sql = $sql.' LIMIT '.$start.', '.$per_page.'';
      $result_page_data = $wpdb->get_results($sql); 
      $msg = "<style type='text/css'>
        /*-----paginations------*/
        #loading{
            width: 50px;
            position: absolute;
            /*top: 100px;
            left: 100px;
            margin-top:200px;*/
            height:50px;
        }
        #inner_content{
           padding: 0 20px 0 0!important;
        }
        #inner_content .pagination ul li.inactive,
        #inner_content .pagination ul li.inactive:hover{
            background-color:#ededed;
            color:#bababa;
            border:1px solid #bababa;
            cursor: default;
        }
        #inner_content .data ul li{
            list-style: none;
            font-family: verdana;
            margin: 5px 0 5px 0;
            color: #000;
            font-size: 13px;
        }

        #inner_content .pagination{
            width: 80%;/*800px;*/
            height: 45px;
        }
        #inner_content .pagination ul li{
            list-style: none;
            float: left;
            border: 1px solid #006699;
            padding: 2px 6px 2px 6px;
            margin: 0 3px 0 3px;
            font-family: arial;
            font-size: 14px;
            color: #006699;
            font-weight: bold;
            background-color: #f2f2f2;

            /*display:inline;
            cursor:pointer;*/
        }
        #inner_content .pagination ul li:hover{
            color: #fff;
            background-color: #006699;
            cursor: pointer;
        }
        .go_button
        {
          background-color:#f2f2f2;
          border:1px solid #006699;
          color:#cc0000;
          padding:2px 6px 2px 6px;
          cursor:pointer;
          position:absolute;
          /*margin-top:-1px;*/
          width:50px;
        }
        .total
        {
          float:right;
          font-family:arial;
          color:#999;
          padding-right:150px;
        }
        #namediv input {
          width:5%!important;
        }
      </style>";
      $msg .= "<div id='content_top'></div>";
      if(count($result_page_data)){
            $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
                        <thead>
                          <tr>
                            <th>Room</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tr>';
                    foreach($result_page_data as $booking){
                      $msg .= '<tr class="alternate">
                          <td>'.$booking->room.'</td>
                          <td>'.$booking->from_date.'</td>
                          <td>'.$booking->to_date.'</td>
                          <td>'.$booking->email.'</td>
                          <td>'.$booking->phone.'</td>

                          <td>
                            ';
                      $msg .= '<a href="'.site_url().'/wp-admin/edit.php?post_type=gen_custom_booking&page=add-hotel-booking-menu&calltype=editbooking&id='.$booking->booking_id.'">edit</a>
                                    &nbsp;&nbsp;|&nbsp;&nbsp;<a style="cursor:pointer;" id="delete_booking">delete</a>
                                    <input type="hidden" id="hdnbookingid" name="hdnbookingid" value="'.$booking->booking_id.'" />
                                  </td>
                              </tr>';
                    }
                    $msg .= '</tr>
                              <tfoot>
                                <tr>
                                  <th>Room</th>
                                  <th>From Date</th>
                                  <th>To Date</th>
                                  <th>Email</th>
                                  <th>Phone</th>
                                  <th></th>
                                </tr>
                              </tfoot>
                            </table>';	
      }
      else{
        $msg .= '<div style="padding:80px;color:red;">Sorry! No Data Found!</div>';
      }	
      $msg = "<div class='data'>" . $msg . "</div>"; // Content for Data


      $no_of_paginations = ceil($count / $per_page);

      /* ---------------Calculating the starting and endign values for the loop----------------------------------- */
      if ($cur_page >= 7) {
          $start_loop = $cur_page - 3;
          if ($no_of_paginations > $cur_page + 3)
              $end_loop = $cur_page + 3;
          else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
              $start_loop = $no_of_paginations - 6;
              $end_loop = $no_of_paginations;
          } else {
              $end_loop = $no_of_paginations;
          }
      } else {
          $start_loop = 1;
          if ($no_of_paginations > 7)
              $end_loop = 7;
          else
              $end_loop = $no_of_paginations;
      }
      /* ----------------------------------------------------------------------------------------------------------- */
      $msg .= "<div class='pagination'><ul>";

      // FOR ENABLING THE FIRST BUTTON
      if ($first_btn && $cur_page > 1) {
          $msg .= "<li p='1' class='active'>First</li>";
      } else if ($first_btn) {
          $msg .= "<li p='1' class='inactive'>First</li>";
      }

      // FOR ENABLING THE PREVIOUS BUTTON
      if ($previous_btn && $cur_page > 1) {
          $pre = $cur_page - 1;
          $msg .= "<li p='$pre' class='active'>Previous</li>";
      } else if ($previous_btn) {
          $msg .= "<li class='inactive'>Previous</li>";
      }
      for ($i = $start_loop; $i <= $end_loop; $i++) {

          if ($cur_page == $i)
              $msg .= "<li p='$i' style='color:#fff;background-color:#006699;' class='active'>{$i}</li>";
          else
              $msg .= "<li p='$i' class='active'>{$i}</li>";
      }

      // TO ENABLE THE NEXT BUTTON
      if ($next_btn && $cur_page < $no_of_paginations) {
          $nex = $cur_page + 1;
          $msg .= "<li p='$nex' class='active'>Next</li>";
      } else if ($next_btn) {
          $msg .= "<li class='inactive'>Next</li>";
      }

      // TO ENABLE THE END BUTTON
      if ($last_btn && $cur_page < $no_of_paginations) {
          $msg .= "<li p='$no_of_paginations' class='active'>Last</li>";
      } else if ($last_btn) {
          $msg .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
      }
      $goto = "<input type='text' class='goto' size='1' style='margin-left:30px;height:24px;'/><input type='button' id='go_btn' class='go_button' value='Go'/>";
      $total_string = "<span class='total' a='$no_of_paginations'>Page <b>" . $cur_page . "</b> of <b>$no_of_paginations</b></span>";
      $img_loading = "<span ><div id='loading'></div></span>";
      $msg = $msg . "" . $goto . $total_string . $img_loading . "</ul></div>";  // Content for pagination
      echo $msg;
    }
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_load_managebooking_data','gen_load_managebooking_data' );
  add_action( 'wp_ajax_gen_load_managebooking_data', 'gen_load_managebooking_data' );
  
  function gen_activate_booking(){
    if ( count($_POST) > 0 ){ 
      global $table_prefix,$wpdb;
      $bookingid = $_REQUEST['booking_id'];	

      //$sql = "UPDATE ".$table_prefix."scbooking SET confirmed = 1 WHERE booking_id = ".$bookingid."";
       $values = array('confirmed'=>1);
       $wpdb->update(
             $table_prefix.'gen_scbooking',
             $values,
             array('booking_id' =>$bookingid)
           );
       echo $bookingid;		 
    }
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_activate_booking','gen_activate_booking' );
  add_action( 'wp_ajax_gen_activate_booking', 'gen_activate_booking' );
  function gen_delete_booking(){
    if ( count($_POST) > 0 ){ 
      global $table_prefix,$wpdb;
      $bookingid = $_REQUEST['booking_id'];	

      $aff_rows = $wpdb->query("delete from ".$table_prefix."gen_scbooking where booking_id='".$bookingid."'");
      echo $aff_rows;		 
    }
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_delete_booking','gen_delete_booking' );
  add_action( 'wp_ajax_gen_delete_booking', 'gen_delete_booking' );
  function gen_get_bookings_by_room(){
    global $table_prefix,$wpdb;
    $room = $_REQUEST['room'];
    $sql = "select * from ".$table_prefix."gen_scbooking where room like '%".$room."%'";
    $result = $wpdb->get_results($sql);
    echo json_encode($result);
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_get_bookings_by_room','gen_get_bookings_by_room' );
  add_action( 'wp_ajax_gen_get_bookings_by_room', 'gen_get_bookings_by_room' );
  
  function gen_export_booking(){
    if($_POST){
      $export_data = $_REQUEST['export_data'];
      $file_name = "booking_".uniqid().".csv";
      $file_path = GEN_SCBOOKING_PLUGIN_URL."/operations/".$file_name;
      $fp = fopen($file_path, 'w');
      fwrite($fp, $export_data);
      fclose($fp);
    }
    exit;
  }
  add_action( 'wp_ajax_nopriv_gen_export_booking','gen_export_booking' );
  add_action( 'wp_ajax_gen_export_booking', 'gen_export_booking' );