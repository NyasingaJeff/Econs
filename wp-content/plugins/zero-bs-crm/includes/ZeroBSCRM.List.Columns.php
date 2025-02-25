<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V2.2
 *
 * Copyright 2017 ZeroBSCRM.com
 *
 * Date: 30/07/2017
 */


/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */


   #{ Wh NOTE: this should all be in 1 global, messy to ahve them all separate! }


/* ======================================================
  Hard Coded Columns for each list view (UI2.0)
  (Defaults which can be overriden by custom views)
   ====================================================== */

   /*
    // LABEL, column ??, grouping (in column manager)
      array('LABEL','zbsDefault_column_customerid','basefield')

   */


   /* ======================================================================================================
      ======================== Customers
      ===================================================================================================== */

      
   global $zeroBSCRM_columns_customer;

   $zeroBSCRM_columns_customer = array();
   $zeroBSCRM_columns_customer['default'] =  array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'nameavatar' => array(__('Name and Avatar',"zero-bs-crm")),
                                                    //'email' => array('Email','zbsDefault_column_customeremail'),
                                                    'status' => array(__('Status',"zero-bs-crm"),false,'basefield','editinline'=>1),
                                                    'totalvalue' => array(__('Total Value',"zero-bs-crm")),
                                                    'added' => array(__('Added',"zero-bs-crm"),false,'basefield')
                                            );

   $zeroBSCRM_columns_customer['all'] = array(

                                                    'id' => array('ID',false,'basefield'),
                                                    'name' => array(__('Name',"zero-bs-crm"),false,'basefield'),
                                                    'nameavatar' => array(__('Name and Avatar',"zero-bs-crm")),
                                                    'email' => array(__('Email',"zero-bs-crm"),false,'basefield'),
                                                    'status' => array(__('Status',"zero-bs-crm"),false,'basefield','editinline'=>1),

                                                    'hasquote' => array(__('Has Quote',"zero-bs-crm")),
                                                    'hasinvoice' => array(__('Has Invoice',"zero-bs-crm")),
                                                    
                                                    'quotecount' => array(__('Quote Count',"zero-bs-crm")),
                                                    'invoicecount' => array(__('Invoice Count',"zero-bs-crm")),
                                                    'transactioncount' => array(__('Transaction Count',"zero-bs-crm")),

                                                    'quotetotal' => array(__('Quotes Total',"zero-bs-crm")),
                                                    'invoicetotal' => array(__('Invoices Total',"zero-bs-crm")),
                                                    'transactiontotal' => array(__('Transactions Total',"zero-bs-crm")),

                                                    'totalvalue' => array(__('Total Value',"zero-bs-crm")),

                                                    'added' => array(__('Added',"zero-bs-crm"),false,'basefield'),
                                                    'assigned' => array(__('Assigned To',"zero-bs-crm"),false,'basefield','editinline'=>1),
                                                    'latestlog' => array(__('Latest Log',"zero-bs-crm")),
                                                    'tagged' => array(__(__('Tagged',"zero-bs-crm"),"zero-bs-crm")),
                                                    'editlink' => array(__('View',"zero-bs-crm")),
                                                    'editdirectlink' => array(__('Edit',"zero-bs-crm")),
                                                    'phonelink' => array(__('Phone Link',"zero-bs-crm")),
                                                    
                                                    'lastcontacted' => array(__('Last Contacted',"zero-bs-crm")),

                                                    // added 18/10/18
                                                    'company' => array(__('Company',"zero-bs-crm")),
                                                    
                            );

   /* ======================================================================================================
      ======================== / Customers
      ===================================================================================================== */

   /* ======================================================================================================
      ======================== Companies
      ===================================================================================================== */

      
   global $zeroBSCRM_columns_company;

   $zeroBSCRM_columns_company = array();
   $zeroBSCRM_columns_company['default'] =  array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'name' => array(__('Name',"zero-bs-crm"),false,'basefield'),
                                                    'status' => array(__('Status',"zero-bs-crm"),false,'basefield'),
                                                    'contacts' => array(__('Contacts',"zero-bs-crm")),
                                                    'added' => array(__('Added',"zero-bs-crm"),false,'basefield'),
                                                    'viewlink' => array(__('View',"zero-bs-crm"))
                                            );

   $zeroBSCRM_columns_company['all'] = array(


                                                    'id' => array('ID',false,'basefield'),
                                                    'name' => array(__('Name',"zero-bs-crm"),false,'basefield'),
                                                    'email' => array(__('Email',"zero-bs-crm"),false,'basefield'),
                                                    'status' => array(__('Status',"zero-bs-crm"),false,'basefield'),
/* WH removed - these don't work/make sense until db2
                                                    'hasquote' => array(__('Has Quote',"zero-bs-crm"),'zbsDefault_column_hasquote'),
                                                    'hasinvoice' => array(__('Has Invoice',"zero-bs-crm"),'zbsDefault_column_hasinvoice'),
                                                    
                                                    'quotecount' => array(__('Quote Count',"zero-bs-crm"),'zbsDefault_column_quotecount'),
                                                    'invoicecount' => array(__('Invoice Count',"zero-bs-crm"),'zbsDefault_column_invoicecount'),
                                                    'transactioncount' => array(__('Transaction Count',"zero-bs-crm"),'zbsDefault_column_transactioncount'),

                                                    'quotetotal' => array(__('Quotes Total',"zero-bs-crm"),'zbsDefault_column_quotetotal'),
                                                    'invoicetotal' => array(__('Invoices Total',"zero-bs-crm"),'zbsDefault_column_invoicetotal'),
                                                    'transactiontotal' => array(__('Transactions Total',"zero-bs-crm"),'zbsDefault_column_transactiontotal'),

                                                    'totalvalue' => array(__('Total Value',"zero-bs-crm"),'zbsDefault_column_totalvalue'),
*/

                                                    // this added 20/7/18 as added trans :)
                                                    'transactioncount' => array(__('Transaction Count',"zero-bs-crm")),
                                                    'transactiontotal' => array(__('Transactions Total',"zero-bs-crm")),

                                                    'contacts' => array(__('Contacts',"zero-bs-crm")),
                                                    'added' => array(__('Added',"zero-bs-crm"),false,'basefield'),
                                                    'viewlink' => array(__('View',"zero-bs-crm")),
                                                    'editlink' => array(__('Edit',"zero-bs-crm")),
                                                    'assigned' => array(__('Assigned To',"zero-bs-crm"),false,'basefield'),
                                                    'tagged' => array(__(__('Tagged',"zero-bs-crm"),"zero-bs-crm")),
                                                    'phonelink' => array(__('Phone Link',"zero-bs-crm")),
                                                    // Should this be in company? (removed 04/9/18, don't think wired up) ''latestlog' => array(__('Latest Log',"zero-bs-crm")),
                                                    // Should this be in company? (removed 04/9/18, don't think wired up) 'lastcontacted' => array(__('Last Contacted',"zero-bs-crm")),
                                                    
                            );

   /* ======================================================================================================
      ======================== / Companies
      ===================================================================================================== */

      /* ======================================================================================================
      ======================== Quotes
      ===================================================================================================== */

      
   global $zeroBSCRM_columns_quote;

   $zeroBSCRM_columns_quote = array();
   $zeroBSCRM_columns_quote['default'] =  array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'title' => array(__('Quote Title','zero-bs-crm'),false,'basefield'),
                                                    'customer' => array(__('Customer','zero-bs-crm')),
                                                    'status' => array(__('Status','zero-bs-crm'),false,'basefield'),
                                                    'value' => array(__('Quote Value',"zero-bs-crm"),false,'basefield'),
                                                    'editlink' => array(__('Edit',"zero-bs-crm"))


                                            );

   $zeroBSCRM_columns_quote['all'] =  array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'title' => array(__('Quote Title','zero-bs-crm'),false,'basefield'),
                                                    'customer' => array(__('Customer','zero-bs-crm')),
                                                    'status' => array(__('Status','zero-bs-crm'),false,'basefield'),
                                                    'value' => array(__('Quote Value',"zero-bs-crm"),false,'basefield'),
                                                    'editlink' => array(__('Edit',"zero-bs-crm")),

                                                    // not user-configurable, so disabling for now
                                                    //'assignedobj' => array(__('Assigned To',"zero-bs-crm"),false,'basefield'),

                                            );

   /* ======================================================================================================
      ======================== / Quotes
      ===================================================================================================== */


      /* ======================================================================================================
      ======================== Invoices
      ===================================================================================================== */

        
     global $zeroBSCRM_columns_invoice;

     $zeroBSCRM_columns_invoice = array();
     $zeroBSCRM_columns_invoice['default'] =  array(

                                            
                                                      'id' => array('ID',false,'basefield'),

                                                      'ref' => array(__('Reference','zero-bs-crm'),false,'basefield'),
                                                      'customer' => array(__('Customer','zero-bs-crm')),
                                                      'status' => array(__('Status','zero-bs-crm'),false,'basefield'),
                                                      'value' => array(__('Value',"zero-bs-crm"),false,'basefield'),
                            

                                                      'editlink' => array(__('Edit',"zero-bs-crm"))


                                              );

     $zeroBSCRM_columns_invoice['all'] =  array(

                                            
                                                      'id' => array('ID',false,'basefield'),

                                                      'ref' => array(__('Reference','zero-bs-crm'),false,'basefield'),
                                                      'customer' => array(__('Customer','zero-bs-crm')),
                                                      'status' => array(__('Status','zero-bs-crm'),false,'basefield'),
                                                      'value' => array(__('Value',"zero-bs-crm"),false,'basefield'),
                            

                                                      'editlink' => array(__('Edit',"zero-bs-crm")),

                                                      // not user-configurable, so disabling for now
                                                      //'assignedobj' => array(__('Assigned To',"zero-bs-crm"),false,'basefield'),

                                              );


   /* ======================================================================================================
      ======================== / Invoices
      ===================================================================================================== */



