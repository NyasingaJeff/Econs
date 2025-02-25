<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V1.0
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 07/03/2017
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

/*
#} #coreintegration - MOVED to core.extensions

#} Log with main core (Has to be here, outside of all funcs)
#} Note, this function permacode (e.g. woo) needs to have a matching function for settings page named "zeroBSCRM_extensionhtml_settings_woo" (e.g.)
global $zeroBSCRM_extensionsInstalledList; 
if (!is_array($zeroBSCRM_extensionsInstalledList)) $zeroBSCRM_extensionsInstalledList = array();
$zeroBSCRM_extensionsInstalledList[] = 'csvimporterlite'; #woo #pay #env
#} Super simpz function to return this extensions name to core (for use on settings tabs etc.)
function zeroBSCRM_extension_name_csvimporterlite(){ return 'CSV Importer LITE'; }
*/

	#} IMPORTANT FOR SETTINGS EXTENSIONS MODEL!
	#} Unique str for each plugin extension, e.g. "mail" or "wooimporter" (lower case no numbers or spaces/special chars)
	$zeroBSCRM_CSVImporterconfigkey = 'csvimporterlite';
	$zeroBSCRM_extensions[] = $zeroBSCRM_CSVImporterconfigkey;

	global $zeroBSCRM_CSVImporterLiteslugs; $zeroBSCRM_CSVImporterLiteslugs = array();
	$zeroBSCRM_CSVImporterLiteslugs['app'] = 'zerobscrm-csvimporterlite-app'; // NOTE: this should now be ignored, use $zbs->slugs['csvlite'] as is WL friendly

	global $zeroBSCRM_CSVImporterLiteversion;
	$zeroBSCRM_CSVImporterLiteversion = '2.0';


/* No settings included in CSV Importer LITE - pro only :)
#} If legit... #CORELOADORDER
if (!defined('ZBSCRMCORELOADFAILURE')){
	
	#} Should be safe as called from core

	#} Settings Model. req. > v1.1 

		#} Init settings model using your defaults set in the file above
		#} Note "zeroBSCRM_extension_extensionName_defaults" var below must match your var name in the config.
		global $zeroBSCRM_CSVImporterSettings, $zeroBSCRM_extension_extensionName_defaults;
		$zeroBSCRM_CSVImporterSettings = new WHWPConfigExtensionsLib($zeroBSCRM_CSVImporterconfigkey,$zeroBSCRM_extension_extensionName_defaults);

} */

function zeroBSCRM_CSVImporterLite_extended_upload ( $mime_types =array() ) {
  
   //$mime_types['csv']  = "text/csv";
   //wonder it actually this..
   $mime_types['csv']  = "text/plain";
  
   return $mime_types;
}  
add_filter('upload_mimes', 'zeroBSCRM_CSVImporterLite_extended_upload');

#} Add le admin menu
function zeroBSCRM_CSVImporterLiteadmin_menu() {

	global $zbs,$zeroBSCRM_CSVImporterLiteslugs; #req

	wp_register_style('zerobscrm-csvimporter-admcss', ZEROBSCRM_URL.'css/ZeroBSCRM.admin.csvimporter.min.css' );
    $csvAdminPage = add_submenu_page( null, 'CSV Importer', 'CSV Importer', 'admin_zerobs_customers', $zbs->slugs['csvlite'], 'zeroBSCRM_CSVImporterLitepages_app', 1 ); //$zeroBSCRM_CSVImporterLiteslugs['app']
	add_action( "admin_print_styles-{$csvAdminPage}", 'zeroBSCRM_CSVImporter_lite_admin_styles' );
	add_action( "admin_print_styles-{$csvAdminPage}", 'zeroBSCRM_global_admin_styles' ); #} and this.


}
add_action('zerobs_admin_menu', 'zeroBSCRM_CSVImporterLiteadmin_menu');

function zeroBSCRM_CSVImporter_lite_admin_styles(){
	wp_enqueue_style( 'zerobscrm-csvimporter-admcss' );

}


#================== Admin Pages

