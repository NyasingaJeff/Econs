/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V2.52+
 *
 * Copyright 2018 ZeroBSCRM.com
 *
 * Date: 28/02/18
 */
 jQuery(document).ready(function(){

    // init if settings there (not on non-listview pages)
    if (typeof window.zbsEditSettings != "undefined") zeroBSCRMJS_initEditView();


    // check type :)
    if (typeof window.zbsEditSettings != "undefined" && typeof window.zbsEditSettings.objdbname != "undefined" && window.zbsEditSettings.objdbname == 'contact'){

        zeroBSCRMJS_editContactInit();

    } // / is contact

    if (typeof window.zbsEditSettings != "undefined" && typeof window.zbsEditSettings.objdbname != "undefined" && window.zbsEditSettings.objdbname == 'event'){

        // bind 
        setTimeout(function(){
            zeroBSCRMJS_events_showContactLinkIf(jQuery("#zbse_customer").val());
            zeroBSCRMJS_events_showCompanyLinkIf(jQuery("#zbse_company").val());
        },0);

    } // / is task




});

// Generic / all edit views
function zeroBSCRMJS_initEditView(){

    //console.log('settings init');

        // actions button
        jQuery('.ui.dropdown').dropdown();

        // metabox dropdowns:
        //jQuery('.zbs-metabox .ui.dropdown').dropdown();

        // Submit button (learn bar)
        jQuery('#zbs-edit-save').off('click').click(function(){

            // copy tags into input (if any)
            zeroBSCRMJS_buildTagsInput();

            // set flag to say 'okay to save changes (skip warning)'
            window.zbscrmjsPageChangesSave = true;

            // save
            jQuery('#zbs-edit-form').submit();

            // catch all (save didn't work?)
            setTimeout(function(){
                window.zbscrmjsPageChangesSave = false;
            },2000);

        });

        // draw 
        zeroBSCRMJS_drawEditView();

        // init pre-leave if dirty
        zeroBSCRMJS_preLeaveEditView();

}


var zbscrmjsPageChangesSave = false; // this is a flag, letting it not prompt when 
function zeroBSCRMJS_preLeaveEditView(){

    /* moved inline to the save func assigned to button to have absolute control of order.
    jQuery('#zbs-edit-save').click(function(){
        window.zbscrmjsPageChangesSave = true;

        setTimeout(function(){
            window.zbscrmjsPageChangesSave = false;
        },2000);
    }); */

    jQuery(window).on('beforeunload', function(){

        if (Object.keys(window.zbscrmjsPageChanges).length > 0 && !window.zbscrmjsPageChangesSave){

            // Chrome doesn't even show this message, it defaults to its own 
            // Leave Site? Changes you have made might not be saved?
            // so leave english here, will probs be ignored.
            return 'Are you sure you want to leave, you will loose unsaved changes?';
        }

    });
}

// passes language from window.zbsEditViewLangLabels (js set in listview php)
function zeroBSCRMJS_editViewLang(key,fallback){

    if (typeof fallback == 'undefined') var fallback = '';

    if (typeof window.zbsEditViewLangLabels[key] != "undefined") return window.zbsEditViewLangLabels[key];

    return fallback;
}

function zeroBSCRMJS_drawEditView(){

    //console.log('drawing with',window.zbsListViewParams);

    // if no blocker
    if (!window.zbsDrawEditViewBlocker){

        // put blocker up
        window.zbsDrawEditViewBlocker = true;

        // empty table, show loading
        jQuery('#zbs-list-table-wrap').html(window.zbsDrawListLoadingBoxHTML);

        // Draw tags
        zeroBSCRMJS_buildTags();

        zeroBSCRMJS_editViewBinds();

        // hide any notifications
        zeroBSCRMJS_hideNotificationsAfter();

    }

}

function zeroBSCRMJS_editViewBinds(){


}

// hides non-urgent notifications after 1.5s
function zeroBSCRMJS_hideNotificationsAfter(){

    setTimeout(function(){

        jQuery('#zbs-edit-notification-wrap .zbs-not-urgent').slideUp(300,function(){

            // if no notifications, after, hide the notification wrap :)
            if (jQuery('#zbs-edit-notification-wrap .ui.info:visible').length == 0) jQuery('#zbs-edit-notification-row').hide();

        });      

    },1500);

}



/* ============================================================================================================
    
    Edit contact specific JS (Taken from editcust.js old file for DB2 edit view)

============================================================================================================ */

