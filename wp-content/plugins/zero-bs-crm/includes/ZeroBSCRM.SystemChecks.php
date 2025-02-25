<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V1.20
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 01/11/16
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */





/* ======================================================
  Generic System Check Wrapper/Helper funcs
   ====================================================== */

	#} Can use to check php feat is properly installed :)
	#} zeroBSCRM_checkSystemFeat('zlib')
	function zeroBSCRM_checkSystemFeat($key='',$withInfo=false){


		$featList = array('zlib','dompdf','pdffonts','curl','autodraftgarbagecollect','phpver','wordpressver','locale','assetdir','executiontime','memorylimit','postmaxsize','uploadmaxfilesize','wpuploadmaxfilesize','dbver','dalver','corever','local','localtime','serverdefaulttime','sqlrights','devmode','permalinks','mysql','innodb');

		if (in_array($key,$featList) && function_exists("zeroBSCRM_checkSystemFeat_".$key)) return call_user_func_array("zeroBSCRM_checkSystemFeat_".$key,array($withInfo));

		if (!$withInfo)
			return false;
		else
			return array(false,'No Check!');

	}

	function zeroBSCRM_checkSystemFeat_permalinks(){
		 if(zeroBSCRM_checkPrettyPermalinks()){
			 	$enabled = true;
			   $enabledStr = 'Permalinks ' . get_option('permalink_structure');
				return array($enabled, $enabledStr);
		 }else{
			 	$enabled = false;
			  $enabledStr = ' Pretty Permalinks need to be enabled';
				return array($enabled, $enabledStr);
		 }
	}

	function zeroBSCRM_checkSystemFeat_corever($withInfo=false){

		if (!$withInfo)
			return true;
		else {

			global $zbs;

			$enabled = true;
			$enabledStr = 'Version ' . $zbs->version;

			return array($enabled, $enabledStr);
		}
	}

	function zeroBSCRM_checkSystemFeat_dbver($withInfo=false){

		if (!$withInfo)
			return true;
		else {

			global $zbs;

			$enabled = true;
			$enabledStr = 'Database Version ' . $zbs->db_version;

			return array($enabled, $enabledStr);
		}
	}

	function zeroBSCRM_checkSystemFeat_dalver($withInfo=false){

		if (!$withInfo)
			return true;
		else {

			global $zbs;

			$enabled = true;
			$enabledStr = 'Data Access Layer Version ' . $zbs->dal_version;

			return array($enabled, $enabledStr);
		}
	}

	function zeroBSCRM_checkSystemFeat_phpver($withInfo=false){

		if (!$withInfo)
			return true;
		else {

			$enabled = true;
			$enabledStr = 'PHP Version ' . phpversion();

			return array($enabled, $enabledStr);
		}
	}

	function zeroBSCRM_checkSystemFeat_wordpressver($withInfo=false){

		if (!$withInfo)
			return true;
		else {

			global $wp_version;

			$enabled = true;
			$enabledStr = sprintf(__("WordPress Version %s", 'zero-bs-crm'), $wp_version);

			return array($enabled, $enabledStr);
		}
	}



	function zeroBSCRM_checkSystemFeat_local($withInfo=false){

		$local = zeroBSCRM_isLocal();

		if (!$withInfo)
			return !$local;
		else {

			$enabled = !$local;
			if ($local) 
				$enabledStr = 'Running Locally<br />This may cause connectivity issues with SMTP (Emails) and updates/feature downloads.';
			else
				$enabledStr = 'Connectivity Okay.';

			return array($enabled, $enabledStr);
		}
	}
	function zeroBSCRM_checkSystemFeat_serverdefaulttime($withInfo=false){

		/*if (function_exists('locale_get_default'))
			$locale = locale_get_default();
		else
			$locale = Locale::getDefault();
		*/
			$tz = date_default_timezone_get();

		if (!$withInfo)
			return true;
		else {

			$enabled = true;
			$enabledStr = $tz;

			return array($enabled, $enabledStr);
		}
	}
	function zeroBSCRM_checkSystemFeat_localtime($withInfo=false){

		$enabled = true;

		if (!$withInfo)
			return true;
		else {

			$enabledStr = 'CRM Time: '.zeroBSCRM_date_i18n('Y-m-d H:i:s', time() ).' (GMT: '.date_i18n('Y-m-d H:i:s', time(),true).')';

			return array($enabled, $enabledStr);
		}
	}
    	
    // in devmode or not?
	function zeroBSCRM_checkSystemFeat_devmode($withInfo=false){

		$isLocal = zeroBSCRM_isLocal();

		if (!$withInfo)
			return $isLocal;
		else {

			global $zbs;

			$devModeStr = '';

			if (!$isLocal){

				// check if overriden
				$key = $zbs->DAL->setting('localoverride',false);

		    	// if set, less than 48h ago, is overriden
		    	if ($key !== false && $key > time()-172800)
		    		$devModeStr = __('Production','zero-bs-crm').' (override)';
		    	else // normal production (99% users)
		    		$devModeStr = __('Production','zero-bs-crm');

		    } else {

		    	// devmode proper
		    	$devModeStr = __('Developer Mode','zero-bs-crm');

		    }

			return array($isLocal, $devModeStr);
		}
	}

	// https://wordpress.stackexchange.com/questions/6424/mysql-database-user-which-privileges-are-needed
	// can we create tables?
	function zeroBSCRM_checkSystemFeat_sqlrights($withInfo=false){

		global $wpdb;
		
	  	// run check tables
	  	zeroBSCRM_checkTablesExist();
	  	$lastError = $wpdb->last_error;
	  	$okay = true; if (strpos($lastError,'command denied') > -1) $okay = false;

		if (!$withInfo)
			return $okay;
		else {

			global $zbs;

			$enabled = $okay;
			if ($enabled) 
				$enabledStr = __('Appears Okay','zero-bs-crm');
			else
				$enabledStr = __('Error','zero-bs-crm').': '.$lastError;

			return array($enabled, $enabledStr);
		}
	}

	// what mysql we running
	function zeroBSCRM_checkSystemFeat_mysql($withInfo=false){

		if (!$withInfo)
			return zeroBSCRM_database_getVersion();
		else
			return array(1, zeroBSCRM_database_getVersion());

	}

	// got InnoDB?
	function zeroBSCRM_checkSystemFeat_innodb($withInfo=false){

		if (!$withInfo)
			return zeroBSCRM_DB_canInnoDB() ? __('Available','zero-bs-crm') :  __('Not Available','zero-bs-crm');
		else {
			$innoDB = zeroBSCRM_DB_canInnoDB();
			return array($innoDB, ($innoDB ? __('Available','zero-bs-crm') :  __('Not Available','zero-bs-crm')));			
		}

	}



	// below here: https://stackoverflow.com/questions/8744107/increase-max-execution-time-in-php


	function zeroBSCRM_checkSystemFeat_executiontime($withInfo=false){


			$maxExecution = ini_get('max_execution_time');

		if (!$withInfo)
			return $maxExecution;
		else {

			$str = $maxExecution.' seconds';

			// catch infinites
			if ($maxExecution == '0') $str = 'No Limit';

			return array($maxExecution,$str);

		}


	}

	function zeroBSCRM_checkSystemFeat_memorylimit($withInfo=false){

			$maxMemory = ini_get('memory_limit');

		if (!$withInfo)
			return $maxMemory;
		else {

			$str = $maxMemory;

			return array($maxMemory,$str);

		}


	}

	function zeroBSCRM_checkSystemFeat_postmaxsize($withInfo=false){

			$post_max_size = ini_get('post_max_size');

		if (!$withInfo)
			return $post_max_size;
		else {

			$str = $post_max_size;

			return array($post_max_size,$str);

		}


	}

	function zeroBSCRM_checkSystemFeat_uploadmaxfilesize($withInfo=false){

			$upload_max_filesize = ini_get('upload_max_filesize');

		if (!$withInfo)
			return $upload_max_filesize;
		else {

			$str = $upload_max_filesize;

			return array($upload_max_filesize,$str);

		}


	}

	function zeroBSCRM_checkSystemFeat_wpuploadmaxfilesize($withInfo=false){

			//https://codex.wordpress.org/Function_Reference/wp_max_upload_size
			$wp_max_upload_size = zeroBSCRM_prettyformatBytes(wp_max_upload_size());

		if (!$withInfo)
			return $wp_max_upload_size;
		else {

			$str = $wp_max_upload_size;

			return array($wp_max_upload_size,$str);

		}


	}


