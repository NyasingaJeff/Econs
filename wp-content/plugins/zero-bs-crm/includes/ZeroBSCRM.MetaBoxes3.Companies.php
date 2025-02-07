<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V3.0
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 20/02/2019
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */




/* ======================================================
   Init Func
   ====================================================== */

   function zeroBSCRM_CompaniesMetaboxSetup(){

        // main deets
        $zeroBS__Metabox_Company = new zeroBS__Metabox_Company( __FILE__ );

        // Actions (Save + status)
        $zeroBS__Metabox_CompanyActions = new zeroBS__Metabox_CompanyActions( __FILE__ );

        // contacts
        $zeroBS__Metabox_CompanyContacts = new zeroBS__Metabox_CompanyContacts( __FILE__ );

        // Tags
        $zeroBS__Metabox_CompanyTags = new zeroBS__Metabox_CompanyTags( __FILE__ );

        // files
        $zeroBS__Metabox_CompanyFiles = new zeroBS__Metabox_CompanyFiles( __FILE__ );

        // external sources
        $zeroBS__Metabox_ExtSource = new zeroBS__Metabox_ExtSource( __FILE__, 'company','zbs-add-edit-company-edit');

        #} Activity box on view page
        if(zeroBSCRM_is_company_view_page()){
            $zeroBS__Metabox_Company_Activity = new zeroBS__Metabox_Company_Activity( __FILE__ );
        }

        #} Ownership
        if (zeroBSCRM_getSetting('perusercustomers') == "1") $zeroBS__CoMetabox_Ownership = new zeroBS__Metabox_Ownership( __FILE__, ZBS_TYPE_COMPANY);

        
   }

   add_action( 'admin_init','zeroBSCRM_CompaniesMetaboxSetup');


/* ======================================================
   / Init Func
   ====================================================== */

