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
Customer Typeaheads
   ====================================================== */

	#} Outputs the html for a customer type-ahead list
	function zeroBSCRM_CustomerTypeList($jsCallbackFuncStr='',$inputDefaultValue='',$showFullWidthSmaller=false,$jsChangeCallbackFuncStr=''){

		$ret = ''; $extraClasses = '';
		
		if ($showFullWidthSmaller) $extraClasses .= 'zbsbtypeaheadfullwidth';
		
			#} Wrap
			$ret .= '<div class="zbstypeaheadwrap '.$extraClasses.'">';

			#} Build input
			$ret .= '<input class="zbstypeahead" type="text" value="'.$inputDefaultValue.'" placeholder="' . __('Customer name or email...',"zero-bs-crm") . '" data-zbsopencallback="'.$jsCallbackFuncStr.'" data-zbschangecallback="'.$jsChangeCallbackFuncStr.'"  autocomplete="zbscon-'.time().'-typeahead" data-autokey="cotypelist"">';	

			#} close wrap
			$ret .= '</div>';

			#} Also need to make sure this is dumped out for js
			global $haszbscrmBHURLCustomersOut; 
			if (!isset($haszbscrmBHURLCustomersOut)){ 


				// cachebusting for now... (ESP needed when migrating from DAL1 -> DAL2)

				$cacheBusterStr = '&time='.time();
				
				//change to proper WP REST (not cached) and wont be impacted by setup connection issues. Is also the "proper" way to do it
				$nonce = wp_create_nonce( 'wp_rest' );
				$rest_url = get_rest_url() . 'zbscrm/v1/contacts?_wpnonce=' .$nonce;
				$ret .= '<script type="text/javascript">var zbscrmBHURLCustomers = "'. $rest_url .'";</script>';
				$haszbscrmBHURLCustomersOut = true;
			}

		#} Global JS does the rest ;)
		#} see zbscrm_JS_Bind_Typeaheads_Customers

		return $ret;

	}

	#} Outputs the html for a Company type-ahead list
	function zeroBSCRM_CompanyTypeList($jsCallbackFuncStr='',$inputDefaultValue='',$showFullWidthSmaller=false,$jsChangeCallbackFuncStr=''){

		$ret = ''; $extraClasses = '';
		
		if ($showFullWidthSmaller) $extraClasses .= 'zbsbtypeaheadfullwidth';

		// typeahead or select?
		// turned off until JS bind's work
		// #TODOCOLIST in /wdev/ZeroBSCRM/zerobs-core/js/ZeroBSCRM.admin.global.js
		if (isset($neverGoingToBeSet) && zeroBS_companyCount() < 50){ 

			#} Wrap
			$ret .= '<div class="zbs-company-select '.$extraClasses.'">';

			#} Build input			
        	$companies = zeroBS_getCompanies(true,10000,0);
			$ret .= '<select class="zbs-company-select-input" autocomplete="zbsco-'.time().'-typeahead" data-zbsopencallback="'.$jsCallbackFuncStr.'" data-zbschangecallback="'.$jsChangeCallbackFuncStr.'">';	

				if (is_array($companies)) foreach ($companies as $co){

					if (isset($co['name']) && $co['name'] !== 'Auto Draft'){

						$ret .= '<option value="'.$co['id'].'"';
						if ($co['name'] == $inputDefaultValue) $ret .= ' selected="selected"';
						$ret .= '>'.$co['name'].'</option>';

					}

				}

			$ret .= '</select>';

			#} close wrap
			$ret .= '</div>';


		} else {

			// typeahead

			#} Wrap
			$ret .= '<div class="zbstypeaheadwrap '.$extraClasses.'">';

			#} Build input
				// NOTE on autocomplete:
				// needs "data-autokey" attr
				// see my answer https://stackoverflow.com/questions/34585783/disable-browsers-autofill-when-using-typeahead-js
				// see Admin.global.js #AUTOCOMPLETE
			$ret .= '<input class="zbstypeaheadco" type="text" value="'.$inputDefaultValue.'" placeholder="' . __('Company name...',"zero-bs-crm") . '" data-zbsopencallback="'.$jsCallbackFuncStr.'" data-zbschangecallback="'.$jsChangeCallbackFuncStr.'" autocomplete="zbsco-'.time().'-typeahead" data-autokey="cotypelist">';	

			#} close wrap
			$ret .= '</div>';

			#} Also need to make sure this is dumped out for js
			global $haszbscrmBHURLCompaniesOut; 
			if (!isset($haszbscrmBHURLCompaniesOut)){ 

				$nonce = wp_create_nonce( 'wp_rest' );
				$rest_url = get_rest_url() . 'zbscrm/v1/companies?_wpnonce=' .$nonce;
				$ret .= '<script type="text/javascript">var zbscrmBHURLCompanies = "'. $rest_url . '";</script>';
				$haszbscrmBHURLCompaniesOut = true;
			}

			#} Global JS does the rest ;)
			#} see zbscrm_JS_Bind_Typeaheads_Customers

		}

		return $ret;

	}


	// WH NOTE: WHY is this getting ALL of them and not s? param
	#} Returns json representing the first 10k customers in db... brutal
	#} MS NOTE: useful to return EMAIL in the response (for auto filling - WITHOUT getting ALL meta)?
	function zeroBSCRM_cjson(){

		header('Content-Type: application/json');
		$ret = array();

		if (is_user_logged_in() && zeroBSCRM_permsCustomers()){
		
			$ret = zeroBS_getCustomers(true,10000,0,false,false,'',false,false,false);

        	// quickfix (not req DAL2)
        	global $zbs;
        	if (!$zbs->isDAL2()){

        		$retA = array();
        		foreach ($ret as $r){
        			if (isset($r['name']) && $r['name'] !== 'Auto Draft') $retA[] = $r;
        		}

        		$ret = $retA; unset($retA);
        	}

		}

		echo json_encode($ret);

		exit();

	}

	// WH NOTE: WHY is this getting ALL of them and not s? param
	#} Returns json representing the first 10k customers in db... brutal
	function zeroBSCRM_cojson(){

		header('Content-Type: application/json');
		$ret = array();

		if (is_user_logged_in() && zeroBSCRM_permsCustomers()){
		
			//$ret = zeroBS_getCustomers(false,10000,0,false,false,'',false,false,false);
        	$ret = zeroBS_getCompanies(true,10000,0);

        	// quickfix required until we move co to dal2
        	//if (!$zbs->isDAL2()){

        		$retA = array();
        		foreach ($ret as $r){
        			if (isset($r['name']) && $r['name'] !== 'Auto Draft') $retA[] = $r;
        		}

        		$ret = $retA; unset($retA);
        	//}


		}

		echo json_encode($ret);

		exit();

	}
 
