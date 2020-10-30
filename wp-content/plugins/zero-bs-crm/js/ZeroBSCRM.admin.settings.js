/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V2.98.5
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 12/03/2019
 */

// This file centralises all settings pages JS
// WH started moving inline into here 12/3/19
// ... starting with custom fields.

jQuery(document).ready(function(){

	if (typeof window.wpzbscrm_settings_page != "undefined"){

		switch (window.wpzbscrm_settings_page){

			case 'customfields':
				zeroBSCRM_settingsPage_bindCustomFields();
				break;

		}

	}


});


// ========================================================================
// ====================== Custom Fields
// ========================================================================
function zeroBSCRM_settingsPage_bindCustomFields(){

  console.log("======== CUSTOM FIELDS EDITOR =============");

  // build init
  zbscrmJS_customFields_buildCustomLines();

  // add field 
  jQuery('#zbscrm-addcustomfield-address').click(function(){

    zbscrmJS_customFields_buildLine('addresses','text','','');

  });
  jQuery('#zbscrm-addcustomfield-customer').click(function(){

    zbscrmJS_customFields_buildLine('customers','text','','');

  });
  jQuery('#zbscrm-addcustomfield-customerfiles').click(function(){

    zbscrmJS_customFields_buildLineFiles('customersfiles','');

  });
  jQuery('#zbscrm-addcustomfield-company').click(function(){

    zbscrmJS_customFields_buildLine('companies','text','','');

  });
  jQuery('#zbscrm-addcustomfield-quotes').click(function(){

    zbscrmJS_customFields_buildLine('quotes','text','','');

  });
  jQuery('#zbscrm-addcustomfield-invoices').click(function(){

    zbscrmJS_customFields_buildLine('invoices','text','','');

  });
  jQuery('#zbscrm-addcustomfield-transactions').click(function(){

    zbscrmJS_customFields_buildLine('transactions','text','','');

  });

  	// Post render lines, show
	setTimeout(function(){

		// show 
		zeroBSCRMJS_genericLoaded();

	},0);
  

}

function zbscrmJS_customFields_buildCustomLines(){

	// addresses
	if (typeof window.wpzbscrmCustomFields.addresses != "undefined"){

		// cycle
		jQuery.each(window.wpzbscrmCustomFields.addresses,function(ind,ele){

		  zbscrmJS_customFields_buildLineV3('addresses',ele);

		});

	}

	// customers
	if (typeof window.wpzbscrmCustomFields.customers != "undefined" && window.wpzbscrmCustomFields.customers != "[]"){

		// cycle
		jQuery.each(window.wpzbscrmCustomFields.customers,function(ind,ele){

			  	// build line
      			zbscrmJS_customFields_buildLineV3('customers',ele);

		});

	}

  // customers files
  if (typeof window.wpzbscrmCustomFields.customersfiles != "undefined"){

	    // cycle
	    jQuery.each(window.wpzbscrmCustomFields.customersfiles,function(ind,ele){

	      zbscrmJS_customFields_buildLineFiles('customersfiles',ele[0]);

	    });

  }

  // companies
  if (typeof window.wpzbscrmCustomFields.companies != "undefined"){
    
	    // cycle
	    jQuery.each(window.wpzbscrmCustomFields.companies,function(ind,ele){

	      //zbscrmJS_customFields_buildLine('companies',ele[0],ele[1],ele[2]);
	      zbscrmJS_customFields_buildLineV3('companies',ele);

	    });

  }

  // quotes
  if (typeof window.wpzbscrmCustomFields.quotes != "undefined"){
	    
	    // cycle
	    jQuery.each(window.wpzbscrmCustomFields.quotes,function(ind,ele){

	      //zbscrmJS_customFields_buildLine('quotes',ele[0],ele[1],ele[2]);
	      zbscrmJS_customFields_buildLineV3('quotes',ele);

	    });

  }

  // invoices
  if (typeof window.wpzbscrmCustomFields.invoices != "undefined"){
    
	    // cycle
	    jQuery.each(window.wpzbscrmCustomFields.invoices,function(ind,ele){

	      zbscrmJS_customFields_buildLineV3('invoices',ele);

	    });

  }

  // transactions
  if (typeof window.wpzbscrmCustomFields.transactions != "undefined"){
    
	    // cycle
	    jQuery.each(window.wpzbscrmCustomFields.transactions,function(ind,ele){

	      //zbscrmJS_customFields_buildLine('transactions',ele[0],ele[1],ele[2]);
	      zbscrmJS_customFields_buildLineV3('transactions',ele);

	    });

  }


}

