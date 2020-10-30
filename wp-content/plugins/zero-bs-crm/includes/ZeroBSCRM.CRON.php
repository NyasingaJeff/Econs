<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V1.2.3
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 15/12/16
 */

/*

	To add to cron:

		1) Add to list (#1)
		2) Add func x 2 (#2)


	To see what cron is enabled:
	http://wordpress.stackexchange.com/questions/98032/is-there-a-quick-way-to-view-the-wp-cron-schedule
  <?php 

    $cron_jobs = get_option( 'cron' );
    print_r($cron_jobs);

  ?>
	

*/

/* ======================================================
	Wrapper Arr (lists of cron to add)
   ====================================================== */

	 global 	$zbscrm_CRONList; 
	 
   			$zbscrm_CRONList = array(

   				##WLREMOVE
   				'tele' => 'daily',
   				'ext' => 'daily',
   				##/WLREMOVE

   				# use alpha, will be lower-cased for hook
   				// v3.0+ we do away with this: 'clearAutoDrafts' => 'hourly',
   				'notifyEvents' => 'hourly',
   				//'clearTempHashes' => 'hourly'
   				'clearSecLogs' => 'daily',

   			);



/* ======================================================
	/Wrapper Arr (lists of cron to add)
   ====================================================== */


/* ======================================================
	Add Jetpack CRM Custom schedule (5m)
	// https://wordpress.stackexchange.com/questions/208135/how-to-run-a-function-every-5-minutes
   ====================================================== */
	function zeroBSCRM_cronSchedules($schedules){
	    if(!isset($schedules["5min"])){
	        $schedules["5min"] = array(
	            'interval' => 5*60,
	            'display' => __('Once every 5 minutes'));
	    }
	    return $schedules;
	}
	add_filter('cron_schedules','zeroBSCRM_cronSchedules');
/* ======================================================
	/Add Jetpack CRM Custom schedule (5m)
   ====================================================== */


/* ======================================================
	Scheduler Funcs
   ====================================================== */
function zeroBSCRM_activateCrons(){


	global $zbscrm_CRONList; 
	foreach ($zbscrm_CRONList as $cronName => $timingStr)	{
		
		$hook = 'zbs'.strtolower($cronName);
		$funcName = 'zeroBSCRM_cron_'.$cronName;
		
	    if (! wp_next_scheduled ( $hook )) {
				wp_schedule_event(time(), $timingStr, $hook);
	    }

	}

}
register_activation_hook(ZBS_ROOTFILE, 'zeroBSCRM_activateCrons');
function zeroBSCRM_deactivateCrons(){

	global $zbscrm_CRONList; 
	foreach ($zbscrm_CRONList as $cronName)	{
		
		$hook = 'zbs'.strtolower($cronName);
		$funcName = 'zeroBSCRM_cron_'.$cronName;

		wp_clear_scheduled_hook($hook);

	}

}
register_deactivation_hook(ZBS_ROOTFILE, 'zeroBSCRM_deactivateCrons');
/* ======================================================
	/ Scheduler Funcs
   ====================================================== */





/* ======================================================
	Actual Action Funcs #2
   ====================================================== */

   # ======= Clear Auto-drafts
	function zeroBSCRM_cron_clearAutoDrafts() {

		#} Simple
		zeroBSCRM_clearCPTAutoDrafts();

	}

	add_action('zbsclearautodrafts', 'zeroBSCRM_cron_clearAutoDrafts');


	function zeroBSCRM_cron_notifyEvents() {

		#} Simple
		zeroBSCRM_notifyEvents();

	}

	add_action('zbsnotifyevents', 'zeroBSCRM_cron_notifyEvents');


   # ======= Clear temporary hashes
	/* function zeroBSCRM_cron_clearTempHashes() {

		#} Simple
		zeroBSCRM_clearTemporaryHashes();

	}

	add_action('zbscleartemphashes', 'zeroBSCRM_cron_clearTempHashes'); */

   # ======= Clear security logs (from easy-pay hash requests) *after 72h
	function zeroBSCRM_cron_clearSecLogs() {

		#} Simple
		zeroBSCRM_clearSecurityLogs();

	}

	add_action('zbsclearseclogs', 'zeroBSCRM_cron_clearSecLogs'); 

/* ======================================================
	/ Actual Action Funcs
   ====================================================== */

/* ======================================================
	CRONNABLE FUNCTION (should house these somewhere)
   ====================================================== */