#} Admin Page header
function zeroBSCRM_CSVImporterLitepages_header($subpage=''){

	global $wpdb, $zbs, $zeroBSCRM_CSVImporterLiteversion;	#} Req
	
	if (!current_user_can('admin_zerobs_customers'))  { wp_die( __('You do not have sufficient permissions to access this page.',"zero-bs-crm") ); }
    
    
?>


    <script type="text/javascript">

        jQuery(document).ready(function($){

        jQuery('.learn')
          .popup({
            inline: false,
            on:'click',
            lastResort: 'bottom right',
        });

        });
    </script>

    <div id="zbs-admin-top-bar">
      <div id="zbs-list-top-bar">
          <h2 class="zbs-white"><span class="add-new-button"><?php _e('CSV Importer Lite',"zero-bs-crm"); if (!empty($subpage)) echo ': '.$subpage; ?></span>
            <div class="ui button grey tiny learn" id="learn"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Learn</div>
            <div class="ui special popup top left transition hidden" id="learn-pop">
              <h3 class="learn-h3"><?php _e("Import contacts from CSV","zero-bs-crm");?></h3>
              <div class="content">
                <p>
                    <?php _e("If you have contacts you need to import to Jetpack CRM, doing so via a CSV is a quick and easy way to get your data in.","zero-bs-crm"); ?>
                </p>
                <p>
                    <strong><?php _e("Formatting Tips","zero-bs-crm");?></strong> <?php _e("it's important that you format your CSV file correctly for the upload. We have written a detailed guide on how to do this below.","zero-bs-crm");?> 
                </p>

				<?php 
				##WLREMOVE
					if (!empty($zbs->urls['extcsvimporterpro'])) { ?>

						<p><?php _e("Want to import companies as well as keep a record of your imports.","zero-bs-crm"); ?>
						<a href="<?php echo $zbs->urls['extcsvimporterpro']; ?>" target="_blank">
						<?php _e("CSV importer PRO is the perfect tool.","zero-bs-crm"); ?></a></p>

					<?php 
					} 
				##/WLREMOVE
				?>

                <br/>
				<?php
				##WLREMOVE
				?>
                <a href="https://kb.jetpackcrm.com/knowledge-base/what-should-my-csv-be-formatted-like/" target="_blank" class="ui button orange"><?php _e("Learn More","zero-bs-crm");?></a>
				<?php
				##/WLREMOVE
				?>
			  </div>
              <div class="video">
        
                  <!--
                  <iframe src="https://www.youtube.com/embed/2YAO7hEICwk?ecver=2" width="385" height="207" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen style="margin-top:-15px;"></iframe>
                  -->
              </div>
            </div>

            <?php if (!empty($zbs->urls['extcsvimporterpro'])) { ?>
            	<a href="<?php echo $zbs->urls['extcsvimporterpro']; ?>" target="_blank" class="ui button blue tiny" id="gopro"><?php _e("Get CSV Importer Pro","zero-bs-crm"); ?></a>
            <?php } ?>

          </h2>
        </div>
      </div>


<div id="sgpBody">

    <div id="ZeroBSCRMAdminPage" class="ui segment">
    <?php 	
	
	#} Check for required upgrade
	#zeroBSCRM_CSVImportercheckForUpgrade();
	
}


#} Admin Page footer
function zeroBSCRM_CSVImporterLitepages_footer(){
    
	?></div><?php 	
	
}


#} Main Uploader Page
function zeroBSCRM_CSVImporterLitepages_app() {
	
	global $wpdb, $zbs, $zeroBSCRM_CSVImporterLiteversion;	#} Req
	
	if (!current_user_can('admin_zerobs_customers'))  { wp_die( __('You do not have sufficient permissions to access this page.',"zero-bs-crm") ); }
    
	#} Header
    #} Moved into page to control subtitle zeroBSCRM_CSVImporterLitepages_header();

	#} Homepage	
	zeroBSCRM_CSVImporterLitehtml_app(); 

	#} Footer
	zeroBSCRM_CSVImporterLitepages_footer();

?>
</div>
<?php 
}