/* ======================================================
	/ Customer Typeaheads
   ====================================================== */



 
/* ======================================================
	Customer Filter Funcs
   ====================================================== */

function zbs_customerFiltersGetApplied($srcArr='usepost',$requireEmail=false){

	$fieldPrefix = '';

	global $zbs;

	#} Can't use post as a default, so...
	if (is_string($srcArr) && $srcArr == 'usepost') {
		$srcArr = $_POST;
		#} Also, posted fields need this prefix
		$fieldPrefix = 'zbs-crm-customerfilter-';

		$fromPost = true;
	}

	#} Req.
	global $zbsCustomerFields, $zbsCustomerFiltersInEffect, $zbsCustomerFiltersPosted;
	$allZBSTags = zeroBS_integrations_getAllCategories();

	#} start
	$appliedFilters = array(); $activeFilters = 0;

	/*
		status (str)
		namestr (str)
		source (str) linked to cf1
		valuefrom
		valueto
		addedfrom
		addedto		

			zbs-crm-customerfilter-tag-'.$tagGroupKey.'-'.$tag->term_id

		hasquote (bool int)
		hasinv (bool int)
		hastransact (bool int)

		postcode (str)

		To add:

			#} modifiedfromtoo
			#} External source/id (LATER)


	*/

		#} process filters
		$possibleFilters = array(

			#} key => array(type, matching field(notyetused))
			'status' => array('str','status'),
			'namestr' => array('str','custom:fullname'),
			'source' => array('str','cf1'),
			'valuefrom' => array('float','custom:totalval'),
			'valueto' => array('float','custom:totalval'),
			'addedrange' => array('str',''), // x - y (dates)
			#} these will be added by func below
			//'addedfrom' => array('str',''),
			//'addedto' => array('str',''),
			'hasquote' => array('bool',''),
			'hasinv' => array('bool',''),
			'hastransact' => array('bool',''),
			'postcode' => array('str','postcode')

		);
		#} Tags dealt with seperately.

		foreach ($possibleFilters as $key => $filter){

			$type = $filter[0];

			if (isset($srcArr[$fieldPrefix.$key])){

				switch ($type){

					case 'str':

						#} Is it a str? cleanse?
						if (!empty($srcArr[$fieldPrefix.$key])) {

							#} add 
							$appliedFilters[$key] = sanitize_text_field($srcArr[$fieldPrefix.$key]);
							$activeFilters++;
						}

						break;

					case 'float':

						#} Is it a no? cleanse?
						if (!empty($srcArr[$fieldPrefix.$key])) {

							#} Cast
							$no = (float)sanitize_text_field($srcArr[$fieldPrefix.$key]);

							#} add 
							$appliedFilters[$key] = $no;
							$activeFilters++;
						}

						break;

					case 'int':

						#} Is it a no? cleanse?
						if (!empty($srcArr[$fieldPrefix.$key])) {

							#} Cast
							$no = (int)sanitize_text_field($srcArr[$fieldPrefix.$key]);

							#} add 
							$appliedFilters[$key] = $no;
							$activeFilters++;
						}

						break;

					case 'bool':

						#} Is it a bool? cleanse?
						#} double check? no need...
						#} made a hack bool here - is either:
							#} empty (not set)
							#} 1 = true
							#} -1 = false
						if (isset($srcArr[$fieldPrefix.$key])) {

							if ($srcArr[$fieldPrefix.$key] == "1"){

								#} add 
								$appliedFilters[$key] = true;
								$activeFilters++;

							} else if ($srcArr[$fieldPrefix.$key] == "-1"){

								#} add 
								$appliedFilters[$key] = false;
								$activeFilters++;

							}

						}

						break;


				}

			}


		} # / foreach

		#} Added date range
		if (isset($appliedFilters['addedrange']) && !empty($appliedFilters['addedrange'])){

			#} Try split
			if (strpos($appliedFilters['addedrange'],'-') > 0){

				$dateParts = explode(' - ',$appliedFilters['addedrange']);
				if (count($dateParts) == 2){

					#} No validation here (yet)
					if (!empty($dateParts[0])) {
						$appliedFilters['addedfrom'] = $dateParts[0];
						$activeFilters++;
					}
					if (!empty($dateParts[1])) {
						$appliedFilters['addedto'] = $dateParts[1];
						$activeFilters++;
					}

				}

			}

		}

		#} Tags (From POST)
		if (isset($fromPost)){
			$appliedFilters['tags'] = array();
			if (isset($allZBSTags) && count($allZBSTags) > 0){
			
				#} Cycle through + catch active
				foreach ($allZBSTags as $tagGroupKey => $tagGroup){
					
					if (count($tagGroup) > 0) foreach ($tagGroup as $tag){

					      // DAL support
					      $tagID = -1; $tagName = '';
					      if ($zbs->isDAL2()){

					          $tagID = $tag['id'];
					          $tagName = $tag['name'];

					      } else {
					          
					          $tagID = $tag->term_id;
					          $tagName = $tag->name;

					      }
					
						#} set?
						if (isset($_POST['zbs-crm-customerfilter-tag-'.$tagGroupKey.'-'.$tagID])) {

							#} Tagged :) Add
							$appliedFilters['tags'][$tagGroupKey][$tagID] = true;
							$activeFilters++;

						}

					}


				}


			}

		} else {

			#} From passed array, so just make sure it's an array of arrays and pass :)
			#} This all assumes passing a json obj made into array with (array) cast (see mail camp search #tempfilterjsonpass)
			$appliedFilters['tags'] = array();

			if (isset($srcArr['tags'])){

				$srcTags = (array)$srcArr['tags'];

				if (is_array($srcTags) && count($srcTags) > 0) foreach ($srcTags as $tagKey=>$tagObj){

					$appliedFilters['tags'][$tagKey] = (array)$tagObj;

				}


			}

		}


		#} if req email
		if (
			$requireEmail || 
			( isset($srcArr[$fieldPrefix.'require-email']) && !empty($srcArr[$fieldPrefix.'require-email']) )
			) $appliedFilters['require_email'] = true;

		#} this will only be set if filters have been posted/some actually apply:
		#} $zbsCustomerFiltersPosted;
		if ($activeFilters > 0) $zbsCustomerFiltersPosted = $activeFilters;

		return $appliedFilters;

}

