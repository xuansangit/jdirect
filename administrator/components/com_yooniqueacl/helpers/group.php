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

class YooniqueaclHelperGroup 
{

	/**
	 * Returns a list of types
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function &getAll( $published='0' ) {
		$database = &JFactory::getDBO();

		$published_query = "";
		if (intval($published) > 0) { $published_query = " AND db.published = '1' "; }
			
		$query = "
			SELECT
				db.*
			FROM
				 " . TABLE_YOONIQUEACL_GROUPS . " AS db
			WHERE 1
				{$published_query}
			ORDER BY
				db.title ASC
		";

		$database->setQuery( $query );
		$data = $database->loadObjectList();
		
		return $data;
	}
	
	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getSelectListTypes( $exception='' ) {
		static $_selectListTypes;
		
		if (empty($_selectListTypes)) {
			$types[] = JHTML::_('select.option', '', '- '.JText::_( 'Select from List' ).' -' );
			$data = YooniqueaclHelperGroup::getAll();
			if ($data) {
			  foreach ($data as $d) {
			  	if ($d->id > 0) {
			  		if (intval($exception) != $d->id) {
				  		$types[] = JHTML::_('select.option', $d->id, JText::_( htmlspecialchars( $d->title ) ) );
			  		}
			  	}
			  }
			}
		
			$_selectListTypes = $types;
		
		}

		return $_selectListTypes;
	}

	/**
	 *
	 * @return
	 * @param $field Object
	 * @param $default Object[optional]
	 * @param $options Object[optional]
	 */
	function getSelectList( $field, $default='', $options='' )
	{
		return JHTML::_('select.genericlist', YooniqueaclHelperGroup::getSelectListTypes(), $field, $options, 'value','text', $default);
	}
	
}