/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V1.0
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 17/06/2016
 */
var zbscrmjsCustomerFilterRetrieving = false; var zbscrmjsCustomerFilterHasRetrieved = false;

// Following was first used in mail campaigns v1.0
// it auto-initiates on any zbs customfilter ajax setups :)
// it's requried for mailcampaigns :)
//jQuery(document).ready(function(){

//});


// generic funcs to be called from other JS
function zbscrmJS_customfilters_bindDateRange(dateRangeSelector){

	// Debug console.log('zbscrmJS_customfilters_bindDateRange',dateRangeSelector);

	// Date Range Picker
		jQuery(dateRangeSelector).daterangepicker({
			autoUpdateInput: false,
			locale: {format: 'DD.MM.YYYY',cancelLabel: 'Clear'},
			ranges: {'Today': [moment(), moment()],'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],'Last 7 Days': [moment().subtract(6, 'days'), moment()],'Last 30 Days': [moment().subtract(29, 'days'), moment()],'This Month': [moment().startOf('month'), moment().endOf('month')],'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]}
		}, zbsJSFilterDateRangeCallback);
		jQuery(dateRangeSelector).on('cancel.daterangepicker', function(ev, picker) { jQuery(this).val(''); zbsJSFilterDateRangeClear(); });
		

}
function zbscrmJS_customfilters_bindAjaxButton(formSelector,buttonSelector,cb,errcb){

	// Debug console.log('zbscrmJS_customfilters_bindAjaxButton',[formSelector,buttonSelector,cb,errcb]);

			// ajax filter
			jQuery(buttonSelector).off('click').click(function(){

				if (!window.zbscrmjsCustomerFilterRetrieving){

					// loading + blocker
					window.zbscrmjsCustomerFilterRetrieving = true;
					jQuery(formSelector + ' .zbs-crm-customerfilter-ajax-output').show().html('<i class="fa fa-circle-o-notch fa-spin"></i>');

					// clear filters in effect obj
					window.zbscrmjs_mailExt_CustomerFiltersInEffect = 'none';

					//Debug console.log("zbs-ajax-customer-filters:");

					// postbag!
					var data = {
						'action': 'filterCustomers',
						'sec': window.zbscrmjs_secToken
						// rest is filter data, added below
					};

					// inputs + select
					jQuery(formSelector + ' :input, ' + formSelector + ' select').each(function() {

						// checkboxes
						if (jQuery(this).attr('type') != "checkbox"){
							data[this.name] = jQuery(this).val();
						} else {
							if (jQuery(this).attr('checked'))	data[this.name] = jQuery(this).val();
						}
					});


					//Debug console.log("Sending AJAX DATA:",data);

					// Send 
					jQuery.ajax({
					      type: "POST",
					      url: ajaxurl, // admin side is just ajaxurl not wptbpAJAX.ajaxurl,
					      "data": data,
					      dataType: 'json',
					      timeout: 20000,
					      success: function(response) {

					        //Debug console.log("RESPONSE",response);
							// blocker
							window.zbscrmjsCustomerFilterRetrieving = false;

					        // Update UI
					        if (typeof response.count !== "undefined"){
					        	
					        	jQuery(formSelector + ' .zbs-crm-customerfilter-ajax-output').html('<i class="fa fa-users"></i> <strong>' + response.count + '</strong> Customers Found');

					        	// callback
					        	if (typeof cb == "function") cb(response);

					        } else {
					        	jQuery(formSelector + ' .zbs-crm-customerfilter-ajax-output').html('<i class="fa fa-user-times"></i> No customers found matching these filters');
					        }

					      },
					      error: function(response){ console.error("RESPONSE",response);

					      	// if has retrieved, this is normal error
					      	if (window.zbscrmjsCustomerFilterHasRetrieved){

								// blocker
								window.zbscrmjsCustomerFilterRetrieving = false;

						        //Debug

					        	// callback
					        	if (typeof errcb == "function") errcb(response);

						        // UI
						        jQuery(formSelector + ' .zbs-crm-customerfilter-ajax-output').html('<i class="fa fa-user-times"></i> Error Retrieving Customers');

						    } else {

						    	// hasn't been able to retrieve, this is a sign of caching of big db.

								// has retrieved (for caching of large dbs)
								window.zbscrmjsCustomerFilterHasRetrieved = true;

								// blocker
								window.zbscrmjsCustomerFilterRetrieving = false;

					        	// callback
					        	if (typeof errcb == "function") errcb(response);

						        // UI
						        jQuery(formSelector + ' .zbs-crm-customerfilter-ajax-output').html('<i class="fa fa-tachometer"></i> It\'s taking some time to retrieve your customers.<br />If you have a large database of customers, this initial query can take some time.<br /><strong>Please try again in a few minutes</strong>, (after your customers are cached, this\'ll be fast!)');


						    }


					      }

					});


				} // end blocker



			});


}

function zbsJSFilterDateRangeCallback(start, end) {
        jQuery('#zbs-crm-customerfilter-addedrange-reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        jQuery('#zbs-crm-customerfilter-addedrange').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
function zbsJSFilterDateRangeClear() {
        jQuery('#zbs-crm-customerfilter-addedrange-reportrange span').html('');
        jQuery('#zbs-crm-customerfilter-addedrange').val('');
    }