/*

	zbs_customerFiltersGUI

		Takes a posted set of data from the GUI it puts out and sets the filters in the global var:
		$zbsCustomerFiltersInEffect

	params: (Not req)
		$selected = array of pre-filled vars
		$echo = output via echo (ignore it and you get html returned)
	
*/
function zbs_customerFiltersGUI($selected=array(),$echo=false,$wrapClassAdditions='',$useAJAX=false,$requireEmail=false){

	#} Query Performance index
	#global $zbsQPI; if (!isset($zbsQPI)) $zbsQPI = array();
	#$zbsQPI['UIbuild'] = zeroBSCRM_mtime_float();

	#} Req.
	global $zbs,$zbsCustomerFields, $zbsCustomerFiltersInEffect, $zbsCustomerFiltersPosted;
	$allZBSTags = zeroBS_integrations_getAllCategories();

	
	#} validate selected filters passed
	$appliedFilters = zbs_customerFiltersGetApplied(); #array(); 
	#} No validation here
	if (isset($selected) && is_array($selected) && count($selected) > 0) $appliedFilters = $selected;


	#} Return via html unless asked to echo :) 

		#} Build html
		$zbsCustomerFiltersHTML = '';
		$currencyChar = zeroBSCRM_getCurrencyChr();

		#} Add a nonce to start
		$zbsCustomerFiltersHTML .= '<script type="text/javascript">var zbscrmjs_secToken = \''.wp_create_nonce( "zbscrmjs-ajax-nonce" ).'\';</script>';
		
		#} Our ZBS styled filter block

			#} Style overrides:
			$zbsClassAdditions = ''; if (!empty($wrapClassAdditions)) $zbsClassAdditions = ' '.$wrapClassAdditions;

			#} Wrap
			$zbsCustomerFiltersHTML .= '<div class="zbs-crm-customerfilters-wrap'.$zbsClassAdditions.'"><form method="post" id="zbs-crm-customerfilter-form">';

				#} if require email, pass via field
				if ($requireEmail) $zbsCustomerFiltersHTML  .= '<input type="hidden" name="zbs-crm-customerfilter-require-email" value="1" />';

				#} Options

					#} Status
					$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter"><label for="zbs-crm-customerfilter-status">'.__('Status',"zero-bs-crm").':</label>' 
											.	'<select id="zbs-crm-customerfilter-status" name="zbs-crm-customerfilter-status">' 
											.	'<option value="">Any</option>' 
											.	'<option value="" disabled="disabled">=========</option>';

												#} Put out statuses
												if (isset($zbsCustomerFields['status']) && isset($zbsCustomerFields['status'][3]) && is_array($zbsCustomerFields['status'][3])) foreach ($zbsCustomerFields['status'][3] as $statusStr){

													$zbsCustomerFiltersHTML .=	'<option value="'.$statusStr.'"';
													if (isset($appliedFilters['status']) && $appliedFilters['status'] == $statusStr) $zbsCustomerFiltersHTML .=	' selected="selected"';
													$zbsCustomerFiltersHTML .=	'>'.$statusStr.'</option>';

												}

					$zbsCustomerFiltersHTML .=	'</select>'
											.	'</div>';

					#} Name containing Str
					$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter zbs-crm-customerfilter-namestr"><label for="zbs-crm-customerfilter-namestr">'.__('Name Contains',"zero-bs-crm").':</label>' 
											.	'<input type="text" id="zbs-crm-customerfilter-namestr" name="zbs-crm-customerfilter-namestr" value="';
					if (isset($appliedFilters['namestr']) && !empty($appliedFilters['namestr'])) $zbsCustomerFiltersHTML .= $appliedFilters['namestr'];
					$zbsCustomerFiltersHTML .=	'" placeholder="'.__('e.g. Mike',"zero-bs-crm").'" />'
											.	'</div>';

					#} Total Value Range
					$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter zbs-crm-customerfilter-valuerange"><label for="zbs-crm-customerfilter-valuefrom">'.__('Total Value (Range)',"zero-bs-crm").':</label>' 
											.	'<div class="input-group"><span class="input-group-addon">'.$currencyChar.'</span><input type="text" id="zbs-crm-customerfilter-valuefrom" name="zbs-crm-customerfilter-valuefrom" value="';
					if (isset($appliedFilters['valuefrom']) && !empty($appliedFilters['valuefrom'])) $zbsCustomerFiltersHTML .= $appliedFilters['valuefrom'];
					$zbsCustomerFiltersHTML .=	'" /></div> <span class="to-label">To</span> <div class="input-group"><span class="input-group-addon">'.$currencyChar.'</span>'
											.	'<input type="text" id="zbs-crm-customerfilter-valueto" name="zbs-crm-customerfilter-valueto" value="';
					if (isset($appliedFilters['valueto']) && !empty($appliedFilters['valueto'])) $zbsCustomerFiltersHTML .= $appliedFilters['valueto'];
					$zbsCustomerFiltersHTML .=	'" /></div>'
											.	'</div>';

					/*#} Added Date Range
					$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter"><label for="zbs-crm-customerfilter-addedrange">'.__('Date Added (Range)',"zero-bs-crm").':</label>' 
											.	'<input type="text" id="zbs-crm-customerfilter-addedrange" name="zbs-crm-customerfilter-addedrange" class="zbs-date-range" value="';
					if (isset($appliedFilters['addedrange']) && !empty($appliedFilters['addedrange'])) $zbsCustomerFiltersHTML .= $appliedFilters['addedrange'];
					$zbsCustomerFiltersHTML .=	'" />'
					/* moved to one field			.	'<input type="text" id="zbs-crm-customerfilter-addedto" name="zbs-crm-customerfilter-addedto" value="';
					if (isset($appliedFilters['addedto']) && !empty($appliedFilters['addedto'])) $zbsCustomerFiltersHTML .= $appliedFilters['addedto'];
					$zbsCustomerFiltersHTML .=	'" />' * /
											.	'</div>';*/


					#} Added Date Range (styled)
					$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter"><label for="zbs-crm-customerfilter-addedrange">'.__('Date Added (Range)',"zero-bs-crm").':</label>' 
											.	'<div id="zbs-crm-customerfilter-addedrange-reportrange">'
											.	'    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;<span></span> <b class="caret"></b>'
											.	'</div>'
											#} Following input is maintained via daterangepicker
											.	'<input type="hidden" id="zbs-crm-customerfilter-addedrange" name="zbs-crm-customerfilter-addedrange" class="zbs-date-range" value="';
					if (isset($appliedFilters['addedrange']) && !empty($appliedFilters['addedrange'])) $zbsCustomerFiltersHTML .= $appliedFilters['addedrange'];
					$zbsCustomerFiltersHTML .=	'" />'
											.	'</div>';






					#} Has Tag (for MVP zerobscrm_worktag and zerobscrm_customertag)
					if (isset($allZBSTags) && count($allZBSTags) > 0){
					
					#} Outer group
					$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter-group">';

						$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter zbs-full-line"><label for="zbs-crm-customerfilter-tags" class="zbs-full-line">'.__('Has Tag(s)',"zero-bs-crm").':</label>';

						#} One group per taxonomy
						foreach ($allZBSTags as $tagGroupKey => $tagGroup){

							#} If is tags
							if (count($tagGroup) > 0) {

								#} Retrieve taxonomy (labels etc)
								$taxonomyDeets = get_taxonomy($tagGroupKey);
								$taxonomyName = $tagGroupKey; if (isset($taxonomyDeets->labels) && isset($taxonomyDeets->labels->name)) $taxonomyName = $taxonomyDeets->labels->name;

								#} Wrap + label
								$zbsCustomerFiltersHTML .=	'<div class="zbs-crm-customerfilter-group zbs-panel"><label class="zbs-panel-title" for="zbs-crm-customerfilter-tags-'.$tagGroupKey.'">'.$taxonomyName.'</label>';

									#} Put out tags
									foreach ($tagGroup as $tag){

								      // DAL support
								      $tagID = -1; $tagName = '';
								      if ($zbs->isDAL2()){

								          $tagID = $tag['id'];
								          $tagName = $tag['name'];

								      } else {
								          
								          $tagID = $tag->term_id;
								          $tagName = $tag->name;

								      }

										#} @)
										$fieldName = 'zbs-crm-customerfilter-tag-'.$tagGroupKey.'-'.$tagID;

										#} selected?
										$fieldActive = false; if (isset($appliedFilters['tags'][$tagGroupKey]) && isset($appliedFilters['tags'][$tagGroupKey][$tagID])){ $fieldActive = true; }

										$zbsCustomerFiltersHTML .= '<div class="zbs-crm-customerfilter-groupline"><input type="checkbox" id="'.$fieldName.'" name="'.$fieldName.'" value="1"';
										if ($fieldActive) $zbsCustomerFiltersHTML .= ' checked="checked"';
										$zbsCustomerFiltersHTML .= ' /> <label for="'.$fieldName.'">'.$tagName.'</label></div>';

									}														

								$zbsCustomerFiltersHTML .=	'</div>';

							}

						}

						#} Close wrap
						$zbsCustomerFiltersHTML .=	'</div>';

						#} Close group
						$zbsCustomerFiltersHTML .=	'</div>';

					}


					#} Following 3 work well in a group
					$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter-group">';

						#} Has Quote
						$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter"><label for="zbs-crm-customerfilter-hasquote">'.__('Has Quote',"zero-bs-crm").':</label>' 
												.	'<input type="checkbox" id="zbs-crm-customerfilter-hasquote" name="zbs-crm-customerfilter-hasquote" value="1"';
											if (isset($appliedFilters['hasquote']) && $appliedFilters['hasquote']) $zbsCustomerFiltersHTML .= ' checked="checked"';
											$zbsCustomerFiltersHTML .= ' />'
												.	'</div>';

						#} Has Invoice
						$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter"><label for="zbs-crm-customerfilter-hasinv">'.__('Has Invoice',"zero-bs-crm").':</label>' 
												.	'<input type="checkbox" id="zbs-crm-customerfilter-hasinv" name="zbs-crm-customerfilter-hasinv" value="1"';
											if (isset($appliedFilters['hasinv']) && $appliedFilters['hasinv']) $zbsCustomerFiltersHTML .= ' checked="checked"';
											$zbsCustomerFiltersHTML .= ' />'
												.	'</div>';

						#} Has Transaction
						$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter"><label for="zbs-crm-customerfilter-hastransact">'.__('Has Transaction',"zero-bs-crm").':</label>' 
												.	'<input type="checkbox" id="zbs-crm-customerfilter-hastransact" name="zbs-crm-customerfilter-hastransact" value="1"';
											if (isset($appliedFilters['hastransact']) && $appliedFilters['hastransact']) $zbsCustomerFiltersHTML .= ' checked="checked"';
											$zbsCustomerFiltersHTML .= ' />'
												.	'</div>';

					$zbsCustomerFiltersHTML .= '</div>';
	

					#} From Source
					if (
						isset($zbsCustomerFields['cf1']) && 
						isset($zbsCustomerFields['cf1'][0]) && 
						isset($zbsCustomerFields['cf1'][1]) && 
						$zbsCustomerFields['cf1'][0] == 'select' && 
						$zbsCustomerFields['cf1'][1] == 'Source'
					){
						$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter"><label for="zbs-crm-customerfilter-source">'.__('Source',"zero-bs-crm").':</label>' 
												.	'<select id="zbs-crm-customerfilter-source" name="zbs-crm-customerfilter-source">' 
												.	'<option value="">Any</option>' 
												.	'<option value="" disabled="disabled">=========</option>';

													#} Put out sources
													if (isset($zbsCustomerFields['cf1']) && isset($zbsCustomerFields['cf1'][3]) && is_array($zbsCustomerFields['cf1'][3])) foreach ($zbsCustomerFields['cf1'][3] as $sourceStr){

														$zbsCustomerFiltersHTML .=	'<option value="'.$sourceStr.'"';
														if (isset($appliedFilters['source']) && $appliedFilters['source'] == $sourceStr) $zbsCustomerFiltersHTML .=	' selected="selected"';
														$zbsCustomerFiltersHTML .=	'>'.$sourceStr.'</option>';

													}

						$zbsCustomerFiltersHTML .=	'</select>'
												.	'</div>';
					}

					#} In Postcode
					$zbsCustomerFiltersHTML .= 	'<div class="zbs-crm-customerfilter"><label for="zbs-crm-customerfilter-postcode">'.__('Within Postal Code',"zero-bs-crm").':</label>' 
											.	'<input type="text" id="zbs-crm-customerfilter-postcode" name="zbs-crm-customerfilter-postcode" value="';
					if (isset($appliedFilters['postcode']) && !empty($appliedFilters['postcode'])) $zbsCustomerFiltersHTML .= $appliedFilters['postcode'];
					$zbsCustomerFiltersHTML .=	'" placeholder="'.__('e.g. AL1 or 90012',"zero-bs-crm").'" />'
											.	'</div>';



					#} (All require emails)


			#} Submit Button
			if (!$useAJAX){
				$zbsCustomerFiltersHTML .= '<div class="zbs-crm-closing-action"><button type="submit" class="">Apply</button></div>';
			} else {
				$zbsCustomerFiltersHTML .= '<div class="zbs-crm-closing-action"><button type="button" class="zbs-ajax-customer-filters">Apply</button></div>';				
				$zbsCustomerFiltersHTML .= '<div class="zbs-crm-customerfilter-ajax-output" style="display:none"></div>';
			}

			#} Wrap
			$zbsCustomerFiltersHTML .= '</form></div>';

			#} Scripts
			$zbsCustomerFiltersHTML .= 	'<script type="text/javascript">'
									.	"</script>";
									#} Moved into ZeroBSCRM.customerfilters.js



			#} Debug			
			#$zbsCustomerFiltersHTML .= '<h2>Debug: $appliedFilters</h2><pre>'.json_encode($appliedFilters).'</pre>';
			#Debug $zbsCustomerFiltersHTML .= '<h2>Debug: $allZBSTags</h2><pre>'.json_encode($allZBSTags).'</pre>';


		#} set global var with applicable query and count
		$zbsCustomerFiltersInEffect = $appliedFilters;

		#} QPI
		#$zbsQPI['UIbuild'] = round(zeroBSCRM_mtime_float() - #$zbsQPI['UIbuild'],2).'s';


	#} Return or echo?
	if ($echo) echo $zbsCustomerFiltersHTML;		
	return $zbsCustomerFiltersHTML;

}

