<?php
/**
 * @version   1.4.17
 * @date      Fri Mar 29 15:34:01 2013 -0700
 * @package   yoonique ACL
 * @author    yoonique[.]net
 * @copyright Copyright (C) 2012 yoonique[.]net and all rights reserved.
 *
 * based on
 *
 * @package	Juga
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2010 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/**
 * @package	
 */
class YooniqueaclHelper {
	
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		
		JSubMenuHelper::addEntry(
						JText::_('Dashboard'), "index.php?option=com_yooniqueacl&controller=yooniqueacl", $vName == 'yooniqueacl'
		);
		JSubMenuHelper::addEntry(
						JText::_('Groups'), "index.php?option=com_yooniqueacl&controller=groups&task=list", $vName == 'groups'
		);
		JSubMenuHelper::addEntry(
						JText::_('Variables'), "index.php?option=com_yooniqueacl&controller=variables&task=list", $vName == 'variables'
		);
		JSubMenuHelper::addEntry(
						JText::_('Items'), "index.php?option=com_yooniqueacl&controller=siteitems&task=list", $vName == 'siteitems'
		);
		JSubMenuHelper::addEntry(
						JText::_('Users'), "index.php?option=com_yooniqueacl&controller=users&task=list", $vName == 'users'
		);
		JSubMenuHelper::addEntry(
						JText::_('Configuration'), "index.php?option=com_yooniqueacl&controller=config", $vName == 'config'
		);
		JSubMenuHelper::addEntry(
						JText::_('Statistics'), "index.php?option=com_yooniqueacl&controller=statistics", $vName == 'statistics'
		);
		
	}

	/**
	 * 
	 * @param $site
	 * @return unknown_type
	 */
	function getQueryFromRequest( $site='site' )
	{
		$parsed_url = array();
		
		// option has been found
		$site_option = JRequest::getCmd( 'option' );
		$parsed_url['option'] = $site_option; 
		
		// pass parsed_url variables
		$validVariables 	= YooniqueaclHelper::validVariables( $site_option, $site );
		$variables         	= $validVariables->variables;
		$variables_int    	= $validVariables->variables_int;
		$variables_add    	= $validVariables->variables_add;
		
		if ($variables) 
		{ 
			foreach ($variables as $key) 
			{
				if (!isset($parsed_url[$key])) 
				{ 
					if ($value = JRequest::getVar( $key, JRequest::getVar( $key, "", "GET" ), "POST" ) )
					{
						$parsed_url[$key] = $value;	
					}
				}
			}
		}
		
		return $parsed_url;
	}
	
	/**
	* validVariables
	* 
	* @param mixed A valid component name (com_whatever)
	* @param mixed site or administrator
	* @return object
	*/
	function validVariables( $option, $site="site", $refresh='0' ) {
		
		// To cut down on the number of queries to the DB, we have two static variables
		// where: 
		// $site_vars[$option]->variables = $variables
		// $site_vars[$option]->variables_add = $variables_add
		// $site_vars[$option]->variables_int = $variables_int
		static $site_vars;
		static $admin_vars;
		
		switch (strtolower($site)) {
			case "administrator":
				if (!is_array($admin_vars)) {
					$admin_vars = array();
				}
				if (!array_key_exists( $option, $admin_vars ) || !is_object($admin_vars[$option]) || $refresh == '1') {
					$admin_vars[$option] = YooniqueaclHelper::getVariables( $option, 'administrator' );	
				}
				$return = $admin_vars[$option];
			  break;
			case "site":
			default:
				if (!is_array($site_vars)) {
					$site_vars = array();
				}
				if (!array_key_exists( $option, $site_vars ) || !is_object($site_vars[$option]) || $refresh == '1') {
					$site_vars[$option] = YooniqueaclHelper::getVariables( $option, 'site' );	
				}
				$return = $site_vars[$option];
			  break;
		}

		return $return;
	}
	
	/**
	 * 
	 * @param $option
	 * @param $site
	 * @return unknown_type
	 */
	function getVariables( $option, $site='site' )
	{
		$object = new JObject();
		$database = JFactory::getDBO();
		
		// grab all variables being processed for current option
		$data = '';
		$return 		= array();
		$variables 		= array();
		$variables_add 	= array();
		$variables_int 	= array();
		
		$site_query = $database->escape( $site );
		$option_query = $database->escape( $option );
		
		$query = "
			SELECT 
				*
			FROM 
				 " . TABLE_YOONIQUEACL_VARIABLES . " AS v
			WHERE 
				v.site = '{$site_query}'
			AND 
				v.site_option = '{$option_query}'
		";
		
		$database->setQuery($query);
		$data = $database->loadObjectList();
		
		for ($i=0; $i<count($data); $i++) {
			$d = $data[$i];
			$variables[$d->variable] = $d->variable;
			if ($d->force_integer == '1') { $variables_int[$d->variable] = $d->force_integer; }
		}
		
		// TODO Decide if we want to keep these
		//if (!isset($variables["controller"]))	{ $variables["controller"] = "controller"; }
		//if (!isset($variables["view"])) 		{ $variables["view"] = "view"; }
		//if (!isset($variables["task"])) 		{ $variables["task"] = "task"; }
		//if (!isset($variables["id"])) 		{ $variables["id"] = "id"; }
	
		$object->variables 		= $variables;
		$object->variables_add 	= $variables_add;
		$object->variables_int 	= $variables_int;
		
		return $object;
	}
	
	/**
	* rightsCheck - grabs whether the user has the rights to perform this action.
	* is an internal method -- 
	* -- 3pds should use YooniqueaclHelper::checkRights( $userid, $query, $site ) instead
	* 
	* @param object 
	* 
	* @details = array(
	*		[user]
	*		[option]
	*		[section]
	*		[task]
	*		[id]
	*		[site]
	* )
	*/
	function rightsCheck( $details ) 
	{		
		$return = new JObject();
		
		// TODO Decide whether this is a whitelist or blacklist site
		// TODO User config param for setting this
		$config = &YooniqueaclConfig::getInstance();
		$whitelist = $config->get( 'whitelist', '0' );
		$return->access = $whitelist;
		
		// If details is not an array, then this fails
		if (!is_array($details)) {
			$return->error = true;
			$return->errorMsg = JText::_( 'Invalid Input' );
			return $return;
		}
		
		// set the variables
			$user 	= $details["user"];
			$site 	= $details["site"];
			$query 	= $details["query"];
			$option	= $details["option"];
			
		// grab the config settings
			$super_group			= $config->get( 'super_group', '0' );
			$public_yooniqueacl			= $config->get( 'public_yooniqueacl', '0' );
			$default_group_site		= $config->get( 'default_group_site', '0' );
			$default_group_admin	= $config->get( 'default_group_admin', '0' );
			
			if ($site == "site") { 
				$default_group = $default_group_site; 
			} else { 
				$default_group = $default_group_admin; 
			}
			
		// clean the query
			// this should have already been done, but just in case -- it doesn't cause extra DB queries
			$cleanquery = YooniqueaclHelper::_cleanQuery( $query, $site );
			if (!empty($cleanquery->error)) {
				$return->error = true;
				$return->errorMsg = $cleanquery->errorMsg;
				return $return;
			}	
			$query	= $cleanquery->query;		
			$option	= $cleanquery->option;		
			
		// get the item from the db based on the query val
			$item = YooniqueaclHelper::getSiteItem( $query, $site );
			if ( isset($item->error) ) {
				$return->access = true;
				return $return;
			}
			
		// now that the item definitely exists, does the user have access?
			// if no, return the item's error URL
			// if yes, grant access
			if (!$hasAccess = YooniqueaclHelper::userHasAccess( $item, $user ) ) {
				$return->access = false;
				// return the error_url
				if ( isset($item->error_url_published) && intval($item->error_url_published) == '1' && isset($item->error_url) ) 
				{
					// TODO is there a built-in Joomla function for cleaning a URL?  If so, use it here
					$return->error_url 				= strval($item->error_url);
					$return->error_url_published 	= intval($item->error_url_published);
				} else {
					$return->error_url				= "";
					$return->error_url_published 	= false;
				}
			} else {
				$return->access = true;
			}
	
		return $return;
	}
	
	/**
	 * Determines whether/not a user has access to a particular item
	 * 
	 * @param $item		Valid item object
	 * @param $user		Valid user object
	 * @return unknown_type
	 */
	function userHasAccess( $item, $user )
	{
		$success = false;
		
			$config = &YooniqueaclConfig::getInstance();
			if( $super_group = $config->get( 'super_group', '0' ) ) {
				if ($isInGroup = YooniqueaclHelper::isUserInGroup( $user->id, $super_group )) {
					$success = true;
				}
			}
				
		// determine if this query is part of an excluded set
			if (!$success && $isExcluded = YooniqueaclHelperItem::isExcluded( $item )) 
			{
				$success = true;
			}

		// check the user's groups
			if (!$success) {
				
				$database = JFactory::getDBO();
				
				// Get the user's groups
				$userGroups = YooniqueaclHelper::getUsersGroups( $user->id );
				
				$group_array = array();
				for ($i=0; $i<count($userGroups); $i++)
				{
					$d = $userGroups[$i];
					$group_array[] = (int) $d->id;
				}
				
				// implode array of user's groups to use mysql IN ('xx', 'xy', 'x', 'y') statement for seeing if item is in group
				$in_query = "'".implode( "', '", $group_array )."'";
				
				$item_id_query = $database->escape($item->id);

				$query = "
					SELECT
						`item_id`
					FROM
						 " . TABLE_YOONIQUEACL_G2I . "
					WHERE
						`item_id` = '{$item_id_query}'
					AND
						`group_id` IN ( {$in_query} )
					LIMIT 1
				";
				
				$database->setQuery($query);
				if ($data = $database->loadObject()) 
				{
					$success = true;
				}				
			}

		return $success;	
	}
	
	/**
	 * Returns an array of objects
	 * @param $userid
	 * @return unknown_type
	 */
	function getUsersGroups( $userid, $refresh='0', $addPublic='1' )
	{
		static $groups;
		$userid = intval($userid);
		
		if (!is_array($groups)) {
			$groups = array();
		}
		
		if ($refresh == '1' || !array_key_exists( $userid, $groups ))
		{
			$database = JFactory::getDBO();

			$query = "
				SELECT 
					* 
				FROM 
					 " . TABLE_YOONIQUEACL_GROUPS . ",  " . TABLE_YOONIQUEACL_U2G . " 
				WHERE 
					 " . TABLE_YOONIQUEACL_U2G . ".group_id =  " . TABLE_YOONIQUEACL_GROUPS . ".id 
				AND 
					 " . TABLE_YOONIQUEACL_U2G . ".user_id = '{$userid}'
			";
			$database->setQuery($query);
			$data = $database->loadObjectList();

			if ($addPublic) {
				// if none of the groups is the Public Access group, add it to array
				$config = &YooniqueaclConfig::getInstance();
				$public_yooniqueacl = $config->get( 'public_yooniqueacl', '0' );
				if ($public_yooniqueacl) {
					$found = false;
					for ($i=0; $i<count($data); $i++)
					{
						$d = $data[$i];
						if ($d->id == $public_yooniqueacl) {
							$found = true;
							break;
						}
					}
					
					if (!$found) {
						// get a group object for public access
						JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/tables' );
						$table = JTable::getInstance( 'Group', 'TableYooniqueacl' );
						$table->load( $public_yooniqueacl );
						// add it
						$data[] = $table;	
					}
				}				
			}

			$groups[$userid] = $data;
		}
		
		return $groups[$userid];
    
	}
	
	
	/**
	 * 
	 * @param $userid
	 * @param $groupid
	 * @return unknown_type
	 */
	function isUserInGroup( $userid, $groupid )
	{
		// TODO Make this a wrapper for a method in YooniqueaclHelperUser?
		$success = false;
		
		// get the database object
		$database = &JFactory::getDBO();
		$groupid_query = $database->escape($groupid);
		$userid_query = $database->escape($userid);
		
		$query = "
			SELECT 
				`user_id` 
			FROM 
				 " . TABLE_YOONIQUEACL_U2G . " 
            WHERE 
            	`group_id` = '$groupid_query'
            AND 
            	`user_id` = '$userid_query' 
		";
        $database->setQuery( $query );
        if ($database->loadResult()) {
        	$success = true;
        }
        
        return $success;
	}
	
	/**
	 * add user to group
	 *
	 * @param int $userid
	 * @param int $groupid
	 * @return array
	 */
	function addToGroup($userid = null, $groupid = null)
	{
		// TODO Make this a wrapper for a method in YooniqueaclHelperUser?
		$return = new stdClass();
		$return->error = false;	// Error
		$return->errorMsg = '';	// Error Message
		
		// get the database object
		$database = &JFactory::getDBO();
		
		// check for user id
		if (null === $userid) {
			$user = &JFactory::getUser();
			$userid = $user->id; 
		}
		
		// check for group id
		if (null === $groupid) {
			$return->error = true;	// Error
			$return->errorMsg = JText::_( 'No Group ID' );	// Error Message
			return $return;
		}
		
        if (!$isUser = YooniqueaclHelper::isUserInGroup( $userid, $groupid ) ) 
        {
			// fire plugins
			$dispatcher =& JDispatcher::getInstance();
			$before 	= $dispatcher->trigger( 'onBeforeAddUserToGroup', array( $userid, $groupid, $return ) );
	        if (in_array(false, $before, true)) {
				JError::raiseError(500, $return->errorMsg );
				return false;
			}
			$groupid_query = $database->escape($groupid);
			$userid_query = $database->escape($userid);
			
		  	$query = "
		  		INSERT INTO 
		  			 " . TABLE_YOONIQUEACL_U2G . " 
		    	SET 
		    		`user_id` = '$userid_query',
		    		`group_id` = '$groupid_query',
		    		`created_datetime` = now()
		    ";
		    $database->setQuery( $query );
		    if (!$database->query()) {
				$return->error = true;
		    	$return->errorMsg = $database->getErrorMsg();
		    }
            // fire plugins
			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger( 'onAfterAddUserToGroup', array( $userid, $groupid ) );
			
		} else {
			// Don't need an error messssage for this
			$return->error = false;
		    $return->errorMsg = JText::_( 'Already Member' );
		}
		
		return $return;
		
	} // end of function addToGroup()
	
	
	/**
	 * remove user from group
	 *
	 * @param int $userid
	 * @param int $groupid
	 * @return array
	 */
	function removeFromGroup($userid = null, $groupid = null) 
	{
		// TODO Make this a wrapper for a method in YooniqueaclHelperUser?
		
		// get the database object
		$database = &JFactory::getDBO();
		
		// check for userid
		if (null === $userid) {
			$user = &JFactory::getUser();
			$userid = $user->id; 
		}
		
		// check for groupid
		if (null === $groupid) {
			$return->error = true;	// Error
			$return->errorMsg = JText::_( 'No Group ID' );	// Error Message
			return $return;
		}

		// fire plugins
		$dispatcher =& JDispatcher::getInstance();
		$before 	= $dispatcher->trigger( 'onBeforeRemoveUserFromGroup', array( $userid, $groupid, $return ) );
        if (in_array(false, $before, true)) {
			JError::raiseError(500, $return->errorMsg );
			return false;
		}
		
		$groupid_query = $database->escape($groupid);
		$userid_query = $database->escape($userid);

		// if user is a member of the group, remove them from the group
		$query = "
			DELETE FROM 
				 " . TABLE_YOONIQUEACL_U2G . "
		    WHERE 
		    	`user_id` = '$userid_query' 
		    AND 
		    	`group_id` = '$groupid_query'
		";
		$database->setQuery( $query );
		
		if (!$database->query()) {
			$return->error = true; 
			$return->errorMsg = $database->getErrorMsg();
			return $return;
		}
		
        // fire plugins
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onAfterRemoveUserFromGroup', array( $userid, $groupid ) );
		
		return $return;
		
	} // end of function removeFromGroup()
	
	/**
	 * get site item based upon item ID
	 *
	 * @param int $itemid
	 * @return mixed
	 */
	function getSiteItem( $query, $site='site' )
	{	
		// clean if raw string and not object
		if (is_string($query)) {
			$returned = YooniqueaclHelper::_cleanQuery( $query, $site );
			if ($returned->error) { 
				return $returned;
			} else {
				$query['option'] = $returned->option;
				$query['string'] = $returned->query;
			}
		} 
		
		// use item helper to retrieve item opposed to doing it here
		return YooniqueaclHelperItem::getItem( $query, '', $site );
		
	}
	
	/**
	 * Prepares the query
	 *
	 * @access public
	 * @param mixed query/url
	 * @param site/administrator
	 * @return mixed Parameter value
	 */
	function _cleanQuery( $rawquery, $site='site' ) {		
		$return = new stdClass();
		$return->query = '';	// Cleaned query
		$return->option = '';	// Cleaned option
		$return->error = false;	// Error
		$return->errorMsg = '';	// Error Message

		$config = &YooniqueaclConfig::getInstance();	

		// get the query value
		$url = $rawquery;
		
		// this little bit corrects for incomplete URLs
		$parsed = parse_url($url);
		if (isset($parsed['query'])) {
		    $query = trim( $parsed['query'] );
		} else {
			$x = array_pad( explode( '?', $url ), 2, false );

			$find   = '?';
			$pos = strpos($url, $find);
			if ($pos === false) {
				// not found
				$parsed['query'] = ( $x[0] )? $x[0] : '' ;				
			} else {
				// found
				$parsed['query'] = ( $x[1] )? $x[1] : '' ;
			}
		    $query = trim( $parsed['query'] );
		}
				
		// if no query
		if (!isset($query)) { 
			$return->error = true;
			$return->errorMsg = JText::_('No Query');
			return $return;
		}
		
		// prepare the query
		parse_str($query, $parsed_url);
		ksort($parsed_url);
		
		if (!isset($parsed_url["option"])) { 
			$return->error = true;
			$return->errorMsg = JText::_('No Option');
			return $return;
		}
		
		// option has been found
		$site_option = trim( $parsed_url["option"] ); 
		
		// pass parsed_url variables
		$validVariables 	= YooniqueaclHelper::validVariables( $site_option, $site );
		$variables         	= $validVariables->variables;
		$variables_int    	= $validVariables->variables_int;
		$variables_add    	= $validVariables->variables_add;

		$newarray = array();
		if ($parsed_url) { foreach ($parsed_url as $key=>$value) {
		    switch ($key) {
		        // case "Itemid":
		        case "mosmsg":
		            // don't send the above variables
		          break;
		        case "cid":
					$cid_array	= JRequest::getVar('cid', array('0'), '', 'array');
					$value = $cid_array['0'];
					$clean_value = $value;
	    		    if (isset($variables_int["$key"])) {
	                    if (intval($variables_int["$key"]) == '1') {
	                        $clean_value = intval($value);
	                    }
	                }
	                //echo "key: $key, value: $value<br />";
					$newarray[$key] = trim($clean_value);
		          break;
		        default:
		            // all other variables should be passed
                    if (is_array($value))
                    {
                        $value = "";
                    	$array = JRequest::getVar( $key, array(), '', 'array');
                    	if (!empty($array) && is_array($array))
                    	{
                            $keys = array_keys( $array ); 
                            $value = $array[$keys[0]];
                    	}
                    }
                    
		            if (isset($value) && strlen($value) > 0) {
		            	$value = strval($value);
		                $clean_value = $value;
		                
		                if (isset($variables_int["$key"])) {
		                    if (intval($variables_int["$key"]) == '1') {
		                        $clean_value = intval($value);
		                    }
		                }		
		                //echo "key: $key, value: $value<br />";
		                $newarray[$key] = trim($clean_value);
		            }
		          break;
		    }
		} }
			
		// the query has now been cleaned, run through newarray and determine what query & query_add is
		// this loop forces option to be first
		// and prepares the query for direct comparison to values in the db
		$string = '';
		$string = "option=".$site_option;
		$string_add = "option=".$site_option;
		if ($variables) { 
			ksort($variables);
			foreach ($variables as $var) {
				if ( isset($newarray[$var]) && $var != "option" ) {	
					$string .= "&".$var."=".$newarray[$var]; 
					if (isset($variables_add[$var])) {
						$string_add .= "&".$var."=".$newarray[$var]; 
					}
				}
			} 
		}

		$return->query 		= $string;
		$return->query_add 	= $string_add;
		$return->option 	= $site_option;
		return $return;
	}
	
}
