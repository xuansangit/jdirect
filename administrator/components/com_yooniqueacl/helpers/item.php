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

class YooniqueaclHelperItem
{

	/**
	 * gets item either by yooniqueacl item id or by generated query string.
	 *
	 * @param mixed $query
	 * @param int $itemid
	 * @return array
	 */
	function getItem( $query=null, $itemid=null, $site='site' )
	{
		// clean if raw string and not object
		if (is_string($query)) {
			$returned = YooniqueaclHelper::_cleanQuery( $query, $site );
			if ($returned->error) { 
				return $returned;
			} else {
				$query = array();
				$query['option'] = $returned->option;
				$query['string'] = $returned->query;
			}
		} 

		$return = new stdClass();
		$return->error = true;	// Error
		$return->errorMsg = '';	// Error Message
		
		// get the database object
		$database = &JFactory::getDBO();

		if ($query['option']=='com_zoo') {
			parse_str ($query['string'], $parsed_url);
			if (isset ($parsed_url['item_id'])) {
				$item_id = (int) $parsed_url ['item_id'];
				$new_query = "SELECT `category_id` FROM `#__zoo_category_item` WHERE item_id =". (int) $item_id;
				$database->setQuery( $new_query );
				if ($database->query()) {
					if ($data = $database->loadColumn()) {
						$new_queries = array ();
						$new_queries [] = $database->escape($query['string']);
						foreach( $data as $column ) {
							$new_queries[] = preg_replace('/(.*)item_id=\d*(.*)/', '\1zoocategory='.$column.'\2', $query['string']);
						}
					$new_queries = implode( "', '", $new_queries );
					}
				}
			}
		}
		if ($query['option']=='com_kunena') {
			parse_str ($query['string'], $parsed_url);
			if (isset ($parsed_url['catid'])) {
				$catid = (int) $parsed_url ['catid'];
				$new_query = "SELECT `parent_id` FROM `#__kunena_categories` WHERE id =". (int) $catid;
				$database->setQuery( $new_query );
				if ($database->query()) {
					if ($data = $database->loadColumn()) {
						$new_queries = array ();
						$new_queries [] = $database->escape($query['string']);
						foreach( $data as $column ) {
							$new_queries[] = preg_replace('/(.*)catid=\d*(.*)/', '\1kunenacategory='.$column.'\2', $query['string']);
						}
					$new_queries = implode( "', '", $new_queries );
					}
				}
			}
		}
		
		if (!isset($new_queries)) {
			$string_query = $database->escape($query['string']);
		} else {
			$string_query = $new_queries;
		}
		$option_query = $database->escape($query['option']);
		$site_query = $database->escape($site);

		if (null === $itemid || intval($itemid) == 0) {
			// if no id passed the attempt database interogation based upon query
			$dbquery = "
				SELECT 
					*
				FROM 
					 " . TABLE_YOONIQUEACL_ITEMS . "
				WHERE
					`query` IN ( '" . $string_query . "')
				AND 
					`site_option` = '" . $option_query ."'
				AND
					`site` = '{$site_query}'
			";
		} else {
			// get item based upon item ID
			$dbquery = "
				SELECT 
					* 
				FROM 
					 " . TABLE_YOONIQUEACL_ITEMS . " 
			 	WHERE			
			 		`id` = '".(int)$itemid."' 
			";
		}
		
		$database->setQuery( $dbquery );
		
		if (!$database->query()) {
			$return->error = true; 
			$return->errorMsg = $database->getErrorMsg();
			return $return;
		} else {
			if ($data = $database->loadObject()) {
				return $data;
			} else {
				$return->error = true; 
				$return->errorMsg = JText::_('No Corresponding Item Found');
				return $return;					
			}
			
			// return $database->loadObject();
		}
		
	} // end of function getItem()
	
	/**
	 * 
	 * @param $userid
	 * @param $groupid
	 * @return unknown_type
	 */
	function isInGroup( $itemid, $groupid )
	{
		$success = false;
		
		// get the database object
		$database = &JFactory::getDBO();
		
		$query = "
			SELECT 
				`item_id` 
			FROM 
				 " . TABLE_YOONIQUEACL_G2I . " 
            WHERE 
            	`group_id` = '".(int)$groupid."'
            AND 
            	`item_id` = '".(int)$itemid."' 
		";
        $database->setQuery( $query );
        if ($database->loadResult()) {
        	$success = true;
        }
        
        return $success;
	}
	