#} HTML for main app
function zeroBSCRM_CSVImporterLitehtml_app(){

	global $zbsCustomerFields, $zeroBSCRM_CSVImporterLiteslugs,  $zbs;#,$zeroBSCRM_CSVImporterSettings;

	#$settings = $zeroBSCRM_CSVImporterSettings->getAll();
	$default_status = $zbs->settings->get( 'defaultstatus' );
	$settings = array('savecopy'=>false,'defaultcustomerstatus'=> $default_status ? $default_status : __('Customer','zero-bs-crm') );
	$saveCopyOfCSVFile = false; # Not in LITE : ) if (isset($settings['savecopy'])) $saveCopyOfCSVFile = $settings['savecopy'];

	#} 3 stages: 
	#} - Upload
	#} - Map
	#} - Complete (button)
	#} - Process
	$stage = 0; if (isset($_POST['zbscrmcsvimpstage']) && !empty($_POST['zbscrmcsvimpstage']) && in_array($_POST['zbscrmcsvimpstage'],array(1,2,3))){ $stage = (int)sanitize_text_field($_POST['zbscrmcsvimpstage']); }
	$nonceOkay = true;

	#} Validation (pre stage load)
		
		#} Check nonce
    	if (! isset( $_POST['zbscrmcsvimportnonce'] ) || ! wp_verify_nonce( $_POST['zbscrmcsvimportnonce'], 'zbscrm_csv_import' )) {
    		$nonceOkay = false;
    	}

		#} Catch file or nonce errors (back to beginning)
		if ($stage == 1){

			#} Nonce?
			if (!$nonceOkay) {

				#} Send back to start
				$stage = 0; 
				$stageError = 'There was an error uploading your CSV file. Please try again.';
			}

			#} File uploads
			#} https://codex.wordpress.org/Function_Reference/wp_handle_upload
			if ( ! function_exists( 'wp_handle_upload' ) ) {
			    require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$uploadedfile = $_FILES['zbscrmcsvfile'];

			$upload_overrides = array( 'test_form' => false );

			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides ); #

			if ( $movefile && ! isset( $movefile['error'] ) ) {
			    #echo "File is valid, and was successfully uploaded.\n";
			    #var_dump( $movefile );
				
				#} All good. Set var
				$fileDetails = $movefile;

				#} Check valid filetype:

					#} Extension
					$fileName = basename($fileDetails['file']);
					$fileURL = $fileDetails['url']; #} Just passed through for later use
					$extension = pathinfo($fileName, PATHINFO_EXTENSION);
					if (strtolower($extension) != "csv"){

						#} Del file
						unlink( $fileDetails['file'] );
			    		
			    		#} Send back to start
						$stage = 0; 
						$stageError = 'Your file is not a ".csv" file. If you are having continued problems please email support with a copy of your CSV file attached.';

					}
		
					#} MIME
					if ($fileDetails['type'] != "text/csv" && $fileDetails['type'] != "text/plain") {

						#} Del file
						unlink( $fileDetails['file'] );
			    		
			    		#} Send back to start
						$stage = 0; 
						$stageError = 'Your file is not a correctly formatted CSV, please check your file format. If you are having continued problems please email support with a copy of your CSV file attached.';

					}

					#} Check format internally
					$fullCSV = file_get_contents($fileDetails['file']);
					#$csvLines = explode("\n",$fullCSV)
					$csvLines = preg_split("/\\r\\n|\\r|\\n/", $fullCSV);
					if (count($csvLines) <= 0){
			    		
			    		#} Send back to start
						$stage = 0; 
						$stageError = 'Your file does not appear to be a correctly formatted CSV File. We did not find any usable lines to import. If you are having continued problems please email support with a copy of your CSV file attached.';

					}


			} else {
			    /**
			     * Error generated by _wp_handle_upload()
			     * @see _wp_handle_upload() in wp-admin/includes/file.php
			     */
			    #echo $movefile['error'];

			    #} Send back to start
				$stage = 0; 
				$stageError = $movefile['error'];
			}

		}

		#} Check stage 2
		if ($stage == 2){

			#} Grab zbscrmcsvimpignorefirst
			$ignoreFirstLine = false; if (isset($_POST['zbscrmcsvimpignorefirst'])) $ignoreFirstLine = true;

			#} No record of file
			if (!isset($_POST['zbscrmcsvimpf']) || !file_exists($_POST['zbscrmcsvimpf'])){

			    #} Send back to start
				$stage = 0;
				$stageError = 'There was an error maintaining a link to your uploaded CSV file. Please contact support if this error persists, quoting error code #457'; #} lol.

			} else {

				#} set csv file var
				$csvFileLoca = sanitize_text_field($_POST['zbscrmcsvimpf']);
				$csvFileURL = sanitize_text_field($_POST['zbscrmcsvimpfurl']);

				#} Get line count
				$fullCSV = file_get_contents($csvFileLoca);
				#$csvLines = explode("\n",$fullCSV)
				$csvLines = preg_split("/\\r\\n|\\r|\\n/", $fullCSV);

				#} Count total
				$totalCSVLines = count($csvLines);
				if ($ignoreFirstLine) $totalCSVLines--;

			}

			#} Retrieve fields
			$fieldMap = array(); $realFields = 0;
			for ($fieldI = 1; $fieldI <= 30; $fieldI++){

				#} Default to ignore
				$mapTo = 'ignorezbs';

				#} Map :)
				if (isset($_POST['zbscrm-csv-fieldmap-'.$fieldI]) && !empty($_POST['zbscrm-csv-fieldmap-'.$fieldI]) && $_POST['zbscrm-csv-fieldmap-'.$fieldI] !== -1){

					$mapTo = sanitize_text_field($_POST['zbscrm-csv-fieldmap-'.$fieldI]);
					
					#} Count actual mapped fields
					if ($mapTo != 'ignorezbs') $realFields++;

					#} Pass it.
					$fieldMap[$fieldI] = $mapTo;

				} 

			}

			if ($realFields == 0) $stageError = 'No fields were matched. You cannot import customers without at least one field mapped to a customer attribute.';


		}

		#} Check stage 3
		if ($stage == 3){

			#} Grab zbscrmcsvimpignorefirst
			$ignoreFirstLine = false; if (isset($_POST['zbscrmcsvimpignorefirst'])) $ignoreFirstLine = true;

			#} No record of file
			if (!isset($_POST['zbscrmcsvimpf']) || !file_exists($_POST['zbscrmcsvimpf'])){

			    #} Send back to start
				$stage = 0;
				$stageError = 'There was an error maintaining a link to your uploaded CSV file. Please contact support if this error persists, quoting error code #457'; #} lol.

			} else {

				#} set csv file var
				$csvFileLoca = sanitize_text_field($_POST['zbscrmcsvimpf']);
				$csvFileURL = sanitize_text_field($_POST['zbscrmcsvimpfurl']);
				$csvFileName = basename($csvFileLoca);

				#} Get line count
				$fullCSV = file_get_contents($csvFileLoca);
				#$csvLines = explode("\n",$fullCSV)
				$csvLines = preg_split("/\\r\\n|\\r|\\n/", $fullCSV);

			}

			#} Retrieve fields
			$fieldMap = array(); $realFields = 0;
			for ($fieldI = 0; $fieldI <= 30; $fieldI++){

				#} Default to ignore
				$mapTo = 'ignorezbs';

				#} Map :)
				if (isset($_POST['zbscrm-csv-fieldmap-'.$fieldI]) && !empty($_POST['zbscrm-csv-fieldmap-'.$fieldI]) && $_POST['zbscrm-csv-fieldmap-'.$fieldI] !== -1){

					#} NO validation?! #validationtodo
					$mapTo = sanitize_text_field($_POST['zbscrm-csv-fieldmap-'.$fieldI]);
					
					#} Count actual mapped fields
					if ($mapTo != 'ignorezbs') $realFields++;

					#} Pass it.
					$fieldMap[$fieldI] = $mapTo;

				} 

			}

			if ($realFields == 0) {
				
				#} Back
				$stage = 0;
				$stageError = 'No fields were matched. You cannot import customers without at least one field mapped to a customer attribute.';

			}


		}



	switch ($stage){

		case 1:

			#} Title
			zeroBSCRM_CSVImporterLitepages_header('2. Map Fields');

			?><div class="zbscrm-csvimport-wrap">
				<h2><?php esc_html_e('Map Columns from your CSV to Customer Fields','zero-bs-crm'); ?></h2>
				<?php if (isset($stageError) && !empty($stageError)){ zeroBSCRM_html_msg(-1,$stageError); } ?>
				<div class="zbscrm-csv-map">
					<p class="zbscrm-csv-map-help"><?php _e('Your CSV File has been successfully uploaded. Before we can complete your import, you\'ll need to specify which field in your CSV file matches which field in ZBS.<br />You can do so by using the drop down options below:','zero-bs-crm'); ?></p>
					<form method="post" class="zbscrm-csv-map-form">
						<input type="hidden" id="zbscrmcsvimpstage" name="zbscrmcsvimpstage" value="2" />
						<input type="hidden" id="zbscrmcsvimpf" name="zbscrmcsvimpf" value="<?php echo $fileDetails['file']; ?>" />
						<input type="hidden" id="zbscrmcsvimpfurl" name="zbscrmcsvimpfurl" value="<?php echo $fileDetails['url']; ?>" />
   						<?php wp_nonce_field( 'zbscrm_csv_import', 'zbscrmcsvimportnonce' ); ?>

						<hr />
						<div class="zbscrm-csv-map-ignorefirst">
						    <input type="checkbox" id="zbscrmcsvimpignorefirst" name="zbscrmcsvimpignorefirst" value="1" />
							<label><?php _e('Ignore first line of CSV file when running import.<br />(Use this if you have a "header line" in your CSV file.)','zero-bs-crm'); ?></label>
						</div>
						<hr />

   						<?php #print_r($fileDetails); 

   							#} Cycle through each field and display a mapping option
   							#} Using first line of import
   							$firstLine = $csvLines[0];
   							$firstLineParts = explode(",",$firstLine); 

   							#} Retrieve possible map fields from fields model
   							$possibleFields = array();
   							foreach ($zbsCustomerFields as $fieldKey => $fieldDeets){

   								// not custom-fields
   								if (!isset($fieldDeets['custom-field'])) $possibleFields[$fieldKey] = __($fieldDeets[1],'zero-bs-crm');
								
								if (in_array($fieldKey, array('secaddr1', 'secaddr2', 'seccity', 'seccounty', 'seccountry', 'secpostcode'))) $possibleFields[$fieldKey] .= ' ('.__('2nd Address','zero-bs-crm').')';

   							}

   							#} Loop 
   							$indx = 1;
   							foreach ($firstLineParts as $userField){

   								#} Clean user field - ""
   								if (substr($userField,0,1) == '"' && substr($userField,-1) == '"'){
   									$userField = substr($userField,1,strlen($userField)-2);
   								}
   								#} Clean user field - ''
   								if (substr($userField,0,1) == "'" && substr($userField,-1) == "'"){
   									$userField = substr($userField,1,strlen($userField)-2);
   								}

   								?>
   								<div class="zbscrm-csv-map-field">
   									<span><?php echo esc_html_x('Map','As in map CSV column to field','zero-bs-crm'); ?>:</span> <div class="zbscrm-csv-map-user-field">"<?php echo $userField; ?>"</div><br />
   									<div class="zbscrm-csv-map-zbs-field">
   										<span class="to"><?php esc_html_e('To:','zero-bs-crm'); ?></span> <select name="zbscrm-csv-fieldmap-<?php echo $indx; ?>" id="zbscrm-csv-fieldmap-<?php echo $indx; ?>">
	   										<option value="-1" disabled="disabled"><?php _e('Select a field','zero-bs-crm'); ?></option>
	   										<option value="-1" disabled="disabled">==============</option>
	   										<option value="ignorezbs" selected="selected"><?php _e('Ignore this field','zero-bs-crm'); ?></option>
	   										<option value="-1" disabled="disabled">==============</option>
	   										<?php foreach ($possibleFields as $fieldID => $fieldTitle){ ?>
	   										<option value="<?php echo $fieldID; ?>"><?php echo _e($fieldTitle,'zero-bs-crm'); ?></option>
	   										<?php } ?>
	   									</select>
	   								</div>
   								</div>
   								<?php

   								$indx++;

   							}



   						?>
   						<hr />
						<div style="text-align:center">
							<button type="submit" name="csv-map-submit" id="csv-map-submit" class="button button-primary button-large" type="submit"><?php _e('Continue','zero-bs-crm'); ?></button>	
						</div>
					</form>
				</div>
			</div><?php


			break;
		case 2:

			#} Title
			zeroBSCRM_CSVImporterLitepages_header('3. Run Import');

			#} Stolen from plugin-install.php?tab=upload
			?><div class="zbscrm-csvimport-wrap">
				<h2>Complete Customer Import</h2>
				<?php if (isset($stageError) && !empty($stageError)){ zeroBSCRM_html_msg(-1,$stageError); } ?>
				<div class="zbscrm-confirmimport-csv">
					<p class="zbscrm-csv-help">Ready to run the import.<br />Please confirm the following is correct <i>before</i> continuing.<br /></p>
					<div style=""><?php echo zeroBSCRM_html_msg(1,'Note: There is no easy way to "undo" a CSV import, to remove any customers that have been added you will need to manually remove them.'); ?>
					<form method="post" enctype="multipart/form-data" class="zbscrm-csv-import-form">
						<input type="hidden" id="zbscrmcsvimpstage" name="zbscrmcsvimpstage" value="3" />
						<input type="hidden" id="zbscrmcsvimpf" name="zbscrmcsvimpf" value="<?php echo $csvFileLoca; ?>" />
						<input type="hidden" id="zbscrmcsvimpfurl" name="zbscrmcsvimpfurl" value="<?php echo $csvFileURL; ?>" />
   						<?php wp_nonce_field( 'zbscrm_csv_import', 'zbscrmcsvimportnonce' ); ?>
   						<h3>Import <?php echo zeroBSCRM_prettifyLongInts($totalCSVLines); ?> Customers</h3>
   						<hr />
	   					<?php if ($ignoreFirstLine){ ?>
	   					<p style="font-size:16px;text-align:center;">Ignore first line of CSV <i class="fa fa-check"></i></p>
	   					<hr />
						<input type="hidden" id="zbscrmcsvimpignorefirst" name="zbscrmcsvimpignorefirst" value="1" />
						<?php } ?>   						
   						<?php if ($realFields > 0){ ?>
	   						<p style="font-size:16px;text-align:center;">Map the following fields:</p>
	   						<?php

   							#} Cycle through each field
   							#} Using first line of import
   							$firstLine = $csvLines[0];
   							$firstLineParts = explode(",",$firstLine); 

	   						foreach ($fieldMap as $fieldID => $fieldTarget){

	   							$fieldTargetName = $fieldTarget; if (isset($zbsCustomerFields[$fieldTarget]) && isset($zbsCustomerFields[$fieldTarget][1]) && !empty($zbsCustomerFields[$fieldTarget][1])) $fieldTargetName = __($zbsCustomerFields[$fieldTarget][1],'zero-bs-crm');
								
								if (in_array($fieldTarget, array('secaddr1', 'secaddr2', 'seccity', 'seccounty', 'seccountry', 'secpostcode'))) $fieldTargetName .= ' ('.__('2nd Address','zero-bs-crm').')';
								
	   							$fromStr = '';
	   							if (isset($firstLineParts[$fieldID-1])) $fromStr = $firstLineParts[$fieldID-1];

   								#} Clean user field - ""
   								if (substr($fromStr,0,1) == '"' && substr($fromStr,-1) == '"'){
   									$fromStr = substr($fromStr,1,strlen($fromStr)-2);
   								}
   								#} Clean user field - ''
   								if (substr($fromStr,0,1) == "'" && substr($fromStr,-1) == "'"){
   									$fromStr = substr($fromStr,1,strlen($fromStr)-2);
   								}



	   							?>
								<input type="hidden" id="zbscrm-csv-fieldmap-<?php echo ($fieldID-1); ?>" name="zbscrm-csv-fieldmap-<?php echo ($fieldID-1); ?>" value="<?php echo $fieldTarget; ?>" />
	   							<div class="zbscrm-impcsv-map">
	   								<div class="zbscrm-impcsv-from"><?php if (!empty($fromStr)) echo '"'.$fromStr.'"'; else echo 'Field #'.$fieldID; ?></div>
	   								<div class="zbscrm-impcsv-arrow"><?php if ($fieldTarget != "ignorezbs") echo '<i class="fa fa-long-arrow-right"></i>'; else echo '-'; ?></div>
	   								<div class="zbscrm-impcsv-to"><?php if ($fieldTarget != "ignorezbs") echo '"'.$fieldTargetName.'"'; else echo 'Ignore'; ?></div>
	   							</div><?php

	   						} 

	   						?>
   						<hr />
						<div style="text-align:center">
							<button type="submit" name="csv-map-submit" id="csv-map-submit" class="button button-primary button-large" type="submit">Run Import</button>	
						</div>
   						<?php } else { 
   							#} No fields? wtf?
   							?><button type="button" class="button button-primary button-large" onclick="javascript:window.location='?page=<?php echo $zbs->slugs['csvlite']; ?>';"><?php _e('Back',"zero-bs-crm"); ?></button><?php 
   						} ?>
					</form>
				</div>
			</div><?php

			break;
		case 3:

			#} Title
			zeroBSCRM_CSVImporterLitepages_header('4. Import');


			?><div class="zbscrm-csvimport-wrap">
				<h2><?php _e("Running Import...","zero-bs-crm"); ?></h2>
				<?php if (isset($stageError) && !empty($stageError)){ zeroBSCRM_html_msg(-1,$stageError); } ?>
				<div class="zbscrm-final-stage">
					<div class="zbscrm-import-log">
						<div class="zbscrm-import-log-line"><?php _e("Loading CSV File: ","zero-bs-crm"); ?> <?php echo $csvFileName; ?> ... <i class="fa fa-check"></i></div>
						<div class="zbscrm-import-log-line"><?php _e("Parsing rows...","zero-bs-crm");?> <i class="fa fa-check"></i></div>
						<div class="zbscrm-import-log-line"><?php _e("Beginning Import of ","zero-bs-crm");?> <?php echo zeroBSCRM_prettifyLongInts(count($csvLines)); ?> <?php _e("rows...","zero-bs-crm");?> </div>
						<?php 

							#} Cycle through 
							$lineIndx = 0; $linesAdded = 0; $existingOverwrites = array(); $brStrs = array('<br>','<BR>','<br />','<BR />','<br/>','<BR/>');
							if (count($csvLines) > 0) foreach ($csvLines as $line){

								#} Check line
								if ($lineIndx == 0 && $ignoreFirstLine){

									echo '<div class="zbscrm-import-log-line">Skipping header row... <i class="fa fa-check"></i></div>';

								} else {

									#} split
   									$lineParts = explode(",",$line); 
	   								#debug echo '<pre>'; print_r(array($lineParts,$fieldMap)); echo '</pre>';

   									#} build arr
   									$customerFields = array();
   									#} Catch first if there

	   								foreach ($fieldMap as $fieldID => $fieldTarget){

	   									#} id
	   									$fieldIndx = $fieldID;

	   									#} Anything to set?
	   									if (
	   										
		   										// data in line
		   										isset($lineParts[$fieldIndx]) && !empty($lineParts[$fieldIndx]) &&

		   										// isn't ignore
		   										$fieldTarget != "ignorezbs"

	   										) {


	   										// for <br> passes, we convert them to nl
	   										$cleanUserField = str_replace($brStrs,"\r\n",$lineParts[$fieldIndx]);

	   										$cleanUserField = trim($cleanUserField);


			   								#} Clean user field - ""
			   								if (substr($cleanUserField,0,1) == '"' && substr($cleanUserField,-1) == '"'){
			   									$cleanUserField = substr($cleanUserField,1,strlen($cleanUserField)-2);
			   								}
			   								#} Clean user field - ''
			   								if (substr($cleanUserField,0,1) == "'" && substr($cleanUserField,-1) == "'"){
			   									$cleanUserField = substr($cleanUserField,1,strlen($cleanUserField)-2);
			   								}

			   								if ($cleanUserField == 'NULL') $cleanUserField = '';

	   									
	   										#} set customer fields
	   										$customerFields['zbsc_'.$fieldTarget] = $cleanUserField;
	   									
	   									}


	   								}

	   								#} Any legit fields?
	   								if (count($customerFields) > 0){

	   									#} Try and find a unique id for this user
	   									$userUniqueID = md5($line.'#'.$csvFileName);

	   										#} 1st use email if there
	   										if (isset($customerFields['zbsc_email']) && !empty($customerFields['zbsc_email'])) $userUniqueID = $customerFields['zbsc_email'];

	   										#} else use md5 of the line + Filename

	   									#} If no STATUS have to add one!
	   									if (!isset($customerFields['zbsc_status'])) {
	   									
	   										#} Get from setting, if present
	   										if (isset($settings['defaultcustomerstatus']) && !empty($settings['defaultcustomerstatus'])) 
	   											$customerFields['zbsc_status'] = $settings['defaultcustomerstatus'];
	   										else
	   											$customerFields['zbsc_status'] = 'Customer';

	   									}

	   									#} Already exists? (This is only used to find dupes
	   									$potentialCustomerID = zeroBS_getCustomerIDWithExternalSource('csv',$userUniqueID);
	   									if (!empty($potentialCustomerID) && $potentialCustomerID > 0) {

	   										$thisDupeRef = '#'.$potentialCustomerID;
	   										if (isset($customerFields['zbsc_email']) && !empty($customerFields['zbsc_email'])) $thisDupeRef .= ' ('.$customerFields['zbsc_email'].')';

	   										$existingOverwrites[] = $thisDupeRef;
	   									}

	   									#} Add customer 
	   									$newCustID = zeroBS_integrations_addOrUpdateCustomer('csv',$userUniqueID,$customerFields);

	   									if (!empty($newCustID) && empty($potentialCustomerID)) {

	   										$linesAdded++;

		   									#} Line
											echo '<div class="zbscrm-import-log-line">'.esc_html__(
																									  sprintf(
																									    'Successfully added contact #<a href="%s" target="_blank">%d</a>... <i class="fa fa-user"></i><span>+1</span>',
																									    zbsLink( 'edit', $newCustID, 'contact', false, false ),
																									    $newCustID
																									  ),
																									  'zero-bs-crm'
																									).'</div>';

										} else {

											// dupe overriten?
											if (!empty($potentialCustomerID)){

			   									#} Line
												echo '<div class="zbscrm-import-log-line">'.__('Contact Already Exists!:','zero-bs-crm').' #'.$newCustID.'... <i class="fa fa-user"></i><span>['.__('Updated','zero-bs-crm').']</span></div>';


											}

										}

	   								} else {

										echo '<div class="zbscrm-import-log-line">'.__('Skipping row (no usable fields)','zero-bs-crm').'... <i class="fa fa-check"></i></div>';

	   								}

								}

								$lineIndx++;

							}	

							// any of these?
							if (count($existingOverwrites) > 0) {

								echo '<div class="zbscrm-import-log-line"><strong>The following customers were already in your Jetpack CRM, and were updated:</strong></div>';
								
								foreach ($existingOverwrites as $l){
								
									echo '<div class="zbscrm-import-log-line">'.$l.'</div>';
								}
							}



							#} Delete file if flagged
							if (!$saveCopyOfCSVFile){

								if (file_exists($csvFileLoca)) unlink($csvFileLoca);
								echo '<div class="zbscrm-import-log-line">CSV Upload File Deleted... <i class="fa fa-check"></i></div>';

							} else {
								/* not in lite 
								#} Leave in place.
									#} In fact add to log :)
									if (isset($settings['csvimportlog']) && is_array($settings['csvimportlog'])){
										$existingLog = $settings['csvimportlog'];
									} else {
										$existingLog = array();
									}
									array_unshift($existingLog,array($csvFileLoca,$csvFileURL,$linesAdded.' lines imported '.date('F jS Y',time()).' at '.date('g:i a',time())));
									$zeroBSCRM_CSVImporterSettings->update('csvimportlog',$existingLog);

								echo '<div class="zbscrm-import-log-line">Skipping deletion of CSV File (as per settings)... <i class="fa fa-check"></i></div>';
								echo '<div class="zbscrm-import-log-line">Added CSV Import to log... <i class="fa fa-check"></i></div>';
								*/

							}

						?>
						<hr />
						<button type="button" class="button button-primary button-large" onclick="javascript:window.location='admin.php?page=<?php echo $zbs->slugs['datatools']; ?>';"><?php _e('Finish',"zero-bs-crm"); ?></button>
					</div>
				</div>
			</div><?php


			break;
		default: #} Also case 0

			#} Title
			zeroBSCRM_CSVImporterLitepages_header('1. Upload');

			#} Stolen from plugin-install.php?tab=upload
			?><div class="zbscrm-csvimport-wrap">
				<h2><?php _e("Import Customers from a CSV File","zero-bs-crm");?></h2>
				<?php if (isset($stageError) && !empty($stageError)){ zeroBSCRM_html_msg(-1,$stageError); } ?>
				<div class="zbscrm-upload-csv">
					<p class="zbscrm-csv-import-help"><?php _e("If you have a CSV file of customers that you would like to import into Jetpack CRM, you can start the import wizard by uploading your .CSV file here.","zero-bs-crm");?></p>
					<form method="post" enctype="multipart/form-data" class="zbscrm-csv-import-form">
						<input type="hidden" id="zbscrmcsvimpstage" name="zbscrmcsvimpstage" value="1" />
   						<?php wp_nonce_field( 'zbscrm_csv_import', 'zbscrmcsvimportnonce' ); ?>
						<label class="screen-reader-text" for="csvfile"><?php _e(".CSV file","zero-bs-crm");?></label>
                        <input type="file" id="zbscrmcsvfile" name="zbscrmcsvfile">
                        <div class="csv-import__start-btn">
						    <input type="submit" name="csv-file-submit" id="csv-file-submit" class="ui button green" value="<?php _e("Start CSV Import Now","zero-bs-crm");?>">
                        </div>
					</form>
				</div>
			</div><?php

			#} Lite upsell (remove from rebrander) but also make it translation OK.
			##WLREMOVE

			  // WH added: Is now polite to License-key based settings like 'entrepreneur' doesn't try and upsell
			  // this might be a bit easy to "hack out" hmmmm
			  $bundle = false; if ($zbs->hasEntrepreneurBundleMin()) $bundle = true;

			  	if (!$bundle){
					?>
					<hr style="margin-top:40px" />
					<div class="zbscrm-lite-notice">
						<h2><?php _e("CSV Importer: Lite Version","zero-bs-crm");?></h2>
						<p><?php _e("If you would like to benefit from more features (such as logging your imports, automatically creating companies (B2B), and direct support etc. then please purchase a copy of our","zero-bs-crm");?> <a href="<?php echo $zbs->urls['extcsvimporterpro']; ?>" target="_blank">CSV Importer PRO</a> <?php _e('extension','zero-bs-crm'); ?>).<br /><br /><a href="<?php echo $zbs->urls['extcsvimporterpro']; ?>" target="_blank" class="ui button blue large"><?php _e("Get","zero-bs-crm");?> CSV Importer PRO</a></p>

					</div><?php

				} else {

					// has bundle should download + install
					?>
					<hr style="margin-top:40px" />
					<div class="zbscrm-lite-notice">
						<h2><?php _e("CSV Importer: Lite Version","zero-bs-crm");?></h2>
						<p><?php _e("You have the PRO version of CSV importer available because you're using a bundle. Please download and install from","zero-bs-crm");?> <a href="<?php echo $zbs->urls['account']; ?>" target="_blank"><?php _e('Your Account','zero-bs-crm'); ?></a></p>
					</div><?php
				}
			##/WLREMOVE

			break;


	}


}
