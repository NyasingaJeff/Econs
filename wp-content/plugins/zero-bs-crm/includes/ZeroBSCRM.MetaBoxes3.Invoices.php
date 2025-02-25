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

   function zeroBSCRM_InvoicesMetaboxSetup(){

        // main detail
        $zeroBS__Metabox_Invoice = new zeroBS__Metabox_Invoice( __FILE__ );

        // actions (status + save)
        $zeroBS__Metabox_InvoiceActions = new zeroBS__Metabox_InvoiceActions( __FILE__ );

        // invoice tags box
        $zeroBS__Metabox_InvoiceTags = new zeroBS__Metabox_InvoiceTags( __FILE__ );

        // external sources
        $zeroBS__Metabox_ExtSource = new zeroBS__Metabox_ExtSource( __FILE__, 'invoice','zbs-add-edit-invoice-edit');

        // files
        $zeroBS__Metabox_InvoiceFiles = new zeroBS__Metabox_InvoiceFiles( __FILE__ );
   }

   add_action( 'admin_init','zeroBSCRM_InvoicesMetaboxSetup');

/* ======================================================
   / Init Func
   ====================================================== */

/* ======================================================
  Invoicing Metabox
   ====================================================== */

    class zeroBS__Metabox_Invoice extends zeroBS__Metabox{ 
        
        // this is for catching 'new' invoice
        private $newRecordNeedsRedir = false;

        public function __construct( $plugin_file ) {

            // set these
            $this->objType = 'invoice';
            $this->metaboxID = 'zerobs-invoice-edit';
            $this->metaboxTitle = __('Invoice Information','zero-bs-crm'); // will be headless anyhow
            $this->headless = true;
            $this->metaboxScreen = 'zbs-add-edit-invoice-edit';
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

        public function html( $invoice, $metabox ) {

                // localise ID
                $invoiceID = -1; if (is_array($invoice) && isset($invoice['id'])) $invoiceID = (int)$invoice['id'];

                /* this doesn't work, because MS getting Invs from AJAX, 
                should just pass here via json_encode so this'd work...
                    #DEAJAXINV

                // if new + $zbsObjDataPrefill passed, use that instead of loaded trans.
                if ($invoiceID == -1){
                    global $zbsObjDataPrefill;
                    $invoice = $zbsObjDataPrefill;
                }
                */

                global $zbs;

                #} Prefill ID and OBJ are added to the #zbs_invoice to aid in prefilling the data (when drawn with JS)
                $prefill_id = -1; $prefill_obj = -1; $prefill_email = '';
                if (isset($_GET['zbsprefillcust']) && !empty($_GET['zbsprefillcust'])){
                    $prefill_id = (int)sanitize_text_field($_GET['zbsprefillcust']);
                    $prefill_obj = ZBS_TYPE_CONTACT;
                    $prefill_email = zeroBS_customerEmail($prefill_id);  
                }   
                if (isset($_GET['zbsprefillco']) && !empty($_GET['zbsprefillco'])){
                    $prefill_id = (int)sanitize_text_field($_GET['zbsprefillco']);
                    $prefill_obj = ZBS_TYPE_COMPANY;
                    $prefill_email = zeroBS_companyEmail($prefill_id);  
                }                
                ?>
                <?php #} AJAX NONCE ?><script type="text/javascript">var zbscrmjs_secToken = '<?php echo wp_create_nonce( "zbscrmjs-ajax-nonce" ); ?>';</script><?php # END OF NONCE ?>
                <?php #} AJAX NONCE for inv sending... defunct v3.0?
                echo '<input type="hidden" name="inv-ajax-nonce" id="inv-ajax-nonce" value="' . wp_create_nonce( 'inv-ajax-nonce' ) . '" />';

                //invoice UI divs (loader and canvas)
                echo '<div id="zbs_loader"><div class="ui active dimmer inverted"><div class="ui text loader">'. __('Loading Invoice','zero-bs-crm')  .'</div></div><p></p></div>';
                echo "<div id='zbs_invoice' class='zbs_invoice_html_canvas' data-invid='". $invoiceID. "'></div>";

                // we pass the hash along the chain here too :)
                if (isset($invoice['hash'])) echo '<input type="hidden" name="zbsi_hash" id="zbsi_hash" value="' . $invoice['hash'] . '" />';
              
                // custom fields
                $customFields = $zbs->DAL->getActiveCustomFields(array('objtypeid' => ZBS_TYPE_INVOICE));
                if (!is_array($customFields)) $customFields = array();

                // pass data:
                ?><script type="text/javascript">
                    
                    <?php 
                        if ($prefill_obj > 0) echo 'var zbsJS_prefillobjtype = '.$prefill_obj.';';
                        if ($prefill_id > 0) echo 'var zbsJS_prefillid = '.$prefill_id.';';
                        echo 'var zbsJS_prefillemail = \''.$prefill_email.'\';';

                        // only sendemail if have active template :)
                        echo 'var zbsJS_invEmailActive = '.((zeroBSCRM_get_email_status(ZBSEMAIL_EMAILINVOICE) == 1) ? '1' : '-1' ).';';
                    ?>
                </script><?php

                ?><div id="zbs-invoice-custom-fields-holder" style="display:none">
                    <table>
                    <?php

                        // here we put the fields out then:
                        // 1) copy the fields into the UI
                        foreach ($customFields as $cfK => $cF){

                            zeroBSCRM_html_editField($invoice, $cfK, $cF, 'zbsi_');

                        }
                    ?>
                    </table>
                </div><?php


                // allow hook-ins from invoicing pro etc.
                do_action('zbs_invoicing_append');
        }

        public function save_data( $invoiceID, $invoice ) {

            if (!defined('ZBS_OBJ_SAVED')){

                define('ZBS_OBJ_SAVED',1);

                // DAL3.0+
                global $zbs;

                // check this
                if (empty($invoiceID) || $invoiceID < 1)  $invoiceID = -1;

                    // DAL3 way: 
                    $autoGenAutonumbers = true; // generate if not set :)
                    $removeEmpties = false; // req for autoGenAutonumbers
                    $invoice = zeroBS_buildObjArr($_POST,array(),'zbsi_','',$removeEmpties,ZBS_TYPE_INVOICE,$autoGenAutonumbers);            

                    // Use the tag-class function to retrieve any tags so we can add inline.
                    // Save tags against objid
                    $invoice['tags'] = zeroBSCRM_tags_retrieveFromPostBag(true,ZBS_TYPE_INVOICE);  

                        // pay via
                        $invoice['pay_via'] = 0; if (isset($_POST['pay_via']) && $_POST['pay_via'] == -1) $invoice['pay_via'] = -1;

                        //send attachments setting
                        if (isset($_POST['zbsc_sendattachments']) && !empty($_POST['zbsc_sendattachments'])) $invoice['send_attachments'] = 1;

                        //new way..  now not limited to 30 lines as now they are stored in [] type array in JS draw
                        $zbsInvoiceLines = array();
                        foreach($_POST['zbsli_itemname'] as $k => $v){

                            $ks = sanitize_text_field( $k ); // at least this
                            
                            if (!isset($zbsInvoiceLines[$ks]['net'])) $zbsInvoiceLines[$ks]['net']            = 0.0;
                            $zbsInvoiceLines[$ks]['title']         = sanitize_text_field($_POST['zbsli_itemname'][$k]);
                            $zbsInvoiceLines[$ks]['desc']          = sanitize_text_field($_POST['zbsli_itemdes'][$k]);
                            $zbsInvoiceLines[$ks]['quantity']      = sanitize_text_field($_POST['zbsli_quan'][$k]);
                            $zbsInvoiceLines[$ks]['price']         = sanitize_text_field($_POST['zbsli_price'][$k]);

                            // calc a net, if have elements
                            if (
                                isset($zbsInvoiceLines[$ks]['quantity']) && $zbsInvoiceLines[$ks]['quantity'] > 0
                                &&
                                isset($zbsInvoiceLines[$ks]['price']) && $zbsInvoiceLines[$ks]['price'] > 0
                                ){

                                $zbsInvoiceLines[$ks]['net'] = $zbsInvoiceLines[$ks]['quantity']*$zbsInvoiceLines[$ks]['price'];

                            } else {

                                // leave net as empty :)

                            }
                            
                            // taxes now stored as csv in 'taxes', 'tax' contains a total, but that's not passed by MS UI (yet? not needed?)
                            $zbsInvoiceLines[$ks]['tax']           = 0; //if (isset($_POST['zbsli_tax'][$k])) $zbsInvoiceLines[$ks]['tax'] = sanitize_text_field($_POST['zbsli_tax'][$k]);
                            $zbsInvoiceLines[$ks]['taxes']         = ''; if (isset($_POST['zbsli_tax'][$k])) $zbsInvoiceLines[$ks]['taxes'] = sanitize_text_field($_POST['zbsli_tax'][$k]);

                            /* as at 22/2/19, each lineitme here could hold:
                                'order' => '',
                                'title' => '',
                                'desc' => '',
                                'quantity' => '',
                                'price' => '',
                                'currency' => '',
                                'net' => '',
                                'discount' => '',
                                'fee' => '',
                                'shipping' => '',
                                'shipping_taxes' => '',
                                'shipping_tax' => '',
                                'taxes' => '',
                                'tax' => '',
                                'total' => '',
                                'created' => '',
                                'lastupdated' => '',
                            */
                        }
                        if (count($zbsInvoiceLines) > 0) $invoice['lineitems'] = $zbsInvoiceLines;

                        //other items to update

                            // hours or quantity switch
                            if (isset($_POST['invoice-customiser-type'])){
                                $zbsInvoiceHorQ = sanitize_text_field($_POST['invoice-customiser-type']);
                                if ($zbsInvoiceHorQ == 'quantity')  $invoice['hours_or_quantity'] = 1;
                                if ($zbsInvoiceHorQ == 'hours')     $invoice['hours_or_quantity'] = 0;
                            }


                            // totals passed
                            $invoice['discount'] = 0; if (isset($_POST['invoice_discount_total'])) $invoice['discount'] = (float)sanitize_text_field($_POST['invoice_discount_total']);                            
                            $invoice['discount_type'] = 0; if (isset($_POST['invoice_discount_type'])) $invoice['discount_type'] = sanitize_text_field($_POST['invoice_discount_type']);
                            $invoice['shipping'] = 0; if (isset($_POST['invoice_postage_total'])) $invoice['shipping'] = (float)sanitize_text_field($_POST['invoice_postage_total']);
                            $invoice['shipping_tax'] = 0; if (isset($_POST['zbsli_tax_ship'])) $invoice['shipping_taxes'] = (float)sanitize_text_field($_POST['zbsli_tax_ship']);
                            // or shipping_taxes (not set by MS script)

                            // ... js pass through :o Will be overwritten on php calc on addUpdate, actually, v3.0+
                            $invoice['total'] = 0; if (isset($_POST['zbs-inv-grand-total-store'])) $invoice['total'] = (float)sanitize_text_field( $_POST['zbs-inv-grand-total-store'] );

                            // assignments                        
                            $zbsInvoiceContact = (int)sanitize_text_field($_POST['zbs_invoice_contact']);
                            if ($zbsInvoiceContact > 0) $invoice['contacts'] = array($zbsInvoiceContact);
                            $zbsInvoiceCompany = (int)sanitize_text_field($_POST['zbs_invoice_company']);
                            if ($zbsInvoiceCompany > 0) $invoice['companies'] = array($zbsInvoiceCompany);
                            // Later use: 'address_to_objtype'

                            // other fields
                            if (isset($_POST['invoice_status']))    $invoice['status']      = sanitize_text_field($_POST['invoice_status']);
                            if (isset($_POST['zbsi_logo']))         $invoice['logo_url']    = sanitize_text_field($_POST['zbsi_logo']);
                            if (isset($_POST['zbsi_ref']))          $invoice['id_override'] = sanitize_text_field($_POST['zbsi_ref']);

                            // this needs to be translated to UTS (GMT)
                            if (isset($_POST['zbsi_date'])){
                                $invoice['date']        = sanitize_text_field($_POST['zbsi_date']);
                                $invoice['date']        = zeroBSCRM_locale_dateToUTS($invoice['date'],false);
                            }

                            // due date is now calculated on save, then stored as UTS, if passed this way:
                            // ... if due_date not set, editor will keep showing "due in x days" select
                            // ... once set, that'll always show as a datepicker, based on the UTS in due_date
                            if (isset($_POST['zbsi_due'])){

                                // days (-1 - 90)
                                $dueInDays = sanitize_text_field($_POST['zbsi_due']);

                                // got date + due days?
                                if (isset($invoice['date']) && $dueInDays >= 0){

                                    // project it forward
                                    $invoice['due_date'] = $invoice['date'] + ($dueInDays * 60 * 60 * 24);

                                }

                            }
                            // ... this then catches datepicker-picked dates (if passed by datepicker variant to due_days)
                            if (isset($_POST['zbsi_due_date'])){
                                $invoice['due_date']    = sanitize_text_field($_POST['zbsi_due_date']);
                                $invoice['due_date']   = zeroBSCRM_locale_dateToUTS($invoice['due_date'],false);
                            }


                            // Custom Fields.
                            /*$customFields = $zbs->DAL->getActiveCustomFields(array('objtypeid' => ZBS_TYPE_INVOICE));
                            if (!is_array($customFields)) $customFields = array();
                            foreach ($customFields as $cfK => $cfV){
                                
                                if (isset($_POST['zbsi_'.$cfK]))    $invoice[$cfK]      = sanitize_text_field($_POST['zbsi_'.$cfK]);

                            }*/
                            

                    //}

                // add/update
                $addUpdateReturn = $zbs->DAL->invoices->addUpdateInvoice(array(

                            'id'    => $invoiceID,
                            'data'  => $invoice,
                            'limitedFields' => -1,

                            // here we want PHP to calculate the total, tax etc. where we don't calc all the specifics in js
                            'calculate_totals' => 1

                    ));
                //echo 'adding inv:<pre>'.print_r(array($addUpdateReturn,$invoice),1).'</pre>'; exit();

                // Note: For NEW objs, we make sure a global is set here, that other update funcs can catch 
                // ... so it's essential this one runs first!
                // this is managed in the metabox Class :)
                if ($invoiceID == -1 && !empty($addUpdateReturn) && $addUpdateReturn != -1) {
                    
                    $invoiceID = $addUpdateReturn;
                    global $zbsJustInsertedMetaboxID; $zbsJustInsertedMetaboxID = $invoiceID;

                    // set this so it redirs
                    $this->newRecordNeedsRedir = true;
                }

                // success?
                if ($addUpdateReturn != -1 && $addUpdateReturn > 0){

                    // Update Msg
                    // this adds an update message which'll go out ahead of any content
                    // This adds to metabox: $this->updateMessages['update'] = zeroBSCRM_UI2_messageHTML('info olive mini zbs-not-urgent',__('Contact Updated',"zero-bs-crm"),'','address book outline','contactUpdated');
                    // This adds to edit page
                    $this->updateMessage();

                    // catch any non-critical messages
                    $nonCriticalMessages = $zbs->DAL->getErrors(ZBS_TYPE_INVOICE);
                    if (is_array($nonCriticalMessages) && count($nonCriticalMessages) > 0) $this->dalNoticeMessage($nonCriticalMessages);


                } else {

                    // fail somehow
                    $failMessages = $zbs->DAL->getErrors(ZBS_TYPE_INVOICE);

                    // show msg (retrieved from DAL err stack)
                    if (is_array($failMessages) && count($failMessages) > 0)
                        $this->dalErrorMessage($failMessages);
                    else
                        $this->dalErrorMessage(array(__('Insert/Update Failed with general error','zero-bs-crm')));

                    // pass the pre-fill:
                    global $zbsObjDataPrefill; $zbsObjDataPrefill = $invoice;

        
                }

            }

            return $invoice;
        }

        // This catches 'new' contacts + redirs to right url
        public function post_save_data($objID,$obj){

            if ($this->newRecordNeedsRedir){

                global $zbsJustInsertedMetaboxID;
                if (!empty($zbsJustInsertedMetaboxID) && $zbsJustInsertedMetaboxID > 0){

                    // redir
                    wp_redirect( zbsLink('edit',$zbsJustInsertedMetaboxID,$this->objType) );
                    exit;

                }

            }

        }

        public function updateMessage(){

            global $zbs;

            // zbs-not-urgent means it'll auto hide after 1.5s
            // genericified from DAL3.0
            $msg = zeroBSCRM_UI2_messageHTML('info olive mini zbs-not-urgent',$zbs->DAL->typeStr($zbs->DAL->objTypeKey($this->objType)).' '.__('Updated',"zero-bs-crm"),'','address book outline','contactUpdated');

            $zbs->pageMessages[] = $msg;

        }

    }


/* ======================================================
  / Invoicing Metabox
   ====================================================== */


/* ======================================================
  Invoice Files Metabox
   ====================================================== */

    class zeroBS__Metabox_InvoiceFiles extends zeroBS__Metabox{

        public function __construct( $plugin_file ) {

            // DAL3 switched for objType $this->postType = 'zerobs_customer';
            $this->objType = 'invoice';
            $this->metaboxID = 'zerobs-invoice-files';
            $this->metaboxTitle = __('Attachments',"zero-bs-crm");
            $this->metaboxScreen = 'zbs-add-edit-invoice-edit'; //'zerobs_edit_contact'; // we can use anything here as is now using our func
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

        public function html( $invoice, $metabox ) {

                global $zbs;

                $html = '';

                // localise ID
                $invoiceID = -1; if (is_array($invoice) && isset($invoice['id'])) $invoiceID = (int)$invoice['id'];

                #} retrieve
                $zbsFiles = array(); if ($invoiceID > 0) $zbsFiles = zeroBSCRM_files_getFiles('invoice',$invoiceID);
                $zbsSendAttachments = -1; if (is_array($invoice) && isset($invoice['send_attachments'])) $zbsSendAttachments = $invoice['send_attachments'];

                ?><table class="form-table wh-metatab wptbp" id="wptbpMetaBoxMainItemFiles">

                    <?php 

                        // WH only slightly updated this for DAL3 - could do with a cleanup run (contact file edit has more functionality)

                        #} Any existing
                        if (is_array($zbsFiles) && count($zbsFiles) > 0){ 
                          ?><tr class="wh-large"><th><label><?php echo count($zbsFiles).' '.__('Attachment','zero-bs-crm').':'; ?></label></th>
                                    <td id="zbsFileWrapInvoices">
                                        <?php $fileLineIndx = 1; foreach($zbsFiles as $zbsFile){
                                            
                                            $file = zeroBSCRM_files_baseName($zbsFile['file'],isset($zbsFile['priv']));

                                            echo '<div class="zbsFileLine" id="zbsFileLineInvoice'.$fileLineIndx.'"><a href="'.$zbsFile['url'].'" target="_blank">'.$file.'</a> (<span class="zbsDelFile" data-delurl="'.$zbsFile['url'].'"><i class="fa fa-trash"></i></span>)</div>';
                                            $fileLineIndx++;

                                        } ?>
                                    </td></tr><?php

                        } 
                    ?>

                    <?php #adapted from http://code.tutsplus.com/articles/attaching-files-to-your-posts-using-wordpress-custom-meta-boxes-part-1--wp-22291

                            wp_nonce_field(plugin_basename(__FILE__), 'zbsobj_file_attachment_nonce');
                             
                            $html .= '<input type="file" id="zbsobj_file_attachment" name="zbsobj_file_attachment" value="" size="25" class="zbs-dc">';
                            
                            ?><tr class="wh-large"><th><label><?php _e('Add File',"zero-bs-crm");?>:</label><br />(<?php _e('Optional',"zero-bs-crm");?>)<br /><?php _e('Accepted File Types',"zero-bs-crm");?>:<br /><?php echo zeroBS_acceptableFileTypeListStr(); ?></th>
                                <td><?php
                            echo $html;
                    ?></td></tr>

                <?php 

                    // optionally send with email as attachment?

                ?><tr class="">
                    <td colspan="2">
                        <table style="width:100%;border:0">
                            <tr>
                                <td style="width:50%">
                                    <label for="zbsc_sendattachments"><?php _e('Send as Attachments',"zero-bs-crm");?>:</label> <input type="checkbox" id="zbsc_sendattachments" name="zbsc_sendattachments" class="form-control" value="1"<?php if ($zbsSendAttachments == "1") echo ' checked="checked"'; ?> style="line-height: 1em;vertical-align: middle;display: inline-block;margin: 0;margin-top: -0.5em;" />
                                    <br /><?php _e('Optionally send a copy of the attached files along with any invoice emails sent.',"zero-bs-crm");?>                            
                                </td>
                                <td>
                                    <label><?php _e('Note:','zero-bs-crm'); ?></label><br />
                                    <div><em><?php _e("It is the user's responsibility to create an invoice that is compliant with local laws and regulations, including, but not limited to, the application of the correct tax rate(s)","zero-bs-crm");?></em></div>
                                </td>
                            </tr>
                        </table></td>
                    </tr>


            
            </table>
            <script type="text/javascript">

                var zbsInvoicesCurrentlyDeleting = false;
                var zbsMetaboxFilesLang = {
                    'err': '<?php echo zeroBSCRM_slashOut(__('Error',"zero-bs-crm")); ?>',
                    'unabletodel' : '<?php echo zeroBSCRM_slashOut(__('Unable to delete this file',"zero-bs-crm")); ?>',

                }

                jQuery('document').ready(function(){

                    jQuery('.zbsDelFile').click(function(){

                        if (!window.zbsInvoicesCurrentlyDeleting){

                            // blocking
                            window.zbsInvoicesCurrentlyDeleting = true;

                            var delUrl = jQuery(this).attr('data-delurl');
                            var lineIDtoRemove = jQuery(this).closest('.zbsFileLine').attr('id');

                            if (typeof delUrl != "undefined" && delUrl != ''){



                                  // postbag!
                                  var data = {
                                    'action': 'delFile',
                                    'zbsfType': 'invoices',
                                    'zbsDel':  delUrl, // could be csv, never used though
                                    'zbsCID': <?php echo $invoiceID; ?>,
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

                                            // visually remove
                                            jQuery('#' + lineIDtoRemove).remove();

                                            // file deletion errors, show msg:
                                            if (typeof response.errors != "undefined" && response.errors.length > 0){

                                                jQuery.each(response.errors,function(ind,ele){

                                                    jQuery('#zerobs-invoice-files-box').append('<div class="ui warning message" style="margin-top:10px;">' + ele + '</div>');

                                                });
                                                     

                                            }

                                          },
                                          error: function(response){

                                            jQuery('#zerobs-invoice-files-box').append('<div class="ui warning message" style="margin-top:10px;"><strong>' + window.zbsMetaboxFilesLang.err + ':</strong> ' + window.zbsMetaboxFilesLang.unabletodel + '</div>');

                                          }

                                        });

                            }

                            window.zbsInvoicesCurrentlyDeleting = false;

                        } // / blocking

                    });

                });


            </script><?php

        }

        public function save_data( $invoiceID, $invoice ) {

            global $zbsobj_justUploadedObjFile;
            $id = $invoiceID;

            if(!empty($_FILES['zbsobj_file_attachment']['name']) && 
                (!isset($zbsobj_justUploadedObjFile) ||
                    (isset($zbsobj_justUploadedObjFile) && $zbsobj_justUploadedObjFile != $_FILES['zbsobj_file_attachment']['name'])
                )
                ) {


            /* --- security verification --- */
            if(!wp_verify_nonce($_POST['zbsobj_file_attachment_nonce'], plugin_basename(__FILE__))) {
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
            if (!zeroBSCRM_permsInvoices()){
                return $id;
            }
            /* - end security verification - */

            #} Blocking repeat-upload bug
            $zbsobj_justUploadedObjFile = $_FILES['zbsobj_file_attachment']['name'];

                // proceed
                $supported_types = zeroBS_acceptableFileTypeMIMEArr(); //$supported_types = array('application/pdf');
                $arr_file_type = wp_check_filetype(basename($_FILES['zbsobj_file_attachment']['name']));
                $uploaded_type = $arr_file_type['type'];

                if(in_array($uploaded_type, $supported_types) || (isset($supported_types['all']) && $supported_types['all'] == 1)) {
                    $upload = wp_upload_bits($_FILES['zbsobj_file_attachment']['name'], null, file_get_contents($_FILES['zbsobj_file_attachment']['tmp_name']));

                    if(isset($upload['error']) && $upload['error'] != 0) {
                        wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                    } else {
                        //update_post_meta($id, 'zbsobj_file_attachment', $upload);

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
                            $zbsFiles = zeroBSCRM_files_getFiles('invoice',$invoiceID);
               
                            if (is_array($zbsFiles)){

                                //add it
                                $zbsFiles[] = $upload;

                            } else {

                                // first
                                $zbsFiles = array($upload);

                            }
 
                            // update
                            zeroBSCRM_files_updateFiles('invoice',$invoiceID, $zbsFiles);

                            // Fire any 'post-upload-processing' (e.g. CPP makes thumbnails of pdf, jpg, etc.)
                            // not req invoicing: do_action('zbs_post_upload_contact',$upload);
                    }
                }
                else {
                    wp_die("The file type that you've uploaded is not an accepted file format.");
                }
            }

            return $invoice;
        }
    }


/* ======================================================
  / Attach files to invoice metabox
   ====================================================== */





/* ======================================================
    Invoicing Metabox Helpers
   ====================================================== */
function zeroBS__InvoicePro(){

        $upTitle = __('Want more from invoicing?',"zero-bs-crm");
        $upDesc = __('Accept Payments Online with Invoicing Pro.',"zero-bs-crm");
        $upButton = __('Buy Now',"zero-bs-crm");
        $upTarget = "https://jetpackcrm.com/product/invoicing-pro/";

        echo zeroBSCRM_UI2_squareFeedbackUpsell($upTitle,$upDesc,$upButton,$upTarget); 
            
}

/* ======================================================
  / Invoicing Metabox Helpers
   ====================================================== */


/* ======================================================
  Create Tags Box
   ====================================================== */

class zeroBS__Metabox_InvoiceTags extends zeroBS__Metabox_Tags{


    public function __construct( $plugin_file ) {
    
        $this->objTypeID = ZBS_TYPE_INVOICE;
        // DAL3 switched for objType $this->postType = 'zerobs_customer';
        $this->objType = 'invoice';
        $this->metaboxID = 'zerobs-invoice-tags';
        $this->metaboxTitle = __('Invoice Tags',"zero-bs-crm");
        $this->metaboxScreen = 'zbs-add-edit-invoice-edit'; //'zerobs_edit_contact'; // we can use anything here as is now using our func
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
    Invoice Actions Metabox
   ====================================================== */

    class zeroBS__Metabox_InvoiceActions extends zeroBS__Metabox{ 

        public function __construct( $plugin_file ) {

            // set these
            $this->objType = 'invoice';
            $this->metaboxID = 'zerobs-invoice-actions';
            $this->metaboxTitle = __('Invoice Actions','zero-bs-crm'); // will be headless anyhow
            $this->headless = true;
            $this->metaboxScreen = 'zbs-add-edit-invoice-edit';
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

        public function html( $invoice, $metabox ) {

            // debug print_r($invoice); exit();

            ?><div class="zbs-generic-save-wrap">

                    <div class="ui medium dividing header"><i class="save icon"></i> <?php _e('Invoice Actions','zero-bs-crm'); ?></div>

            <?php

            // localise ID & content
            $invoiceID = -1; if (is_array($invoice) && isset($invoice['id'])) $invoiceID = (int)$invoice['id'];
            
                #} if a saved post...
                //if (isset($post->post_status) && $post->post_status != "auto-draft"){
                if ($invoiceID > 0){ // existing

                    $potentialStatuses = zeroBSCRM_getInvoicesStatuses();
                    //print_r($potentialStatuses); exit();

                    // status
                    $zbs_stat = __('Draft','zero-bs-crm'); $sel='';
                    if (is_array($invoice) && isset($invoice['status'])) $zbs_stat = $invoice['status'];


                    /* grid doesn't work great for long-named:

                    <div class="ui grid">
                        <div class="six wide column">
                        </div>
                        <div class="ten wide column">
                        </div>
                    </div>

                    */
                    ?>
                    <div>
                        <label for="invoice_status"><?php _e('Status',"zero-bs-crm"); ?>: </label>
                        <select id="invoice_status" name="invoice_status">
                                <?php foreach($potentialStatuses as $z){
                                    if($z == $zbs_stat){$sel = 'selected'; }else{ $sel = '';}
                                    echo '<option value="'.$z.'"'. $sel .'>'.__($z,"zero-bs-crm").'</option>';
                                } ?>
                        </select>
                    </div>

                    <div class="clear"></div>

                    <?php do_action('zbs_invpro_itemlink'); ?>

                    <div class="clear"></div>


                    <div class="zbs-invoice-actions-bottom zbs-objedit-actions-bottom">
                        <button class="ui button green" type="button" id="zbs-edit-save"><?php _e("Update","zero-bs-crm"); ?> <?php _e("Invoice","zero-bs-crm"); ?></button>
                        <?php

                            #} Quick ver of this: http://themeflection.com/replace-wordpress-submit-meta-box/

                        ?><div id="zbs-invoice-actions-delete zbs-objedit-actions-delete"><?php
                             // for now just check if can modify invs, later better, granular perms.
                             if ( zeroBSCRM_permsInvoices() ) {
                                
                                /* WP Deletion: 
                                      no trash (at least v3.0)
                                       if ( !EMPTY_TRASH_DAYS )
                                            $delete_text = __('Delete Permanently', "zero-bs-crm");
                                       else
                                            $delete_text = __('Move to Trash', "zero-bs-crm");
                            
                                ?><a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
                                */

                                $delete_text = __('Delete Permanently', "zero-bs-crm");
                                ?><a class="submitdelete deletion" href="<?php echo zbsLink('delete',$invoiceID,'invoice'); ?>"><?php echo $delete_text; ?></a><?php
                                

                             } //if ?>
                        </div>
                        
                        <div class='clear'></div>

                    </div>
                <?php


                } else {

                    ?>

                    <?php do_action('zbs_invpro_itemlink'); ?>

                    <button class="ui button green" type="button" id="zbs-edit-save"><?php _e("Save","zero-bs-crm"); ?> <?php _e("Invoice","zero-bs-crm"); ?></button>

                 <?php

                    #} If it's a new post 

                    #} Gross hide :/


            }

            ?></div><?php // / .zbs-generic-save-wrap
              
        }

        // saved via main metabox

    }


/* ======================================================
  / Invoice Actions Metabox
   ====================================================== */



/*#} Currently not used. Started to get confusing. To chat through as part of v3.1+ (and Recurring Invoices work?)
 function zerBSCRM_invoice_admin_submenu(){
     ?>
    <div class="ui menu" id="invoice_menu_ui">
    <div class="ui simple dropdown link item">
        <span class="text"><?php _e("Manage Invoices","zero-bs-crm");?></span>
        <i class="dropdown icon"></i>
        <div class="menu">
            <div class="item"><?php _e("Manage Invoices","zero-bs-crm");?></div>
            <div class="item"><?php _e("Manage Recurring Invoices","zero-bs-crm");?></div>
        </div>
    </div>
    <a class="item">
        <?php _e("Create Invoice", "zero-bs-crm"); ?>
    </a>
    <a class="item">
        <?php _e("Invoice Items", "zero-bs-crm"); ?>
    </a>

    <div class="ui simple dropdown item">
        <span class="text"><?php _e("Settings","zero-bs-crm");?></span>
        <i class="dropdown icon"></i>
        <div class="menu">
            <div class="item"><?php _e("Invoice Settings","zero-bs-crm");?></div>
            <div class="item"><?php _e("Business Information","zero-bs-crm");?></div>
            <div class="item"><?php _e("Tax Information","zero-bs-crm");?></div>
            <div class="item"><?php _e("Templates","zero-bs-crm");?></div>
        </div>
    </div>

    <a class="item right">
        <i class='ui icon info circle'></i><?php _e("Help", "zero-bs-crm"); ?>
    </a>

    </div>
     <?php
 }  */
