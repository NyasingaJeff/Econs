<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V2.99.12+
 *
 * Copyright 2019+ ZeroBSCRM.com
 *
 * Date: 29/10/2019
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

    ##WLREMOVE

    global $zbs;    

    #} Assets we need specifically here
    
        // js
        wp_enqueue_script("jquery");
        wp_enqueue_script('zbswelcomeblock', plugins_url('/js/welcome-to-zbs/jquery.blockUI.min.js',ZBS_ROOTFILE), array( 'jquery' ), $zbs->version);
        wp_enqueue_script('zbswelcomebootstrap', plugins_url('/js/welcome-to-zbs/bootstrap.min.js',ZBS_ROOTFILE), array( 'jquery' ), $zbs->version);
        wp_enqueue_script('zbswelcomewizard', plugins_url('/js/welcome-to-zbs/wizard2.min.js',ZBS_ROOTFILE), array( 'jquery' ), $zbs->version);
        
        // css
        wp_enqueue_style('zbswelcomebootstrap',         plugins_url('/css/welcome-to-zbs/bootstrap.min.css',ZBS_ROOTFILE) );
        wp_enqueue_style('zbswelcomeloadstyles',        plugins_url('/css/welcome-to-zbs/loadstyles.min.css',ZBS_ROOTFILE) );
        wp_enqueue_style('zbswelcomeopensans',          plugins_url('/css/welcome-to-zbs/opensans.css',ZBS_ROOTFILE) );
        wp_enqueue_style('zbswelcomeadmin',             plugins_url('/css/welcome-to-zbs/admin.min.css',ZBS_ROOTFILE) );
        wp_enqueue_style('zbswelcomeexitform',          plugins_url('/css/welcome-to-zbs/zbs-exitform.min.css',ZBS_ROOTFILE) );
        wp_enqueue_style('zbswelcomeactivation',        plugins_url('/css/welcome-to-zbs/activation.min.css',ZBS_ROOTFILE) );
        wp_enqueue_style('zbswelcomewizard',            plugins_url('/css/welcome-to-zbs/wizard.min.css',ZBS_ROOTFILE) );

        // dequeue anything?
        wp_dequeue_style('admin-bar-css');

    #} Image URLS
    $assetsURLI = ZEROBSCRM_URL.'i/';
   
    global $zeroBSCRM_killDenied; $zeroBSCRM_killDenied = true; 
    global $zbs; $settings = $zbs->settings->getAll();

?><!DOCTYPE html>
<html lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php _e("Welcome to","zero-bs-crm");?> Jetpack CRM</title>
    <style type="text/css">img.wp-smiley,img.emoji{display:inline !important;border:none !important;box-shadow:none !important;height:1em !important;width:1em !important;margin:0 .07em !important;vertical-align:-0.1em !important;background:none !important;padding:0 !important}#zbscrm-logo img{max-width:20% !important}#feedbackPage{display:none}.zbscrm-setup .zbscrm-setup-actions .button-primary{background-color:#408bc9 !important;border-color:#408bc9 !important;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.25),0 1px 0 #408bc9 !important;box-shadow:inset 0 1px 0 rgba(255,255,255,.25),0 1px 0 #408bc9 !important;text-shadow:0 -1px 1px #408bc9,1px 0 1px #408bc9,0 1px 1px #408bc9,-1px 0 1px #408bc9 !important;float:right;margin:0;opacity:1}</style>
    <?php 

        wp_print_styles();
        wp_print_scripts(); //wp_scripts

     ?>
	<style type="text/css" media="print">#wpadminbar { display:none !important; }</style>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
</head>
<body class="zbscrm-setup wp-core-ui">
			<h1 id="zbscrm-logo"><a href="https://jetpackcrm.com" target="_blank"><img src="<?php echo $assetsURLI.'jetpack-crm.png'; ?>" alt="Jetpack CRM"></a></h1>
		<div class="zbscrm-setup-content" id="firstPage">
