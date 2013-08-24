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


class YooniqueaclHelperDiagnostics
{
    /**
     * Redirects with message
     * 
     * @param object $message [optional]    Message to display
     * @param object $type [optional]       Message type
     */
    function redirect($message = '', $type = '')
    {
        $mainframe = JFactory::getApplication();
        
        if ($message) 
        {
            $mainframe->enqueueMessage($message, $type);
        }
        
        JRequest::setVar('controller', 'dashboard');
        JRequest::setVar('view', 'dashboard');
        JRequest::setVar('task', '');
        return;
    }    
    
    /**
     * Inserts fields into a table
     * 
     * @param string $table
     * @param array $fields
     * @param array $definitions
     * @return boolean
     */
    function insertTableFields($table, $fields, $definitions)
    {
        $database = JFactory::getDBO();
        $fields = (array) $fields;
        $errors = array();
        
        foreach ($fields as $field)
        {
            $query = " SHOW COLUMNS FROM {$table} LIKE '{$field}' ";
            $database->setQuery( $query );
            $rows = $database->loadObjectList();
            if (!$rows && !$database->getErrorNum()) 
            {       
                $query = "ALTER TABLE `{$table}` ADD `{$field}` {$definitions[$field]}; ";
                $database->setQuery( $query );
                if (!$database->query())
                {
                    $errors[] = $database->getErrorMsg();
                }
            }
        }
        
        if (!empty($errors))
        {
            $this->setError( implode('<br/>', $errors) );
            return false;
        }
        return true;
    }
    
	/**
	 * Performs basic checks on your Yooniqueacl installation to ensure it is configured OK
	 * @return unknown_type
	 */
	function checkInstallation() 
	{
		// checks that the db tables are correct
//			$this->checkTables();
		// is there at least one Yooniqueacl Group?
			$this->firstGroup();
		// is admin-side com_login excluded?
//			$this->comLogin();
			
        if (!$this->checkU2GTable()) 
        {
            return $this->redirect( JText::_('DIAGNOSTIC CHECK_U2G_TABLE FAILED') .' :: '. $this->getError(), 'error' );
        }
			
	}
	
