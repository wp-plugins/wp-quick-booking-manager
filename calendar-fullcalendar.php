<?php 
  global $table_prefix,$wpdb;
  	$room = '';
  	if(isset($_SESSION['roomid'])){
		$room = $_SESSION['roomid'];
	}
	
	if($_POST){
		$room = $_REQUEST['optrooms'];
	}
	$sql = "select * from ".$table_prefix."gen_scbooking where room_id like '%".$room."%'";
	$scbookings = $wpdb->get_results($sql);
	?>
  <script type="text/javascript">
	function gen_submit_form(){
		var room = jQuery('#optrooms').val();
		var sel = jQuery("option[value=" + room + "]", jQuery("select[name=optrooms]") );
		if (sel.length > 0){
			sel.attr('selected', 'selected');
		}
		var room = jQuery("select[name=optrooms] option:selected").text();
		jQuery('#frmrooms').submit();
	}
  </script>
  <link rel="stylesheet" href="<?php echo GEN_SCBOOKING_PLUGIN_URL; ?>/assets/css/calendar-fullcalendar.css">
  
  <div style="height:auto;">
      <div id="icon-options-general" class="icon32">
      </div>
      <h2 style="padding-top:10px;">Booking Calendar</h2>
      <div style="height:15px;"></div>
      <div style="padding-left:30px;">
        <div style="float:left;">Rooms: </div>
        <div style="float:left;">
        <form id="frmrooms" method="post">
          <select id="optrooms" name="optrooms" onchange="gen_submit_form()">
           <?php 
			$sql_room = "select * from ".$table_prefix."posts p inner join ".$table_prefix."postmeta pm on pm.post_id = p.id where p.post_status = 'publish' and p.post_type='gen_custom_booking' and pm.meta_key='_room_price'";
            $rooms = $wpdb->get_results($sql_room);
				
            foreach($rooms as $room){
            ?>
            <option value="<?php echo $room->ID;?>"><?php echo $room->post_title;?></option>
            <?php } ?>
          </select>
          
        </form>
        </div>
        <div id='calendar' style="clear:both;padding-top:15px;"></div>
      </div>
      <?php include_once('includes/add_booking.php');?>
            
  </div>

  <script type='text/javascript'>
	function gen_get_bookings(){
		var room = jQuery('#optrooms')
		jQuery.ajax({
				type: "POST",
        url: '<?php echo admin_url( 'admin-ajax.php' );?>',
				data: {
          action: 'gen_get_bookings_by_room',  
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
				left: 'prev, next today, agenda',
				center: 'title',
				right: 'month, agendaWeek, agendaDay'
			},
			theme:true,
			selectable: true,
			selectHelper: true,
			editable: true,
			dayClick: function(date, allDay, jsEvent, view) {
					 jQuery('#dtpfromdate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
					 jQuery('#dtptodate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
					 jQuery(this).css('cursor', 'pointer');
					 jQuery("#addbooking_back_dialog").dialog("open");
			},
			events: [
			<?php  foreach($scbookings as $booking){ ?>
				{
					id: <?php echo $booking->booking_id;?>,
					title: '<?php echo $booking->room.", ".$booking->guest_type;?>',
					start: '<?php echo $booking->from_date;?>',
					end: '<?php echo $booking->to_date;?>',
					backgroundColor : '#ED5B45',
					editable: true
				},
			  <?php } ?>	
			],
			eventColor: '#F05133'
		});
	}
	function gen_set_room_cookie(){
			var room = jQuery("select[name=optrooms] option:selected").val();
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>?calltype=setcookie',
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
	jQuery(document).ready(function() {
		gen_set_room_cookie();
		gen_generate_calendar();
		jQuery('#optrooms').val(<?php if(isset($_REQUEST['optrooms'])) echo $_REQUEST['optrooms'];?>);
		
		jQuery("#addbooking_back_dialog").dialog({
					autoOpen: false,
					height: 550,
					width: 530,
					modal: true,
					buttons: {
							'Add Booking': function () {
									if(gen_save_booking()){
										jQuery(this).dialog("close");
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
	
 </script>