// Contact Edit specifics
function zeroBSCRMJS_editContactInit(){

        //any code in here specific for edit customer page
        console.log("======== CUSTOMER EDIT SCRIPTS =============");


        // profile pic edit
        jQuery('#zbs-contact-edit-avatar').mouseenter(function(){

            jQuery('#zbs-edit-contact-avatar').show();

        }).mouseleave(function(){

            jQuery('#zbs-edit-contact-avatar').hide();

        });

        // Uploader
        // http://stackoverflow.com/questions/17668899/how-to-add-the-media-uploader-in-wordpress-plugin (3rd answer)                    
        jQuery('#zbs-edit-contact-avatar').click(function(e) {
            e.preventDefault();
            var image = wp.media({ 
                title: window.zbsContactAvatarLang.upload,
                // mutiple: true if you want to upload multiple files at once
                multiple: false
            }).open()
            .on('select', function(e){
                
                // This will return the selected image from the Media Uploader, the result is an object
                var uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                // Output to the console uploaded_image
                //console.log(uploaded_image);
                var image_url = uploaded_image.toJSON().url;
                // Let's assign the url value to the input field
                jQuery('#zbs-contact-avatar-custom-url').val(image_url);
                console.log('assigned:',image_url);

                // load
                jQuery('h2',jQuery('#zbs-contact-edit-avatar')).html('<img src="' + image_url + '" alt="" />');

            });
        });

        jQuery('.send-sms-none').on("click",function(e){

            console.log("SMS button clicked");

                    swal(
                        'Twilio Extension Needed!',
                        'To SMS your contacts you need the <a target="_blank" style="font-weight:900;text-decoration:underline;" href="https://jetpackcrm.com/extension-bundles/">Twilio extension</a> (included in our Entrepreneurs Bundle)',
                        'info'
                    );
                    


        });

        // automatic "linkify" check + add
        // note - not certain if this may interfere with some, if so, exclude via class (as they'll be added e.g. email)
        jQuery('.zbs-text-input input').keyup(function(){

            zeroBSCRMJS_initLinkify(this);

        });
        // fire linkify for all inputs on load
        jQuery('.zbs-text-input input').each(function(ind,ele){

            zeroBSCRMJS_initLinkify(ele);

        });
}


 function zeroBSCRMJS_initLinkify(ele){
    // find any links?
        var v = jQuery(ele).val(); var bound = false;
        if (v.length > 5) {

            var possMatch = zeroBSCRMJS_retrieveURLS(v);

            if (typeof possMatch == "object" && typeof possMatch !== "null"){

                if (possMatch != null && possMatch[0] != "undefined"){

                    // remove any prev
                    jQuery('.zbs-linkify',jQuery(ele).parent()).remove();

                    // linkify
                    jQuery(ele).parent().addClass('ui action input fluid').append('<button class="ui icon button zbs-linkify" type="button" data-url="' + possMatch[0] + '" title="Go To ' + possMatch[0] + '"><i class="linkify icon"></i></button>');

                    // rebind
                    zeroBSCRMJS_bindLinkify();

                    bound = true;

                }

            } else {

                /* not inc in rollout - wait for MS email func + tie in

                // emails
                var possMatch = zeroBSCRMJS_retrieveEmails(v);

                if (possMatch != null && possMatch[0] != "undefined"){

                    // remove any prev
                    jQuery('.zbs-linkify',jQuery(ele).parent()).remove();

                    // linkify
                    jQuery(ele).parent().addClass('ui action input').append('<button class="ui icon button zbs-linkify" type="button" data-url="mailto:' + possMatch[0] + '" title="Email "' + possMatch[0] + '""><i class="mail outline icon"></i></button>');

                    // rebind
                    zeroBSCRMJS_bindLinkify();

                    bound = true;

                }

                */


            }
        }

        // unlinkify if not
        if (!bound) {
            jQuery('.zbs-linkify',jQuery(ele).parent()).remove();
            jQuery(ele).parent().removeClass('ui action input fluid');
        }
 }

 function zeroBSCRMJS_bindLinkify(){

    jQuery('.zbs-linkify').off('click').click(function(){

        var u = jQuery(this).attr('data-url');
        if (typeof u != "undefined" && u != '') window.open(u, '_blank');

    });
 }
/* ============================================================================================================
    
   /  Edit contact specific JS (Taken from editcust.js old file for DB2 edit view)

============================================================================================================ */



/* ============================================================================================================
    
    Edit task specific JS

============================================================================================================ */


