<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V2.0
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 06/04/17
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

	if (!zeroBSCRM_API_is_zbs_api_authorised()){

		   #} NOPE
		   zeroBSCRM_API_AccessDenied(); 
		   exit();

	} else {

        $json_params = file_get_contents("php://input");
        $new_email = json_decode($json_params,true);
        
		$emailFields['subject'] = ''; if (isset($new_email['subject'])) $emailFields['subject'] = sanitize_text_field($new_email['subject']);
		$emailFields['content'] = ''; if (isset($new_email['content'])) $emailFields['content'] = wp_kses_post($new_email['content']);

        $emailFields['thread'] = -1; if(isset($new_email['thread'])) $emailFields['thread'] = (int)$new_email['thread'];
        $emailFields['from'] = ''; if(isset($new_email['from'])) $emailFields['from'] = sanitize_email($new_email['from']);

        $new_email = zeroBS_inbox_api_catch($emailFields);
        
		wp_send_json($json_params);  

	}
	




?>