/*

	zbs_customerFiltersRetrieveCustomers
	#} Retrieves array of customers filtered by zbs_customerFilters

	#} Notes:
		- This can + will be fired by zeroBS__customerFiltersRetrieveCustomerCount if that is fired BEFORE THIS
		.. Thereafter it'll use a cached list (and apply paging) - unless $forceRefresh is set to true

*/
function zbs_customerFiltersRetrieveCustomers($perPage=10,$page=1,$forcePaging=false,$forceRefresh=false){

	#} Query Performance index
	#global $zbsQPI; if (!isset($zbsQPI)) $zbsQPI = array();
	#$zbsQPI['retrieveCustomers1'] = zeroBSCRM_mtime_float();

	#} Req.
	global $zbs,$zbsCustomerFields, $zbsCustomerFiltersInEffect, $zbsCustomerFiltersCurrentList;


	#} Already cached?
	if (
		#} Already cached - yep + force refresh
		(isset($zbsCustomerFiltersCurrentList) && is_array($zbsCustomerFiltersCurrentList) && $forceRefresh) ||
		#} Not cached
		(!isset($zbsCustomerFiltersCurrentList) || !is_array($zbsCustomerFiltersCurrentList))
		){

		#DEBUG echo 'NOT CACHED: zbs_customerFiltersRetrieveCustomers<br />';

			#} Any applied filters will be here: $zbsCustomerFiltersInEffect
			$appliedFilters = array(); 

			#} No validation here
			if (isset($zbsCustomerFiltersInEffect) && is_array($zbsCustomerFiltersInEffect) && count($zbsCustomerFiltersInEffect) > 0) $appliedFilters = $zbsCustomerFiltersInEffect;

			#} Output

				#} First build query
				
					#} PAGING NOTE:
						#} MOVED TO POST retrieve, to allow for counts to be made :)
						#} MVP... search #postpaging
						#} Note $forcePaging FORCES pre-paging
							#} Page legit? - lazy check
							if ($forcePaging) {
								
								if ($perPage < 0) $perPageArg = 10; else $perPageArg = (int)$perPage;

							} else {

								$perPageArg = 10000; #} lol.

							}

					#} Defaults
					$args = array (
						'post_type'              => 'zerobs_customer',
						'post_status'            => 'publish',
						'posts_per_page'         => $perPageArg,
						'order'                  => 'DESC',
						'orderby'                => 'post_date'
					);
				
					if ($forcePaging){ 
						#} Add page if page... - dodgy meh
						$actualPage = $page-1; if ($actualPage < 0) $actualPage = 0;
						if ($actualPage > 0) $args['offset'] = $perPageArg*$actualPage;
					}

					// DAL 2 support :)
					$dal2Args = array(
						'perPage' => $perPageArg,
						'sortByField' => 'zbsc_created',
						'sortOrder' => 'DESC',
						'ignoreowner' => true,
						'withQuotes' 		=> true,
						'withInvoices' 		=> true,
						'withTransactions' 	=> true
					);


					#} This is brutal, and needs rethinking #v1.2
					#} For now, is split into two sections
						#1) Can be queried via wp_post args
						#2) Can't be... (filtered post query...)
					#} Inefficient, but for launch...


					#} ===============================================================
					#} get_posts queriable attrs
					#} ===============================================================

					#} Name
						#} As of v1.1
						#'name' => 	$customerEle->post_title
						if (isset($appliedFilters['namestr']) && !empty($appliedFilters['namestr'])){

							#} Simples
							$args['s'] = $appliedFilters['namestr'];

							// DAL2
							$dal2Args['searchPhrase'] = $appliedFilters['namestr'];

						}

					#} Added From + To

						#'created' => $customerEle->post_date_gmt OR post_modified_gmt for modified
						if (isset($appliedFilters['addedfrom']) && !empty($appliedFilters['addedfrom'])){

							#} add holder if req
							if (!isset($args['date_query'])) $args['date_query'] = array();

							#} Add 
							$args['date_query'][] = array(
															'column' => 'post_date_gmt',
															'after' => $appliedFilters['addedfrom'],
														);

							// DAL2
							// TBC $dal2Args['searchPhrase'] = $appliedFilters['namestr'];

						}
						if (isset($appliedFilters['addedto']) && !empty($appliedFilters['addedto'])){

							#} add holder if req
							if (!isset($args['date_query'])) $args['date_query'] = array();

							#} Add 
							$args['date_query'][] = array(
															'column' => 'post_date_gmt',
															'before' => $appliedFilters['addedto'],
														);

							// DAL2
							// TBC $dal2Args['searchPhrase'] = $appliedFilters['namestr'];

						}



					#} Tags
					if (isset($appliedFilters['tags']) && is_array($appliedFilters['tags']) && count($appliedFilters['tags']) > 0){

						#} Temp holder
						$tagQueryArrays = array(); 

						// DAL2 - ignoring taxonomy here
						$tagIDS = array();

						#} Foreach taxonomy type:
						foreach ($appliedFilters['tags'] as $taxonomyKey => $tagItem){

							$thisTaxonomyArr = array();

							#} Foreach tag in taxonomy
							foreach ($tagItem as $tagID => $activeFlag){

								#} If logged here, is active, disregard $activeFlag
								$thisTaxonomyArr[] = $tagID;

								// dal2
								$tagIDS[] = $tagID;

							}

							if (count($thisTaxonomyArr) > 0){

								#} Add it
								$tagQueryArrays[] = array(
															'taxonomy' => $taxonomyKey,
															'field'    => 'term_id',
															'terms'    => $thisTaxonomyArr,
														);

							}

							/*

								#} Later for "not in"
								'terms'    => array( 103, 115, 206 ),
								'operator' => 'NOT IN',

							*/


						}

						#} Any to add?
						if (count($tagQueryArrays) > 0){

								#} Set 
								$args['tax_query'] = array();

								#} if multiple, needs this
								if (count($tagQueryArrays) > 1){

									$args['tax_query']['relation'] = 'AND';

								}

								#} Add em all :)
								foreach ($tagQueryArrays as $tqArr) $args['tax_query'][] = $tqArr;

						}

						// DAL2
						if (count($tagIDS) > 0) $dal2Args['isTagged'] = $tagIDS;

					}

					#} ===============================================================
					#} / end of get_posts queriable attrs
					#} ===============================================================

					#Debug echo '<h2>ARGS</h2><pre>'; print_r($args); echo '</pre>';

					#} QPI
					#$zbsQPI['retrieveCustomers1'] = round(zeroBSCRM_mtime_float() - #$zbsQPI['retrieveCustomers1'],2).'s';
					#$zbsQPI['retrieveCustomers2'] = zeroBSCRM_mtime_float();

					#} Run query
					#$potentialCustomerList = get_posts( $args );
					if ($zbs->isDAL2()){

						$potentialCustomerList = $zbs->DAL->contacts->getContacts($dal2Args);

					} else {

						// DAL1 
						$potentialCustomerList = zeroBS_getCustomers(true,10,0,true,true,'',true,$args);

					}
					#$endingCustomerList = zeroBS_getCustomers(true,10,0,true,true,'',true,$args);
					$endingCustomerList = array();

					#} QPI
					#$zbsQPI['retrieveCustomers2'] = round(zeroBSCRM_mtime_float() - #$zbsQPI['retrieveCustomers2'],2).'s';
					#$zbsQPI['retrieveCustomers3'] = zeroBSCRM_mtime_float();


					#} ===============================================================
					#} filter post-query
					#} ===============================================================
					$x = 0;
					if (count($potentialCustomerList) > 0) foreach ($potentialCustomerList as $potentialCustomer){

						#} Innocent until proven...
						$includeThisCustomer = true;

						#} Stops excess queries
						#$botheredAboutQuotes = false; if (isset($appliedFilters['hasquote']) && $appliedFilters['hasquote']) $botheredAboutQuotes = true;
						#$botheredAboutInvs = false; if (isset($appliedFilters['hasinv']) && $appliedFilters['hasinv']) $botheredAboutInvs = true;
						#$botheredAboutTransactions = false; if (isset($appliedFilters['hastransact']) && $appliedFilters['hastransact']) $botheredAboutTransactions = true;
						#} Need them all, whatever, for total value etc.
						$botheredAboutQuotes = true; $botheredAboutInvs = true; $botheredAboutTransactions = true;

						#} Retrieve full cust
						#$fullCustomer = zeroBS_getCustomer($potentialCustomer->ID,$botheredAboutQuotes,$botheredAboutInvs,$botheredAboutTransactions);
						#} Optimised away from this :)
						$fullCustomer = $potentialCustomer;

						#} Require email?
						if (isset($appliedFilters['require_email'])){

							if (!zeroBSCRM_validateEmail($fullCustomer['email'])) $includeThisCustomer = false;

						}

						#} Status
						if (isset($appliedFilters['status']) && !empty($appliedFilters['status'])){

							#} Check status
							if ($appliedFilters['status'] != $fullCustomer['status']) $includeThisCustomer = false;

						}

						#} Source - ASSUMES is CF1!!!
						if (isset($appliedFilters['source']) && !empty($appliedFilters['source'])){

							#} Check Source
							if ($appliedFilters['source'] != $fullCustomer['cf1']) $includeThisCustomer = false;

						}

						#} Postcode (can be AL1* etc.)
						if (isset($appliedFilters['postcode']) && !empty($appliedFilters['postcode'])){

							#} Remove spaces from both
							$cleanPostcode = str_replace(' ','',$fullCustomer['postcode']);
							$filterPostcode = str_replace(' ','',$appliedFilters['postcode']);

							#} Check Postcode
							if (substr($cleanPostcode,0,strlen($filterPostcode)) != $filterPostcode) $includeThisCustomer = false;

						}

						#} Value From + To

							#} Calc total
							$totVal = zeroBS_customerTotalValue($potentialCustomer['id'],$fullCustomer['invoices'],$fullCustomer['transactions']);

							#} Compare
							if (isset($appliedFilters['valuefrom']) && !empty($appliedFilters['valuefrom'])){

								#} If less than valuefrom, then remove
								if ($totVal < $appliedFilters['valuefrom']) $includeThisCustomer = false;

							}
							if (isset($appliedFilters['valueto']) && !empty($appliedFilters['valueto'])){

								#} If more than valueto, then remove
								if ($totVal > $appliedFilters['valueto']) $includeThisCustomer = false;

							}


						#} Has Quote, inv, transaction
						if (isset($appliedFilters['hasquote']) && $appliedFilters['hasquote'] && count($fullCustomer['quotes']) < 1) $includeThisCustomer = false;
						if (isset($appliedFilters['hasinv']) && $appliedFilters['hasinv'] && count($fullCustomer['invoices']) < 1) $includeThisCustomer = false;
						if (isset($appliedFilters['hastransact']) && $appliedFilters['hastransact'] && count($fullCustomer['transactions']) < 1) $includeThisCustomer = false;


						#} Finally... include or not?
						if ($includeThisCustomer) $endingCustomerList[] = $fullCustomer;


					}
					#} ===============================================================
					#} / end filter post-query
					#} ===============================================================


					#} External source/id (LATER)

					   #'meta_key'   => 'zbs_customer_ext_'.$approvedExternalSource,
					   #'meta_value' => $externalID


					#} Set as global
					$zbsCustomerFiltersCurrentList = $endingCustomerList;


		} else { #} / end of "is already cached/not needed"

			#} Use cached list
			$endingCustomerList = $zbsCustomerFiltersCurrentList;

		}

		#} Do paging (lol wrong end) #postpaging
		if (!$forcePaging){

			#} Per Page
		 	if ($perPage < 0) $perPage = 10; else $perPage = (int)$perPage;

		 	#} Offset
		 	$thisOffset = 0;
			$actualPage = $page-1; if ($actualPage < 0) $actualPage = 0;
			if ($actualPage > 0) $thisOffset = $perPage*$actualPage;


			#} Anything to do?
			if (isset($thisOffset)){

				#} SLICE
				$endingCustomerList = array_slice($endingCustomerList, $thisOffset, $perPage);

			}

		}

		#DEBUG echo '<h2>endingCustomerList</h2><pre>'; print_r($endingCustomerList); echo '</pre>';

		#} QPI
		#$zbsQPI['retrieveCustomers3'] = round(zeroBSCRM_mtime_float() - #$zbsQPI['retrieveCustomers3'],2).'s';


		#} Return
		return $endingCustomerList;
}

