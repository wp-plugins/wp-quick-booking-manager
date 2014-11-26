<?php
   global $table_prefix,$wpdb;
   $sql_taxonomy = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."terms t on tt.term_id = t.term_id where tt.taxonomy = 'sc_custom_category'";
   $taxonomies = $wpdb->get_results( $sql_taxonomy );
	
   $sql_paymentmethod = "select * from ".$table_prefix."gen_scbooking_paymentmethods";
   $payment_methods = $wpdb->get_results( $sql_paymentmethod );
  ?>
  <script type="text/javascript">
  jQuery(function() {
    jQuery( "#dtpfromdate" ).datepicker({ dateFormat: "yy-mm-dd" });
		jQuery( "#dtptodate" ).datepicker({ dateFormat: "yy-mm-dd" });
  });
	// Read a page's GET URL variables and return them as an associative array.
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
	//
	function gen_get_rooms_for_bookingcell(roomid){
		  var term_id = jQuery('#roomtype').val();
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					dataType:'json', 
					data: {
            action: 'gen_get_room_bycat',
            term_id:term_id
          },
					success: function (data) {
							var count = data.length;
							jQuery('#optroom').empty();
							if(data.length > 0 ){
								for(var i=0;i<data.length;i++){
										if(i==0){
											jQuery('#optroom').append('<option value="'+data[i]['ID']+'" selected="selected">'+data[i]['post_title']+'</option>');
										}
										else{
											jQuery('#optroom').append('<option value="'+data[i]['ID']+'">'+data[i]['post_title']+'</option>');
										}
								}
								gen_get_roomprice();
							}
							else{
								jQuery('#optroom').empty();
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			}).done(function(msg){
					jQuery('#optroom').val(roomid);
			});
	}
	function gen_get_rooms_for_editbooking(roomid){
		  var term_id = jQuery('#roomtype').val();
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					dataType:'json', 
					data: {
            action:'gen_get_room_bycat',
            term_id:term_id
          },
					success: function (data) {
							var count = data.length;
							jQuery('#optroom').empty();
							if(data.length > 0 ){
								for(var i=0;i<data.length;i++){
										if(i==0){
											jQuery('#optroom').append('<option value="'+data[i]['ID']+'" selected="selected">'+data[i]['post_title']+'</option>');
										}
										else{
											jQuery('#optroom').append('<option value="'+data[i]['ID']+'">'+data[i]['post_title']+'</option>');
										}
								}
								gen_get_roomprice();
							}
							else{
								jQuery('#optroom').empty();
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			}).done(function(msg){
					jQuery('#optroom').val(roomid);
			});
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
					type: "POST",
					url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					data: {
            action: 'gen_get_roomprice_by_custompost',
            post_ids_arr : arr_rooms ,
            from_date: fromdate,
            to_date: todate
          },
					success: function (data) {
							var count = data.length;
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
	jQuery(document).ready(function(){
			jQuery('.multiselect').multipleSelect({
				placeholder: 'Please select Room',
				selectAll: false,
				//filter: true,
				width:'40%',
				onClick: function(view){
					gen_get_roomprice();
				}
			});
			//-------------------------------------------------
			var calltype = gen_getUrlVars()["calltype"];
			if(calltype){
				if(calltype == 'editbooking'){
					//alert('inside 1');
					<?php
					$id = "";
					if(isset($_REQUEST['id'])){
						$id = $_REQUEST['id'];	
					
						global $table_prefix,$wpdb;
						$sql = "select * from ".$table_prefix."gen_scbooking where booking_id=".$id;
						$result = $wpdb->get_results($sql);
						?>
						var booking = <?php if(count($result)) echo json_encode($result[0]);?>;
						jQuery('#hdnbookingid').val(booking['booking_id']);
						jQuery('#roomtype').val(booking['room_type']);
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
					
					<?php }	?>
				}	
			}
			
			jQuery('#dtpfromdate').on("change",function(){
				gen_get_roomprice();
				//calculate_due();
			});
			jQuery('#dtptodate').on("change",function(){
				gen_get_roomprice();
				//calculate_due();
			});
			//----save booking----
			jQuery('#frmbooking').on('submit',function(e){
	  		 e.preventDefault();
				 gen_save_booking();
			});
			//---------------------------
			<?php if(isset($_REQUEST['calendarcell'])){
			$calendarcell = $_REQUEST['calendarcell'];
			$calendarcell_data = explode("|",$calendarcell);
			$cell_month_cat = $calendarcell_data[0];
			$cell_month = $calendarcell_data[1];
			$cell_date =  $calendarcell_data[2];
			?>
					jQuery("#rooms_multiselect").multiselect("select",<?php echo $cell_month;?>);
					gen_get_roomprice();
					jQuery('#roomtype').val(<?php echo $cell_month_cat;?>);
					gen_get_rooms_for_bookingcell(<?php echo $cell_month;?>);
					jQuery('#dtpfromdate').val('<?php echo $cell_date;?>');
					jQuery('#dtptodate').val('<?php echo $cell_date;?>');  
			<?php }?>
			//--------------------------------
	});
	function gen_save_booking(){
			var hdnbookingid = jQuery('#hdnbookingid').val();
			var roomtype='';
			var roomsarr = jQuery('select.multiselect').multipleSelect('getSelects', 'text');
			var rooms= "";
			for(var j=0;j<roomsarr.length;j++){
				if(j==0){
					rooms += roomsarr[j];
				}else{
					rooms += ","+roomsarr[j];
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
					room_id += ","+sli.attr('value');
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
			if(rooms == "Please select Room"){
				alert('Please choose at Least a Room .');
				return;
			}
			else if(from_date==""){
				alert('Please choose a from date.');
				return;
			}
			else if(to_date==""){
				alert('Please choose a to date.');
				return;
			}
			else if(email!=''){
				if(!gen_validateEmail(email)){
					alert('Please input a valid email Address.');
					return false;
				}
			}
			else if(phone==''){
				alert('please input your phone number.');
				return;
			}
			else if(paid == ''){
				alert('Please input paid amount.');
				return;
			}
			//---
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					data: {
            action: 'gen_check_booking',
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
											type: "POST",
                      url: '<?php echo admin_url( 'admin-ajax.php' );?>',
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
                        tracking_no: tracking_no },
											success: function (data) {
													if(data.length>0){
														alert('added successfully');
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
			var atpos=email.indexOf("@");
			var dotpos=email.lastIndexOf(".");
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
  
  <?php $current_user = wp_get_current_user();?>	  
  <div id="addbooking_backend" class="wrap">
    <h2>Hotel Booking</h2>
    <div class="metabox-holder" style="width:69%;">
       <div id="namediv" class="stuffbox" style="width:99%;">
        <h3 class="top_bar">Add Booking</h3>
        
        	<form id="frmbooking" action="" method="post" novalidate="novalidate">
            <table class="tbladdbooking" style="margin:10px;width:100%;">
                <tr>
                  <td class="bookinglavel">Room</td>
                  <td class="bookinginput" id="multi_rooms_select">
                    <select id="rooms_multiselect" class="multiselect" multiple="multiple" >
					  <?php 
                         $sql_room = "select * from ".$table_prefix."posts p inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and p.post_type='gen_custom_booking' and pm.meta_key='_room_price'";
                         $rooms = $wpdb->get_results($sql_room);	
                         foreach($rooms as $room){
                        ?>
                        <option value="<?php echo $room->ID;?>"><?php echo $room->post_title;?></option>
                        <?php } ?>
                    </select><span style="color:red;">*</span>
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    From Date:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="dtpfromdate" name="dtpfromdate" value="" style="width:230px;" /><span style="color:red;">*</span>
                  </td>
                </tr>
                <tr>
                    <td class="bookinglavel">
                    To Date:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="dtptodate" name="dtptodate" value="" style="width:230px;" /><span style="color:red;">*</span>
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    First Name:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtFirstName" name="txtFirstName" value="" />
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Last Name:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtLastName" name="txtLastName" value="" />
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Email:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtEmail" name="txtEmail" value="" /><!--<span style="color:red;">*</span>-->
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Phone:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtPhone" name="txtPhone" value="" /><span style="color:red;">*</span>
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Details:
                  </td>
                  <td class="bookinginput">
                    <textarea cols="53" rows="15" id="details" name="details"></textarea>
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Booking By:
                  </td>
                  <td class="bookinginput">
                    <input type="text" readonly="readonly" id="txtbookingby" name="txtbookingby" value="<?php echo $current_user->display_name; ?>" />
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Guest Type:
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
                    Price:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtCustomPrice" name="txtCustomPrice" value="" />
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Paid:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtPaid" name="txtPaid" onkeyup="gen_calculate_due()" value="" /><span style="color:red;">*</span>
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Due:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtDue" name="txtDue" value="" />
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Payment Method:
                  </td>
                  <td class="bookinginput">
                    <select id="optpaymentmethod" name="optpaymentmethod" >
                        <?php foreach($payment_methods as $pm){?>
                        <option value="<?php echo $pm->payment_method;?>"><?php echo $pm->payment_method;?></option>
                        <?php }?>  
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="bookinglavel">
                    Receipt/Tracking No:
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtTrackingNo" name="txtTrackingNo" value="" />
                  </td>
                </tr>
                <tr>
                  <td></td>
                  <td>
                  <input type="submit" id="btnaddbooking" name="btnaddbooking" value="Add Booking" style="width:150px;cursor: pointer;"/>
                  <input type="hidden" id="hdnbookingid" name="hdnbookingid" value="" style="width:150px;"/>
                  </td>
                </tr>
            </table>
        </form>
        
        </div>
    </div>
 </div>