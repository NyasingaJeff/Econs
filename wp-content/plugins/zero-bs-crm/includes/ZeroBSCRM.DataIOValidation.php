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
  Data Processing Functions
   ====================================================== */

    // Ensures storage and return as UTF8 without slashes
    function zeroBSCRM_textProcess($string=''){
      return htmlentities(stripslashes($string),ENT_QUOTES,'UTF-8');
    } 
    function zeroBSCRM_textExpose($string=''){
      return html_entity_decode($string,ENT_QUOTES,'UTF-8');
    } 

    // hitting this issue
    // https://core.trac.wordpress.org/ticket/43087
    // + 
    // https://core.trac.wordpress.org/ticket/32315#comment:43
    // (pasted emoji's in inputs (log text) would cause a silent wpdb error)
    // so for now, passing any emoji-ridden text through here:
    function zeroBSCRM_preDBStr($string=''){

        // encode emoji's - https://core.trac.wordpress.org/ticket/43087
        return wp_encode_emoji($string);

    }

    // strips all except <br />
    function zeroBSCRM_stripExceptLineBreaks($string=''){

        // simplistic switchout. can surely be done more elegantly
        $brs = array('<br />','<br>','<br/>','<BR />','<BR>','<BR/>');
        $str = str_replace($brs,'###BR###',$string);
        $str = wp_strip_all_tags($str,1);
        $str = str_replace('###BR###','<br />',$str);

        return $str;
    }

    // takes WP Editor content + applies WP conversion (adds <p> and deals with <b> as if was wp post)
    // KSES = Strips evil scripts
    // NOTE: html strings saved in this way should be output via
    function zeroBSCRM_io_sanitizeInt($input=''){

        if (isset($input)){

            // overkill/unnecessary check
            $i = sanitize_text_field( $input );

            // https://wordpress.stackexchange.com/questions/168315/sanitizing-integer-input-for-update-post-meta
            $i = intval( $i );
            if ( ! $i ) return -1;
            return $i;

        } 

        return -1;

    }

    // takes WP Editor content + applies WP conversion (adds <p> and deals with <b> as if was wp post)
    // KSES = Strips evil scripts
    // NOTE: html strings saved in this way should be output via
    function zeroBSCRM_io_WPEditor_WPEditorToDB($string=''){

        // leave this here for now... long term seperate from wp hook linkage      
        // sanitizes, which is good
        // https://core.trac.wordpress.org/browser/tags/4.9.8/src/wp-includes/kses.php#L0
        return wp_kses_post($string);

    }

    // This takes Database saved HTML and puts it back out in the wp editor
    function zeroBSCRM_io_WPEditor_DBToWPEditor($string=''){

        // See https://wordpress.stackexchange.com/questions/245201/how-to-save-html-and-text-in-the-database
        return wp_specialchars_decode( $string, $quote_style = ENT_QUOTES );

    }

    // This takes Database saved HTML (from wp_editor via zeroBSCRM_io_WPEditor_WPEditorToDB)
    // .. and returns raw HTML (with paragraphs) (e.g. for output in quote portal page)
    function zeroBSCRM_io_WPEditor_DBToHTML($string=''){

        // MS original
        //return html_entity_decode(nl2br(stripslashes($string)));

        // See https://wordpress.stackexchange.com/questions/245201/how-to-save-html-and-text-in-the-database
        return wpautop(html_entity_decode(stripslashes($string)));

    }

    // This takes Database saved HTML (from wp_editor via zeroBSCRM_io_WPEditor_WPEditorToDB)
    // .. and returns first X characters, no tags
    function zeroBSCRM_io_WPEditor_DBToHTMLExcerpt($string='',$len=200){

        $string = strip_tags(html_entity_decode(stripslashes($string)));
        return substr($string , 0, $len);
        
    }