// this puts through zbscrmJS_customFields_buildLine
// ... in one of 2 styles. 
// ... PRE DAL3, all but contacts were "proper" unpacked custom fields, so those go out simply
// ... if DAL3+, all go out as per unpacked custom fields, with slugs :)
// .. this uses zbs_root.dal to distinguish
function zbscrmJS_customFields_buildLineV3(key,ele){

	var dal = 1; // assume
	if (typeof window.zbs_root.dal != "undefined") dal = parseInt(window.zbs_root.dal);

	// this is a lazy sidestep. for the sake of "contacts", it's DAL3 whatever, if it's here.
	// # LEGACY stuff. 
	if (key == "customers") dal = 3;

	switch (dal){


		case 3:


				var placeholder = ele[2];
				if (ele[0] == 'select' && ele[2] == '' && typeof ele[3] == 'object'){
					// this implodes the cf select array, because since DAL2 we store as array, not CSV
					placeholder = ele[3].join();
				}

				var slug = '';
				if (typeof ele[3] !== "undefined") slug = ele[3];

			  	// build line
				zbscrmJS_customFields_buildLine(key,ele[0],ele[1],placeholder,slug);

				break;

		default:

	   	   		zbscrmJS_customFields_buildLine(key,ele[0],ele[1],ele[2]);

	   	   		break;

	  }
}

// filebox specific:
// ignores everything except name :)
function zbscrmJS_customFields_buildLineFiles(area,namestr){

  if (typeof namestr == "undefined") namestr = zeroBSCRMJS_settingsLang('customfield','Custom Field');

  // count existing - no need to add one as "Add new" line counts
  var i = jQuery('#zbscrm-' + area + '-custom-fields tr').length;

  var html = '<tr class="zbscrm-cf"><td class="">';
    html += '<input type="text" class="form-control" name="wpzbscrm_cf_' + area + i + '_n" value="' + namestr + '" placeholder="' + zeroBSCRMJS_settingsLang('fileboxname','File Box Name') + '" /><br />';
    html += '<button type="button" class="zbscrm-remove button" style="margin:5px">' + zeroBSCRMJS_settingsLang('remove','Remove') + '</button></td></tr>';

  // add it
  jQuery('#zbscrm-' + area + '-custom-fields tr').last().before(html);

  // rebind
  setTimeout(function(){ zbscrmJS_customFields_bindRowControls(); },0);

}