	/**
	 * function checks for existing relationship between item and group and inserts if none found
	 *
	 * @param int $itemid
	 * @param int $groupid
	 * @return mixed
	 */
	function addToGroup( $itemid, $groupid )
	{
		$return = new stdClass();
		$return->error = false;	// Error
		$return->errorMsg = '';	// Error Message
		
		$database = &JFactory::getDBO();
		
		if ($isInGroup = YooniqueaclHelperItem::isInGroup( $itemid, $groupid ) ) {
			$return->error = false; 
			$return->errorMsg = JText::_( 'Item Already Belongs To Group' );
			return $return;
		} 
		
		$insertSql = "
			INSERT INTO 
				 " . TABLE_YOONIQUEACL_G2I . "
			SET
				`item_id` = '".(int)$itemid."',
				`group_id` = '".(int)$groupid."'
		";
		
		$database->setQuery( $insertSql );
		if (!$database->query()) {
			$return->error = true; 
			$return->errorMsg = JText::_( 'Unable to add item to group') . $database->getErrorMsg();
			return $return;
		}  else {
			return true;
		}
		
	} // end of function addToGroup()
	
	/**
	 * Enter description here...remove selected item from group
	 * 
	 * @param int $itemid
	 * @param int $groupid
	 * @return mixed
	 */
	function removeFromGroup( $itemid, $groupid )
	{
		$return = new stdClass();
		$return->error = false;	// Error
		$return->errorMsg = '';	// Error Message
		
		$database = &JFactory::getDBO();
		
		$sql = "
			DELETE FROM
				 " . TABLE_YOONIQUEACL_G2I . "
			WHERE
				`group_id` = '".(int)$groupid."'
			AND
				`item_id` = '".(int)$itemid."'
		";
		
		$database->setQuery( $sql );
		
		if (!$database->query()) {
			$return->error = true; 
			$return->errorMsg = JText::_( 'Unable to remove item from group') . $database->getErrorMsg();
			return $return;
		}  else {
			return true;
		}
		
	} // end of function removeFromGroup()
	
	/**
	 * Determines whether or not a query is excluded from Yooniqueacl's controls
	 * @param $item
	 * @return unknown_type
	 */
	function isExcluded( $item, $refresh='0' )
	{
		if (!is_object($item) || !array_key_exists( 'id', $item )) {
			return false;
		}

		// To cut down on the number of queries to the DB, we have two static variables
		// where: 
		// $site_items[$option] 	= an array of objects from __yooniqueacl_items
		// $admin_items[$option] 	= an array of objects from __yooniqueacl_items

		static $site_items;
		static $admin_items;
		
		$site	= strtolower($item->site);
		$option = strtolower($item->site_option);
				
		switch ($site) {
			case "administrator":
				if (!is_array($admin_items)) {
					$admin_items = array();
				}
				if (!array_key_exists( $option, $admin_items ) || !is_object($admin_items[$option]) || $refresh == '1') {
					$admin_items[$option] = YooniqueaclHelperItem::getExcludedItems( $option, 'administrator' );	
				}
				$data = $admin_items[$option];
			  break;
			case "site":
			default:
				if (!is_array($site_items)) {
					$site_items = array();
				}
				if (!array_key_exists( $option, $site_items ) || !is_object($site_items[$option]) || $refresh == '1') {
					$site_items[$option] = YooniqueaclHelperItem::getExcludedItems( $option, 'site' );	
				}
				$data = $site_items[$option];
			  break;
		}

		parse_str($item->query, $query_array);
		
		// determine if this query is part of an excluded set
		$exclude = false;
		
		for ($i=0; $i<count($data); $i++) {
			$d = $data[$i];
			// if this is an exact match, then exclude
			if (strval($d->query) == strval($item->query)) { 
				$exclude = true;
				break; 
			}
			
			if (!$exclude) {
				// parse query into array
				parse_str($d->query, $d_query_array);
				foreach ($d_query_array as $key=>$value) 
				{
					// if value doesn't match, then isn't excluded item, break this loop and move to next one
					if (!isset($query_array[$key]) || $query_array[$key] != $value) { 
						$exclude = false; 
						break; // break only the foreach $d_query_array loop 
					} else { 
						$exclude = true; 
					}
				}
			}
		}
		
		return $exclude;
	}
	
	/**
	 * 
	 * @param $option
	 * @param $site
	 * @return unknown_type
	 */
	function getExcludedItems( $option, $site='site' )
	{
		$database = JFactory::getDBO();

		$site_query = $database->escape($site);
		$option_query = $database->escape($option);

		$query = "
			SELECT
				*
			FROM 
				 " . TABLE_YOONIQUEACL_ITEMS . "
			WHERE 
				 " . TABLE_YOONIQUEACL_ITEMS . ".site = '{$site_query}'
			AND 
				 " . TABLE_YOONIQUEACL_ITEMS . ".site_option = '{$option_query}'
			AND 
				 " . TABLE_YOONIQUEACL_ITEMS . ".item_exclude = '1' 
		";
		$database->setQuery($query);
		$data = $database->loadObjectList();
		
		return $data;
	}
	
}