/* MS quick fix, superceded by above

    //new one for WP editor input
    function zeroBSCRM_textProcessWP($string=''){
        //this is the only way I could get it to reliably store the content. Output without /' 
        //keep paragraphs and also not lose Command + b type formatting.
        return wp_kses_post($string);
    }

    function zeroBSCRM_textExposeWP($string=''){
        //decodes the HTML entities
        return html_entity_decode(nl2br(stripslashes($string)));
    }
*/
    
    // lol https://stackoverflow.com/questions/6063184/how-to-strip-all-characters-except-for-alphanumeric-and-underscore-and-dash
    function zeroBSCRM_strings_stripNonAlphaNumeric_dash($str=''){
        return preg_replace("/[^a-z0-9_\-\s]+/i", "", $str);
    }

    // https://stackoverflow.com/questions/33993461/php-remove-all-non-numeric-characters-from-a-string
    function zeroBSCRM_strings_stripNonNumeric($str=''){
        return preg_replace("/[^0-9]/", "", $str);
    }

/* ======================================================
  / Data Processing Functions
   ====================================================== */




/* ======================================================
  Data Validation Functions
   ====================================================== */

	#} Checks an email addr
	function zeroBSCRM_validateEmail($emailAddr){

		if (filter_var($emailAddr, FILTER_VALIDATE_EMAIL)) return true;

		return false;

	}


	#} roughly adopted from a Non-chosen answer from here:
	#} http://stackoverflow.com/questions/3090862/how-to-validate-phone-number-using-php
	function zeroBSCRM_validateUSTel($string=''){

		$isPhoneNum = false;

		//eliminate every char except 0-9
		$justNums = preg_replace("/[^0-9]/", '', $string);

		//eliminate leading 1 if its there
		if (strlen($justNums) == 11) $justNums = preg_replace("/^1/", '',$justNums);

		//if we have 10 digits left, it's probably valid.
		if (strlen($justNums) == 10) $isPhoneNum = true;

		return $isPhoneNum;
	}

	#} roughly adopted from a Non-chosen answer from here:
	#} http://stackoverflow.com/questions/8099177/validating-uk-phone-numbers-in-php
	function zeroBSCRM_validateUKMob($aNumber=''){
		$origNo = $aNumber;
		#DEBUG echo 'zeroBSCRM_validateUKMob:'.$aNumber.'|';
		#DEBUG $aNumber = intval($aNumber);
		#DEBUG echo 'int:'.$aNumber.'|';
		#DEBUG echo 'a:'.preg_match('/(^\d{12}$)|(^\d{10}$)/', $aNumber).'|';
		#DEBUG echo 'b:'.preg_match('/(^7)|(^447)/', $aNumber).'|';

		#} intval doesn't work yet!
		$aNumber = preg_replace("/[^0-9]/", '', $origNo);
		if (substr($aNumber,0,1) == '0') $aNumber = substr($aNumber,1);
		#DEBUG echo '<br >justNums:'.$aNumber.'|';
		#DEBUG echo 'a:'.preg_match('/(^\d{12}$)|(^\d{10}$)/', $aNumber).'|';
		#DEBUG echo 'b:'.preg_match('/(^7)|(^447)/', $aNumber).'|';

	    return preg_match('/(^\d{12}$)|(^\d{10}$)/', $aNumber) && preg_match('/(^7)|(^447)/', $aNumber);

	}

	#} Country based validation... doesn't work outside UK US
	function zeroBSCRM_ValidateMob($number){

		$nation = zeroBSCRM_getSetting('googcountrycode');

		switch ($nation){

			case 'GB':
				return zeroBSCRM_validateUKMob($number);
				break;
			case 'US':
				return zeroBSCRM_validateUSTel($number);
				break;
			default:
				return true;
				break;


		}

		return false;

	}

    function zeroBSCRM_dataIO_postedArrayOfInts($array=false){

        $ret = array(); if (is_array($array)) $ret = $array; 

        // sanitize
        $ret = array_map( 'sanitize_text_field', $ret );
        $ret = array_map( 'intval', $ret );

        return $ret;
    }

