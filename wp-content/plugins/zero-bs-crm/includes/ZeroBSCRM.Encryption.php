<?php
/*!
 * Jetpack CRM
 * http://zerobscrm.com
 * V2.2+
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 12/09/2017
 */

define( 'ZBS_ENCRYPTION_METHOD', "AES-256-CBC" );

 // NOTE - NOT GOOD for hard encryption, for now used basically
 // https://gist.github.com/joashp/a1ae9cb30fa533f4ad94
function zeroBSCRM_encryption_unsafe_process($action, $string, $key, $iv) {
    $output = false;
    $encrypt_method = ZBS_ENCRYPTION_METHOD;

    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt( $string, $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function zeroBSCRM_get_iv() {
	static $iv = null;
	if ( null === $iv ) {
		$iv = pack( 'C*', ...array_slice( unpack( 'C*', AUTH_KEY ), 0,  openssl_cipher_iv_length( ZBS_ENCRYPTION_METHOD ) ) );
	}
	return $iv;
}

function zeroBSCRM_encrypt( $string, $key ) {
	return zeroBSCRM_encryption_unsafe_process( 'encrypt', $string, $key, zeroBSCRM_get_iv() );
}

function zeroBSCRM_decrypt( $string, $key ) {
	return zeroBSCRM_encryption_unsafe_process( 'decrypt', $string, $key, zeroBSCRM_get_iv() );
}
