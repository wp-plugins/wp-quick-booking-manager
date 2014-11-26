<?php global $table_prefix,$wpdb;
	$sql = "select * from ".$table_prefix."gen_scbooking";
	$bookings = $wpdb->get_results($sql);
	?>
  <script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#btnsearchbooking').on('click',function(){
				var searchtext = jQuery('#txtsearchbooking').val();
				jQuery.ajax
				({
						type: "POST",
            url: '<?php echo admin_url( 'admin-ajax.php' );?>',
						data: {
              action: 'gen_search_booking',
              searchtext: searchtext
            },
						success: function(data)
						{
						},
						error : function(s , i , error){
								console.log(error);
						}
				}).done(function(data){
					data = data.trim();
					gen_loading_hide();
					jQuery("#inner_content").html(data);
				});
				
				
		});
		
		gen_load_moredeals_data(1);
    /* ---------pagination script ---------------- */
		function gen_load_moredeals_data(page){
				gen_loading_show();                    
				jQuery.ajax
				({
						type: "POST",
            url: '<?php echo admin_url( 'admin-ajax.php' );?>',
						data: {
              action:'gen_load_managebooking_data',
              page: page
            },
						success: function(msg)
						{
            
						}
				}).done(function(msg){
					gen_loading_hide();
					jQuery("#inner_content").html(msg);
				});
		}
		function gen_loading_show(){
				jQuery('#loading').html("<img src='<?php echo GEN_SCBOOKING_PLUGIN_URL; ?>/images/loading.gif'/>").fadeIn('fast');
		}
		function gen_loading_hide(){
				jQuery('#loading').fadeOut('fast');
		}                
		jQuery('#inner_content').delegate('.pagination li.active','click',function(){
				var page = jQuery(this).attr('p');
				gen_load_moredeals_data(page);
				jQuery('html, body').animate({
						scrollTop: jQuery("#content_top").offset().top
				}, 1950);
				
		});           
		jQuery('#inner_content').delegate("#go_btn",'click',function(){
				var page = parseInt(jQuery('.goto').val());
				var no_of_pages = parseInt(jQuery('.total').attr('a'));
				if(page != 0 && page <= no_of_pages){
						gen_load_moredeals_data(page);
						jQuery('html, body').animate({
								scrollTop: jQuery("#content_top").offset().top
						}, 2050);
				}else{
						alert('Enter a PAGE between 1 and '+no_of_pages);
						jQuery('.goto').val("").focus();
						return false;
				}
				
		});
		//=========================== End pagination Script =====================================
		jQuery('#inner_content').delegate("#lnkapprove",'click',function(e){
			e.preventDefault();
			var bookingid = jQuery(this).parent().children('#hdnbookingid').val();
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					data: {
            action: 'gen_activate_booking',
            booking_id:bookingid
          },
					success: function (data) {
							var count = data.length;
							if(count>0){
								alert('Booking Activated');
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
			
		});	
		
		jQuery('#inner_content').delegate("#delete_booking",'click',function(e){
			e.preventDefault();
      if(!confirm('Are you sure want to delete')){
        return false;
      }
			var bookingid = jQuery(this).parent().children('#hdnbookingid').val();
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' )?>',
					data: {
            action: 'gen_delete_booking',
            booking_id:bookingid
          },
					success: function (data) {
							var count = data.length;
							if(count>0){
								alert('Booking Deleted');
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
			console.log(jQuery(this).parent().parent().remove());
		});
	});
	
</script>
 <style type="text/css">
	#btnsearchbooking{
		background:url('<?php echo GEN_SCBOOKING_PLUGIN_URL ?>/images/search.png') no-repeat;
		width: 30px; 
		height: 30px; 
		cursor:pointer;
	}
	.date{
		width:40px;
	}
</style>
<div class="wrapper">
  <div class="wrap" style="float:left; width:100%;">
    <div id="icon-options-general" class="icon32"></div>
    <div style="width:70%;float:left;"><h2>Hotel Booking</h2></div>
    <div style="width:29%;float:left;margin-top:15px;">
    	<form id="frmsearchb" method="post" action="">
            <input type="text" name="txtsearchbooking" id="txtsearchbooking" value="" style="width:250px;height:40px;" />
            <input type="button" id="btnsearchbooking" name="btnsearchbooking" value="" />
        </form>
    </div>
    
    <div class="main_div">
     	<div class="metabox-holder" style="width:98%; float:left;">
            <div id="namediv" class="stuffbox" style="width:99%;">
                <h3 class="top_bar">Manage Booking</h3>
                <div id="inner_content">		
                    <div class="data"></div>
                    <div class="pagination"></div>			
                    <table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
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
                      <tbody>
                      <?php
                      foreach($bookings as $booking){
                      ?>
                        <tr class="alternate">
                            <td><?php echo $booking->room;?></td>
                            <td><?php echo $booking->from_date;?></td>
                            <td><?php echo $booking->to_date;?></td>
                            <td><?php echo $booking->email;?></td>
                            <td><?php echo $booking->phone;?></td>
                            <td>
                              <a href="<?php echo get_option('siteurl');?>/wp-admin/edit.php?post_type=gen_custom_booking&page=add-hotel-booking-menu&calltype=editbooking&id=<?php echo $booking->booking_id;?>">edit</a>|
                              <a id="delete_booking" href="#" onclick="return confirm('Are you sure want to delete');">delete</a>
                              <input type="hidden" id="hdnbookingid"  name="hdnbookingid" value="<?php echo $booking->booking_id;?>" />
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                      </tbody>  
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
                    </table>
                </div>
            </div>
		</div>
	</div>
     
  </div>
</div>
  
<div id='loading'></div>