function zeroBS__customerFiltersRetrieveCustomerCount(){

	#} REQUIRES that zbs_customerFiltersRetrieveCustomers has been run BEFORE this
	global $zbsCustomerFiltersCurrentList;

	if (isset($zbsCustomerFiltersCurrentList) && is_array($zbsCustomerFiltersCurrentList)) {
		
		#} return count
		return count($zbsCustomerFiltersCurrentList);

	} else {

		#} Run - without params it'll return first page, but retrieve all into cache var (what we need for count)
		zbs_customerFiltersRetrieveCustomers();

		#} return count
		return count($zbsCustomerFiltersCurrentList);

	}

}

#} Only used by AJAX, also returns top X customers :)
function zeroBS__customerFiltersRetrieveCustomerCountAndTopCustomers($countToReturn=3){

	#} REQUIRES that zbs_customerFiltersRetrieveCustomers has been run BEFORE this
	global $zbsCustomerFiltersCurrentList;

	if (isset($zbsCustomerFiltersCurrentList) && is_array($zbsCustomerFiltersCurrentList)) {
		
		#} return
		return array('count'=>count($zbsCustomerFiltersCurrentList),'top'=>array_slice($zbsCustomerFiltersCurrentList,0,$countToReturn));

	} else {

		#} Run - without params it'll return first page, but retrieve all into cache var (what we need for count)
		$zbsCustomersFiltered = zbs_customerFiltersRetrieveCustomers();

		#} return count
		return array('count'=>count($zbsCustomerFiltersCurrentList),'top'=>array_slice($zbsCustomersFiltered,0,$countToReturn));

	}

}