/* ======================================================
  Company Metabox
   ====================================================== */

    class zeroBS__Metabox_Company extends zeroBS__Metabox{ 
        
        // this is for catching 'new' companys
        private $newRecordNeedsRedir = false;

        private $coOrgLabel = '';

        public function __construct( $plugin_file ) {

            // oldschool.
            $this->coOrgLabel = __(zeroBSCRM_getSetting('coororg'),'zero-bs-crm');
            if ($this->coOrgLabel == 'co') $this->coOrgLabel = __('Company','zero-bs-crm');
            if ($this->coOrgLabel == 'org') $this->coOrgLabel = __('Organisation','zero-bs-crm');
            if ($this->coOrgLabel == 'domain') $this->coOrgLabel = __('Domain','zero-bs-crm');

            // set these
            // DAL3 switched for objType $this->postType = 'zerobs_customer';
            $this->objType = 'company';
            $this->metaboxID = 'zerobs-company-edit';
            $this->metaboxTitle = $this->coOrgLabel.' '.__('Details','zero-bs-crm');
            $this->metaboxScreen = 'zbs-add-edit-company-edit';
            $this->metaboxArea = 'normal';
            $this->metaboxLocation = 'high';
            $this->saveOrder = 1;
            $this->capabilities = array(

                'can_hide'          => false, // can be hidden
                'areas'             => array('normal'), // areas can be dragged to - normal side = only areas currently
                'can_accept_tabs'   => true,  // can/can't accept tabs onto it
                'can_become_tab'    => false, // can be added as tab
                'can_minimise'      => true, // can be minimised
                'can_move'          => true // can be moved

            );

            // call this 
            $this->initMetabox();

        }

        public function html( $company, $metabox ) {

                global $zbs;

                // localise ID
                $companyID = -1; if (is_array($company) && isset($company['id'])) $companyID = (int)$company['id'];

               // PerfTest: zeroBSCRM_performanceTest_startTimer('custmetabox-dataget');

                #} Rather than reload all the time :)
                global $zbsCompanyEditing; 

                #} retrieve
                //$zbsCustomer = get_post_meta($company['id'], 'zbs_customer_meta', true);
                if (!isset($zbsCompanyEditing)){
                    $zbsCompany = zeroBS_getCompany($company['id'],false);
                    $zbsCompanyEditing = $zbsCompany;
                } else {
                    $zbsCompany = $zbsCompanyEditing;
                }

                // Get field Hides...
                $fieldHideOverrides = $zbs->settings->get('fieldhides');
                $zbsShowID = $zbs->settings->get('showid');

                // Click 2 call?
                $click2call = $zbs->settings->get('clicktocall');

                global $zbsCompanyName; $zbsCompanyName = ''; if (isset($zbsCompany['name'])) $zbsCompanyName = $zbsCompany['name'];

                global $zbsCompanyFields; $fields = $zbsCompanyFields;

                #} Address settings
                $showAddresses = zeroBSCRM_getSetting('showaddress');
                $showSecondAddress = zeroBSCRM_getSetting('secondaddress');
                $showCountryFields = zeroBSCRM_getSetting('countries');

               // PerfTest: zeroBSCRM_performanceTest_finishTimer('custmetabox-dataget');
               // PerfTest: zeroBSCRM_performanceTest_startTimer('custmetabox-draw');

                //echo 'Debug:<pre>'; print_r(array($company,$zbsCompanyFields)); echo '</pre>';
            
                //sticky tape some CSS until new UI!!
            ?>
                <style>
                    #post-body-content{
                        display:none;
                    }
                        @media all and (max-width:699px){
                        table.wh-metatab{
                            min-width:100% !important;
                        }
                    }  
                </style>
                <?php #} AJAX NONCE ?><script type="text/javascript">var zbscrmjs_secToken = '<?php echo wp_create_nonce( "zbscrmjs-ajax-nonce" ); ?>';</script><?php # END OF NONCE ?>

                <?php #} Pass this if it's a new customer (for internal automator) - note added this above with DEFINE for simpler.

                    if (gettype($zbsCompany) != "array") echo '<input type="hidden" name="zbscrm_newcompany" value="1" />';

                ?>


                <table class="form-table wh-metatab wptbp" id="wptbpMetaBoxMainItem">

                    <?php #} WH Hacky quick addition for MVP 
                    # ... further hacked

                    if ($zbsShowID == "1" && $companyID > 0) { ?>
                    <tr class="wh-large"><th><label><?php echo $this->coOrgLabel.' '; _e("ID","zero-bs-crm");?>:</label></th>
                    <td style="font-size: 20px;color: green;vertical-align: top;">
                        #<?php echo $companyID; ?>
                    </td></tr>
                    <?php } ?>

                    <?php /* if (has_post_thumbnail( $post->ID ) ): ?>
                      <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
                      <tr class="wh-large"><th><label><?php echo $this->coOrgLabel; ?> Image:</label></th>
                                    <td>
                                        <a href="<?php echo $image[0]; ?>" target="_blank"><img src="<?php echo $image[0]; ?>" alt="<?php echo $this->coOrgLabel; ?> Image" style="max-width:300px;border:0" /></a>
                                    </td></tr>
                    <?php endif; */ ?>

                    <?php /*<tr><td><pre><?php print_r($fields); ?></pre></td></tr> */

            

                    #} This global holds "enabled/disabled" for specific fields... ignore unless you're WH or ask
                    global $zbsFieldsEnabled; if ($showSecondAddress == '1') $zbsFieldsEnabled['secondaddress'] = true;
                    
                    #} This is the grouping :)
                    $zbsFieldGroup = ''; $zbsOpenGroup = false;

                    foreach ($fields as $fieldK => $fieldV){

                        $showField = true;

                        #} Check if not hard-hidden by opt override (on off for second address, mostly)
                        if (isset($fieldV['opt']) && (!isset($zbsFieldsEnabled[$fieldV['opt']]) || !$zbsFieldsEnabled[$fieldV['opt']])) $showField = false;


                        // or is hidden by checkbox? 
                        if (isset($fieldHideOverrides['company']) && is_array($fieldHideOverrides['company'])){
                            if (in_array($fieldK, $fieldHideOverrides['company'])){
                              $showField = false;
                            }
                        }

                        // 'show/hide Countries' setting:
                        if (isset($fieldV[0]) && $fieldV[0] == 'selectcountry' && $showCountryFields !== "1") $showField = false;


                        // ++ We hide status, because it's now in the 'company action' box
                        if ($fieldK == 'status') $showField = false;


                        // ==================================================================================
                        // Following grouping code needed moving out of ifShown loop:

                            #} Whatever prev fiedl group was, if this is diff, close (post group)
                            if (
                                $zbsOpenGroup &&
                                    #} diff group
                                    ( 
                                        (isset($fieldV['area']) && $fieldV['area'] != $zbsFieldGroup) ||
                                        #} No group
                                         !isset($fieldV['area']) && $zbsFieldGroup != ''
                                    )
                                ){

                                    #} Special cases... gross
                                    $zbsCloseTable = true; if ($zbsFieldGroup == 'Main Address') $zbsCloseTable = false;

                                    #} Close it
                                    echo '</table></div>';
                                    if ($zbsCloseTable) echo '</td></tr>';

                            }

                            #} Any groupings?
                            if (isset($fieldV['area'])){

                                #} First in a grouping? (assumes in sequential grouped order)
                                if ($zbsFieldGroup != $fieldV['area']){

                                    #} set it
                                    $zbsFieldGroup = $fieldV['area'];
                                    $fieldGroupLabel = str_replace(' ','_',$zbsFieldGroup); $fieldGroupLabel = strtolower($fieldGroupLabel);

                                    #} Special cases... gross
                                    $zbsOpenTable = true; if ($zbsFieldGroup == 'Second Address') $zbsOpenTable = false;


                                    #} Make class for hiding address (this form output is weird) <-- classic mike saying my code is weird when it works fully. Ask if you don't know!
                                    $zbsLineClass = ''; $zbsGroupClass = '';

                                    // if addresses turned off, hide the lot
                                    if ($showAddresses != "1") {

                                        // addresses turned off
                                        $zbsLineClass = 'zbs-hide';
                                        $zbsGroupClass = 'zbs-hide';

                                    } else { 

                                        // addresses turned on
                                        if ($zbsFieldGroup == 'Second Address'){

                                            // if we're in second address grouping:

                                                // if second address turned off
                                                if ($showSecondAddress != "1"){

                                                    $zbsLineClass = 'zbs-hide';
                                                    $zbsGroupClass = 'zbs-hide';

                                                }

                                        }

                                    }
                                    // / address  modifiers


                                    #} add group div + label
                                    if ($zbsOpenTable) echo '<tr class="wh-large zbs-field-group-tr '.$zbsLineClass.'"><td colspan="2">';
                                    echo '<div class="zbs-field-group zbs-fieldgroup-'.$fieldGroupLabel.' '. $zbsGroupClass .'"><label class="zbs-field-group-label">'.__($fieldV['area'],"zero-bs-crm").'</label>';
                                    echo '<table class="form-table wh-metatab wptbp" id="wptbpMetaBoxGroup-'.$fieldGroupLabel.'">';
                                    
                                    #} Set this (need to close)
                                    $zbsOpenGroup = true;

                                }


                            } else {

                                #} No groupings!
                                $zbsFieldGroup = '';

                            }

                        // / grouping
                        // ==================================================================================

                        #} If show...
                        if ($showField) {

                            if (isset($fieldV[0])){

                                // we now put these out via the centralised func (2.95.3+)
                                //... rather than distinct switch below
                                zeroBSCRM_html_editField($zbsCompany, $fieldK, $fieldV, $postPrefix = 'zbsco_');

                            }

                        } #} / if show


                        // ==================================================================================
                        // Following grouping code needed moving out of ifShown loop:

                            #} Closing field?
                            if (
                                $zbsOpenGroup &&
                                    #} diff group
                                    ( 
                                        (isset($fieldV['area']) && $fieldV['area'] != $zbsFieldGroup) ||
                                        #} No group
                                         !isset($fieldV['area']) && $zbsFieldGroup != ''
                                    )
                                ){

                                    #} Special cases... gross
                                    $zbsCloseTable = true; if ($zbsFieldGroup == 'Main Address') $zbsCloseTable = false;

                                    #} Close it
                                    echo '</table></div>';
                                    if ($zbsCloseTable) echo '</td></tr>';

                            }
                        // / grouping
                        // ==================================================================================

                    }


                    /* Debug <tr><td colspan="2"><pre><?php print_r($zbsCompany) ?></pre></td></tr> */

                    ?>
                    
            </table>


            <style type="text/css">
                #submitdiv {
                    display:none;
                }
            </style>
            <script type="text/javascript">

                jQuery(document).ready(function(){

                    zbscrm_JS_bindFieldValidators();

                });


            </script><?php

            // PerfTest: zeroBSCRM_performanceTest_finishTimer('custmetabox-draw');
        }

        public function save_data( $company_id, $company ) {

            if (!defined('ZBS_CO_SAVED')){

                // debug if (get_current_user_id() == 12) echo 'FIRING<br>';
                define('ZBS_CO_SAVED',1);

                // DAL3.0+
                global $zbs;

                // check this
                if (empty($company_id) || $company_id < 1)  $company_id = -1;

                // retrieve data in format
                //... by using zeroBS_buildCompanyMeta, custom fields are 'dealt with' automatically
                $dataArr = zeroBS_buildCompanyMeta($_POST);

                // Use the tag-class function to retrieve any tags so we can add inline.
                // Save tags against objid
                $dataArr['tags'] = zeroBSCRM_tags_retrieveFromPostBag(true,ZBS_TYPE_COMPANY); 
 
                // owner - saved here now, rather than ownership box, to allow for pre-hook update. (as tags)
                $owner = -1; if (isset($_POST['zerobscrm-owner'])){

                    // should this have perms check to see if user can actually assign to? or should that be DAL?
                    $potentialOwner = (int)sanitize_text_field( $_POST['zerobscrm-owner'] );
                    if ($potentialOwner > 0) $owner = $potentialOwner;

                }

                /* debug 
                echo '_POST:<pre>'.print_r($_POST,1).'</pre>';
                echo 'dataArr:<pre>'.print_r($dataArr,1).'</pre>'; exit();
                */

                // now we check whether a user with this email already exists (separate to this company id), so we can warn them
                // ... that it wont have changed the email
                if (isset($dataArr['email']) && !empty($dataArr['email'])){

                    $potentialID = zeroBS_getCompanyIDWithEmail($dataArr['email']);

                    if (!empty($potentialID) && $potentialID != $company_id){

                        // no go.
                        $this->updateEmailDupeMessage($potentialID);

                        // unset email change (leave as was)
                        $dataArr['email'] = zeroBS_companyEmail($company_id);

                    }

                }

                #AVATARSAVE - save any avatar change if changed :)
                if (isset($_POST['zbs-company-avatar-custom-url']) && !empty($_POST['zbs-company-avatar-custom-url'])) $dataArr['avatar'] = sanitize_text_field( $_POST['zbs-company-avatar-custom-url'] );

                    // TAGS get dealt with by tags metabox
                    //$tags 

                    #} UPDATE!
                    // DAL1 way: update_post_meta($post_id, 'zbs_company_meta', $zbsCompanyMeta);

                    // add update directly
                    $addUpdateReturn = $zbs->DAL->companies->addUpdateCompany(array(

                            'id'    => $company_id,
                            'owner' => $owner,
                            'data'  => $dataArr,
                            'limitedFields' => -1,

                    ));

                    // Note: For NEW contacts, we make sure a global is set here, that other update funcs can catch 
                    // ... so it's essential this one runs first!
                    // this is managed in the metabox Class :)
                    if ($company_id == -1 && !empty($addUpdateReturn) && $addUpdateReturn != -1) {
                        
                        $company_id = $addUpdateReturn;
                        global $zbsJustInsertedMetaboxID; $zbsJustInsertedMetaboxID = $company_id;

                        // set this so it redirs
                        $this->newRecordNeedsRedir = true;
                    }

                    // success?
                    if ($addUpdateReturn != -1 && $addUpdateReturn > 0){

                        // Update Msg
                        // this adds an update message which'll go out ahead of any content
                        // This adds to metabox: $this->updateMessages['update'] = zeroBSCRM_UI2_messageHTML('info olive mini zbs-not-urgent',__('Contact Updated',"zero-bs-crm"),'','address book outline','contactUpdated');
                        // This adds to edit page
                        $this->updateMessage( $this->newRecordNeedsRedir );

                        // catch any non-critical messages
                        $nonCriticalMessages = $zbs->DAL->getErrors(ZBS_TYPE_COMPANY);
                        if (is_array($nonCriticalMessages) && count($nonCriticalMessages) > 0) $this->dalNoticeMessage($nonCriticalMessages);

                    } else {

                        // fail somehow
                        $failMessages = $zbs->DAL->getErrors(ZBS_TYPE_COMPANY);

                        // show msg (retrieved from DAL err stack)
                        if (is_array($failMessages) && count($failMessages) > 0)
                            $this->dalErrorMessage($failMessages);
                        else
                            $this->dalErrorMessage(array(__('Insert/Update Failed with general error','zero-bs-crm')));

                        // pass the pre-fill:
                        global $zbsObjDataPrefill; $zbsObjDataPrefill = $dataArr;

            
                    }

            }

            return $company;
        }

        // This catches 'new' contacts + redirs to right url
        public function post_save_data($objID,$obj){

            if ($this->newRecordNeedsRedir){

                global $zbs, $zbsJustInsertedMetaboxID;
                if (!empty($zbsJustInsertedMetaboxID) && $zbsJustInsertedMetaboxID > 0){

                    // redir
                    $zbs->new_record_edit_redirect( $this->objType, $zbsJustInsertedMetaboxID );

                }

            }

        }

        public function updateMessage( $created = false ) {
            $message = $this->coOrgLabel . ' ' . ( $created ? __( 'Created', 'zero-bs-crm' ) : __( 'Updated', 'zero-bs-crm' ) );
            // zbs-not-urgent means it'll auto hide after 1.5s
            $msg = zeroBSCRM_UI2_messageHTML('info olive mini zbs-not-urgent', $message,'','address book outline','companyUpdated');

            // quick + dirty
            global $zbs;

            $zbs->pageMessages[] = $msg;

        }

        public function updateEmailDupeMessage($otherCompanyID=-1){

            global $zbs;

            $viewHTML = ' <a href="'.zbsLink('view',$otherCompanyID,$this->objType).'" target="_blank">'.__('View','zero-bs-crm').' '.$this->coOrgLabel.'</a>';

            $msg = zeroBSCRM_UI2_messageHTML('info orange mini',__('Email could not be updated. (A record already exists with this email address).',"zero-bs-crm").$viewHTML,'','address book outline','companyNotUpdated');

            $zbs->pageMessages[] = $msg;

        }
    }