// https://codex.wordpress.org/Using_Permalinks#Tips_and_Tricks
function zeroBSCRM_checkPrettyPermalinks(){
	if ( get_option('permalink_structure') ) {  
		return true;
	}else{
		return false;
	}
}



/* ======================================================
  / Generic System Check Wrapper/Helper funcs
   ====================================================== */




/* ======================================================
  Jetpack CRM Check Wrapper/Helper funcs
   ====================================================== */
	
	function zeroBSCRM_checkSystemFeat_autodraftgarbagecollect($withInfo=false){

		#} just returns the date last cleared
		$lastCleared = get_option('zbscptautodraftclear','');

		if (!$withInfo){

			$enabledStr = 'Not yet cleared'; if (!empty($lastCleared)) $enabledStr = 'Cleared '.date(zeroBSCRM_getTimeFormat().' '.zeroBSCRM_getDateFormat(),$lastCleared); 
			return $enabledStr;

		} else {

			$enabled = false; $enabledStr = 'Not yet cleared'; 
			if (!empty($lastCleared)){
				$enabledStr = 'Cleared '.date(zeroBSCRM_getTimeFormat().' '.zeroBSCRM_getDateFormat(),$lastCleared); 
				$enabled = true;
			}
			return array($enabled,$enabledStr);

		}

	}