#} MC was hitting issue where this func causes only 1 page to return (of 10)
#} Perhaps was this zbs_customerFiltersRetrieveCustomers - which is fired without params, that later got params added?
#} Either way, here until segments take over... #MOD
#} Only used by AJAX, also returns top X customers :)
/* NOTE added directly to ZeroBSCRM_MailCampaigns.php as core peeps don't need it as much so don't want to have to deploy 
function zeroBS__customerFiltersRetrieveCustomerCountAndTopCustomersMCHOTFIX($countToReturn=3){

	#} REQUIRES that zbs_customerFiltersRetrieveCustomers has been run BEFORE this
	global $zbsCustomerFiltersCurrentList;

	if (isset($zbsCustomerFiltersCurrentList) && is_array($zbsCustomerFiltersCurrentList)) {
		
		#} return
		return array('count'=>count($zbsCustomerFiltersCurrentList),'top'=>array_slice($zbsCustomerFiltersCurrentList,0,$countToReturn));

	} else {

		#} Run - without params it'll return first page, but retrieve all into cache var (what we need for count)
		#MOD - added 100000 per page ;)
		$zbsCustomersFiltered = zbs_customerFiltersRetrieveCustomers(10000);

		#} return count
		return array('count'=>count($zbsCustomerFiltersCurrentList),'top'=>array_slice($zbsCustomersFiltered,0,$countToReturn));

	}

}*/

/* ======================================================
	/ Customer Filter Funcs
   ====================================================== */