// area = customers, quotes, invoices
function zbscrmJS_customFields_buildLine(area,typestr,namestr,placeholder,slug){

  //if (typeof area == "undefined") area = 'customer';
  if (typeof typestr == "undefined") typestr = 'text';
  if (typeof namestr == "undefined") namestr = zeroBSCRMJS_settingsLang('customfield','Custom Field');
  if (typeof placeholder == "undefined") placeholder = '';
  if (typeof slug == "undefined") slug = '';

  // count existing - no need to add one as "Add new" line counts
  var i = jQuery('#zbscrm-' + area + '-custom-fields tr').length;

  /*
		12/3/19 added:

            'autonumber',
            'radio',
            'checkbox',
            'encrypted'


  */

	// HTML Building
	//'text','textarea','date','select','tel','price','numberfloat','numberint','email',
	// select, radio, checkbox 
	// ^^ need all this except those below hidden
	var html = '<tr class="zbscrm-cf"><td class="zbscrm-cf-n">';
	html += '<input type="text" class="form-control" name="wpzbscrm_cf_' + area + i + '_n" value="' + namestr + '" placeholder="' + zeroBSCRMJS_settingsLang('fieldname','Field Name') + '" />';
	if (slug !== '') html += '<div class="ui tiny label teal">' + slug + '</div>';
	html += '<div style="margin-top:.5em"><button type="button" class="zbscrm-remove button" style="margin:5px;">Remove</button></div></td><td>';
	html += '<label for="wpzbscrm_cf_'  + area + i + '_t" class="zbscrm-cf-fieldtype-label">' + zeroBSCRMJS_settingsLang('fieldtype','Field Type:') + '</label>';
		// help link - shown for autonumber - not WL peeps
		if (typeof window.zbs_root.wl != "undefined" && window.zbs_root.wl != 1) html += '<a href="' + window.wpzbscrm_settings_urls.autonumberhelp + '" target="_blank" class="ui mini right floated basic blue button zbs-generic-hide zbs-cf-type-autonumber"><i class="info icon"></i> ' + zeroBSCRMJS_settingsLang('autonumberguide','Autonumber Guide') + '</a>';
	
	// from 2.99.9.10 don't allow the CHANGING of custom field types after initial set
	if (namestr == '' && placeholder == '' && slug == ''){

		// new addition
		html += zbscrmJS_customFields_buildSelect(area,typestr,i);

	} else {

		// existing field, no choice to change from 2.99.9.10
		html += zbscrmJS_customFields_buildNonSelect(area,typestr,i);

	}


		// wrap all the settings:
	html += '<div class="zbscrm-cf-settings-wrap">';
		html += '<div class="zbs-placeholder-text"></div>';

		html += '<input type="text" class="form-control zbs-generic-hide zbs-generic" name="wpzbscrm_cf_' + area + i + '_p" value="' + placeholder + '" placeholder="' + zeroBSCRMJS_settingsLang('fieldplacehold','Field Placeholder Text') + '" />';
	
	// encrypted (only shows if )
	// Removed encrypted (for now), see JIRA-ZBS-738
	//html += '<input type="text" class="form-control zbs-generic-hide zbs-cf-type-encrypted" name="wpzbscrm_cf_' + area + i + '_enp" value="' + placeholder + '" placeholder="' + zeroBSCRMJS_settingsLang('fieldplacehold','Field Placeholder Text') + '" />';
	//html += '<input type="text" class="form-control zbs-generic-hide zbs-cf-type-encrypted" name="wpzbscrm_cf_' + area + i + '_enpass" value="" placeholder="' + zeroBSCRMJS_settingsLang('password','Password') + '" />';

	// autonumber (only shows if )

		// for autonumbers, break placeholder str into autoslots :)
		var autonumberPrefix = '', autonumberNumb = 1, autonumberSuffix = '';
		if (typestr == 'autonumber' && typeof placeholder != "undefined" && placeholder !== ''){

			var autoNumberArray = placeholder.split("#");
			if (autoNumberArray.length == 3){

				autonumberPrefix = autoNumberArray[0];
				autonumberNumb = autoNumberArray[1];
				autonumberSuffix = autoNumberArray[2];

			}

		}

		// output
		html += '<div class="zbs-cf-type-autonumber-wrap zbs-generic-hide zbs-cf-type-autonumber">';

			// fields
			html += '<div class="zbs-cf-type-autonumber-input-wrap"><div class="ui labeled input"><div class="ui label">' + zeroBSCRMJS_settingsLang('prefix','Prefix') + '</div>';
				html += '<input type="text" class="form-control zbs-generic-hide zbs-cf-type-autonumber" name="wpzbscrm_cf_' + area + i + '_anprefix" value="' + autonumberPrefix + '" placeholder="' + zeroBSCRMJS_settingsLang('prefix','Prefix') + ' ' + zeroBSCRMJS_settingsLang('prefixe','(e.g. ABC-)') + '" />';
			html += '</div></div>';
			html += '<div class="zbs-cf-type-autonumber-input-wrap"><div class="ui labeled input"><div class="ui label">' + zeroBSCRMJS_settingsLang('nextnumber','Next Number') + '</div>';
				html += '<input type="text" class="form-control zbs-generic-hide zbs-cf-type-autonumber intOnly zbs-dc" name="wpzbscrm_cf_' + area + i + '_annextnumber" value="' + autonumberNumb + '" placeholder="' + zeroBSCRMJS_settingsLang('nextnumber','Next Number') + ' ' + zeroBSCRMJS_settingsLang('nextnumbere','(e.g. 1)') + '" />';
			html += '</div></div>';
			html += '<div class="zbs-cf-type-autonumber-input-wrap"><div class="ui labeled input"><div class="ui label">' + zeroBSCRMJS_settingsLang('suffix','Suffix') + '</div>';
				html += '<input type="text" class="form-control zbs-generic-hide zbs-cf-type-autonumber" name="wpzbscrm_cf_' + area + i + '_ansuffix" value="' + autonumberSuffix + '" placeholder="' + zeroBSCRMJS_settingsLang('suffix','Suffix') + ' ' + zeroBSCRMJS_settingsLang('suffixe',' (e.g. -FINI)') + '" />';
			html += '</div></div>';
		html += '</div>';

	// close settings wrap:
	html += '</div>'; // / .zbscrm-cf-settings-wrap

	html += '</td></tr>';

  // add it
  jQuery('#zbscrm-' + area + '-custom-fields tr').last().before(html);

  // rebind
  setTimeout(function(){ zbscrmJS_customFields_bindRowControls(); },0);

}