/* ======================================================================================================
      ======================== Transactions
      ===================================================================================================== */

      
   global $zeroBSCRM_columns_transaction;

   $zeroBSCRM_columns_transaction = array();
   $zeroBSCRM_columns_transaction['default'] =  array(

                                          
                                                    'id' => array('ID','zbsDefault_column_customerid','basefield'),
                                                    'customer' => array(__('Customer',"zero-bs-crm")),
                                                    'status' => array(__('Status',"zero-bs-crm"),false,'basefield'),
                                                    'total' => array(__('Value',"zero-bs-crm"),false,'basefield'),
                                                    'item' => array(__('Item',"zero-bs-crm"),false,'basefield'),
                                                    'added' => array(__('Added',"zero-bs-crm"),false,'basefield'),
                                                    'editlink' => array(__('Edit Link',"zero-bs-crm"))

                                            );

   $zeroBSCRM_columns_transaction['all'] = array(

                                                    'id' => array('ID','zbsDefault_column_customerid','basefield'),
                                                    'customer' => array(__('Customer',"zero-bs-crm")),
                                                    'customeremail' => array(__('Email',"zero-bs-crm")),
                                                    'tagged' => array(__(__('Tagged',"zero-bs-crm"),"zero-bs-crm")),
                                                    'status' => array(__('Status',"zero-bs-crm"),false,'basefield'),
                                                    'total' => array(__('Value',"zero-bs-crm"),false,'basefield'),
                                                    'item' => array(__('Item',"zero-bs-crm"),false,'basefield'),
                                                    
                                                    'added' => array(__('Added',"zero-bs-crm"),false,'basefield'),
                                                    'editlink' => array(__('Edit Link',"zero-bs-crm")),
                                                    
                                                    // not user-configurable, so disabling for now
                                                    //'assignedobj' => array(__('Assigned To',"zero-bs-crm"),false,'basefield'),

                            );

   /* ======================================================================================================
      ======================== / Transactions
      ===================================================================================================== */


