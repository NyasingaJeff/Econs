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

		#} Checks out, retrieve + return customers
		#} MIKE TODO - add paging/params for get count (max 50 at a time I think)
		$quotes = zeroBS_getQuotes(true, 20);
		echo json_encode($quotes);
		exit();

	}

	exit();

?>