<div class="container">
<div class="stepwizard">
    <div class="stepwizard-row setup-panel">
        <div class="stepwizard-step">
            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
            <p><?php _e("Essentials","zero-bs-crm");?></p>
        </div>
        <div class="stepwizard-step">
            <a href="#step-2" type="button" class="btn btn-default btn-circle">2</a>
            <p><?php _e("Your Customers","zero-bs-crm");?></p>
        </div>
        <div class="stepwizard-step">
            <a href="#step-3" type="button" class="btn btn-default btn-circle">3</a>
            <p><?php _e("Which Extensions?","zero-bs-crm");?></p>
        </div>
        <div class="stepwizard-step">
            <a href="#step-4" type="button" class="btn btn-default btn-circle">4</a>
            <p><?php _e("Finish","zero-bs-crm");?></p>
        </div>
    </div>
</div>
    <div class="row setup-content" id="step-1">
        <div class="col-xs-12">
            <div class="col-md-12">
                <h3><?php _e('Essential Details',"zero-bs-crm"); ?></h3>

                <div class="wizopt">

                    <label><?php _e('Company Name / CRM Name:',"zero-bs-crm");?></label>
                    <p style="margin-bottom:0"><?php _e("This name will be shown at the top left of your CRM (as shown below). E.g. 'Widget Co CRM'","zero-bs-crm");?></p>
                    <div style="width:90%;">
                        <div style="width:48%;float:left">                        
                            <input class='form-control' type="text" name="zbs_crm_name" id='zbs_crm_name' value="" placeholder="<?php _e("Name of your CRM (e.g Jetpack CRM)","zero-bs-crm");?>" style="width:90%" onchange = "zbs_crm_name_change();"/>
                        </div>
                        <div style="width:48%;float:right;overflow:hidden;border: 1px solid #ccc;" class='pos-rel'>
                            <img src="<?php echo ZEROBSCRM_URL; ?>i/welcome-to-zbs/crm-name.png" alt="Jetpack CRM" id="crm-name-img" style="border:0;margin-bottom: 0;" />
                            <div id='crm-name'>Jetpack CRM</div>
                        </div>                        
                    </div>

                    <div class='clear'></div>
                
                </div>

                <div class="wizopt">
                    
                    <label><?php _e('What Currency should Jetpack CRM use?',"zero-bs-crm");?></label>
                    <br/>
                    <select class='form-control' id='zbs_crm_curr' name='zbs_crm_curr'>
                        <?php                       

                                                        $currSetting = ''; if (isset($settings['currency']) && isset($settings['currency']['strval']) && $settings['currency']['strval']) $currSetting = $settings['currency']['strval'];

                            if (empty($currSetting)){
                             
                                                                $locale = get_locale(); 

                                if ($locale == 'en_US') $currSetting = 'USD';
                                if ($locale == 'en_GB') $currSetting = 'GBP';
                                
                            }

                                                        global $whwpCurrencyList;
                            if(!isset($whwpCurrencyList)) require_once(ZEROBSCRM_PATH . 'includes/wh.currency.lib.php');

                            

                        ?>
                        <option value="" disabled="disabled" selected="selected"><?php _e("Select","zero-bs-crm");?>...</option>
                        <?php foreach ($whwpCurrencyList as $currencyObj){ ?>
                            ?><option value="<?php echo $currencyObj[1]; ?>"<?php if ($currSetting == $currencyObj[1]) echo ' selected="selected"'; ?>><?php echo $currencyObj[0].' ('.$currencyObj[1].')'; ?></option>
                        <?php } ?>
                    </select>

                </div>

                <div class="wizopt">

                    <label><?php _e('What sort of business do you do?',"zero-bs-crm");?></label>
                    <select class="form-control" id="zbs_crm_type" name="zbs_crm_type" onchange = "zbs_biz_select();">
                      <option value="" disabled="disabled" selected="selected"><?php _e("Select a type...","zero-bs-crm");?></option>
                      <option value="Freelance"><?php _e("Freelance","zero-bs-crm");?></option>
                      <option value="FreelanceDev"><?php _e("Freelance (Developer)","zero-bs-crm");?></option>
                      <option value="FreelanceDes"><?php _e("Freelance (Design)","zero-bs-crm");?></option>
                      <option value="SmallBLocal"><?php _e("Small Business: Local Service (e.g. Hairdresser)","zero-bs-crm");?></option>
                      <option value="SmallBWeb"><?php _e("Small Business: Web Business","zero-bs-crm");?></option>
                      <option value="SmallBOther"><?php _e("Small Business (Other)","zero-bs-crm");?></option>
                      <option value="ecommerceWoo"><?php _e("eCommerce (WooCommerce)","zero-bs-crm");?></option>
                      <option value="ecommerceShopify"><?php _e("eCommerce (Shopify)","zero-bs-crm");?></option>
                      <option value="ecommerceOther"><?php _e("eCommerce (Other)","zero-bs-crm");?></option>
                      <option value="Other"><?php _e("Other","zero-bs-crm");?></option>
                    </select>
                    <label class='hide' id='zbs_other_label'><?php _e("Please let us know more details about how you intend to your Jetpack CRM so we can refine the product","zero-bs-crm"); ?></label>
                    <textarea class='form-control' name='zbs_other_details' id='zbs_other_details'></textarea>

                </div>


                <div class="wizopt">

                    <label><?php _e('Menu Style',"zero-bs-crm"); ?></label>
                    <p>Jetpack CRM <?php _e("can override the WordPress menu, or sit nicely amongst the existing options. Which of the following best suits your use?","zero-bs-crm");?></p>

                    <div class="zbs-menu-opts">

                        <div class="zbs-menu-opt" data-select="zbs-menu-opt-choice-override">

                            <div class="zbs-menu-opt-porthole override">
                                <img src="<?php echo ZEROBSCRM_URL; ?>i/welcome-to-zbs/zbs-menu-override.png" alt="Override Menu" />
                            </div>
                            <div class="zbs-menu-opt-desc">Jetpack CRM <?php _e("Override","zero-bs-crm");?></div>
                            <input type="radio" name="zbs-menu-opt-choice" id="zbs-menu-opt-choice-override" value="override"/>
                            <!-- zbs override mode + menu layout ZBS only -->

                        </div>

                        <div class="zbs-menu-opt" data-select="zbs-menu-opt-choice-slimline">

                            <div class="zbs-menu-opt-porthole slimline">
                                <img src="<?php echo ZEROBSCRM_URL; ?>i/welcome-to-zbs/zbs-menu-slimline.png" alt="Slim Menu" />
                            </div>
                            <div class="zbs-menu-opt-desc">Jetpack CRM <?php _e("Slimline","zero-bs-crm");?></div>
                            <input type="radio" name="zbs-menu-opt-choice" id="zbs-menu-opt-choice-slimline" value="slimline" checked="checked" />
                            <!-- zbs override mode off + menu layout ZBS Slimline-->

                        </div>

                        <div class="zbs-menu-opt" data-select="zbs-menu-opt-choice-full">

                            <div class="zbs-menu-opt-porthole full">
                                <img src="<?php echo ZEROBSCRM_URL; ?>i/welcome-to-zbs/zbs-menu-full.png" alt="Full WP Menu" />
                            </div>
                            <div class="zbs-menu-opt-desc">Jetpack CRM &amp; WordPress</div>
                            <input type="radio" name="zbs-menu-opt-choice" id="zbs-menu-opt-choice-full" value="full" />
                            <!-- zbs override mode off + menu layout "Full"-->

                        </div>

                        <div class='clear'></div>

                    </div> 

                    <div class="zbs-extrainfo">
                        <?php _e("Override mode clears up the admin menu and hides 'posts', 'pages', etc.","zero-bs-crm");?><br />
                        <em><strong><?php _e("This is super useful if you intend to use the CRM on it's own domain (e.g. crm.yourdomain.com)","zero-bs-crm");?>.</strong></em><br />
                        <?php _e("We recommend that you try this mode - you can change it in the Jetpack CRM settings at any time","zero-bs-crm");?>.
                    </div>

                </div>


                <!-- for now keep lean, ignore b2b -->
                <div class="wizopt" style="display:none">

                    <label><?php _e('B2B Mode'); ?></label>
                    <p><?php _e("Jetpack CRM an run in 'Business to business' (B2B) Mode, which allows you to manage 'Contacts' under 'Companies', instead of just 'Contacts'. For most small businesses and freelancers, this isn't necessary","zero-bs-crm");?>.</p>
                    
                    <div>  
                        
                        <div class="switchBox">
                            <div class="switchBoxLabel">B2B <?php _e("Mode","zero-bs-crm");?></div>
                            <div class="switchCheckbox">
                                <input type="checkbox" id="zbs_b2b" value="zbs_b2b" />
                                <label for="zbs_b2b"></label>
                            </div>
                        </div>                        

                    </div>

                </div>



                <div class="wizopt">

                    <label for="zbs_ess"><?php _e('Share essentials',"zero-bs-crm"); ?></label>

                    <div style="width:100%;">
                        <div style="width:25%;float:left;">
                            <div class='yesplsess'><p><?php _e('Share essentials',"zero-bs-crm"); ?> <input type="checkbox" id="zbs_ess" value="zbs_ess" checked='checked'/></p></div>
                        </div>
                        <div style="width:75%;float:right;">
                            <div class="zbs-extrainfo">
                                <?php _e("Sharing these essentials helps to build a better CRM that fits your needs. No confidential information is ever shared with us. Highly recommended","zero-bs-crm");?>.
                            </div>
                        </div>
                        <div class='clear'></div>
                    </div>

                </div>

                <hr />

                <div class='clear'></div>
                <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" ><?php _e("Next","zero-bs-crm");?></button>
            </div>
        </div>
    </div>


    <div class="row setup-content" id="step-2" style="display:none">
        <div class="col-xs-12">
            <div class="col-md-12">

                <!-- ingest -->
                <h3 id="zbs-lead-header"><span class="zbs-nob2b-show"><?php _e('Getting Customers into your CRM',"zero-bs-crm"); ?></span><span class="zbs-b2b-show"><?php _e('Getting Contacts into your CRM',"zero-bs-crm"); ?></span></h3>

                <p class="lead zbs-freelancer-lead">
                    <?php _e("<strong>Freelancing is hard enough without having to manage a customer list as well!</strong><br />We feel your pain.<br /><br />To make life easier we've built extensions which automatically pulls through all of your customers who've ever paid you into your CRM. If you're using PayPal or Stripe for payments, this is a HUGE timesaver.","zero-bs-crm"); ?>
                </p>
                <p class="lead zbs-smallbiz-lead">
                    <?php _e("<strong>Running a small business is hard work...</strong><br />It's busy. Time passes and you forget to add a customer detail... <em>Then when you need it, it's not there!</em>. We've run businesses for years,<strong>we feel your pain</strong>.<br /><br />To make life easier we've built a few extensions which take a lot of the pain out of this. PayPal Sync &amp; Woo Sync automatically pull through all customers into your CRM. If you're using PayPal for payments, or WooCommerce for sales, this is a HUGE timesaver.","zero-bs-crm"); ?>
                </p>
                <p class="lead zbs-ecomm-lead">
                    <?php _e("<strong>Running an ecommerce business is hard work...</strong><br />We feel your pain.<br /><br />To make life easier we've built extensions which take a lot of the pain out of this. PayPal sync &amp; WooSync automatically pull through all customers and transactions, and then it keeps their details up to date. If you're using PayPal for payments, or WooCommerce for sales, this is a HUGE timesaver.","zero-bs-crm");?>
                </p>

                <div class="zbs-sync-ext">

                    <div class="zbs-show-starterbundle zbs-sync-wrap">



                        <div class='zbs-eb-wizard'>
                            <div class='zbs-eb-callout'>
                                <h3>Every extension, every update</h3>
                                <p>
                                    The Entrepreneur Bundle will give you every current and future extension, plus ongoing updates and improvements. 
                                </p>    
                                <p>
                                    <em>Simple. Straight forward. Good value.</em>
                                </p>
                                <a href="https://jetpackcrm.com/extension-bundles/" target="_blank" class="btn btn-jetpack">Connect your CRM to other services</a>
                            </div>
                            <div id="zbs-starterbundle-img" class="zbs-sync-img">
                                <a href="https://jetpackcrm.com/extension-bundles/" target="_blank"><img src="<?php echo ZEROBSCRM_URL; ?>i/welcome-to-zbs/entrepreneur-bundle.png" alt="Entrepreneur'sBundle" /></a>
                            </div>

                        </div>
                    </div>
                  


                    
                    <div class='clear'></div>

                </div>

                <hr />


                <div class='clear'></div>
                <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" ><?php _e("Next","zero-bs-crm");?></button>
            </div>
        </div>
    </div>

    <div class="row setup-content" id="step-3" style="display:none">
        <div class="col-xs-12">
            <div class="col-md-12">

                <h3><?php _e("Optional Features","zero-bs-crm");?></h3>

                <div class="wizopt">

                    <div class="switchbox-right">  
                        
                        <div class="switchBox">
                            <div class="switchCheckbox">
                                <input type="checkbox" id="zbs_quotes" value="zbs_quotes" checked="checked" />
                                <label for="zbs_quotes"></label>
                            </div>
                        </div>                        

                    </div>

                    <label><?php _e('Enable Quotes',"zero-bs-crm"); ?></label>
                    <p><?php _e("Quotes (or proposals) are a super powerful part of","zero-bs-crm");?> Jetpack CRM. <?php _e("We recommend you use this feature, but if you don't want quotes you can turn it off here.","zero-bs-crm");?></p>
                    

                </div>

                <hr />

                <div class="wizopt">

                    <div class="switchbox-right">  
                        
                        <div class="switchBox">
                            <div class="switchCheckbox">
                                <input type="checkbox" id="zbs_invoicing" value="zbs_invoicing" checked="checked" />
                                <label for="zbs_invoicing"></label>
                            </div>
                        </div>                        

                    </div>

                    <label><?php _e('Enable Invoices',"zero-bs-crm"); ?></label>
                    <p><?php _e("You can run Jetpack CRM with or without Invoicing. We recommend you use this though, as it's very useful (you can invoice online!)","zero-bs-crm");?></p>
                    <p class="zbs-extra"><?php _e("Accept online payments with","zero-bs-crm");?> <a href="https://jetpackcrm.com/product/invoicing-pro/?utm_content=zbsplugin_welcomewiz" target="_blank">Invoicing Pro</a> <?php _e("(Let your clients pay with Stripe or PayPal)","zero-bs-crm");?></p>
                    
                </div>

                <hr />

                <div class="wizopt">

                    <div class="switchbox-right">  
                        
                        <div class="switchBox">
                            <div class="switchCheckbox">
                                <input type="checkbox" id="zbs_forms" value="zbs_forms" />
                                <label for="zbs_forms"></label>
                            </div>
                        </div>                        

                    </div>

                    <label><?php _e('Enable Forms',"zero-bs-crm"); ?></label>
                    <p><?php _e("Forms allow you to embed customer forms into the front-end of your WordPress site. This is useful for lead generation. Recommendation: enable this if you want to collect leads via forms.","zero-bs-crm");?></p>
                    <p class="zbs-extra"><?php _e("(We also have a","zero-bs-crm");?> <a href="https://jetpackcrm.com/product/gravity-forms/?utm_content=zbsplugin_welcomewiz" target="_blank">Gravity Forms <?php _e("Extension","zero-bs-crm");?></a> <?php _e("and a","zero-bs-crm");?> <a href="https://jetpackcrm.com/product/contact-form-7?utm_content=zbsplugin_welcomewiz" target="_blank">Contact Form 7</a> <?php _e("integration","zero-bs-crm");?>)</p>
                    

                </div>

                <hr />

                <div class="wizopt">

                    <div class="zbs-extrainfo">
                        <strong><?php _e("Hint","zero-bs-crm");?>:</strong> <?php _e("You can enable/disable any CRM features from the 'Extensions' page under 'Settings' on your WordPress admin menu","zero-bs-crm");?>
                    </div>

                </div>


                <hr />

                <div class='clear'></div>

                <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" ><?php _e("Next","zero-bs-crm");?></button>
            </div>
        </div>
    </div>



    <div class="row setup-content" id="step-4" style="display:none">
        <div class="col-xs-12">
            <div class="col-md-12 laststage">
                <?php
                                global $current_user;
                wp_get_current_user();
                $fname  = $current_user->user_firstname;
                $lname  = $current_user->user_lastname;
                $em     = $current_user->user_email;
                ?>
                <h3><?php _e("Leverage your new CRM! (BONUSES)", "zero-bs-crm"); ?></h3>
                <p style="font-size:16px;color:#e06d17"><strong><?php _e("Join the","zero-bs-crm");?> Jetpack CRM <?php _e("community today","zero-bs-crm");?>:</strong><br />(<?php _e("Bonuses, Critical update notifications","zero-bs-crm");?>.)</p>

                <p style="text-align:center">
                    <input type="hidden" id="zbs_crm_subblogname" name="zbs_crm_subblogname" value="<?php bloginfo('name'); ?>" />
                    <input class='form-control' style="width:40%;margin-right:5%;display:inline-block;font-size:15px;line-height:16px;" type="text" name="zbs_crm_first_name" id="zbs_crm_first_name" value="<?php echo $fname; ?>" placeholder="<?php _e("Type your first name","zero-bs-crm");?>..." />                    
                    <input class='form-control' style="width:40%;margin-right:5%;display:inline-block;font-size:15px;line-height:16px;"  type="text" name="zbs_crm_email" id="zbs_crm_email" value="<?php echo $em; ?>" placeholder="<?php _e("Enter your best email","zero-bs-crm");?>..." />

                    <input class='form-control' style="display:none !important"  type="text" name="zbs_crm_last_name" id="zbs_crm_last_name" value="<?php echo $lname; ?>" placeholder="<?php _e("And your last name","zero-bs-crm");?>..." />
                </p>

                <div class='clear'></div>
                <div class='yespls'><p style="text-align: center;margin-top: 6px;"><?php _e('Get updates',"zero-bs-crm"); ?> <input type="checkbox" id="zbs_sub" value="zbs_sub" checked='checked'/></p></div>



                <hr />

                <div class='clear'></div>

                <button class="btn btn-primary btn-lg pull-right zbs-gogogo" type="button" ><?php _e("Next","zero-bs-crm");?></button>
            </div>

            <div class="col-md-12 finishingupblock" style="display:none">
                <h3><?php _e("Configuring your","zero-bs-crm");?> Jetpack CRM</h3>
                <div style='text-align:center'>
                <img src="<?php echo $zbs->urls['extimgrepo']; ?>go.gif" alt="Jetpack CRM" style="margin:40px">
                <p><?php _e("Just sorting out your new Jetpack CRM setup using the information you have provided, this shouldn't take a moment","zero-bs-crm");?>...</p>
                
                </div>
            </div>

            <div class="col-md-12 finishblock" style="display:none">
                <h3> <?php _e("Finished","zero-bs-crm");?></h3>
                <div style='text-align:center'>
                <p><?php _e("That’s it, you’re good to go. Get cracking with using your new CRM and rock on!","zero-bs-crm");?></p>
                <img src="<?php echo $zbs->urls['extimgrepo']; ?>bear.gif" alt="Jetpack CRM">
                </div>
                <?php
                    //$loc = 'admin.php?page=zerobscrm-plugin';
                    $loc = 'admin.php?page='.$zbs->slugs['home'];
                    echo '<input type="hidden" name="zbswf-ajax-nonce" id="zbswf-ajax-nonce" value="' . wp_create_nonce( 'zbswf-ajax-nonce' ) . '" />';
                    echo '<input type="hidden" name="phf-finish" id="phf-finish" value="' . admin_url($loc) . '" />';  
                ?>
                <a class="btn btn-success btn-lg pull-right zbs-finito" href="<?php echo admin_url($loc); ?>"><?php _e("Finish and go to your CRM","zero-bs-crm");?>!</a>
            </div>
        </div>
    </div>
</div>
</div>			
</body></html><?php ##/WLREMOVE ?>