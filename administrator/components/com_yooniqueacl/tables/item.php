<?php
/**
 * @package	Juga
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2010 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/**
 * 
 */
class TableYooniqueaclItem extends JTable {

	var $id						= null;
	var $site					= null;
	var $title					= null;
	var $query					= null;
	var $site_option			= null; 	
	var $site_section			= null; 
	var $site_view				= null; 
	var $site_task				= null; 
	var $type					= null; 
	var $type_id				= null;
	var $error_url_published	= null; 
	var $error_url				= null; 
	var $option_exclude			= null; 
	var $item_exclude			= null; 
	var $created_datetime		= null; 	
	var $checked_out			= 0; 	
	var $checked_out_time		= null;
	var $content_category		= null;

	function TableYooniqueaclItem( &$db ) {
		parent::__construct(  TABLE_YOONIQUEACL_ITEMS, 'id', $db );	
	}
	/** 
	 * overloaded check function 
	 **/
	function check() {
		// filter malicious code
		$ignoreList = array( 'params' );
		$this->filter( $ignoreList );

		// specific filters
		$iFilter = new JFilterInput();
		return true;
	}	
    
}
