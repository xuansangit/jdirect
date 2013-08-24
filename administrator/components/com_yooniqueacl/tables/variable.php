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
class TableYooniqueaclVariable extends JTable {

	var $id						= null;
	var $site					= null;
	var $site_option			= null;
	var $variable				= null; 	
	var $force_integer			= 0; 
	var $checked_out			= 0; 	
	var $checked_out_time		= null;

	function TableYooniqueaclVariable( &$db ) {
		parent::__construct(  TABLE_YOONIQUEACL_VARIABLES, 'id', $db );	
	}
    
}