/* ======================================================
  / Data Validation Functions
   ====================================================== */


/* ======================================================
  Data Validation Functions: Segments
   ====================================================== */

// filters out segment conditions (From anything passed) which are not 'safe' 
// e.g. on our zeroBSCRM_segments_availableConditions() list
// ACCEPTS a POST arr
// $processCharacters dictates whether or not to pass strings through zeroBSCRM_textProcess
// ... only do so pre-save, not pre "preview" because this html encodes special chars.
   // note $processCharacters now legacy/defunct.
function zeroBSCRM_segments_filterConditions($conditions=array(),$processCharacters=true){

    if (is_array($conditions) && count($conditions) > 0){

        $approvedConditions = array();

        $availableConditions = zeroBSCRM_segments_availableConditions();
        $availableConditionOperators = zeroBSCRM_segments_availableConditionOperators();

        foreach ($conditions as $c){

            // has proper props
            if (isset($c['type']) && isset($c['operator']) && isset($c['value'])){

                // we approve
                if (isset($availableConditions[$c['type']])){

                    // has op 
                    if (in_array($c['operator'], $availableConditions[$c['type']]['operators'])){

                        // retrieve val
                        $val = $c['value'];
                        if ($processCharacters) $val = zeroBSCRM_textProcess($val); // only pre-saving
                        $val = sanitize_text_field( $val );

                        // conversions (e.g. date to uts)
                        $val = zeroBSCRM_segments_typeConversions($val,$c['type'],$c['operator'],'in');

                        // okay. (passing only expected + validated)
                        $addition = array(

                            'type' => $c['type'],
                            'operator' => $c['operator'],
                            'value' => $val

                        );

                        // ranges:

                            // int/floatval
                            if (isset($c['value2'])){

                                // retrieve val2
                                $val2 = $c['value2'];
                                if ($processCharacters) $val2 = zeroBSCRM_textProcess($val2); // only pre-saving
                                $val2 = sanitize_text_field( $val2 );

                                $addition['value2'] = $val2;

                            }

                            // daterange
                            if ($c['operator'] == 'daterange' && !empty($val)){

                                // hmmm what if peeps use ' - ' in their date formats? We're screwed if they do!
                                if (strpos($val,' - ') > -1){

                                    $dates = explode(' - ', $val);
                                    if (count($dates) == 2){

                                        $val = $dates[0];
                                        $addition['value'] = zeroBSCRM_locale_dateToUTS($dates[0]);
                                        $addition['value2'] = zeroBSCRM_locale_dateToUTS($dates[1]);

                                        // for those dates used in 'AFTER' this needs to effectively be midnight on the day (start of next day)
                                        if  (!empty($addition['value2'])) $addition['value2'] += (60*60*24);
                                    }

                                }

                            }

                        // if intrange force it
                        if ($c['type'] == 'intrange' && !isset($addition['value2'])) $addition['value2'] = 0;

                        $approvedConditions[] = $addition;

                    }

                }

            }


        }

        return $approvedConditions;

    }

    return array();


}

// uses zeroBSCRM_textExpose to make query-ready strings, 
// .. because conditions are saved in encoded format, e.g. é = &eacute;
function zeroBSCRM_segments_unencodeConditions($conditions=array()){

    if (is_array($conditions) && count($conditions) > 0){

        $ret = array();

        foreach ($conditions as $c){

            // for now it's just value we're concerned with
            $nC = $c;
            if (isset($nC['value'])) $nC['value'] = zeroBSCRM_textExpose($nC['value']);
            if (isset($nC['value2'])) $nC['value2'] = zeroBSCRM_textExpose($nC['value2']);

            // simple.
            $ret[] = $nC;

        }

        return $ret;

    }

    return array();
}
/* ======================================================
  / Data Validation Functions: Segments
   ====================================================== */