<?php
/*
	Author: Helge Sverre Hessevik Liseth
	Website: www.helgesverre.com

	Email: helge.sverre@gmail.com
	Twitter: @HelgeSverre

	License: Attribution-ShareAlike 4.0 International

*/


/**
 * Class responsible for checking if a domain is registered
 *
 * @author  Helge Sverre <email@helgesverre.com>
 *
 * @param boolean $error_reporting Set if the function should display errors or suppress them, default is false
 *
 * @return boolean true means the domain is NOT registered
 */
class DomainAvailability
{

	private $error_reporting;


	public function __construct( $debug = false ) {
		if ( $debug ) {
			error_reporting( E_ALL );
			$error_reporting = true;
		}
		else {
			error_reporting( 0 );
			$error_reporting = false;
		}

	}


	/**
	 * This function checks if the supplied domain name is registered
	 *
	 * @author  Helge Sverre <email@helgesverre.com>
	 *
	 * @param string  $domain          The domain that will be checked for registration.
	 * @param boolean $error_reporting Set if the function should display errors or suppress them, default is TRUE
	 *
	 * @return boolean true means the domain is NOT registered
	 */
	public function is_available( $domain ) {

		// make the domain lowercase
		$domain = strtolower( $domain );

		// Set the timeout (in seconds) for the socket open function.
		$timeout = 10;

		$whois_arr = zan_dc_whois_servers();


		// gethostbyname returns the same string if it cant find the domain,
		// we do a further check to see if it is a false positive
		// if (gethostbyname($domain) == $domain) {
		// get the TLD of the domain
		$tld = $this->get_tld( $domain );

		// If an entry for the TLD exists in the whois array
		if ( isset( $whois_arr[$tld][0] ) ) {
			// set the hostname for the whois server
			$whois_server = $whois_arr[$tld][0];

			// set the "domain not found" string
			$bad_string = isset( $whois_arr[$tld][1] ) ? $whois_arr[$tld][1] : "Domain not found";
		}
		else {

			//return 'not found';
			return json_encode( array( 'status' => 2 ) );

		}

		$status = $this->checkDomainNameAvailabilty( $domain, $whois_server, $bad_string, null );

		return $status;

	}


	/**
	 * Extracts the TLD from a domain, supports URLS with "www." at the beginning.
	 *
	 * @author  Helge Sverre <email@helgesverre.com>
	 *
	 * @param string $domain The domain that will get it's TLD extracted
	 *
	 * @return string The TLD for $domain
	 */

	public function get_tld( $domain ) {
		$split = explode( '.', $domain );

		if ( count( $split ) === 0 ) {
			//throw new Exception('Invalid domain extension');
			return false;
		}
		//return end($split);
		$tld = strtolower( array_pop( $split ) );

		return $tld;

	}

	public function checkDomainNameAvailabilty( $domain_name, $whois_server, $find_text ) {
		// Open a socket connection to the whois server
		list( $dom, $ext ) = explode( '.', $domain_name, 2 );
		if ( $ext == 'es' ) {

			$check = file_get_contents( "http://whois.virtualname.es/whois.php?domain=$domain_name" );

			if ( preg_match( '/LIBRE/', $check ) ) {

				return json_encode( array( 'status' => 1 ) );

			}
			else {

				return json_encode( array( 'status' => 0 ) );

			}

		}

		if ( $ext == 'be' ) {

			return file_get_contents( "http://api.asdqwe.net/api/whois.php?d=$domain_name" );

		}


		$con = fsockopen( $whois_server, 43 );
		if ( !$con ) {
			//return false;
			return file_get_contents( "http://api.asdqwe.net/api/whois.php?d=$domain_name" );
		}

		// Send the requested domain name
		fputs( $con, $domain_name . "\r\n" );

		// Read and store the server response
		$response = " :";
		while ( !feof( $con ) )
			$response .= fgets( $con, 128 );

		// Close the connection
		fclose( $con );

		// Check the Whois server response
		if ( strpos( $response, $find_text ) )
			//return 'true';
			return json_encode( array( 'status' => 1 ) );
		else
			//return 'false';
			return json_encode( array( 'status' => 0 ) );
	}
}

