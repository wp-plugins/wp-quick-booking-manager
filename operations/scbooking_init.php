<?php
function gen_scbooking_install() {
	global $table_prefix, $wpdb;
  	$table_name1 = $table_prefix.'gen_scbooking';
  	$sql_sfe = "CREATE TABLE IF NOT EXISTS  $table_name1(
							`booking_id` int(11) NOT NULL AUTO_INCREMENT,
							`room_type` varchar(255) DEFAULT NULL,
							`room_id` int(11) DEFAULT NULL,
							`room` varchar(255) DEFAULT NULL,
							`from_date` date DEFAULT NULL,
							`to_date` date DEFAULT NULL,
							`first_name` varchar(255) DEFAULT NULL,
							`last_name` varchar(255) DEFAULT NULL,
							`email` varchar(255) DEFAULT NULL,
							`phone` varchar(20) DEFAULT NULL,
							`details` text,
							`booking_by` varchar(255) DEFAULT NULL,
							`guest_type` varchar(255) DEFAULT NULL,
							`custom_price` decimal(10,2) DEFAULT NULL,
							`paid` decimal(10,2) DEFAULT NULL,
							`due` decimal(10,2) DEFAULT NULL,
							`payment_method` varchar(255) DEFAULT NULL,
							`tracking_no` varchar(255) DEFAULT NULL,
							`confirmed` int(1) DEFAULT '0',
							`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
							PRIMARY KEY (`booking_id`)
						) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
						";

   	$wpdb->query($sql_sfe);

   	$table_name2 = $table_prefix.'gen_scbooking_paymentmethods';
   	$sql_scpm = "CREATE TABLE IF NOT EXISTS  $table_name2(
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `payment_method` varchar(255) DEFAULT NULL,
							 PRIMARY KEY (`id`)
							) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";

     $wpdb->query($sql_scpm);
	 
	 $pm_count = $wpdb->get_results("SELECT COUNT(*) as pm_rowcount FROM ".$table_prefix."gen_scbooking_paymentmethods");
	 if(!$pm_count[0]->pm_rowcount){
		 $sql_pm_data = "INSERT INTO `".$table_prefix."gen_scbooking_paymentmethods` (`id`,`payment_method`) VALUES (1,'bKash'),(2,'Bank'),(3,'Cash'),(4,'Paypal'),(5,'Credit Card'), (6,'Other');";
	
		 $wpdb->query($sql_pm_data);
	 }
	 /*  Page creation for Shopping Cart and Checkout Page */
	 $genbooking_calendar_page_id = gen_programmatically_create_page('Booking Calendar','gen-booking-calendar','[gen_sccalendar]','page');
	 $gen_rooms_id = gen_programmatically_create_page('Rooms','gen-rooms','[gen_roomsgallery]','page');
}