#} this is the event notifier. It should send an email 24 hours before if not complete
function zeroBSCRM_notifyEvents(){

	//86,400 seconds in 24 hours...
	global $wpdb;
	$query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'zerobs_event' AND post_status = 'publish'";
	$results = $wpdb->get_results($query);

	$event = array();
	$i=0;
	foreach($results as $result){

		$zbsEventActions['complete'] = 0;

		$zbsEventMeta 	= 	get_post_meta($result->ID, 'zbs_event_meta', true);
		$zbsEventActions = get_post_meta($result->ID, 'zbs_event_actions', true);

		$zbsEventActions = array_merge($zbsEventMeta, $zbsEventActions);

		#} this is the flag as to whether the event has been notified about
		$notified = get_post_meta($result->ID,'24hnotify', true);

		if($zbsEventActions != '' && empty($notified)){

			if(array_key_exists('complete', $zbsEventActions)){

				if($zbsEventActions['complete'] != 1 && isset($zbsEventActions['notify_crm']) && $zbsEventActions['notify_crm'] == 'on'){    //only mail about non-complete events.

					$date = $zbsEventMeta['from'];

					//so we are testing if the event is in the future (i.e. time() <= $date) AND
					//and the time of the event is less than 24 hours away from now...

					// WH added. Simplify in these situ's man
					$eventTime = strtotime($date);
					$eventTimeMinus24hr = $eventTime - 86400;

					//if ($eventTime <= (time() + 86400) && time() <= $eventTime) {    //as soon as we get within 24 hours.. it'll fire
					if (time() > $eventTimeMinus24hr && time() < $eventTime){


						//the event URL
						$url = admin_url('post.php?post='.$result->ID.'&action=edit');

						$contactID = zeroBS_getOwner($result->ID,true,'zerobs_event');
						$contactID = $contactID['ID'];

						if(array_key_exists('customer', $zbsEventMeta) && $contactID > 0){

							$user_info = get_userdata($contactID);

							/*
							$username = $user_info->user_login;
							$first_name = $user_info->first_name;
							$last_name = $user_info->last_name;
							*/
							$email = $user_info->user_email;


							#} check if the email is active..
							$active = zeroBSCRM_get_email_status(ZBSEMAIL_EVENTNOTIFICATION);
							if ($active){

								// send welcome email (tracking will now be dealt with by zeroBSCRM_mailDelivery_sendMessage)

								// ==========================================================================================
								// =================================== MAIL SENDING =========================================

								// generate html
								$emailHTML = zeroBSCRM_Event_generateNotificationHTML($password='',true, $email, $url, $result->ID);

				                  // build send array
				                  $mailArray = array(
				                    'toEmail' => $email,
				                    'toName' => '',
				                    'subject' => zeroBSCRM_mailTemplate_getSubject(ZBSEMAIL_EVENTNOTIFICATION),
				                    'headers' => zeroBSCRM_mailTemplate_getHeaders(ZBSEMAIL_EVENTNOTIFICATION),
				                    'body' => $emailHTML,
				                    'textbody' => '',
				                    'options' => array(
				                      'html' => 1
				                    ),
				                    'tracking' => array( 
				                      // tracking :D (auto-inserted pixel + saved in history db)
				                      'emailTypeID' => ZBSEMAIL_EVENTNOTIFICATION,
				                      'targetObjID' => $contactID,
				                      'senderWPID' => -13,
				                      'associatedObjID' => $result->ID
				                    )
				                  );

				                  // DEBUG echo 'Sending:<pre>'; print_r($mailArray); echo '</pre>Result:';

				                  // Sends email, including tracking, via setting stored route out, (or default if none)
				                  // and logs trcking :)

									// discern del method
									$mailDeliveryMethod = zeroBSCRM_mailTemplate_getMailDelMethod(ZBSEMAIL_EVENTNOTIFICATION);
									if (!isset($mailDeliveryMethod) || empty($mailDeliveryMethod)) $mailDeliveryMethod = -1;

									// send
									$sent = zeroBSCRM_mailDelivery_sendMessage($mailDeliveryMethod,$mailArray);

									// mark as sent
									update_post_meta($result->ID,'24hnotify', 's');


								// =================================== / MAIL SENDING =======================================
								// ==========================================================================================

							}


						} else {


						} 


					} else {

					} 

				} //  if not completed + 24 hour reminder

			} // if array key complete exists

		} // / if has actions


	} // / for each event

}