/* ======================================================================================================
      ======================== Forms
      ===================================================================================================== */

      
   global $zeroBSCRM_columns_form;

   $zeroBSCRM_columns_form = array();
   $zeroBSCRM_columns_form['default'] =  array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'title' => array(__('Title','zero-bs-crm'),false,'basefield'),
                                                    'style' => array(__('Style',"zero-bs-crm"),false,'basefield'),
                                                    'views' => array(__('Views',"zero-bs-crm")),
                                                    'conversions' => array(__('Conversions',"zero-bs-crm")),
                                                    'added' => array(__('Added',"zero-bs-crm"),false,'basefield'),
                                                    'editlink' => array(__('Edit',"zero-bs-crm"))


                                            );

   $zeroBSCRM_columns_form['all'] =  array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'title' => array(__('Title','zero-bs-crm'),false,'basefield'),
                                                    'style' => array(__('Style',"zero-bs-crm"),false,'basefield'),
                                                    'views' => array(__('Views',"zero-bs-crm")),
                                                    'conversions' => array(__('Conversions',"zero-bs-crm")),
                                                    'added' => array(__('Added',"zero-bs-crm"),false,'basefield'),
                                                    'editlink' => array(__('Edit',"zero-bs-crm"))

                                            );

   /* ======================================================================================================
      ======================== / Forms
      ===================================================================================================== */

   /* ======================================================================================================
      ======================== Segments
      ===================================================================================================== */


   global $zeroBSCRM_columns_segment;

   $zeroBSCRM_columns_segment = array();
   $zeroBSCRM_columns_segment['default'] =  array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'name' => array(__('Name','zero-bs-crm'),false,'basefield'),
                                                    'audiencecount' => array(__('Contact Count','zero-bs-crm')),
                                                    'action' => array(__('Action','zero-bs-crm'))
                                            );

   $zeroBSCRM_columns_segment['all'] = array(

                                                    'id' => array('ID',false,'basefield'),
                                                    'name' => array(__('Name','zero-bs-crm'),false,'basefield'),
                                                    'audiencecount' => array(__('Contact Count','zero-bs-crm')),
                                                    'action' => array(__('Action','zero-bs-crm')),
                                                    'added' => array(__('Added','zero-bs-crm'))
                                                    
                            );



   /* ======================================================================================================
      ======================== / Segments
      ===================================================================================================== */

   /* ======================================================================================================
      ======================== Quote Templates
      ===================================================================================================== */


   global $zeroBSCRM_columns_quotetemplate;

   $zeroBSCRM_columns_quotetemplate = array();
   $zeroBSCRM_columns_quotetemplate['default'] =  array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'title' => array(__('Title','zero-bs-crm'),false,'basefield'),
                                                    'action' => array(__('Action','zero-bs-crm'))
                                            );

   $zeroBSCRM_columns_quotetemplate['all'] = array(

                                          
                                                    'id' => array('ID',false,'basefield'),
                                                    'title' => array(__('Title','zero-bs-crm'),false,'basefield'),
                                                    'action' => array(__('Action','zero-bs-crm'))
                                                    
                            );



   /* ======================================================================================================
      ======================== / Quote Templates
      ===================================================================================================== */










   /* ======================================================================================================
      ======================== CUSTOMER filters
      ===================================================================================================== */

  // Also home to filter button choices
  global $zeroBSCRM_filterbuttons_customer;

  $zeroBSCRM_filterbuttons_customer['default'] = array(

                              'lead' => array(__('Lead',"zero-bs-crm")),
                              'customer' => array(__('Customer',"zero-bs-crm"))
                              /* not in v2.2 - paging makes very unperformant (see DAL getCustomers for deets)
                              'over100' => array('Over '.zeroBSCRM_getCurrencyChr().'100')
                              */
                        );
  $zeroBSCRM_filterbuttons_customer['all'] = array(

        'lead' => array(__('Lead',"zero-bs-crm")),
        'customer' => array(__('Customer',"zero-bs-crm"))
        /* not in v2.2 - paging makes very unperformant (see DAL getCustomers for deets)
        'over100' => array('Over '.$currChar.'100'),
        'over200' => array('Over '.$currChar.'200'),
        'over300' => array('Over '.$currChar.'300'),
        'over400' => array('Over '.$currChar.'400'),
        'over500' => array('Over '.$currChar.'500') */

    );



   /* ======================================================================================================
      ======================== / CUSTOMER filters
      ===================================================================================================== */

   /* ======================================================================================================
      ======================== Company filters
      ===================================================================================================== */

      global $zeroBSCRM_filterbuttons_company;

      $zeroBSCRM_filterbuttons_company['default'] = array(

                                  'lead' => array(__('Lead',"zero-bs-crm")),
                                  'customer' => array(__('Customer',"zero-bs-crm"))
                            );
      $zeroBSCRM_filterbuttons_company['all'] = array(

            'lead' => array(__('Lead',"zero-bs-crm")),
            'customer' => array(__('Customer',"zero-bs-crm"))

        );

   /* ======================================================================================================
      ======================== / Company filters
      ===================================================================================================== */




   /* ======================================================================================================
      ======================== Quote filters
      ===================================================================================================== */

    global $zeroBSCRM_filterbuttons_quote;

    $zeroBSCRM_filterbuttons_quote['default'] = array(

                                'status_accepted' => array(__('Accepted',"zero-bs-crm")),
                                'status_notaccepted'    => array(__('Not Accepted',"zero-bs-crm"))

                          );

    $zeroBSCRM_filterbuttons_quote['all'] = array(

              'status_accepted' => array(__('Accepted',"zero-bs-crm")),
              'status_notaccepted'    => array(__('Not Accepted',"zero-bs-crm"))

      );


   /* ======================================================================================================
      ======================== / Quote filters
      ===================================================================================================== */




   /* ======================================================================================================
      ======================== Invoice filters
      ===================================================================================================== */

    global $zeroBSCRM_filterbuttons_invoice;

    $zeroBSCRM_filterbuttons_invoice['default'] = array(

              'status_draft' => array(__('Draft',"zero-bs-crm")),
              'status_unpaid'    => array(__('Unpaid',"zero-bs-crm")),
              'status_paid'    => array(__('Paid',"zero-bs-crm")),
              'status_overdue'    => array(__('Overdue',"zero-bs-crm"))

      );

    $zeroBSCRM_filterbuttons_invoice['all'] = array(

              'status_draft' => array(__('Draft',"zero-bs-crm")),
              'status_unpaid'    => array(__('Unpaid',"zero-bs-crm")),
              'status_paid'    => array(__('Paid',"zero-bs-crm")),
              'status_overdue'    => array(__('Overdue',"zero-bs-crm"))

      );


   /* ======================================================================================================
      ======================== / Invoice filters
      ===================================================================================================== */



   /* ======================================================================================================
      ======================== Transaction filters
      ===================================================================================================== */

    global $zeroBSCRM_filterbuttons_transaction;

    $zeroBSCRM_filterbuttons_transaction['default'] = array(

                                'status_succeeded' => array(__('Succeeded',"zero-bs-crm")),
                                'status_failed'    => array(__('Failed',"zero-bs-crm")),
                                'status_refunded'  => array(__('Refunded',"zero-bs-crm"))
                          );

    $zeroBSCRM_filterbuttons_transaction['all'] = array(

          'status_succeeded'   => array(__('Succeeded',"zero-bs-crm")),
          'status_failed'      => array(__('Failed',"zero-bs-crm")),
          'status_refunded'    => array(__('Refunded',"zero-bs-crm")),
          'status_pending'     => array(__('Pending',"zero-bs-crm")),
          'status_processing'  => array(__('Processing',"zero-bs-crm"))

      );


   /* ======================================================================================================
      ======================== / Transaction filters
      ===================================================================================================== */




   /* ======================================================================================================
      ======================== Form filters
      ===================================================================================================== */

    global $zeroBSCRM_filterbuttons_form;

    $zeroBSCRM_filterbuttons_form['default'] = array(


      );

    $zeroBSCRM_filterbuttons_form['all'] = array(


      );


   /* ======================================================================================================
      ======================== / Form filters
      ===================================================================================================== */



   /* ======================================================================================================
      ======================== Segment filters
      ===================================================================================================== */

    global $zeroBSCRM_filterbuttons_segment;

    $zeroBSCRM_filterbuttons_segment['default'] = array(


      );

    $zeroBSCRM_filterbuttons_segment['all'] = array(


      );


   /* ======================================================================================================
      ======================== / Segment filters
      ===================================================================================================== */



   /* ======================================================================================================
      ======================== Quote Template filters
      ===================================================================================================== */

    global $zeroBSCRM_filterbuttons_quotetemplate;

    $zeroBSCRM_filterbuttons_quotetemplate['default'] = array(


      );

    $zeroBSCRM_filterbuttons_quotetemplate['all'] = array(


      );


   /* ======================================================================================================
      ======================== / Quote Template filters
      ===================================================================================================== */


    // worth doing here?
    //global $zbs;
    //$zeroBSCRM_columns_customer['current'] = $zbs->settings->get('customviews2');



    function zeroBSCRM_unpackListViewSettings(){

      global $zbs;

      // Auto-add status filters

        global $zeroBSCRM_filterbuttons_customer;

        // if setting on, append these :) - temp fix really :)
        #} 2.17 - filters from status
        if (zeroBSCRM_getSetting('filtersfromstatus') == "1"){

            // get statuses - ripped from settings
            $customisedfields = zeroBSCRM_getSetting('customisedfields');
            if (isset($customisedfields['customers']['status']) && is_array($customisedfields['customers']['status'])) $zbsStatusStr = $customisedfields['customers']['status'][1];                                        
            if (empty($zbsStatusStr)) {
              #} Defaults:
              global $zbsCustomerFields; if (is_array($zbsCustomerFields)) $zbsStatusStr = implode(',',$zbsCustomerFields['status'][3]);
            }                                        


            // split + add to "All" list
            $statuses = explode(',',$zbsStatusStr);

            // add one for each :)
            foreach ($statuses as $s){

              // note this doesn't allow for peeps inclusion of special characters in statuses...
              $permS = str_replace(' ','_',strtolower($s));

              if (
                  // if isn't already directly set
                    !isset($zeroBSCRM_filterbuttons_customer['all']['status_'.strtolower($permS)]) &&
                  // and isn't "lead" or "customer" defaults
                    !isset($zeroBSCRM_filterbuttons_customer['all'][strtolower($permS)])
                  ) {

                    // add it
                    $zeroBSCRM_filterbuttons_customer['all']['status_'.$permS] = array(__($s,"zero-bs-crm"));

              }
            }

        }



      // Auto-add segment filters DAL2 only
      if ($zbs->isDAL2()){

          // if setting on, append these :) - temp fix really :)
          #} 2.17 - filters from status
          if (zeroBSCRM_getSetting('filtersfromsegments') == "1"){

              // get segments
              if ($zbs->isDAL3())
                $segments = $zbs->DAL->segments->getSegments(-1,100,0,false,'','','zbsseg_name','ASC');
              else
                $segments = $zbs->DAL->getSegments(-1,100,0,false,'','','zbsseg_name','ASC');


              // add one for each :)
              foreach ($segments as $s){

                if (
                    // if isn't already directly set
                      !isset($zeroBSCRM_filterbuttons_customer['all']['segment_'.strtolower($s['slug'])])
                    ) {

                      // add it
                      $zeroBSCRM_filterbuttons_customer['all']['segment_'.strtolower($s['slug'])] = array('<i class="pie chart icon"></i>'.$s['name']);

                }
              }

          }

      } // quickfilters for segments - dal2 only

      /* All fields now done globally, beneath this.
      // Contact Fields
      global $zeroBSCRM_columns_customer, $zbsCustomerFields;
        

        if ($zbs->isDAL2()){

            // add all normal fields to columns (DAL2) from 2.95
                if (is_array($zbsCustomerFields) && count($zbsCustomerFields) > 0){ 
                  foreach ($zbsCustomerFields as $fKey => $fDetail){
                    if (!isset($zeroBSCRM_columns_customer['all'][$fKey])){

                      // add it
                      $cfTitle = $fKey; if (is_array($fDetail) && isset($fDetail[1])) $cfTitle = $fDetail[1];
                      $zeroBSCRM_columns_customer['all'][$fKey] = array($cfTitle,false,'basefield'); // note adding as basefield :)

                    }
                  }
              }


        / * this is no longer needed as above does automatically 

            // Optionally add custom fields to columns (DAL2) 
                // get custom fields
                $contactCustomFields = $zbs->DAL->getActiveCustomFields(array('objtypeid'=>1)); // should be ZBS_TYPE_CONTACT when DAL2 everywhere

                // add if not already in
                if (is_array($contactCustomFields) && count($contactCustomFields) > 0){ 
                    foreach ($contactCustomFields as $cfKey => $cfDetail){
                      if (!isset($zeroBSCRM_columns_customer['all'][$cfKey])){

                        // add it
                        $cfTitle = $cfKey; if (is_array($cfDetail) && isset($cfDetail[1])) $cfTitle = $cfDetail[1];
                        $zeroBSCRM_columns_customer['all'][$cfKey] = array($cfTitle,false,'basefield'); // note adding as basefield :)

                      }
                    }
                }

        * /

        } */


      // ALL FIELD TYPES
      global $zeroBSCRM_columns_customer, $zbsCustomerFields;
      global $zeroBSCRM_columns_company, $zbsCompanyFields;
      global $zeroBSCRM_columns_quote, $zbsCustomerQuoteFields;
      global $zeroBSCRM_columns_invoice, $zbsCustomerInvoiceFields;
      global $zeroBSCRM_columns_transaction, $zbsTransactionFields;
      global $zeroBSCRM_columns_form, $zbsFormFields;
        

        if ($zbs->isDAL2()){

          $useSecondAddress = zeroBSCRM_getSetting('secondaddress');

          // Cycle through each + add
          $mappings = array(

              'zeroBSCRM_columns_customer' => 'zbsCustomerFields',
              'zeroBSCRM_columns_company' => 'zbsCompanyFields',
              'zeroBSCRM_columns_quote' => 'zbsCustomerQuoteFields', // not sure why naming convention lost here
              'zeroBSCRM_columns_invoice' => 'zbsCustomerInvoiceFields', // not sure why naming convention lost here
              'zeroBSCRM_columns_transaction' => 'zbsTransactionFields',
              'zeroBSCRM_columns_form' => 'zbsFormFields',

            );

            foreach ($mappings as $columnsObjName => $fieldsObjName){

                // add all normal fields to columns (DAL2) from 2.95
                if (is_array(${$fieldsObjName}) && count(${$fieldsObjName}) > 0){ 
                  foreach (${$fieldsObjName} as $fKey => $fDetail){
                    if (!isset(${$columnsObjName}['all'][$fKey])){

                      // some need hiding (just for api/behind scenes:)
                      $hideCol = false; if (is_array($fDetail) && isset($fDetail['nocolumn']) && $fDetail['nocolumn']) $hideCol = true;

                      if (!$hideCol){

                        $skip = false;  // skip addr 2 if off

                        // add it
                        $cfTitle = $fKey; if (is_array($fDetail) && isset($fDetail[1])) $cfTitle = $fDetail[1];

                        // secaddr get's dealt with:
                        if (substr($fKey,0,8) == 'secaddr_') {
                          $cfTitle .= ' ('.__('Addr #2','zero-bs-crm').')';
                          if ($useSecondAddress !== "1") $skip = true;
                        }

                        if (!$skip) ${$columnsObjName}['all'][$fKey] = array($cfTitle,false,'basefield'); // note adding as basefield :)
                      }

                    }
                  }
                }

            }

        }
        
        
        // Auto-add status filters - COMPANY

        global $zeroBSCRM_filterbuttons_company;

        // if setting on, append these :) - temp fix really :)
        #} 2.17 - filters from status
        if (zeroBSCRM_getSetting('filtersfromstatus') == "1"){

            // get statuses - ripped from settings
            $customisedfields = zeroBSCRM_getSetting('customisedfields');
            if (isset($customisedfields['companies']['status']) && is_array($customisedfields['companies']['status'])) $zbsStatusStr = $customisedfields['companies']['status'][1];                                        
            if (empty($zbsStatusStr)) {
              #} Defaults:
              global $zbsCompanyFields; if (is_array($zbsCompanyFields)) $zbsStatusStr = implode(',',$zbsCompanyFields['status'][3]);
            }                                        


            // split + add to "All" list
            $statuses = explode(',',$zbsStatusStr);

            // add one for each :)
            foreach ($statuses as $s){

              // note this doesn't allow for peeps inclusion of special characters in statuses...
              $permS = str_replace(' ','_',strtolower($s));

              if (
                  // if isn't already directly set
                    !isset($zeroBSCRM_filterbuttons_company['all']['status_'.strtolower($permS)]) &&
                  // and isn't "lead" or "customer" defaults
                    !isset($zeroBSCRM_filterbuttons_company['all'][strtolower($permS)])
                  ) {

                    // add it
                    $zeroBSCRM_filterbuttons_company['all']['status_'.$permS] = array(__($s,"zero-bs-crm"));

              }
            }

        }



        // Auto-add 'not-contacted-in-x-days' (based on listview settings no 30 e.g.)
        $quickFilterListViewSettings = zeroBSCRM_getSetting('quickfiltersettings');
        if (isset($quickFilterListViewSettings['notcontactedinx']) && !empty($quickFilterListViewSettings['notcontactedinx']) && $quickFilterListViewSettings['notcontactedinx'] > 0){

          $days = (int)$quickFilterListViewSettings['notcontactedinx'];
          $permS = 'notcontactedin'.$days;
          $s = __('Not Contacted in '.$days.' days',"zero-bs-crm");

            // Add to contacts

              if (
                  // if isn't already directly set
                    !isset($zeroBSCRM_filterbuttons_customer['all'][$permS])
                  ) {

                    // add it
                    $zeroBSCRM_filterbuttons_customer['all'][$permS] = array($s,"zero-bs-crm");

              }

            // Add to Companies

              if (
                  // if isn't already directly set
                    !isset($zeroBSCRM_filterbuttons_company['all'][$permS])
                  ) {

                    // add it
                    $zeroBSCRM_filterbuttons_company['all'][$permS] = array($s,"zero-bs-crm");

              }

        }


        // only DB2 peeps
        if ($zbs->isDAL2()){

            // Auto-add 'olderthan-x-days' (based on listview settings no 30 e.g.)
            if (isset($quickFilterListViewSettings['olderthanx']) && !empty($quickFilterListViewSettings['olderthanx']) && $quickFilterListViewSettings['olderthanx'] > 0){

              $days = (int)$quickFilterListViewSettings['olderthanx'];
              $permS = 'olderthanx'.$days;
              $s = __('Older than '.$days.' days',"zero-bs-crm");

                // Add to contacts

                  if (
                      // if isn't already directly set
                        !isset($zeroBSCRM_filterbuttons_customer['all'][$permS])
                      ) {

                        // add it
                        $zeroBSCRM_filterbuttons_customer['all'][$permS] = array($s,"zero-bs-crm");

                  }

            }

        }

        

        // Auto-add 'edit link' to quotes invs (somehow it wasn't always adding)
        $customviews2 = zeroBSCRM_getSetting('customviews2'); $cv2Changed = false;
        if (isset($customviews2['quote']) && !array_key_exists('editlink', $customviews2['quote'])){

            // add it
            $customviews2['quote']['editlink'] = array(__('Edit',"zero-bs-crm"));
            $cv2Changed = true;

        }
        if (isset($customviews2['invoice']) && !array_key_exists('editlink', $customviews2['invoice'])){

            // add it
            $customviews2['invoice']['editlink'] = array(__('Edit',"zero-bs-crm"));
            $cv2Changed = true;

        }
        if ($cv2Changed && is_array($customviews2)){

            // Save setting
            global $zbs;
            return $zbs->settings->update('customviews2',$customviews2);
        

        }


    }