/* ======================================================
   / ZBS  Check Wrapper/Helper funcs
   ====================================================== */

/* ======================================================
  Specific System Check Wrapper/Helper funcs
   ====================================================== */

	function zeroBSCRM_checkSystemFeat_zlib($withInfo=false){


		if (!$withInfo)
			return class_exists('ZipArchive');
			#} can't use following as some servers have installed but don't allow ZipArchive
			#return extension_loaded('zlib');
		else {

			$enabled = class_exists('ZipArchive');
			$str = 'zlib is properly enabled on your server.';
			if (!$enabled) $str = 'zlib is disabled on your server.';

			return array($enabled,$str);

		}


	}
	function zeroBSCRM_checkSystemFeat_dompdf($withInfo=false){

		global $zbs; 

		// retrieve info
		$libInfo = $zbs->lib('dompdf');

		if (!$withInfo)
			return is_array($libInfo);
		else {

			$enabled = file_exists($libInfo['include']);
			$str = 'PDF Engine is properly installed on your server.';
			if (isset($libInfo['version'])) $str .= ' (Version '.$libInfo['version'].')';
			if (!$enabled) $str = 'PDF Engine is not installed on your server.';

			return array($enabled,$str);

		}

	}
	function zeroBSCRM_checkSystemFeat_pdffonts($withInfo=false){


		if (!$withInfo)
			return file_exists(ZEROBSCRM_PATH.'includes/lib/dompdf-fonts/fonts-info.txt');
		else {

			$enabled = file_exists(ZEROBSCRM_PATH.'includes/lib/dompdf-fonts/fonts-info.txt');
			$str = 'PDF Font set appears to be installed on your server.';
			if (!$enabled) $str = 'PDF Font set does not appear to be installed on your server.';

			return array($enabled,$str);

		}


	}
	function zeroBSCRM_checkSystemFeat_curl($withInfo=false){


		if (!$withInfo)
			return function_exists('curl_init');
		else {

			$enabled = function_exists('curl_init');
			$str = 'CURL is enabled on your server.';
			if (!$enabled) $str = 'CURL is not enabled on your server.';

			return array($enabled,$str);

		}


	}
	function zeroBSCRM_checkSystemFeat_locale($withInfo=false){


		if (!$withInfo)
			return true;
		else {

			$locale = zeroBSCRM_getLocale();
			$str = 'WordPress Locale is set to <strong>'.$locale.'</strong>';

			$str .= ' (Server: '.zeroBSCRM_locale_getServerLocale().')';

			return array(true,$str);

		}


	}


	function zeroBSCRM_checkSystemFeat_assetdir(){

		$potentialDirObj = zeroBSCRM_privatisedDirCheck();
		if (is_array($potentialDirObj) && isset($potentialDirObj['path'])) 
			$potentialDir = $potentialDirObj['path'];
		else
			$potentialDir = false;

		$enabled = false; 
		$enabledStr = 'Using Default WP Upload Library';

		if (!empty($potentialDir)) {
			$enabled = true;
			$enabledStr = $potentialDir;
		}

		return array($enabled, $enabledStr);
	}

                       




/* ======================================================
  / Specific System Check Wrapper/Helper funcs
   ====================================================== */