/* ======================================================
  / Company Metabox
   ====================================================== */


/* ======================================================
  "Contacts at Company" Metabox
   ====================================================== */

class zeroBS__Metabox_CompanyContacts extends zeroBS__Metabox{

    private $coOrgLabel = '';

    public function __construct( $plugin_file ) {

        // oldschool.
        $this->coOrgLabel = __(zeroBSCRM_getSetting('coororg'),'zero-bs-crm');
        if ($this->coOrgLabel == 'co') $this->coOrgLabel = __('Company','zero-bs-crm');
        if ($this->coOrgLabel == 'org') $this->coOrgLabel = __('Organisation','zero-bs-crm');
        if ($this->coOrgLabel == 'domain') $this->coOrgLabel = __('Domain','zero-bs-crm');
    
        // DAL3 switched for objType $this->postType = 'zerobs_customer';
        $this->objType = 'company';
        $this->metaboxID = 'zerobs-company-contacts';
        $this->metaboxTitle = __('Associated Contacts',"zero-bs-crm");
        $this->metaboxScreen = 'zbs-add-edit-company-edit'; //'zerobs_edit_contact'; // we can use anything here as is now using our func
        $this->metaboxArea = 'normal';
        $this->metaboxLocation = 'high';
        $this->headless = false;
        //$this->metaboxClasses = '';
        $this->capabilities = array(

            'can_hide'          => false, // can be hidden
            'areas'             => array('normal'), // areas can be dragged to - normal side = only areas currently
            'can_accept_tabs'   => false,  // can/can't accept tabs onto it
            'can_become_tab'    => false, // can be added as tab
            'can_minimise'      => false, // can be minimised
            'can_move'          => false // can be moved

        );

            
        // hide if "new" (not edit) - as can't yet add this way
        $isEdit = false;
        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['zbsid']) && !empty($_GET['zbsid'])) $isEdit = true;
        
        if ($isEdit){
            // call this 
            $this->initMetabox();
        }

    }

    public function html( $company, $metabox ) {

            global $zbs;

            $coID = -1; if (is_array($company) && isset($company['id'])) $coID = (int)$company['id'];

            //$contacts = zeroBS_getCustomers(true,1000,0,false,false,'',false,false,$coID);        
            $contacts = array();
            if ($coID > 0){
                $contacts = $zbs->DAL->contacts->getContacts(array(

                        'inCompany' => $coID,

                        'sortByField'   => 'ID',
                        'sortOrder'     => 'ASC',
                        'page'          => 0,
                        'perPage'       => 200,
                        'ignoreowner'   => zeroBSCRM_DAL2_ignoreOwnership(ZBS_TYPE_CONTACT)

                ));
            }


            #} JUST OUTPUT

            ?><table class="form-table wh-metatab wptbp" id="wptbpMetaBoxMainItemContacts">

                <tr class="wh-large"><th>
                    <?php

                        if (count($contacts) > 0){

                            /* WH modified to use more semantic friendly markup v3.0 25/4/19

                            echo '<div id="zbs-co-contacts">';

                            foreach ($contacts as $contact){

                                echo '<div class="zbs-co-contact">';

                                #} Img or ico 
                                echo zeroBS_getCustomerIcoHTML($contact['id']);

                                #} new view link
                                $url = zbsLink('view',$contact['id'],'zerobs_customer');

                                echo '<strong><a href="'.$url.'">'.zeroBS_customerName($contact['id'],$contact,false,false).'</a></strong><br />';

                                echo '</div>';

                            } 
                            echo '</div>';
                            */

                            echo '<div id="zbs-co-contacts" class="ui cards">';

                            foreach ($contacts as $contact){                                

                                #} new view link
                                $contactUrl = zbsLink('view',$contact['id'],'zerobs_customer');

                                #} Name
                                $contactName = zeroBS_customerName($contact['id'],$contact,false,false);
                                $contactFirstName = ''; if (isset($contact['fname'])) $contactFirstName = $contact['fname'];

                                #} Description
                                $contactDesc = '<i class="calendar alternate outline icon"></i>' . __('Contact since',"zero-bs-crm").' '.zeroBSCRM_date_i18n(zeroBSCRM_getDateFormat(), $contact['createduts'], true, false);
                                if (isset($contact['email']) && !empty($contact['email'])) $contactDesc .= '<br /><a href="'.$contactUrl.'" target="_blank">'.$contact['email'].'</a>';

                                ?><div class="card">
                                  <div class="content">
                                    <div class="center aligned header"><?php echo '<a href="'.$contactUrl.'">'.$contactName.'</a>'; ?></div>
                                    <?php if (!empty($contactDesc)){ ?>
                                    <div class="center aligned description">
                                      <p><?php echo $contactDesc; ?></p>
                                    </div>
                                    <?php } ?>
                                  </div>
                                  <div class="extra content">
                                    <div class="center aligned author">
                                      <?php
                                            #} Img or ico 
                                            echo zeroBS_getCustomerIcoHTML($contact['id'],'ui avatar image').' '.$contactFirstName;
                                        ?>
                                    </div>
                                  </div>
                                </div><?php
                            }


                            echo '</div>';

                        } else {

                            echo '<div style="margin-left:auto;margin-right:auto;display:inline-block">';
                            _e('No contacts found at',"zero-bs-crm"); echo ' '.$this->coOrgLabel;
                            echo '</div>';

                        }

                    ?>
                </th></tr>
                
            </table>

            <script type="text/javascript">

                jQuery(document).ready(function(){

                });

            </script>
             


            <?php

    }

    public function save_data( $companyID, $company ) {    

        // none as of yet

        return $company;
    }
}



