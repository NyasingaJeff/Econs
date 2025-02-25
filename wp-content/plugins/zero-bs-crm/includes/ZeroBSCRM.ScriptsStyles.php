<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V2.4+
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 06/02/18
 */

#} ===============================================================================
#} === INIT registration & Global Style & Script setups
#} ===============================================================================

// WH moved this from core.php in v3.0 (filing)
// Registers globally used stuff (mostly)
function zeroBSCRM_scriptStyles_initStyleRegister(){

	global $zbs;

		// ===========================================
		// ================  Global  =================

			//registers the styles on admin init
			// semantic 2.2.11 (global inclusion)
			wp_register_style('semanticui', 	plugins_url('/css/semantic.min.css',ZBS_ROOTFILE),'', '2.2.11' );
			wp_register_style('zbs-wp-semanticui', plugins_url('/css/ZeroBSCRM.admin.semantic-ui.min.css',ZBS_ROOTFILE),'', '2.2.11' );
			wp_register_script('semanticuijs' ,plugins_url('/js/lib/semantic.min.js',ZBS_ROOTFILE), array());

			// global
			wp_register_style('zerobscrmadmcss', 	plugins_url('/css/ZeroBSCRM.admin.global.min.css',ZBS_ROOTFILE),array('zbs-wp-semanticui'), $zbs->version );
			wp_enqueue_script('zerobscrmadmjs', plugins_url('/js/ZeroBSCRM.admin.global.min.js',ZBS_ROOTFILE), array( 'jquery' ), $zbs->version);

		// ================ / Global  ================
		// ===========================================


		// ===========================================
		// ============  Page-specific  ==============

			// list view
			wp_register_style('zerobscrmlistview', 	plugins_url('/css/ZeroBSCRM.admin.listview.min.css',ZBS_ROOTFILE),'', $zbs->version );	
			wp_register_script('zerobscrmlistviewjs', plugins_url('/js/ZeroBSCRM.admin.listview.min.js',ZBS_ROOTFILE), array( 'jquery' ),$zbs->version);

				#} localise the list view...
				// WH note: Pretty sure we do this on page, so should janitor this up (later)
				$zbs_translation_array = array(
					'zbs_edit' => __( 'Edit', 'zero-bs-crm' ),
					'zbs_view'=> __( 'View', "zero-bs-crm")
				);
				wp_localize_script( 'zerobscrmlistviewjs', 'zbs_lang', $zbs_translation_array );

			// Single view
			wp_register_style('zerobscrmsingleview', plugins_url('/css/ZeroBSCRM.admin.singleview.min.css',ZBS_ROOTFILE),'', $zbs->version );
			wp_register_script('zerobscrmsingleview' ,plugins_url('/js/ZeroBSCRM.admin.singleview.min.js',ZBS_ROOTFILE), array( 'jquery' ),$zbs->version);

			// edit view
			wp_register_style('zerobscrmeditview', 	plugins_url('/css/ZeroBSCRM.admin.editview.min.css',ZBS_ROOTFILE),'', $zbs->version );
			wp_register_script('zerobscrmeditviewjs', plugins_url('/js/ZeroBSCRM.admin.editview.min.js',ZBS_ROOTFILE), array( 'jquery' ),$zbs->version);
			wp_register_script('zerobscrmtagmetaboxjs', plugins_url('/js/ZeroBSCRM.admin.tags.metabox.min.js',ZBS_ROOTFILE), array( 'jquery' ),$zbs->version);

			// Metabox manager (rearrange them)
			wp_register_script('zerobscrmmm', plugins_url('/js/ZeroBSCRM.admin.metabox.manager.min.js',ZBS_ROOTFILE),array('jquery'), $zbs->version);

			// Segment Editor
			wp_register_style('zbs-segmentedit-css', plugins_url('/css/ZeroBSCRM.admin.segmentedit.min.css',ZBS_ROOTFILE),'', $zbs->version );
			wp_register_script('zbs-segmentedit-js',ZEROBSCRM_URL.'/js/ZeroBSCRM.admin.segmentedit.min.js',array('jquery'), $zbs->version);

			// home dash
			wp_register_style('zerobscrmhomedash', 	plugins_url('/css/ZeroBSCRM.admin.homedash.min.css',ZBS_ROOTFILE),'', '2.12' );
		
			// settings page
			wp_register_style('zerobscrmsettings', 	plugins_url('/css/ZeroBSCRM.admin.settings.min.css',ZBS_ROOTFILE),'', $zbs->version );
			
			// mail delivery wizard
			wp_register_style('zerobscrmmaildeliverywizard', plugins_url('/css/ZeroBSCRM.admin.maildeliverywizard.min.css',ZBS_ROOTFILE),'', $zbs->version );

		// ============ / Page-specific ==============
		// ===========================================

		// ===========================================
		// ============  Libs  =======================

			// jq ui 
			wp_register_script('zerobscrmadmjqui', plugins_url('/js/lib/jquery-ui.min.js',ZBS_ROOTFILE), array( 'jquery' ));
			// above didn't seem to include all libs req for draggable, this does, specific build for listviews
			// 29/07/2017 http://jqueryui.com/download/#!version=1.12.1&components=111111111111111110000000010000000000000000000000
			wp_register_script('zerobscrmadmjquidraggable', plugins_url('/js/lib/jquery-ui.1.12.1.dragdrop.listview.min.js',ZBS_ROOTFILE), array( 'jquery' ));
		
			// jq modal
			wp_register_style('zerobsjsmodal', 	plugins_url('/css/jquery.modal.min.css',ZBS_ROOTFILE),'', 1.17 );
			wp_register_script('zerobsjsmodal' ,plugins_url('/js/lib/jquery.modal.min.js',ZBS_ROOTFILE), array('jquery'));
		
			// font awesome
			wp_register_style('wh-fa-v4-4-0-corecss', plugins_url('/css/font-awesome.min.css',ZBS_ROOTFILE) );
		
			// chart.js
			wp_register_script('zerobscrmchartjs', plugins_url('/js/lib/chart.min.js',ZBS_ROOTFILE),array('jquery'));
		
			// funnel js
			wp_register_script('zerobscrmfunneljs', plugins_url('/js/lib/jquery.funnel.min.js',ZBS_ROOTFILE),array('jquery'));
		
			// sweet alerts - v2 v7.29.0 - 16th nov 18
			wp_register_style('zerobscrmswa', plugins_url('/css/lib/sweetalert2-7.29.0.min.css',ZBS_ROOTFILE) );
			wp_enqueue_script('zerobscrmswa', plugins_url('/js/lib/sweetalert2-7.29.0.min.js',ZBS_ROOTFILE), array( 'jquery' ));

			#} Bloodhound (for typeahead) - use prefetch from https://twitter.github.io/typeahead.js/examples/
			#} https://github.com/twitter/typeahead.js  0.11.1
			wp_enqueue_script('zerobscrmtajs-0-11-1', plugins_url('/js/lib/typeahead.bundle.min.js',ZBS_ROOTFILE), array( 'jquery' ));
			

		// ============ / Libs =======================
		// ===========================================

		// ===========================================
		// ============  Whitelabel  =================

			// WL css overrides (3/11/18 ++)
			// if the file exists, this registers + enqueues css
			if (file_exists( dirname( ZBS_ROOTFILE ) . '/css/wl.adm.override.css')) wp_enqueue_style('zbswladmcss', 	plugins_url('/css/wl.adm.override.css',ZBS_ROOTFILE), array('zerobscrmadmcss','zerobscrmlistview','zerobscrmeditview') );

		// ============ / Whitelabel =================
		// ===========================================



		#} ===============================================================================
		#} === Per page CSS/JS inc (LEGACY < 3.0)
		#} ===============================================================================
		// this is all legacy includes, done away with from v3.0 ++ (added properly via menu's thereafter)
		if (!$zbs->isDAL3()){

			#} Slight efficiency drive, get this here:
			$postTypeStr = ''; if (isset($_GET['post'])) $postTypeStr = get_post_type((int)$_GET['post']);

			#} Quote Builder CSS
			if (
				(isset($_GET['page']) && $_GET['page'] == 'manage-quotes') ||
				(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_quote') || 
				(!empty($postTypeStr) && $postTypeStr == 'zerobs_quote')
					) zeroBSCRM_scriptStyles_admin_quoteBuilder();

			#} Invoice Builder CSS
			if (
				(isset($_GET['page']) && $_GET['page'] == 'manage-invoices') ||
				(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_invoice') || 
				(!empty($postTypeStr) && $postTypeStr == 'zerobs_invoice')
					) zeroBSCRM_scriptStyles_admin_invoiceBuilder();

			#} Transactions
			if (
				(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_transaction') || 
				(!empty($postTypeStr) && $postTypeStr == 'zerobs_transaction')
					) zeroBSCRM_scriptStyles_admin_transactionBuilder();
		
			#} Forms
			if (
				(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_form') || 
				(!empty($postTypeStr) && $postTypeStr == 'zerobs_form')
					) zeroBSCRM_scriptStyles_admin_formBuilder();
		}
		#} ===============================================================================
		#} === /  per page CSS/JS inc (LEGACY < 3.0 )
		#} ===============================================================================

		// LEGACY SUPPORT for ext's with menus
		if (zeroBSCRM_isAdminPage()){
			zeroBSCRM_global_admin_styles();
		}
}

// This builds our globally available zbs_root object with formatting etc. settings
function zeroBSCRM_scriptStyles_enqueueJSRoot(){

	global $zbs;

	// =================================================
	// ================  Global JS ROOT =================
	// here we expose root for js /i refs etc.
	// WH: we also give locale for datetimepickers everywhere (was zbsDateLocaleOverride, now window.zbs_root.localeOptions)
	/*  var localeOpt = {
        format: "DD.MM.YYYY",
        cancelLabel: 'Clear'
    }; */
    // WH: We also expose our number formats (for js $ formating)
    $zbscrm_currency_position = $zbs->settings->get('currency_position');
    $zbscrm_currency_format_thousand_separator = $zbs->settings->get('currency_format_thousand_separator');
    $zbscrm_currency_format_decimal_separator  = $zbs->settings->get('currency_format_decimal_separator');
    $zbscrm_currency_format_number_of_decimals = $zbs->settings->get('currency_format_number_of_decimals');
    $wl = -1; if (zeroBSCRM_isWL()) $wl = 1;
    $dal = 1; if ($zbs->isDAL2()) $dal = 2; if ($zbs->isDAL3()) $dal = 3;
    $zbs_root = array(
    	'crmname' => __('Jetpack CRM','zero-bs-crm'),
        'root' => ZEROBSCRM_URL,
        'localeOptions' => zeroBSCRM_date_localeForDaterangePicker(),
        'locale' => get_locale(),
        'locale_short' => zeroBSCRM_getLocale(false),
        'currencyOptions' => array(

        	'symbol' => zeroBSCRM_getCurrencyChr(),
        	'currencyStr' => zeroBSCRM_getCurrencyStr(),
        	'position' => $zbscrm_currency_position,
        	'thousandSeparator' => $zbscrm_currency_format_thousand_separator,
        	'decimalSeparator' => $zbscrm_currency_format_decimal_separator,
        	'noOfDecimals' => $zbscrm_currency_format_number_of_decimals,
        ),
        'timezone_offset' => (int)get_option('gmt_offset'),
        'timezone_offset_mins' => ((int)get_option('gmt_offset')*60),
        'wl' => $wl,
        'dal' => $dal
    );

    // this is for wl peeps, if set it'll override WYSIWYG logo + settings logo
	$zbs_root['crmlogo'] = 'i/icon-32.png';

	// this is for GLOBAL js (language strings pass through)
	$langArr = array();

			// WH: not 100% sure where to put this, for now, temporarily, here,
			// WH: to decide common sense location (have made filter:)
			$langArr['send'] = __('Send','zero-bs-crm');
			$langArr['sent'] = __('Sent','zero-bs-crm');
			$langArr['notsent'] = __('Not Sent','zero-bs-crm');
			$langArr['cancel'] = __('Cancel','zero-bs-crm');

			// statement send
			$langArr['sendstatement'] = __('Send Statement','zero-bs-crm');
			$langArr['sendstatementaddr'] = __('Send Statement to Email:','zero-bs-crm');
			$langArr['enteremail'] = __('Enter an Email Address..','zero-bs-crm');
			$langArr['statementsent'] = __('Statement was successfully sent','zero-bs-crm');
			$langArr['statementnotsent'] = __('Statement could not be sent at this time','zero-bs-crm');

	$langStrings = apply_filters( 'zbs_globaljs_lang', $langArr);
	if (is_array($langStrings) && count($langStrings) > 0) $zbs_root['lang'] = $langStrings;
	
	// put the nonce into the $zbs_root script localisation
	// window.zbscrmjs_globSecToken wasn't set anywhere (and only in 2 places)
	// both in ZeroBSCRM.AJAX.php
	//
	//        1.)  zeroBSCRM_AJAX_logClose()
	//        2.)  zeroBSCRM_AJAX_getCustInvs()
	//
	// the 2nd one is what showed up with JIRA-ZBS-384

	// window.zbscrmjs_globSecToken -> window.zbs_root.zbsnonce
	// 
	$zbs_root['zbsnonce'] = wp_create_nonce( "zbscrmjs-glob-ajax-nonce" );

	// GENERIC links for building view/edit links in JS globally:

		// v3.0+ - returns a link with _TYPE_ instead of 'contact' etc. used by js func zeroBSCRMJS_obj_viewLink (globally avail)
		$generic_view_link = str_replace('contact','_TYPE_',zbsLink('view',-1,'zerobs_customer',true));
		// v3.0+ - returns a link with _TYPE_ instead of 'contact' etc. used by js func zeroBSCRMJS_obj_editLink (globally avail)
		$generic_edit_link = str_replace('contact','_TYPE_',zbsLink('edit',-1,'zerobs_customer',true));

        $zbs_root['links'] = array(
        	'generic_view' => $generic_view_link,
        	'generic_edit' => $generic_edit_link,
        );

    ##WLREMOVE
    unset($zbs_root['crmlogo']);
    ##/WLREMOVE

    wp_localize_script( 'zerobscrmadmjs', 'zbs_root', $zbs_root ); // This relies on the script being registered by zeroBSCRM_initStyleRegister() above

}

#} ===============================================================================
#} === /  INIT registration & Global Style & Script setups
#} ===============================================================================


#} ===============================================================================
#} === Edit View individual type functions (e.g. quotebuilder)
#} ===============================================================================

// Cleaned from messy core.php routine in v3.0
function zeroBSCRM_scriptStyles_admin_quoteBuilder(){

	wp_enqueue_style('zerobscrm-quotebuilder', 	plugins_url('/css/ZeroBSCRM.admin.quotebuilder.min.css',ZBS_ROOTFILE) );
	wp_enqueue_script('zerobscrm-quotebuilderjs', plugins_url('/js/ZeroBSCRM.admin.quotebuilder.min.js',ZBS_ROOTFILE), array( 'jquery' ));

}

// Cleaned from messy core.php routine in v3.0
// NOTE, this was firing on Invoice List View (WH removed 3.0), now only fires on invoice editor (correctly)
function zeroBSCRM_scriptStyles_admin_invoiceBuilder(){

	#} Bootstrap (for the modals)
	#} ONLY REQUIRED in invoice editor => AND welcome wizard tour now 
	//wp_enqueue_script('zerobscrmbsjs', plugins_url('/js/lib/bootstrap.min.js',ZBS_ROOTFILE), array( 'jquery' ));

	#}  MS invoice stuff. xxx
	wp_enqueue_style('zerobscrm-invoicebuilder', 	plugins_url('/css/ZeroBSCRM.admin.invoicebuilder.min.css',ZBS_ROOTFILE) );
	wp_enqueue_script('zerobscrm-invoicebuilderjs', plugins_url('/js/ZeroBSCRM.admin.invoicebuilder.min.js',ZBS_ROOTFILE), array( 'jquery', 'semanticuijs' ));

			//localise the invoice builder strings...
	$zbs_invtranslation_array = array(
		'zbs_item_name' => __( 'Item Name', 'zero-bs-crm' ),
		'zbs_item_desc'	=> __('Enter a detailed description (optional)',"zero-bs-crm"),
		'zbs_add_row'	=> __('Add Row',"zero-bs-crm"),
		'zbs_remove_row' => __('Remove Row',"zero-bs-crm")
	);

	$zbs_links = array(
		'admin_url' => admin_url()
	);

	wp_localize_script( 'zerobscrm-invoicebuilderjs', 'zbs_lang', $zbs_invtranslation_array );
	wp_localize_script( 'zerobscrm-invoicebuilderjs', 'zbs_links', $zbs_links);

}

// edit transaction page scripts.
function zeroBSCRM_scriptStyles_admin_transactionBuilder(){
	
	wp_enqueue_script('zerobscrm-transedit-js', ZEROBSCRM_URL .'js/ZeroBSCRM.admin.transactioneditor.min.js', array('jquery'));
	wp_enqueue_style('zerobscrmtranscss', 	plugins_url('/css/ZeroBSCRM.admin.transactionedit.min.css',ZBS_ROOTFILE),'', 1.17 );

}

// Cleaned from messy core.php routine in v3.0
function zeroBSCRM_scriptStyles_admin_formBuilder(){

	wp_enqueue_style('zerobscrmformcss', 	plugins_url('/css/ZeroBSCRM.admin.frontendforms.min.css',ZBS_ROOTFILE),'', 1.17 );

}

#} ===============================================================================
#} === /  Edit View individual type functions (e.g. quotebuilder)
#} ===============================================================================



#} ===============================================================================
#} === Unsorted Styles from pre v3.0
#} ===============================================================================
function zeroBSCRM_global_admin_styles(){

	// It seems we've got this firing multiple times, (WH spotted 2.4), so am putting this lame protection place. 
	// Ideally need to stop it firing twice
	if (!defined('ZBS_GAS')){
	
	//enqueue these (via the admin_print_styles-{$page})

		// prev core
		wp_enqueue_style( 'zerobscrmadmcss' );
		wp_enqueue_style( 'zerobsjsmodal' );
		wp_enqueue_script( 'zerobsjsmodal');
		wp_enqueue_style( 'wh-fa-v4-4-0-corecss'	);
		wp_enqueue_style( 'zerobscrmswa'		);

		// moment everywhere (from 2.98)
		wp_enqueue_script('wh-moment-v2-8-1-js', untrailingslashit(ZEROBSCRM_URL) .'/js/lib/moment-with-locales.min.js',array('jquery'));

		// semantic everywhere (on our pages)
		wp_enqueue_style( 'zbs-wp-semanticui' );  
		wp_enqueue_script( 'semanticuijs');	

		// telemetry
		// V3.0 No more telemetry zeroBSCRM_teleLog('');


		#}bootstrap JS (for onboarding tour...)
		//wp_enqueue_script('zerobscrmbsjs', plugins_url('/js/lib/bootstrap.min.js',ZBS_ROOTFILE), array( 'jquery' ));

		#} EDIT - using STANDALONE tour now instead

		do_action('zbs-global-admin-styles');

		// DEFINE ZBS page :)
		if (!defined('ZBS_PAGE')) define('ZBS_PAGE',true);

		// DEFINE ZBS styles fired / dupe check protection
		if (!defined('ZBS_GAS')) define('ZBS_GAS',true);

	} // / dupe check protection


}

// 2.98.2 - MS styles tidy up that were inline
// for the Extension Manager page (was inline)
function zeroBSCRM_extension_admin_styles(){
	wp_register_style('zerobscrmexts', ZEROBSCRM_URL .'/css/ZeroBSCRM.admin.extensions-page.min.css');
	wp_enqueue_style( 'zerobscrmexts' );
}

// 2.98.2 - MS tidy up of style into compressed sheet (was inline)
function zeroBSCRM_intro_admin_styles(){
	wp_register_style('zerobscrmintro', ZEROBSCRM_URL .'/css/ZeroBSCRM.admin.intro.min.css');
	wp_enqueue_style( 'zerobscrmintro' );
}


function zeroBSCRM_email_styles(){
	wp_register_style('zerobscrmemails', ZEROBSCRM_URL .'/css/ZeroBSCRM.admin.email.min.css');
	wp_enqueue_style( 'zerobscrmemails' );
	wp_register_script('zerobsjsemail' , ZEROBSCRM_URL .'/js/ZeroBSCRM.admin.email.min.js', array('jquery'));
	wp_enqueue_script( 'zerobsjsemail');
	do_action('zbs_extra_email_script_styles');
}
function zeroBSCRM_admin_styles_ui2_listview(){
	
	//enqueue these (via the admin_print_styles-{$page})

			// semantic 2.2.11 (EVENTUALLY these PROBS shouldn't be global)
			wp_enqueue_style( 'zerobscrmlistview' );
			wp_enqueue_script( 'semanticuijs');
			// Removed at request of plugin reviewers. (used wp core ver) wp_enqueue_script( 'zerobscrmadmjqui');
			wp_enqueue_script('jquery-ui-sortable');

			// our list view css
			wp_enqueue_script( 'zerobscrmlistviewjs');
			
			// 2.97.9 - wh updated moment
			//wp_enqueue_script('wh-moment-v2-1-3-js', ZEROBSCRM_URL .'/js/lib/moment.min.js', array('jquery'));
			zeroBSCRM_enqueue_libs_js_momentdatepicker();
}
function zeroBSCRM_admin_styles_ui2_editview(){
	
	//enqueue these (via the admin_print_styles-{$page})

			// Removed at request of plugin reviewers. (used wp core ver) wp_enqueue_script( 'zerobscrmadmjqui');
			wp_enqueue_script('jquery-ui-sortable');
			// semantic 2.2.11 (EVENTUALLY these PROBS shouldn't be global)
			wp_enqueue_style( 'zerobscrmeditview' );
			wp_enqueue_script( 'semanticuijs');
			wp_enqueue_script( 'zerobscrmeditviewjs');
			wp_enqueue_script( 'zerobscrmtagmetaboxjs');
			wp_enqueue_script( 'zerobscrmmm'); // metabox manager
			//wp_enqueue_script('wh-moment-v2-1-3-js', ZEROBSCRM_URL .'/js/lib/moment.min.js', array('jquery'));
			
			// daterange + moment
			zeroBSCRM_enqueue_libs_js_momentdatepicker();

			// catch type-specific includes :)
			if (isset($_GET['zbstype']) && !empty($_GET['zbstype'])){

				switch ($_GET['zbstype']){

					case 'quote':
						zeroBSCRM_scriptStyles_admin_quoteBuilder();
						break;

					case 'invoice':
						zeroBSCRM_scriptStyles_admin_invoiceBuilder();
						break;

					case 'transaction':
						zeroBSCRM_scriptStyles_admin_transactionBuilder();
						break;

					case 'form':
						zeroBSCRM_scriptStyles_admin_formBuilder();
						break;

					case 'event':
						zeroBSCRM_calendar_admin_styles();
						break;


				}
			}

			// extra scripts (e.g. Twilio Connect hooks into this - otherwise the button will not do anything)
			do_action('zbs_postenqueue_editview'); //zbs_extra_custeditscripts
}
function zeroBSCRM_settingspage_admin_styles(){

	global $zbs;

	// needs datepicker (MS needed for paypal sync, was a gross hack elsewhere so put here)
	wp_enqueue_script('wh-daterangepicker-v2-1-21-js',untrailingslashit(ZEROBSCRM_URL).'/js/lib/daterangepicker.min.js',array('jquery'));		

	wp_register_script('zerobscrm-settingspage-js' , ZEROBSCRM_URL .'/js/ZeroBSCRM.admin.settings.min.js', array('jquery'), $zbs->version);
	wp_enqueue_script( 'zerobscrm-settingspage-js');
	wp_register_style('zerobscrmsettings', ZEROBSCRM_URL .'/css/ZeroBSCRM.admin.settings.min.css',array(), $zbs->version);
	wp_enqueue_style( 'zerobscrmsettings' );
	
	#} Field Sorts
	if (isset($_GET['tab']) && $_GET['tab'] == 'fieldsorts'){

		#} jQ UI
		//wp_enqueue_script('zerobscrmadmjqui', plugins_url('/js/lib/jquery-ui.min.js',ZBS_ROOTFILE), array( 'jquery' ));
		// can just call here as registered in main admin init now (2.2 29/07/2017)
		wp_enqueue_script( 'zerobscrmadmjqui');

		#} Our custom sortables css
		wp_enqueue_style('zerobscrmsortscss', 	plugins_url('/css/ZeroBSCRM.admin.sortables.min.css',ZBS_ROOTFILE),array(),'2.10.3');

	}
}

// These now get rolled into zeroBSCRM_global_admin_styles 
/*function zeroBSCRM_admin_styles_ui2_semantic(){
			wp_enqueue_style( 'zbs-wp-semanticui' );  
			wp_enqueue_script( 'semanticuijs');	
}*/
/* WH adding in for old ext compatibility e.g. inv pro was still producing error for lack of func */
function zeroBSCRM_admin_styles_ui2_semantic(){}

function zeroBSCRM_admin_styles_ui2_semantic_settingspage(){
			// These now get rolled into zeroBSCRM_global_admin_styles wp_enqueue_style( 'zbs-wp-semanticui' );  
			wp_enqueue_style( 'zerobscrmmaildeliverywizard' );  
			// These now get rolled into zeroBSCRM_global_admin_styles wp_enqueue_script( 'semanticuijs');	
			wp_enqueue_style('zerobscrmsettings');
}

function zeroBSCRM_admin_styles_chartjs(){
			wp_enqueue_script( 'zerobscrmchartjs');	
			wp_enqueue_script( 'zerobscrmfunneljs');	
}
function zeroBSCRM_admin_styles_singleview(){
	
	// single item view
	wp_enqueue_style( 'zerobscrmsingleview' );
	wp_enqueue_script( 'zerobscrmsingleview');	

}

function zeroBSCRM_admin_styles_homedash(){
	//home dashboard styles
	wp_enqueue_style( 'zerobscrmhomedash' ); 
}

function zeroBSCRM_admin_scripts_editcust(){
	
	zeroBSCRM_dequeueJSModal();

	//scripts here for the edit customer page (for the "Quick Add Company, Events, etc")
	wp_enqueue_script('zerobscrmcustjs');
}


function zeroBSCRM_calendar_admin_styles(){


	// WH Note: No idea, but SOME references on this page would produce zbs//css instead of zbs/css - so using untrailingslashit
    wp_enqueue_style(  'fullcalendar', untrailingslashit(ZEROBSCRM_URL) .'/css/fullcalendar.min.css');

	//2.97.9 WH updated moment
	//wp_enqueue_script('wh-moment-v2-1-3-js', ZEROBSCRM_URL .'js/lib/moment.min.js', array('jquery'));
	zeroBSCRM_enqueue_libs_js_momentdatepicker();


	wp_register_style('zerobscrm-events', ZEROBSCRM_URL .'css/ZeroBSCRM.admin.events.min.css');
	
	//v3.9.0 MS MOD?!??!
	//wp_register_script('zerobscrm-calendar-js' , ZEROBSCRM_URL .'js/lib/fullcalendar.mod.min.js', array('jquery','wh-moment-v2-8-1-js'));//2.97.9 upgraded moment array('jquery','wh-moment-v2-1-3-js'));
	//wp_register_style('zerobscrm-calendar', ZEROBSCRM_URL .'css/fullcalendar.min.css');
	//wp_register_style('zerobscrm-calendar-print', ZEROBSCRM_URL .'css/fullcalendar.print.min.css' );
	//v3.10 - (WH updated 2.97.9) - bringing across mikes mods
	wp_register_script('zerobscrm-calendar-js' , ZEROBSCRM_URL .'js/fullcalendar.mod.min.js', array('jquery','wh-moment-v2-8-1-js'));//2.97.9 upgraded moment array('jquery','wh-moment-v2-1-3-js'));
	wp_register_style('zerobscrm-calendar', ZEROBSCRM_URL .'css/lib/fullcalendar.min.css');
	wp_register_style('zerobscrm-calendar-print', ZEROBSCRM_URL .'css/lib/fullcalendar.print.min.css' );
	wp_register_script('zerobscrm-events-js' , ZEROBSCRM_URL .'js/ZeroBSCRM.admin.task.min.js', array('jquery','wh-moment-v2-8-1-js','zerobscrm-calendar-js'));
	

	// LOCALE Specific
	$languageTag = zeroBSCRM_getLocale();
	$languageTagShort = zeroBSCRM_getLocale(false);
	if (file_exists(ZEROBSCRM_PATH.'/js/lib/calendar-locale/'.$languageTag .'.js')){
	
		// e.g. en-gb	
		wp_enqueue_script('zerobscrm-calendar-js-locale', ZEROBSCRM_URL . 'js/lib/calendar-locale/'.$languageTag .'.js',array('zerobscrm-calendar-js'));
	
	} else {
		
		if (file_exists(ZEROBSCRM_PATH.'/js/lib/calendar-locale/'.$languageTagShort .'.js')){

			// e.g. en
			wp_enqueue_script('zerobscrm-calendar-js-locale', ZEROBSCRM_URL . 'js/lib/calendar-locale/'.$languageTagShort .'.js',array('zerobscrm-calendar-js'));

		} else {

			// no language tag exists, notice?

		}

	} 


	wp_enqueue_style( 'zerobscrm-calendar' );
	wp_enqueue_style( 'zerobscrm-events' );
	// wp_enqueue_style( 'zerobscrm-calendar-print' );	

	zeroBSCRM_enqueue_libs_js_momentdatepicker();
	
	wp_enqueue_script('zerobscrm-calendar-js');
	wp_enqueue_script('zerobscrm-events-js');

}

// Styles + Scripts for Beta Feedback sys
function zeroBSCRM_betaFeedback_styles(){

	// styles in global css

	// js here
	wp_register_script('zerobscrmjs-bfeedback' , ZEROBSCRM_URL .'/js/ZeroBSCRM.admin.betafeedback.min.js', array('jquery'));
	wp_enqueue_script( 'zerobscrmjs-bfeedback');

}

function zeroBSCRM_dequeueJSModal(){

	wp_dequeue_style('zerobsjsmodal');
	wp_dequeue_script('zerobsjsmodal');
}

function zeroBSCRM_add_admin_styles( $hook ) {

    global $post;
    $zeroBSCRM_custom_slug = ''; if (isset($_GET['zbsslug'])) $zeroBSCRM_custom_slug = sanitize_text_field($_GET['zbsslug']);

    if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'edit-tags.php' || 'edit.php' ) { // || $hook == 'edit.php'
        if (is_object($post)){
	        if ( 'zerobs_customer' === $post->post_type || 'zerobs_quote' === $post->post_type || 'zerobs_company' === $post->post_type || 'zerobs_invoice' === $post->post_type || 'zerobs_transaction' === $post->post_type || 'zerobs_form' === $post->post_type || 'zerobs_mailcampaign' === $post->post_type || 'zerobs_event' === $post->post_type || 'zerobs_quo_template' === $post->post_type   ) {     
	           zeroBSCRM_global_admin_styles();
	            //semantic throughout... 
	            if('zerobs_customer' === $post->post_type || 'zerobs_quo_template' === $post->post_type || 'zerobs_transaction' === $post->post_type || 'zerobs_quote' === $post->post_type || 'zerobs_invoice' === $post->post_type || 'zerobs_form' === $post->post_type || 'zerobs_company' === $post->post_type || 'zerobs_event' === $post->post_type){
	            	//semantic roll out (in stages)
	            	// These now get rolled into zeroBSCRM_global_admin_styles zeroBSCRM_admin_styles_ui2_semantic();

	            	//single customer stuff (like click to SMS etc...)
	            	zeroBSCRM_admin_scripts_editcust();

	            	do_action('zbs_extra_custeditscripts');
				}
				
				if('zerobs_event' === $post->post_type){
					zeroBSCRM_calendar_admin_styles();
				}
	            
	        }
    	}else if(isset($_GET['post_type']) && ($_GET['post_type'] == 'zerobs_customer' || $_GET['post_type'] == 'zerobs_quo_template' || $_GET['post_type'] == 'zerobs_event' || $_GET['post_type'] == 'zerobs_transaction' || $_GET['post_type'] == 'zerobs_company')){
    		zeroBSCRM_global_admin_styles();
    		// These now get rolled into zeroBSCRM_global_admin_styles zeroBSCRM_admin_styles_ui2_semantic();
    	}else if(isset($_GET['post_type']) && ($_GET['post_type'] == 'zerobs_form' || $_GET['post_type'] ==  'zerobs_event')){
    		zeroBSCRM_global_admin_styles();
    		// These now get rolled into zeroBSCRM_global_admin_styles zeroBSCRM_admin_styles_ui2_semantic();
    	}else if($zeroBSCRM_custom_slug == 'zbs-add-user' || $zeroBSCRM_custom_slug == 'zbs-edit-user'){
    		zeroBSCRM_global_admin_styles();
    		// These now get rolled into zeroBSCRM_global_admin_styles zeroBSCRM_admin_styles_ui2_semantic();
    	} 

    	// this needed to be separate to the above for some reason on some hosts.
    	if (isset($_GET['page']) && $_GET['page'] == 'zbs-add-edit'){

    		zeroBSCRM_admin_styles_singleview();
    	}
    }
    
}
add_action( 'admin_enqueue_scripts', 'zeroBSCRM_add_admin_styles', 10, 1 );

#} Public ver :)
/*v3.0 removed this, no CPT's and don't think was using anyhow by 2.98+
function zeroBSCRM_add_public_scripts( $hook ) {

    global $post;

    #} Conditionally, for front end: http://wordpress.stackexchange.com/questions/10287/load-scripts-based-on-post-type
    #if ($post->post_type == 'zerobs_quote' && !is_admin()){ 
    if( is_single() && get_query_var('post_type') && 'zerobs_quote' == get_query_var('post_type') ){

    	#} Public proposals
		wp_enqueue_style('zerobscrmpubquocss', ZEROBSCRM_URL .'/css/ZeroBSCRM.public.quotes.min.css' );

    }
}
add_action( 'wp_enqueue_scripts', 'zeroBSCRM_add_public_scripts', 10, 1 ); */


	// THIS IS LEGACY! It's used for <3.0 on CPT edit pages. Otherwise enqueue properly like in zeroBSCRM_settingspage_admin_styles via menus :)
	function zeroBSCRM_load_libs_js_momentdatepicker(){
	    add_action( 'admin_enqueue_scripts', 'zeroBSCRM_enqueue_libs_js_momentdatepicker' );
	}

	function zeroBSCRM_enqueue_libs_js_momentdatepicker(){

		#} moment.js and daterangepicker
		// 2.97.9 wh updated from 2.13 to 2.8.1 (inc locales) 
		//wp_enqueue_script('wh-moment-v2-1-3-js', ZEROBSCRM_URL .'/js/lib/moment.min.js',array('jquery'));
		// WH: 2.98 - WH moved this to be part of ZBS global as used throughout.
		//wp_enqueue_script('wh-moment-v2-8-1-js', ZEROBSCRM_URL .'/js/lib/moment-with-locales.min.js',array('jquery'));

		/*//moment locale
		// WH can you check this out, e.g. the british one is en-gb.js (whereas the below will give just en)
		$lang = explode('-', get_bloginfo('language'));
		$lang = $lang[0];

		wp_enqueue_script('wh-moment-locale', plugins_url('/js/lib/moment-locale/'.$lang .'.js',__FILE__),array('wh-moment-v2-1-3-js'));
		*/
		//moment locale
		// 2.97.9+  this is dealt with in admin.global.js 
		// https://stackoverflow.com/questions/17493309/how-do-i-change-the-language-of-moment-js
		// moment now comes with locales.
		/*
		$languageTag = zeroBSCRM_getLocale();
		$languageTagShort = zeroBSCRM_getLocale(false);
		if (file_exists(ZEROBSCRM_PATH.'/js/lib/moment-locale/'.$languageTag .'.js')){
		
			// e.g. en-gb	
			wp_enqueue_script('wh-moment-locale', ZEROBSCRM_URL .'/js/lib/moment-locale/'.$languageTag .'.js',array('wh-moment-v2-1-3-js'));
		
		} else {
			
			if (file_exists(ZEROBSCRM_PATH.'/js/lib/moment-locale/'.$languageTagShort .'.js')){

				// e.g. en
				wp_enqueue_script('wh-moment-locale', ZEROBSCRM_URL.'/js/lib/moment-locale/'.$languageTagShort .'.js',array('wh-moment-v2-1-3-js'));

			} else {

				// no language tag exists, notice?

			}

		} */


		wp_enqueue_script('wh-daterangepicker-v2-1-21-js',untrailingslashit(ZEROBSCRM_URL).'/js/lib/daterangepicker.min.js',array('jquery'));
		#} CSS is wrapped into main plugin css

	}
	

	#} Customer Filters
	function zeroBSCRM_load_libs_js_customerfilters(){
	    add_action( 'admin_enqueue_scripts', 'zeroBSCRM_enqueue_libs_js_customerfilters' );
	}
	function zeroBSCRM_enqueue_libs_js_customerfilters(){

		#} Customer Filters
		wp_enqueue_script('zbs-js-customerfilters-v1', ZEROBSCRM_URL.'/js/ZeroBSCRM.admin.customerfilters.min.js',array('jquery'));
	}

	#} Media Manager
	function zeroBSCRM_enqueue_media_manager(){
		wp_enqueue_media();
		wp_enqueue_script( 'custom-header' );
	}
	add_action('admin_enqueue_scripts', 'zeroBSCRM_enqueue_media_manager');

// material admin 3.5 compat
function zeroBSCRM_admin_styles_compat_materialAdmin(){

	wp_register_style('zbs-compat-matadm', ZEROBSCRM_URL .'/css/integrations/ZeroBSCRM.MaterialAdmin.min.css');
	wp_enqueue_style( 'zbs-compat-matadm' );
	
}

// compat css :)
function zeroBSCRM_add_admin_compat_scripts($hook) {

	// check
	$zbs_custom_admin = zeroBSCM_custom_admin_detect();
	if ($zbs_custom_admin == 'material') zeroBSCRM_admin_styles_compat_materialAdmin();
    
}
add_action( 'admin_enqueue_scripts', 'zeroBSCRM_add_admin_compat_scripts');



function zeroBSCRM_add_admin_segmenteditor_scripts($hook) {

	global $zbs;

	// if our page page=zbs-add-edit&action=edit&zbstype=segment
	if(isset($_GET['page']) && $_GET['page'] == $zbs->slugs['addedit'] && isset($_GET['zbstype']) && $_GET['zbstype'] == 'segment'){
		
		// NOTE: these are used in mail campaigns v2, make sure if change name here, change there
		wp_enqueue_script( 'zbs-segmentedit-js');
		wp_enqueue_style( 'zbs-segmentedit-css');
		zeroBSCRM_enqueue_libs_js_momentdatepicker();
	}
    
}
add_action( 'admin_enqueue_scripts', 'zeroBSCRM_add_admin_segmenteditor_scripts');


// MAIL TEMPLATES
#} Code Editor - limit to only our edit templates slug... :) 
function zeroBSCRM_mailTemplatesEnqueue(){
	global $zbs, $pagenow;

	$slug = ''; if (isset($_GET['page'])) $slug = sanitize_text_field($_GET['page']);
    if ( $slug != $zbs->slugs['email-templates']) {
        return;
    }

	if(isset($_GET['zbs_template_editor']) && !empty($_GET['zbs_template_editor'])){
		if($_GET['zbs_template_editor'] != 1){
			return;
		}
	}

	if(!isset($_GET['zbs_template_editor'])){
		return;
	}
   
    // Enqueue code editor and settings for manipulating HTML.
    $settings = wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
 
    // Bail if user disabled CodeMirror.
    if ( false === $settings ) {
        return;
    }
 
    wp_add_inline_script(
        'code-editor',
        sprintf(
            'jQuery( function() { wp.codeEditor.initialize( "zbstemplatehtml", %s ); } );',
            wp_json_encode( $settings )
        )
    );
}
add_action( 'admin_enqueue_scripts', 'zeroBSCRM_mailTemplatesEnqueue');

#} ===============================================================================
#} === / Unsorted Styles from pre v3.0
#} ===============================================================================


function zeroBSCRM_admin_styles_exportTools(){

	global $zbs;
	
	wp_register_style('zbs-adm-css-export', 	plugins_url('/css/ZeroBSCRM.admin.export.min.css',ZBS_ROOTFILE),array('zbs-wp-semanticui'), $zbs->version );

	wp_enqueue_style( 'zbs-adm-css-export' );
}


// used in form templates & shortcode outputted forms.
// https://wordpress.stackexchange.com/questions/165754/enqueue-scripts-styles-when-shortcode-is-present
function zeroBSCRM_forms_scriptsStylesRegister(){

	global $zbs;

		// js
        wp_register_script('zbsfrontendformsjs', plugins_url('/js/ZeroBSCRM.public.leadform.js',ZBS_ROOTFILE), array( 'jquery' ), $zbs->version);
        
        // css
        wp_register_style('zbsfrontendformscss', plugins_url('/css/ZeroBSCRM.public.frontendforms.min.css',ZBS_ROOTFILE), array(), $zbs->version );

}
add_action( 'wp_enqueue_scripts', 'zeroBSCRM_forms_scriptsStylesRegister');
