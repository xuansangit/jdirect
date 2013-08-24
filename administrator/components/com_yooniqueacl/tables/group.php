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
class TableYooniqueaclGroup extends JTable {

	var $id						= null;
	var $title					= null;
	var $description			= null;
	var $checked_out			= 0; 	
	var $checked_out_time		= null;

	function TableYooniqueaclGroup( &$db ) {
		parent::__construct(  TABLE_YOONIQUEACL_GROUPS, 'id', $db );	
	}
    
}