	function checkTables()
	{
		$database = JFactory::getDBO();

		// `site` enum('site','administrator') NOT NULL default 'site',
		$site = new stdClass();
		$site->table 			=  TABLE_YOONIQUEACL_ITEMS;
		$site->name 			= 'site';
		$site->definition 		= "enum('site','administrator') NOT NULL default 'site'";
		
		// `query` text NOT NULL,
		$query = new stdClass();
		$query->table 			=  TABLE_YOONIQUEACL_ITEMS;
		$query->name 			= 'query';
		$query->definition 		= 'text NOT NULL';
		
		// `item_exclude` tinyint(1) NOT NULL,
		$item_exclude = new stdClass();
		$item_exclude->table 			=  TABLE_YOONIQUEACL_ITEMS;
		$item_exclude->name 			= 'item_exclude';
		$item_exclude->definition 		= 'tinyint(1) NOT NULL';
				
		// `created_datetime` datetime NOT NULL,
		$created_datetime = new stdClass();
		$created_datetime->table 			=  TABLE_YOONIQUEACL_ITEMS;
		$created_datetime->name 			= 'created_datetime';
		$created_datetime->definition 		= 'datetime NOT NULL';
		
		// `content_category` int(11) unsigned NOT NULL default '0',
		$content_category = new stdClass();
		$content_category->table 		=  TABLE_YOONIQUEACL_ITEMS;
		$content_category->name 		= 'content_category';
		$content_category->definition 	= 'INT(11) NOT NULL';
						
		// fields				
		$fields = array();
		$fields[] = $site;
		$fields[] = $query;
		$fields[] = $item_exclude;
		$fields[] = $created_datetime;
		$fields[] = $content_category;
		
	    // Check if fields exist
	    $error = false;
	    $errors = array();
		foreach ($fields as $f)
		{
			$table 		= $f->table;
			$field 		= $f->name;
			$definition = $f->definition;
			
			$currentfields = $database->getTableColumns( $table );
		
			if (!isset($currentfields[$field])) 
			{
				$query = "ALTER TABLE ".$table." ADD `".$field."` ".$definition.";";
				$database->setQuery( $query );
				if (!$database->query()) 
				{
					$errors[] = $database->stderr();
					$error = true;
				}
			}
		}
		
		if ($error)
		{
			$this->setError( implode(', ', $errors ) );
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function firstGroup()
	{
		$database = JFactory::getDBO();
		$query = "SELECT * FROM " . TABLE_YOONIQUEACL_GROUPS;
		$database->setQuery( $query );
		$data = $database->loadObject();
		if (!isset($data->id)) 
		{
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/tables' );
			$table = JTable::getInstance( 'Group', 'TableYooniqueacl' );

			$table->title		= "Public Access";
			$table->description	= "The default yoonique ACL group for Public Access.";			
			if (!$table->store()) 
			{
				$this->setError( $table->getError() );
				return false;
			}
			$config = &yooniqueaclconfig::getinstance();
			$config_value = $config->get('public_yooniqueacl');
			if (!$config_value) {
				$row = JTable::getInstance ( 'config','TableYooniqueacl' );
				$row->title = 'public_yooniqueacl';
				$row->value = $table->id;
				$row->store();
			}
			$config_value = $config->get('default_group_site');
			if (!$config_value) {
				$row = JTable::getInstance ( 'config','TableYooniqueacl' );
				$row->title = 'default_group_site';
				$row->value = $table->id;
				$row->store();
			}

			$config_value = $config->get('default_ce');
			if (!$config_value) {
				$row = JTable::getInstance ( 'config','TableYooniqueacl' );
				$row->title = 'default_ce';
				$row->value = 'index.php?option=com_users';
				$row->store();
			}

//			$table = JTable::getInstance( 'Group', 'TableYooniqueacl' );
//			$table->title		= "Admin Access";
//			$table->description	= "The default yoonique ACL group for Admin Access.";			
//			if (!$table->store()) 
//			{
//				$this->setError( $table->getError() );
//				return false;
//			}
//			$config = &yooniqueaclconfig::getinstance();
//			$config_value = $config->get('super_group');
//			if (!$config_value) {
//				$row = JTable::getInstance ( 'config','TableYooniqueacl' );
//				$row->title = 'super_group';
//				$row->value = $table->id;
//				$row->store();
//			}
//			$config_value = $config->get('default_group_admin');
//			if (!$config_value) {
//				$row = JTable::getInstance ( 'config','TableYooniqueacl' );
//				$row->title = 'default_group_admin';
//				$row->value = $table->id;
//				$row->store();
//			}
//			$config_value = $config->get('admin_default_ce');
//			if (!$config_value) {
//				$row = JTable::getInstance ( 'config','TableYooniqueacl' );
//				$row->title = 'admin_default_ce';
//				$row->value = 'administrator';
//				$row->store();
//			}

		}
	}

//	/**
//	 * 
//	 * @param $site
//	 * @return unknown_type
//	 */
//	function comLogin( $site='administrator' )
//	{
//		$item = YooniqueaclHelper::getSiteItem( 'option=com_login', $site );
//		
//		if (isset($item->error) && $item->error) 
//		{
//			// create the Site Item and set excluded = '1'
//			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/tables' );
//			$table = JTable::getInstance( 'Item', 'TableYooniqueacl' );
//			$table->site 			= $site;
//			$table->title		 	= JText::_( "Login - {$site} [Do Not Delete]" );
//			$table->query 			= 'option=com_login';
//			$table->site_option 	= 'com_login';
//			$table->item_exclude 	= '1';
//			$table->created_datetime = gmdate( 'Y-m-d H:i:s');
//			if (!$table->store()) 
//			{
//				$this->setError( $table->getError() );
//				return false;
//			}
//		} 
//			elseif ($item->item_exclude != '1') 
//		{
//			// set the existing one to item_exclude = '1' if it isn't
//			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/tables' );
//			$table = JTable::getInstance( 'Item', 'TableYooniqueacl' );
//			$table->load( $item->id );
//			$table->title		 	= JText::_( "Login - {$site} [Do Not Delete]" );
//			$table->item_exclude 	= '1';
//			if (!$table->store()) 
//			{
//				$this->setError( $table->getError() );
//				return false;
//			}				
//		}
//	}
//	

    /**
     * Check if the table is correct
     * 
     * @return boolean
     */
    function checkU2GTable() 
    {
        // if this has already been done, don't repeat
        if (YooniqueaclConfig::getInstance()->get('checkU2GTable', '0'))
        {
            return true;
        }
        
        $table =  TABLE_YOONIQUEACL_U2G;
        $definitions = array();
        $fields = array();

        $fields[] = "created_datetime";
            $definitions["created_datetime"] = " datetime NOT NULL ";
            
        if ($this->insertTableFields( $table, $fields, $definitions ))
        {
            // Update config to say this has been done already
            JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/tables' );
            $config = JTable::getInstance( 'Config', 'TableYooniqueacl' );
            $config->load( 'checkU2GTable' );
            $config->title = 'checkU2GTable';
            $config->value = '1';
            $config->store();
            return true;
        }

        return false;        
    }
}
