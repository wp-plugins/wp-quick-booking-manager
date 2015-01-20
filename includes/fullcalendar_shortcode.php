<?php  
  function gen_sccalendar_shortcode($atts){ 
		//if ( is_user_logged_in() ){
			global $table_prefix,$wpdb;
			$room = "";
			if(isset($_SESSION['roomid_front'])){
				$room = $_SESSION['roomid_front'];
			}
			if($_POST){
				$room = $_REQUEST['optrooms'];
			}
			else{
				if(isset($_GET['room_id'])){
					$room = $_GET['room_id'];
				}
			}
			$scbookings = "";
			$rooms = "";
			$room_att = "";
			if($room!= ""){
				$sql = "select * from ".$table_prefix."gen_scbooking where room_id like '%".$room."%'";
				$scbookings = $wpdb->get_results($sql);
				$sql_rooms = "select * from ".$table_prefix."posts p where p.post_status = 'publish' and p.post_type='gen_custom_booking' and p.id = ".$room." ";
				$rooms = $wpdb->get_results($sql_rooms);	
				$sqlroomatt = "select * from ".$table_prefix."postmeta where post_id=".$room;
				$room_att = $wpdb->get_results($sqlroomatt);
			}
			$output = '<div>';
			$image = '';
			$desc='';
			$noofbed = '';
			$bathroom = '';
			$price = '';
			$capacity = '';
			if($room_att != ""){ 
				foreach($room_att as $ratt){
					if($ratt->meta_key=='_room_image'){
						$image = $ratt->meta_value;
					}
					if($ratt->meta_key=='_room_description'){
						$desc = $ratt->meta_value;
					}
					if($ratt->meta_key=='_room_noofbed'){
						$noofbed = $ratt->meta_value;
					}
					if($ratt->meta_key=='_room_bathroom'){
						$bathroom = $ratt->meta_value;
					}
					if($ratt->meta_key=='_room_price'){
						$price = $ratt->meta_value;
					}
					if($ratt->meta_key=='_room_capacity'){
						$capacity = $ratt->meta_value;
					}
				}
			}
			$img_url = "";
			if($image == "" || $image == NULL){
				$img_url = GEN_SCBOOKING_PLUGIN_URL."/images/no-image.png";
			}
			else{
				$img_url = $image;
			}
			$cssfix_front = get_option('cssfix_front');
			$output .= '<style type="text/css">'.$cssfix_front.'</style>
					<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery("#optrooms").val('.$room.');
					});	
						function gen_submit_form(){
							var room = jQuery("#optrooms").val();
							var sel = jQuery("option[value=" + room + "]", jQuery("select[name=optrooms]") );
							if (sel.length > 0){
								sel.attr("selected", "selected");
							}
							var room = jQuery("select[name=optrooms] option:selected").text();
							jQuery("#frmrooms").submit();
						}
						function room_details(){
							var room = jQuery("select[name=optrooms] option:selected").val();
						}	
					</script>
					<link rel="stylesheet" href="'.GEN_SCBOOKING_PLUGIN_URL.'/assets/css/fullcalendar_shortcode.css">
					
					<div>';
					$post_title = "";
					if(isset($rooms[0])){
						if(count($rooms[0])){
							$post_title = $rooms[0]->post_title;
						}
					}
					$output .='
						<div><h4>'.$post_title.'</h4></div>
						<div style="float:left;width:40%">
							<img style="border:solid 1px #B8B8B8;" src='.$img_url.' width="300px" height="170px"> 
						</div>';
					if($bathroom != '' || $bathroom != NULL){
            $output .='<div style="float:left;width:55%;margin-left:15px;">
                <div>'.$desc.'</div><br>
                <div>No of Bed: '.$noofbed.'</div>
                <div>Bathroom: '.$bathroom.'</div>
                <div>Price: '.$price.' / Day</div>
                <div>Room Capacity: '.$capacity.' Person</div>
              </div>';
          }
          
					$output .='</div>
					
					<div style="clear:both;padding-top:20px;">
							<div style="float:left;">Rooms: </div>
							<div style="float:left;">
								<form id="frmrooms" method="post">
									<select id="optrooms" name="optrooms" onchange="gen_submit_form()">';
										$sql_room = "select * from ".$table_prefix."posts p inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and p.post_type='gen_custom_booking' and pm.meta_key='_room_price'";
										$rooms = $wpdb->get_results($sql_room);	
										foreach($rooms as $room){
										
										$output .= '<option value="'.$room->ID.'">'.$room->post_title.'</option>';
										}
									$output .='</select>
									<!-- <a id="room_id" onclick="javascript:room_details();" style="cursor:pointer;" >room details</a> -->
								</form>
							</div>
							<div style="clear:both;padding-top:20px;"></div>
							<div id="calendar"></div>
							<div style="clear:both"></div>
					</div>
				
					<script type="text/javascript">';
					$output .= "					
					function gen_set_room_cookie(){
							var room = jQuery('select[name=optrooms] option:selected').val();
							jQuery.ajax({
									type: 'POST',
                  url:'".admin_url( 'admin-ajax.php' )."?calltype=setcookie_front',  
									data: {
                    action: 'gen_set_ajax_room_cookie',
                    room: room
                  },
									success: function (data) {
										console.log(data);
									},
									error : function(s , i , error){
										console.log(error);
									}
							});
					}
					function gen_generate_calendar(){
						jQuery('#calendar').fullCalendar({
							header: {
								left: 'prev,next today',
								center: 'title',
								right: 'month,agendaWeek,agendaDay'
							},
							theme:true,
							selectable: true,
							selectHelper: true,
							editable: true,
							dayClick: function(date, allDay, jsEvent, view) {
									 jQuery('#dtpfromdate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
									 jQuery('#dtptodate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
									 jQuery('#addbooking_front_dialog').dialog('open');
							},
							events: [";
							if($scbookings != ""){
								foreach($scbookings as $booking){
								$output .="
									{
										id: '".$booking->booking_id."',
										title: 'Room:".$booking->room.", Guest Type:".$booking->guest_type."',
										start: '".$booking->from_date."',
										end: '".$booking->to_date."',
										backgroundColor : '#ED5B45',
										editable: true
									},";
								}
							}	
							$output .="]
						});
					}
					jQuery(document).ready(function() {
							jQuery('.entry-header').remove();
							gen_set_room_cookie();
							gen_generate_calendar();
							";
							$optrooms_val = "";
							if(isset($_REQUEST['optrooms'])){ 
								$optrooms_val = trim($_REQUEST['optrooms']);
							}
							$output .="
							jQuery('#optrooms').val(".$optrooms_val.");
							jQuery('#addbooking_front_dialog').dialog({
									autoOpen: false,
									height: 550,
									width: 530,
									modal: true,
									buttons: {
											'Add Booking': function () {
													if(gen_save_booking()){
														jQuery(this).dialog('close');
													}
													else{
													}
											},
											Cancel: function () {
													jQuery(this).dialog('close');
													gen_cleardata();
											}
									},
									close: function () {
										gen_cleardata();
									}
					
							});
							
						});
					</script>";
					include_once('add_booking_front_popup.php');	
			return $output;		
		/*}
		else{
			return "<div style='color:#C30909;'>Please login to access this page.</div>";
		}*/
	}
	add_shortcode('gen_sccalendar','gen_sccalendar_shortcode');