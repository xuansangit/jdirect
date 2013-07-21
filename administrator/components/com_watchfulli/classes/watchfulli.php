<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
defined('WATCHFULLI_PATH') or die;

require_once WATCHFULLI_PATH . '/classes/encrypt.php';

/**
 * Watchful Helper Class
 * 
 */
abstract class Watchfulli
{
	/**
	 * gets a static copy of JVersion for use in determining what platform we're in
	 * 
	 * @return JVersion
	 */
	static public function joomla() {
		static $version;
		if (is_null($version)) {
			$version = new JVersion;
		}
		return $version;
	}
	
	/**
	 * checks to see if this server allows core updates
	 * 
	 * @return bool
	 */
	static public function canUpdate() {
		$isJoomla15   = '1.5' == Watchfulli::joomla()->RELEASE;
		$fopenAllowed = in_array(ini_get('allow_url_fopen'), array('On', '1', 1));
		return !$isJoomla15 && $fopenAllowed;
	}
	
	/**
	 * returns the unique token for this site
	 * 
	 * @return string
	 */
	static public function getToken() {
		return md5('watch' . JFactory::getApplication()->getCfg('secret') . 'fulli');
	}
	
	/**
	 * encrypts a string using AES
	 * 
	 * @param unknown_type $string
	 * @return Ambigous <string, boolean>
	 */
	static public function encrypt($string) {
		return WatchfulliUtilEncrypt::AESEncryptCtr($string, Watchfulli::getToken(), 256);
	}
	
	/**
	 * decrypts a string in AES
	 * 
	 * @param unknown_type $string
	 */
	static public function decrypt($string) {
		return strlen($string) ? WatchfulliUtilEncrypt::AESDecryptCtr($string, Watchfulli::getToken(), 256) : false;
	}
	
	/**
	 * encodes anything for transmission to the server
	 * 
	 * @param unknown_type $mixed
	 * 
	 * @return string
	 */
	static public function encodedJson($mixed) {
		return Watchfulli::encrypt(json_encode($mixed));
	}
	
	/**
	 * checks request to ensure it is from a valid source
	 * 
	 * @return bool
	 */
	static public function checkToken() {
		$token = Watchfulli::getToken();
		switch (Watchfulli::joomla()->RELEASE) {
			case '1.5':
				$value = JRequest::getVar(md5($token), '', 'base64');
				break;
			case '2.5':
			case '3.0':
			default:
				$value = JFactory::getApplication()->input->get(md5($token), '', 'base64');
				break;
		}
		return (empty($value) ? false : Watchfulli::decrypt($value) == $token);
	}
}