/* ======================================================
  / "Contacts at Company" Metabox
   ====================================================== */

/* ======================================================
  Create Tags Box
   ====================================================== */

class zeroBS__Metabox_CompanyTags extends zeroBS__Metabox_Tags{


    public function __construct( $plugin_file ) {
    
        $this->objTypeID = ZBS_TYPE_COMPANY;
        $this->objType = 'company';
        $this->metaboxID = 'zerobs-company-tags';
        $this->metaboxTitle = __('Company Tags',"zero-bs-crm");
        $this->metaboxScreen = 'zbs-add-edit-company-edit'; //'zerobs_edit_contact'; // we can use anything here as is now using our func
        $this->metaboxArea = 'side';
        $this->metaboxLocation = 'high';
        $this->showSuggestions = true;
        $this->capabilities = array(

            'can_hide'          => true, // can be hidden
            'areas'             => array('side'), // areas can be dragged to - normal side = only areas currently
            'can_accept_tabs'   => false,  // can/can't accept tabs onto it
            'can_become_tab'    => false, // can be added as tab
            'can_minimise'      => true // can be minimised

        );

        // call this 
        $this->initMetabox();

    }

    // html + save dealt with by parent class :) 

}

/* ======================================================
  / Create Tags Box
   ====================================================== */

/* ======================================================
  Attach files to company metabox
   ====================================================== */

    class zeroBS__Metabox_CompanyFiles extends zeroBS__Metabox{

        public function __construct( $plugin_file ) {

            $this->objType = 'company';
            $this->metaboxID = 'zerobs-company-files';
            $this->metaboxTitle = __('Files',"zero-bs-crm");
            $this->metaboxScreen = 'zbs-add-edit-company-edit'; //'zerobs_edit_contact'; // we can use anything here as is now using our func
            $this->metaboxArea = 'normal';
            $this->metaboxLocation = 'low';
            $this->capabilities = array(

                'can_hide'          => true, // can be hidden
                'areas'             => array('normal'), // areas can be dragged to - normal side = only areas currently
                'can_accept_tabs'   => true,  // can/can't accept tabs onto it
                'can_become_tab'    => true, // can be added as tab
                'can_minimise'      => true // can be minimised

            );

            // call this 
            $this->initMetabox();

        }

        public function html( $company, $metabox ) {

                global $zbs;

                $html = '';
                $zbsFiles = zeroBSCRM_files_getFiles('company',$company['id']);

                ?><table class="form-table wh-metatab wptbp" id="wptbpMetaBoxMainItemFiles">

                    <?php

                    #} Whole file delete method could do with rewrite
                    #} Also sort JS into something usable - should be ajax all this

                    #} Any existing
                    if (is_array($zbsFiles) && count($zbsFiles) > 0){ 
                      ?><tr class="wh-large zbsFileDetails"><th class="zbsFilesTitle"><label><?php echo '<span>'.count($zbsFiles).'</span> '.__('File(s)','zero-bs-crm').':'; ?></label></th>
                                <td id="zbsFileWrapOther">
                                    <table class="ui celled table" id="zbsFilesTable">
                                      <thead>
                                        <tr>
                                            <th><?php _e("File","zerobscrm");?></th>
                                            <th class="collapsing center aligned"><?php _e("Actions","zerobscrm");?></th>
                                        </tr>
                                    </thead><tbody>
                                                <?php $fileLineIndx = 1; foreach($zbsFiles as $zbsFile){

                                                    /* $file = basename($zbsFile['file']);

                                                    // if in privatised system, ignore first hash in name
                                                    if (isset($zbsFile['priv'])){

                                                        $file = substr($file,strpos($file, '-')+1);
                                                    } */
                                                    $file = zeroBSCRM_files_baseName($zbsFile['file'],isset($zbsFile['priv']));

                                                    $fileEditUrl = admin_url('admin.php?page='.$zbs->slugs['editfile']) . "&company=".$company['id']."&fileid=" . ($fileLineIndx-1);

                                                    echo '<tr class="zbsFileLineTR" id="zbsFileLineTRCustomer'.$fileLineIndx.'">';
                                                    echo '<td><div class="zbsFileLine" id="zbsFileLineCustomer'.$fileLineIndx.'"><a href="'.$zbsFile['url'].'" target="_blank">'.$file.'</a></div>';

                                                    // if using portal.. state shown/hidden
                                                    // this is also shown in each file slot :) if you change any of it change that too
                                                    /*if(defined('ZBS_CLIENTPRO_TEMPLATES')){
                                                        if(isset($zbsFile['portal']) && $zbsFile['portal']){
                                                          echo "<p><i class='icon check circle green inverted'></i> ".__('Shown on Portal','zero-bs-crm').'</p>';
                                                        }else{
                                                          echo "<p><i class='icon ban inverted red'></i> ".__('Not shown on Portal','zero-bs-crm').'</p>';
                                                        }
                                                    }*/

                                                    echo '</td>';
                                                    echo '<td class="collapsing center aligned"><span class="zbsDelFile ui button basic" data-delurl="'.$zbsFile['url'].'"><i class="trash alternate icon"></i> '.__('Delete','zero-bs-crm').'</span></td></tr>'; // <a href="'.$fileEditUrl.'" target="_blank" class="ui button basic"><i class="edit icon"></i> '.__('Edit','zero-bs-crm').'</a>
                                                    $fileLineIndx++;

                                                } ?>
                                    </tbody></table>
                                </td></tr><?php

                    } ?>

                    <?php #adapted from http://code.tutsplus.com/articles/attaching-files-to-your-posts-using-wordpress-custom-meta-boxes-part-1--wp-22291

                            wp_nonce_field(plugin_basename(__FILE__), 'zbs_file_attachment_nonce');
                             
                            $html .= '<input type="file" id="zbs_file_attachment" name="zbs_file_attachment" value="" size="25" class="zbs-dc">';
                            
                            ?><tr class="wh-large"><th><label><?php _e('Add File',"zero-bs-crm");?>:</label><br />(<?php _e('Optional',"zero-bs-crm");?>)<br /><?php _e('Accepted File Types',"zero-bs-crm");?>:<br /><?php echo zeroBS_acceptableFileTypeListStr(); ?></th>
                                <td><?php
                            echo $html;
                    ?></td></tr>

                
                </table>
                <?php

                   // PerfTest: zeroBSCRM_performanceTest_finishTimer('custmetabox');
                   // PerfTest: zeroBSCRM_performanceTest_debugOut();

                   ?>
                <script type="text/javascript">

                    var zbsCurrentlyDeleting = false;
                    var zbsMetaboxFilesLang = {

                        'error': '<?php echo zeroBSCRM_slashOut(__('Error','zero-bs-crm')); ?>',
                        'unabletodelete': '<?php echo zeroBSCRM_slashOut(__('Unable to delete this file.','zero-bs-crm')); ?>'
                    };

                    jQuery('document').ready(function(){

                        jQuery('.zbsDelFile').click(function(){

                            if (!window.zbsCurrentlyDeleting){

                                // blocking
                                window.zbsCurrentlyDeleting = true;

                                var delUrl = jQuery(this).attr('data-delurl');
                                //var lineIDtoRemove = jQuery(this).closest('.zbsFileLine').attr('id');
                                var lineToRemove = jQuery(this).closest('tr');

                                if (typeof delUrl != "undefined" && delUrl != ''){



                                      // postbag!
                                      var data = {
                                        'action': 'delFile',
                                        'zbsfType': '<?php echo $this->objType; ?>',
                                        'zbsDel':  delUrl, // could be csv, never used though
                                        'zbsCID': <?php if (!empty($company['id']) && $company['id'] > 0) echo $company['id']; else echo -1; ?>,
                                        'sec': window.zbscrmjs_secToken
                                      };

                                      // Send it Pat :D
                                      jQuery.ajax({
                                              type: "POST",
                                              url: ajaxurl, // admin side is just ajaxurl not wptbpAJAX.ajaxurl,
                                              "data": data,
                                              dataType: 'json',
                                              timeout: 20000,
                                              success: function(response) {

                                                var localLineToRemove = lineToRemove, localDelURL = delUrl;

                                                // visually remove
                                                //jQuery(this).closest('.zbsFileLine').remove();
                                                //jQuery('#' + lineIDtoRemove).remove();
                                                jQuery(localLineToRemove).remove();

                                                // update number
                                                var newNumber = jQuery('#zbsFilesTable tr').length-1;
                                                if (newNumber > 0)
                                                    jQuery('#wptbpMetaBoxMainItemFiles .zbsFilesTitle span').html();
                                                else
                                                    jQuery('#wptbpMetaBoxMainItemFiles .zbsFileDetails').remove();


                                                // remove any filled slots (with this file)
                                                jQuery('.zbsFileSlotTable').each(function(ind,ele){

                                                    if (jQuery(ele).attr('data-sloturl') == localDelURL){

                                                        jQuery('.zbsFileSlotWrap',jQuery(ele)).remove();
                                                
                                                    }

                                                });

                                                // file deletion errors, show msg:
                                                if (typeof response.errors != "undefined" && response.errors.length > 0){

                                                    jQuery.each(response.errors,function(ind,ele){

                                                        jQuery('#zerobs-company-files-box').append('<div class="ui warning message" style="margin-top:10px;">' + ele + '</div>');

                                                    });
                                                         

                                                }

                                              },
                                              error: function(response){

                                                jQuery('#zerobs-company-files-box').append('<div class="ui warning message" style="margin-top:10px;"><strong>' + window.zbsMetaboxFilesLang.error + ':</strong> ' + window.zbsMetaboxFilesLang.unabletodelete + '</div>');

                                              }

                                            });

                                }

                                window.zbsCurrentlyDeleting = false;

                            } // / blocking

                        });

                    });


                </script><?php

               // PerfTest: zeroBSCRM_performanceTest_finishTimer('other');


        }

        public function save_data( $companyID, $company ) {

            global $zbsc_justUploadedFile;


            if(!empty($_FILES['zbs_file_attachment']['name']) && 
                (!isset($zbsc_justUploadedFile) ||
                    (isset($zbsc_justUploadedFile) && $zbsc_justUploadedFile != $_FILES['zbs_file_attachment']['name'])
                )
                ) {


            /* --- security verification --- */
            if(!wp_verify_nonce($_POST['zbs_file_attachment_nonce'], plugin_basename(__FILE__))) {
              return $id;
            } // end if


            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
              return $id;
            } // end if
               
            /* Switched out for WH Perms model 19/02/16 
            if('page' == $_POST['post_type']) { 
              if(!current_user_can('edit_page', $id)) {
                return $id;
              } // end if
            } else { 
                if(!current_user_can('edit_page', $id)) { 
                    return $id;
                } // end if
            } // end if */
            if (!zeroBSCRM_permsCustomers()){
                return $companyID;
            }
            /* - end security verification - */

            #} Blocking repeat-upload bug
            $zbsc_justUploadedFile = $_FILES['zbs_file_attachment']['name'];



                $supported_types = zeroBS_acceptableFileTypeMIMEArr(); //$supported_types = array('application/pdf');
                $arr_file_type = wp_check_filetype(basename($_FILES['zbs_file_attachment']['name']));
                $uploaded_type = $arr_file_type['type'];

                if(in_array($uploaded_type, $supported_types) || (isset($supported_types['all']) && $supported_types['all'] == 1)) {
                    $upload = wp_upload_bits($_FILES['zbs_file_attachment']['name'], null, file_get_contents($_FILES['zbs_file_attachment']['tmp_name']));

                    if(isset($upload['error']) && $upload['error'] != 0) {
                        wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                    } else {
                        //update_post_meta($id, 'zbsc_file_attachment', $upload);

                            // v2.13 - also privatise the file (move to our asset store)
                            // $upload will have 'file' and 'url'
                            $fileName = basename($upload['file']);
                            $fileDir = dirname($upload['file']);
                            $privateThatFile = zeroBSCRM_privatiseUploadedFile($fileDir,$fileName);
                            if (is_array($privateThatFile) && isset($privateThatFile['file'])){ 

                                // successfully moved to our store

                                    // modify URL + file attributes
                                    $upload['file'] = $privateThatFile['file'];
                                    $upload['url'] = $privateThatFile['url'];

                                    // add this extra identifier if in privatised sys
                                    $upload['priv'] = true;

                            } else {

                                // couldn't move to store, leave in uploaded for now :)

                            }

                            // w mod - adds to array :)
                            $zbsCompanyFiles = zeroBSCRM_files_getFiles('company',$companyID);//zeroBSCRM_getCustomerFiles($companyID);

                            if (is_array($zbsCompanyFiles)){

                                //add it
                                $zbsCompanyFiles[] = $upload;

                            } else {

                                // first
                                $zbsCompanyFiles = array($upload);

                            }

                            // update
                            zeroBSCRM_files_updateFiles('company',$companyID,$zbsCompanyFiles);

                            // Fire any 'post-upload-processing' (e.g. CPP makes thumbnails of pdf, jpg, etc.)
                            do_action('zbs_post_upload_company',$upload);
                    }
                }
                else {
                    wp_die("The file type that you've uploaded is not an accepted file format.");
                }
            }

            return $company;
        }
    }