function zbscrmJS_customFields_buildSelect(area,typestr,i){

  var selectHTML = '<select class="form-control zbscrm-customtype" name="wpzbscrm_cf_'  + area + i + '_t" id="wpzbscrm_cf_'  + area + i + '_t">';
    jQuery.each(window.wpzbscrmAcceptableTypes,function(ind,ele){

    	var show = true;
    	// for v2.98.5 only allow autonumber for contacts
    	// 3+ add to other objects, but needs them to go through the "buildFields()" func in the PHP
    	// DAL3 + allow for all: if (area != 'customers' && ele == 'autonumber') show = false;
    	
    	// add?
    	if (show){

		      var eleStr = ucwords(ele); 
		      if (eleStr == 'Tel') eleStr = zeroBSCRMJS_settingsLang('tel','Telephone');
		      if (eleStr == 'Numberfloat') eleStr = zeroBSCRMJS_settingsLang('numbdec','Numeric (Decimals)');
		      if (eleStr == 'Numberint') eleStr = zeroBSCRMJS_settingsLang('numb','Numeric');
		      if (eleStr == 'Encrypted') eleStr = zeroBSCRMJS_settingsLang('encryptedtext','Encrypted');
		      if (eleStr == 'Radio') eleStr = zeroBSCRMJS_settingsLang('radiobuttons','Radio Buttons');

		      selectHTML += '<option value="' + ele + '"';
		      if (ele == typestr) selectHTML += ' selected="selected"';
		      selectHTML += '>' + eleStr + '</option>';                     

		}

    });

    selectHTML += '</select>';

  return selectHTML;
}