// set:
function zbscrmjs_events_setContact(obj){
    if (typeof obj.id != "undefined"){
        jQuery("#zbse_customer").val(obj.id);
        
        setTimeout(function(){

            // when select drop down changed, show/hide quick nav
            zeroBSCRMJS_events_showContactLinkIf(obj.id);

        },0);
    }
}
function zbscrmjs_events_setCompany(obj){
    if (typeof obj.id != "undefined"){
        // set vals
        jQuery("#zbse_company").val(obj.id);
        
        setTimeout(function(){

            // when select drop down changed, show/hide quick nav
            zeroBSCRMJS_events_showCompanyLinkIf(obj.id);

        },0);
    }
} 
// change: (catch emptying):
function zbscrmjs_events_changeContact(o){

    if (typeof o == "undefined" || o == ''){

        jQuery("#zbse_customer").val('');
        
        setTimeout(function(){

            // when select drop down changed, show/hide quick nav
            zeroBSCRMJS_events_showContactLinkIf('');

        },0);
        
    }
}
function zbscrmjs_events_changeCompany(o){

    if (typeof o == "undefined" || o == ''){

        jQuery("#zbse_company").val('');
        
        setTimeout(function(){

            // when select drop down changed, show/hide quick nav
            zeroBSCRMJS_events_showCompanyLinkIf('');

        },0);
        
    }
} 


// if a contact is selected (against a task) can 'quick nav' to contact
function zeroBSCRMJS_events_showContactLinkIf(contactID){

    // remove old
    jQuery('.zbs-task-for .zbs-view-contact').remove();
    jQuery('#zbs-event-learn-nav .zbs-quicknav-contact').remove();

    if (typeof contactID != "undefined" && contactID !== null && contactID !== ''){

        contactID = parseInt(contactID);
        if (contactID > 0){

                var html = '<div class="ui mini animated button zbs-view-contact">';
                        html += '<div class="visible content">' + zeroBSCRMJS_editViewLang('view','View') + '</div>';
                            html += '<div class="hidden content">';
                                html += '<i class="user icon"></i>';
                            html += '</div>';
                        html += '</div>';

                jQuery('.zbs-task-for').prepend(html);

                // ALSO show in header bar, if so
                var navButton = '<a target="_blank" style="margin-left:6px;" class="zbs-quicknav-contact ui icon button blue mini labeled" href="' + window.zbsObjectViewLinkPrefixCustomer + contactID + '"><i class="user icon"></i> ' + zeroBSCRMJS_editViewLang('contact','Contact') + '</a>';
                jQuery('#zbs-event-learn-nav').append(navButton);

                // bind
                zeroBSCRMJS_events_bindContactLinkIf();
        }
    }


}

// click for quicknav :)
function zeroBSCRMJS_events_bindContactLinkIf(){

    jQuery('.zbs-task-for .zbs-view-contact').off('click').click(function(){

        // get from hidden input
        var contactID = parseInt(jQuery("#zbse_customer").val());

        if (typeof contactID != "undefined" && contactID !== null && contactID !== ''){
            contactID = parseInt(contactID);
            if (contactID > 0){

                var url = window.zbsObjectViewLinkPrefixCustomer + contactID;

                window.open(url,'_parent');

            }
        }

    });
}


// if a company is selected (against a task) can 'quick nav' to company
function zeroBSCRMJS_events_showCompanyLinkIf(companyID){

    // remove old
    jQuery('.zbs-task-for-company .zbs-view-company').remove();
    jQuery('#zbs-event-learn-nav .zbs-quicknav-company').remove();

    if (typeof companyID != "undefined" && companyID !== null && companyID !== ''){

        companyID = parseInt(companyID);
        if (companyID > 0){

                var html = '<div class="ui mini animated button zbs-view-company">';
                        html += '<div class="visible content">' + zeroBSCRMJS_editViewLang('view','View') + '</div>';
                            html += '<div class="hidden content">';
                                html += '<i class="building icon"></i>';
                            html += '</div>';
                        html += '</div>';

                jQuery('.zbs-task-for-company').prepend(html);

                // ALSO show in header bar, if so
                var navButton = '<a target="_blank" style="margin-left:6px;" class="zbs-quicknav-contact ui icon button blue mini labeled" href="' + window.zbsObjectViewLinkPrefixCompany + companyID + '"><i class="user icon"></i> ' + zeroBSCRMJS_editViewLang('company','Company') + '</a>';
                jQuery('#zbs-event-learn-nav').append(navButton);

                // bind
                zeroBSCRMJS_events_bindCompanyLinkIf();
        }
    }


}

// click for quicknav :)
function zeroBSCRMJS_events_bindCompanyLinkIf(){

    jQuery('.zbs-task-for-company .zbs-view-company').off('click').click(function(){

        // get from hidden input
        var companyID = parseInt(jQuery("#zbse_company").val());

        if (typeof companyID != "undefined" && companyID !== null && companyID !== ''){
            companyID = parseInt(companyID);
            if (companyID > 0){

                var url = window.zbsObjectViewLinkPrefixCompany + companyID;

                window.open(url,'_parent');

            }
        }

    });
}


/* ============================================================================================================
    
    / Edit task specific JS

============================================================================================================ */