/* ======================================================
  / Attach files to company metabox
   ====================================================== */


/* ======================================================
    Company Actions Metabox Metabox
   ====================================================== */

    class zeroBS__Metabox_CompanyActions extends zeroBS__Metabox{ 

        private $coOrgLabel = '';

        public function __construct( $plugin_file ) {

            // oldschool.
            $this->coOrgLabel = __(zeroBSCRM_getSetting('coororg'),'zero-bs-crm');
            if ($this->coOrgLabel == 'co') $this->coOrgLabel = __('Company','zero-bs-crm');
            if ($this->coOrgLabel == 'org') $this->coOrgLabel = __('Organisation','zero-bs-crm');
            if ($this->coOrgLabel == 'domain') $this->coOrgLabel = __('Domain','zero-bs-crm');

            // set these
            $this->objType = 'company';
            $this->metaboxID = 'zerobs-company-actions';
            $this->metaboxTitle = __('Company','zero-bs-crm').' '.__('Actions','zero-bs-crm'); // will be headless anyhow
            $this->headless = true;
            $this->metaboxScreen = 'zbs-add-edit-company-edit';
            $this->metaboxArea = 'side';
            $this->metaboxLocation = 'high';
            $this->saveOrder = 1;
            $this->capabilities = array(

                'can_hide'          => false, // can be hidden
                'areas'             => array('side'), // areas can be dragged to - normal side = only areas currently
                'can_accept_tabs'   => true,  // can/can't accept tabs onto it
                'can_become_tab'    => false, // can be added as tab
                'can_minimise'      => true, // can be minimised
                'can_move'          => true // can be moved

            );

            // call this 
            $this->initMetabox();

        }

        public function html( $company, $metabox ) {

            ?><div class="zbs-generic-save-wrap">

                    <div class="ui medium dividing header"><i class="save icon"></i> <?php _e('Company','zero-bs-crm'); ?> <?php _e('Actions','zero-bs-crm'); ?></div>

            <?php

            // localise ID & content
            $companyID = -1; if (is_array($company) && isset($company['id'])) $companyID = (int)$company['id'];
            
                #} Status either way
                $potentialStatuses = zeroBSCRM_getCompanyStatuses();

                $status = ''; if (is_array($company) && isset($company['status'])) $status = $company['status'];

                ?>
                <div>
                    <label for="zbsco_status"><?php echo $this->coOrgLabel.' '.__('Status',"zero-bs-crm"); ?>: </label>
                    <select id="zbsco_status" name="zbsco_status">
                            <?php foreach($potentialStatuses as $z){
                                if($z == $status){$sel = 'selected'; }else{ $sel = '';}
                                echo '<option value="'.$z.'"'. $sel .'>'.__($z,"zero-bs-crm").'</option>';
                            } ?>
                    </select>
                </div>

                <div class="clear"></div>
                <?php


                #} if a saved post...
                //if (isset($post->post_status) && $post->post_status != "auto-draft"){
                if ($companyID > 0){ // existing

                    ?>

                    <div class="zbs-company-actions-bottom zbs-objedit-actions-bottom">

                        <button  class="ui button green" type="button" id="zbs-edit-save"><?php _e("Update","zero-bs-crm"); ?> <?php echo $this->coOrgLabel; ?></button>

                        <?php

                            // delete?

                         // for now just check if can modify, later better, granular perms.
                         if ( zeroBSCRM_permsQuotes() ) { 
                        ?><div id="zbs-company-actions-delete zbs-objedit-actions-delete">
                             <a class="submitdelete deletion" href="<?php echo zbsLink('delete',$companyID,'company'); ?>"><?php _e('Delete Permanently', "zero-bs-crm"); ?></a>
                        </div>
                        <?php } // can delete  ?>
                        
                        <div class='clear'></div>

                    </div>
                <?php


                } else {

                    // NEW quote ?>

                    <div class="zbs-company-actions-bottom zbs-objedit-actions-bottom">
                        
                        <button  class="ui button green" type="button" id="zbs-edit-save"><?php _e("Save","zero-bs-crm"); ?> <?php echo $this->coOrgLabel; ?></button>

                    </div>

                 <?php

                }

            ?></div><?php // / .zbs-generic-save-wrap
              
        } // html

        // saved via main metabox

    }


