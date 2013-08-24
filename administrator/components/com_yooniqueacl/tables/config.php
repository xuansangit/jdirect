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

// ************************************************************************
class TableYooniqueaclConfig extends JTable {

	var $id						= null;
	var $title					= null;
	var $description			= null;
	var $ordering				= 99; 	
	var $published				= 0; 	
	var $checked_out			= null; 	
	var $checked_out_time		= null; 	
	var $value					= null;

	function TableYooniqueaclConfig( &$db ) {
		parent::__construct(  TABLE_YOONIQUEACL_CONFIG, 'title', $db );	
	}
	
	function store( $updateNulls = true) {
		$k = 'id';
 
        if( $this->$k)
        {
            $ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
        }
        else
        {
            $ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
        }
        if( !$ret )
        {
            $this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
            return false;
        }
        else
        {
            return true;
        }
	}
    
}
// ************************************************************************