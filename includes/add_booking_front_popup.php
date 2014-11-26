<?php
	global $table_prefix,$wpdb;
	$sql_paymentmethod = "select * from ".$table_prefix."gen_scbooking_paymentmethods";
	$payment_methods = $wpdb->get_results( $sql_paymentmethod );
	$current_user = wp_get_current_user();

  $output .="
  <script type='text/javascript'>
  jQuery(function() {
		jQuery( '#dtpfromdate' ).datepicker({ dateFormat: 'yy-mm-dd' });
		jQuery( '#dtptodate' ).datepicker({ dateFormat: 'yy-mm-dd' });
	});
  
	function gen_getUrlVars()
	{
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++)
			{
					hash = hashes[i].split('=');
					vars.push(hash[0]);
					vars[hash[0]] = hash[1];
			}
			return vars;
	}
	function gen_get_roomprice(){
			var arr_rooms = new Array();
			var fromdate = jQuery('#dtpfromdate').val();
			var todate = jQuery('#dtptodate').val();
			var ull = jQuery('#multi_rooms_select ul');
			var slis = jQuery('li.selected', ull);
			slis.each(function(i){
				var sli = jQuery(this).children().children();
				arr_rooms[i] = sli.attr('value');
			});
			jQuery.ajax({
					type: 'POST',
          url: '".admin_url( 'admin-ajax.php' )."',  
					data: {
            action:'gen_get_roomprice_by_custompost',
            post_ids_arr : arr_rooms,
            from_date: fromdate,
            to_date: todate
          },
					success: function (data) {
						 jQuery('#txtCustomPrice').val(data);
					},
					complete: function (data){
						gen_calculate_due();
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
	}
	function gen_setbooking_info(booking_id){
			jQuery.ajax({
					type: 'POST',
          url: '".admin_url( 'admin-ajax.php' )."',
					dataType:'json', 
					data: {
            action:'gen_get_bookings',
            booking_id:booking_id
          },
					success: function (data) {
							var count = data.length;
							if(data.length > 0 ){
								var booking = data[0];
								jQuery('.hdnbookingidcls').val(booking['booking_id']);
								var roomids = booking['room_id'].split(',');
								jQuery('#rooms_multiselect').multiselect('select',roomids);
					
								jQuery('#dtpfromdate').val(booking['from_date']);
								jQuery('#dtptodate').val(booking['to_date']);
								
								jQuery('#txtFirstName').val(booking['first_name']);
								jQuery('#txtLastName').val(booking['last_name']);
								jQuery('#txtEmail').val(booking['email']);
								jQuery('#txtPhone').val(booking['phone']);
								jQuery('#details').val(booking['details']);
								jQuery('#txtbookingby').val(booking['booking_by']);
								jQuery('#optguest_type').val(booking['guest_type']);
								jQuery('#txtCustomPrice').val(booking['custom_price']);
								jQuery('#txtPaid').val(booking['paid']);
								jQuery('#txtDue').val(booking['due']);
								jQuery('#optpaymentmethod').val(booking['payment_method']);
								jQuery('#txtTrackingNo').val(booking['tracking_no']);
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
			
			
	}
	function gen_cleardata(){
			jQuery('#hdnbookingid').val('');
			jQuery('option', jQuery('#rooms_multiselect')).each(function(element) {
					jQuery(this).removeAttr('selected').prop('selected', false);
			});
			jQuery('select.multiselect').multipleSelect('refresh');
			
			jQuery('#dtpfromdate').val('');
			jQuery('#dtptodate').val('');
			
			jQuery('#txtFirstName').val('');
			jQuery('#txtLastName').val('');
			jQuery('#txtEmail').val('');
			jQuery('#txtPhone').val('');
			jQuery('#details').val('');
			jQuery('#txtbookingby').val('".$current_user->display_name."');
			jQuery('#optguest_type').val('');
			jQuery('#txtCustomPrice').val('');
			jQuery('#txtPaid').val('');
			jQuery('#txtDue').val('');
			jQuery('#txtTrackingNo').val('');
	}
	function gen_load_moredeals_data_pagerefresh(page){
			jQuery.ajax
			({
					type: 'POST',
          url: '".admin_url( 'admin-ajax.php' )."',
					data: {
          action: 'gen_load_managebooking_data_front',
          page: page
          },
					success: function(msg)
					{
							jQuery('#inner_content').ajaxComplete(function(event, request, settings)
							{
									jQuery('#inner_content').html(msg);
							});
					}
					
			});
	}
	jQuery(document).ready(function(){
			jQuery('.multiselect').multipleSelect({
				placeholder: 'Please select Room',
				selectAll: false,
				width:'74%',
				onClick: function(view){
					gen_get_roomprice();
				}
			});

			var calltype = gen_getUrlVars()['calltype'];
			if(calltype){
				if(calltype = 'editbooking'){";
					if(isset($_REQUEST['id'])){
						$id = $_REQUEST['id'];
						global $table_prefix,$wpdb;
						$sql = "select * from ".$table_prefix."gen_scbooking where booking_id=".$id;
						$result = $wpdb->get_results($sql);
						$output .="var booking = ".json_encode($result[0]).";
						jQuery('#hdnbookingid').val(booking['booking_id']);
						var roomids = booking['room_id'].split(',');
						jQuery('select.multiselect').multipleSelect('setSelects', roomids);
						
						jQuery('#dtpfromdate').val(booking['from_date']);
						jQuery('#dtptodate').val(booking['to_date']);
						
						jQuery('#txtFirstName').val(booking['first_name']);
						jQuery('#txtLastName').val(booking['last_name']);
						jQuery('#txtEmail').val(booking['email']);
						jQuery('#txtPhone').val(booking['phone']);
						jQuery('#details').val(booking['details']);
						jQuery('#txtbookingby').val(booking['booking_by']);
						jQuery('#optguest_type').val(booking['guest_type']);
						jQuery('#txtCustomPrice').val(booking['custom_price']);
						jQuery('#txtPaid').val(booking['paid']);
						jQuery('#txtDue').val(booking['due']);
						jQuery('#optpaymentmethod').val(booking['payment_method']);
						jQuery('#txtTrackingNo').val(booking['tracking_no']);
						";
					}
					$output .="
				}	
			}
			
			jQuery('#dtpfromdate').on('change',function(){
				gen_get_roomprice();
			});
			jQuery('#dtptodate').on('change',function(){
				gen_get_roomprice();
			});
			jQuery('#frmbooking').on('submit',function(e){
	  		 	e.preventDefault();
				 gen_save_booking();
			});";
			if(isset($_REQUEST['calendarcell'])){
				$calendarcell = $_REQUEST['calendarcell'];
				$calendarcell_data = explode("|",$calendarcell);
				$cell_month_cat = $calendarcell_data[0];
				$cell_month = $calendarcell_data[1];
				$cell_date =  $calendarcell_data[2];
				$output .="jQuery('#dtptodate').val('".$cell_date."');";
			}
	$output .= "});
	function gen_save_booking(){
			var hdnbookingid = jQuery('.hdnbookingidcls').val();
			var roomtype = '';
			var roomsarr = jQuery('select.multiselect').multipleSelect('getSelects', 'text');
			var rooms= '';
			for(var j=0;j<roomsarr.length;j++){
				if(j==0){
					rooms += roomsarr[j];
				}else{
					rooms += ','+roomsarr[j];
				}
			}
			var arr_ids = new Array();
			var room_id = '';
			var ull = jQuery('#multi_rooms_select ul');
			var slis = jQuery('li.selected', ull);
			slis.each(function(i){
			    var sli = jQuery(this).children().children();
	     		arr_ids[i] = sli.attr('value');
				if(i==0){
					room_id += sli.attr('value');	
				}else{
					room_id += ','+sli.attr('value');
				}
			});
			
			var from_date = jQuery('#dtpfromdate').val();
			var to_date = jQuery('#dtptodate').val();
			
			var first_name = jQuery('#txtFirstName').val();
			var last_name = jQuery('#txtLastName').val();
			var email = jQuery('#txtEmail').val();
			var phone = jQuery('#txtPhone').val();
			var details = jQuery('#details').val();
			var bookingby = jQuery('#txtbookingby').val();
			var guest_type = jQuery('#optguest_type').val();
			var price = jQuery('#txtCustomPrice').val();
			var paid = jQuery('#txtPaid').val();
			var due = jQuery('#txtDue').val();
			var payment_method = jQuery('#optpaymentmethod').find('option:selected').val();
			var tracking_no = jQuery('#txtTrackingNo').val();
	
			if(rooms == 'Please select Room'){
				alert('Please choose at Least a Room .');
				return false;
			}
			else if(from_date==''){
				alert('Please choose a from date.');
				return false;
			}
			else if(to_date==''){
				alert('Please choose a to date.');
				return false;
			}
			else if(email!=''){
				if(!gen_validateEmail(email)){
					alert('Please input a valid email Address.');
					return false;
				}
			}
			else if(phone==''){
				alert('please input your phone number.');
				return false;
			}
			else if(paid == ''){
				alert('Please input paid amount.');
				return false;
			}
			jQuery.ajax({
					type: 'POST',
          url: '".admin_url( 'admin-ajax.php' )."',  
					data: {
            action:'gen_check_booking',
            hdnbookingid: hdnbookingid,
            room: rooms,
            from_date:from_date,
            to_date:to_date
          },
					success: function (data) {
							data = data.trim();
							if(data=='yes'){
								alert('Sorry! Already Booked!');
								return;
							}
							else if(data=='no'){
 								jQuery.ajax({
											type: 'POST',
                      url: '".admin_url( 'admin-ajax.php' )."',  
											data: {
                        action:'gen_save_booking',
                        hdnbookingid: hdnbookingid,
                        room_type:roomtype,
                        roomid: room_id, 
                        room: rooms,
                        from_date:from_date,
                        to_date:to_date,
                        first_name:first_name,
                        last_name:last_name,
                        email:email,
                        phone:phone,
                        details: details,
                        bookingby: bookingby, 
                        guest_type: guest_type, 
                        price: price,
                        paid: paid,
                        due: due, 
                        payment_method: payment_method, 
                        tracking_no: tracking_no 
                      },
											success: function (data) {
													if(data.length>0){
														alert('added successfully');
														jQuery('#frmrooms').submit();
														jQuery('#frmmanagebookingdata').submit();
													}
											},
											error : function(s , i , error){
													console.log(error);
											}
									});
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
			
	}
	function gen_validateEmail(email) {
			var atpos=email.indexOf('@');
			var dotpos=email.lastIndexOf('.');
			if (atpos < 1 || dotpos < atpos+2 || dotpos+2 >= email.length) {
					return false;
			}
			return true;
	}

	function gen_calculate_due(){
		var price = jQuery('#txtCustomPrice').val();
		var paid = jQuery('#txtPaid').val();
		var due = (price - paid);
		jQuery('#txtDue').val(due); 
	}
  </script>
  ";
  $current_user = wp_get_current_user();
  $output .="
 <div id='addbooking_front_dialog' title='Add/Edit Booking' class='wrapper' style='display:none;z-index:5000'>
  <div class='wrap' style='float:left; width:100%;'>
    <div class='main_div'>
     	<div class='metabox-holder' style='width:49%; float:left;'>
        <form id='frmbooking' action='' method='post' style='width:100%'>
          <table >
          	<tr>
            	<td class='bookinglavel'> <label for='room'>Room </label></td>
              <td class='bookinginput' id='multi_rooms_select'>
              	<select id='rooms_multiselect' class='multiselect' multiple='multiple' >";
								
				$sql_room = "select * from ".$table_prefix."posts p inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and p.post_type='gen_custom_booking' and pm.meta_key='_room_price'";
				$rooms = $wpdb->get_results($sql_room);	
				foreach($rooms as $room){
                  	$output .="<option value='".$room->ID."'>".$room->post_title."</option>";
                }
                $output .='</select><span style="color:red;">*</span>
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="from date">From Date:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="dtpfromdate" name="dtpfromdate" class="rounded" value="" style="width:230px;" /><span style="color:red;">*</span>
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="to date">To Date:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="dtptodate" name="dtptodate" value="" class="rounded" style="width:230px;" /><span style="color:red;">*</span>
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="first name">First Name:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="txtFirstName" name="txtFirstName" class="rounded" value="" />
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="last name">Last Name:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="txtLastName" name="txtLastName" class="rounded" value="" />
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="email">Email:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="txtEmail" name="txtEmail"  class="rounded" value="" /><!--<span style="color:red;">*</span>-->
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="phone">Phone:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="txtPhone" name="txtPhone" class="rounded" value="" /><span style="color:red;">*</span>
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="details">Details:</label>
              </td>
              <td class="bookinginput">
              	<textarea cols="10" rows="8" id="details" class="rounded" name="details"></textarea>
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="Booking By">Booking By:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" readonly="readonly" id="txtbookingby" name="txtbookingby" class="rounded" value="'.$current_user->display_name.'" />
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="Guest Type">Guest Type:</label>
              </td>
              <td class="bookinginput">
                <select id="optguest_type" name="optguest_type" >
                    <option value="single">Single</option>
                    <option value="business">Business</option>
                    <option value="couple">Couple</option>
                    <option value="group_of_adults">Group of Adults</option>
                    <option value="family_with_kids">Family with Kids</option>
                </select>
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="price">Price:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="txtCustomPrice" name="txtCustomPrice" class="rounded" value="" />
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="paid">Paid:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="txtPaid" name="txtPaid" class="rounded" onkeyup="gen_calculate_due()" value="" /><span style="color:red;">*</span>
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="due">Due:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="txtDue" name="txtDue" class="rounded" value="" />
                <input type="hidden" class="hdnbookingidcls" id="hdnbookingid" name="hdnbookingid" value="" style="width:150px;"/>
              </td>
            </tr>
             <tr>
            	<td class="bookinglavel">
              	<label for="payment method">Payment Method:</label>
              </td>
              <td class="bookinginput">
              	<select id="optpaymentmethod" name="optpaymentmethod" >
                	';
					foreach($payment_methods as $pm){
                  	$output .= '<option value="'.$pm->payment_method.'">'.$pm->payment_method.'</option>';
                  }
                $output .='</select>
              </td>
            </tr>
            <tr>
            	<td class="bookinglavel">
              	<label for="Receipt No">Receipt/ Tracking No:</label>
              </td>
              <td class="bookinginput">
              	<input type="text" id="txtTrackingNo" name="txtTrackingNo" value="" />
              </td>
            </tr>
          </table>
          </form>
          
    	</div>
      </div>
    </div>
   </div>
 ';