// 2.99.9.10 existing custom fields cannot change type (zbscrmJS_customFields_buildNonSelect vs zbscrmJS_customFields_buildSelect)
function zbscrmJS_customFields_buildNonSelect(area,typestr,i){

	var html = '';
  
    jQuery.each(window.wpzbscrmAcceptableTypes,function(ind,ele){

		      var eleStr = ucwords(ele); 
		      if (eleStr == 'Tel') eleStr = zeroBSCRMJS_settingsLang('tel','Telephone');
		      if (eleStr == 'Numberfloat') eleStr = zeroBSCRMJS_settingsLang('numbdec','Numeric (Decimals)');
		      if (eleStr == 'Numberint') eleStr = zeroBSCRMJS_settingsLang('numb','Numeric');
		      if (eleStr == 'Encrypted') eleStr = zeroBSCRMJS_settingsLang('encryptedtext','Encrypted');
		      if (eleStr == 'Radio') eleStr = zeroBSCRMJS_settingsLang('radiobuttons','Radio Buttons');

		      if (ele == typestr) html += '<div class="ui label blue">' + eleStr + '</div>';

    });

    html += '<input type="hidden" name="wpzbscrm_cf_'  + area + i + '_t"  class="zbscrm-customtype" id="wpzbscrm_cf_'  + area + i + '_t" value="' + typestr + '">';

  return html;
}

// fires after a row is built, and when a select is changed
function zbscrmJS_customFields_updateRow(ele){

	// get type str
	var typestr = jQuery('select.zbscrm-customtype, input.zbscrm-customtype',ele).val(); if (typeof typestr == "undefined") typestr = 'text';

	// update placeholder str
	jQuery('.zbs-placeholder-text',jQuery(ele)).html(zbscrmJS_customFieldTypePlaceholder(typestr));

	// hide/show if .zbs-cf-type-autonumber or .zbs-cf-type-autonumber-hide
	if (typestr == 'autonumber' || typestr == 'encrypted'){

		// hide all
		jQuery('.zbs-generic-hide',jQuery(ele)).hide();

		// show their specific controls:
		jQuery('.zbs-cf-type-'+typestr+'-hide',jQuery(ele)).hide();
		jQuery('.zbs-cf-type-'+typestr,jQuery(ele)).show();

	} else {

		// hide all
		jQuery('.zbs-generic-hide',jQuery(ele)).hide();

		// show all generics
		jQuery('.zbs-generic',jQuery(ele)).show();

	}

}

function zbscrmJS_customFields_bindRowControls(){

	// rebind these (force isInt etc.)
	zbscrm_JS_bindFieldValidators();

	// cycle through em make sure right things are shown.
	jQuery('tr.zbscrm-cf').each(function(ind,ele){

		// fire update row on closest tr (parent's parent in this case)
		zbscrmJS_customFields_updateRow(jQuery(ele).closest('tr.zbscrm-cf'));

	});

	// remove line
	jQuery('.zbscrm-remove').off('click').click(function(){

		jQuery(this).closest('tr').remove();

	});

	// change of type
	jQuery('.zbscrm-customtype').off('change').change(function(){

		// fire update row on closest tr (parent's parent in this case)
		zbscrmJS_customFields_updateRow(jQuery(this).closest('tr.zbscrm-cf'));

	});

}


function zbscrmJS_customFieldTypePlaceholder(typestr){

	placeholderstr = zeroBSCRMJS_settingsLang('placeholder','Placeholder');

	// PREP (language)
	switch (typestr){

			// autonumber
			case 'autonumber':
			placeholderstr = zeroBSCRMJS_settingsLang('autonumberformat','Autonumber Format');

				break;
			// select, radio, checkbox
			case 'select':
			case 'radio':
			case 'checkbox':
			placeholderstr = zeroBSCRMJS_settingsLang('csvopt','CSV of options (e.g. \'a,b,c\')');

				break;

			//'text','textarea','date','select','tel','price','numberfloat','numberint','email',
			// encrypted
			default:
			placeholderstr = zeroBSCRMJS_settingsLang('placeholder','Placeholder');

				break;

	}

	return placeholderstr + ':';
}

// ========================================================================
// ====================== / Custom Fields
// ========================================================================



// passes language from window.wpzbscrm_settings_lang (js set in listview php)
function zeroBSCRMJS_settingsLang(key,fallback){

	if (typeof fallback == 'undefined') var fallback = '';

	if (typeof window.wpzbscrm_settings_lang[key] != "undefined") return window.wpzbscrm_settings_lang[key];

	return fallback;
}