/* ======================================================
  / Company Actions Metabox
   ====================================================== */

/* ======================================================
  Company Activity Metabox
   ====================================================== */
class zeroBS__Metabox_Company_Activity extends zeroBS__Metabox {

    public function __construct( $plugin_file ) {
    
        $this->postType = 'zerobs_company';
        $this->metaboxID = 'zbs-company-activity-metabox';
        $this->metaboxTitle = __('Activity', 'zero-bs-crm');
        $this->metaboxIcon = 'heartbeat';
        $this->metaboxScreen = 'zerobs_view_company'; // we can use anything here as is now using our func
        $this->metaboxArea = 'side';
        $this->metaboxLocation = 'high';

        // call this 
        $this->initMetabox();

    }

    public function html( $obj, $metabox ) {
            
            global $zbs; 
            
            $objid = -1; if (is_array($obj) && isset($obj['id'])) $objid = $obj['id'];
            
            // no need for this, $obj will already be same $zbsCustomer = zeroBS_getCustomer($objid, true,true,true);
            
            echo '<div class="zbs-activity">';
                echo '<div class="">';
                    $zbsCompanyActivity = zeroBSCRM_getCompanyLogs($objid,true,100,0,'',false);
                    zeroBSCRM_html_companyTimeline($objid,$zbsCompanyActivity,$obj);
                echo '</div>';
             echo '</div>';

    }

    // nothing to save here.
    public function save_data( $objID, $obj ) {
        return $obj;
    }
}


/* ======================================================
  Company Activity Metabox
   ====================================================== */
