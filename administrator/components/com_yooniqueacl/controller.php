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

jimport('joomla.application.component.controller');

class YooniqueaclController extends JControllerLegacy
{
	var $_models = array();
	
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/helpers/yooniqueacl.php')) 
		{
			// Require Helpers
			jimport( 'joomla.filesystem.folder' );
			$helpersPath = JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/helpers';
			$helperFiles = JFolder::files($helpersPath, '\.php$', false, true);
			if (count($helperFiles) > 0) {
				//iterate through the helper files
				foreach ($helperFiles as $helperFile) {
					require_once($helperFile);
				}
			}
		}
		
		// run diagnostics checks
		$diagnostics = new YooniqueaclHelperDiagnostics(); 
		$diagnostics->checkInstallation();
	}
	
	/**
	* 	display the view
	*/
    function display() 
    {
		// execute published plugins
		$dispatcher	   =& JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeDisplayAdminComponentYooniqueacl', array() );
		YooniqueaclHelper::addSubmenu(JRequest::getCmd('controller', 'contacts'));
		
        
			parent::display();
		
		// execute published plugins
		$dispatcher	   =& JDispatcher::getInstance();
		$dispatcher->trigger('onAfterDisplayAdminComponentYooniqueacl', array() );		
		
    }
	
	/**
	 * 
	 * @return 
	 */
	function doTask()
	{
		$success = true;
		$msg = new stdClass();
		$msg->message = '';
		$msg->error = '';
				
		// expects $element in URL and $elementTask
		$element = JRequest::getVar( 'element', '', 'request', 'string' );
		$elementTask = JRequest::getVar( 'elementTask', '', 'request', 'string' );

		$msg->error = '1';
		// $msg->message = "element: $element, elementTask: $elementTask";
		
		// gets the plugin named $element
		$import 	= JPluginHelper::importPlugin( 'yooniqueacl', $element );
		$dispatcher	=& JDispatcher::getInstance();
		// executes the event $elementTask for the $element plugin
		// returns the html from the plugin
		// passing the element name allows the plugin to check if it's being called (protects against same-task-name issues)
		$result 	= $dispatcher->trigger( $elementTask, array( $element ) );
		// This should be a concatenated string of all the results, 
			// in case there are many plugins with this eventname 
			// that return null b/c their filename != element) 
		$msg->message = implode( '', $result );
			// $msg->message = @$result['0'];
						
		// encode and echo (need to echo to send back to browser)		
		echo $msg->message;
		$success = $msg->message;

		return $success;
	}
	
	/**
	 * 
	 * @return 
	 */
	function doTaskAjax()
	{
		$success = true;
		$msg = new stdClass();
		$msg->message = '';
				
		// get elements $element and $elementTask in URL 
			$element = JRequest::getVar( 'element', '', 'request', 'string' );
			$elementTask = JRequest::getVar( 'elementTask', '', 'request', 'string' );
			
		// get elements from post
			// $elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
			
		// for debugging
			// $msg->message = "element: $element, elementTask: $elementTask";

		// gets the plugin named $element
			$import 	= JPluginHelper::importPlugin( 'yooniqueacl', $element );
			$dispatcher	=& JDispatcher::getInstance();
			
		// executes the event $elementTask for the $element plugin
		// returns the html from the plugin
		// passing the element name allows the plugin to check if it's being called (protects against same-task-name issues)
			$result 	= $dispatcher->trigger( $elementTask, array( $element ) );
		// This should be a concatenated string of all the results, 
			// in case there are many plugins with this eventname 
			// that return null b/c their filename != element)
			$msg->message = implode( '', $result );
			// $msg->message = @$result['0'];

		// set response array
			$response = array();
			$response['msg'] = $msg->message;
			
		// encode and echo (need to echo to send back to browser)
			echo ( json_encode( $response ) );

		return $success;
	}
	
    /**
     * 
     * @return unknown_type
     */
    function getNamespace()
    {
    	$app = JFactory::getApplication();
    	$model = $this->getModel( $this->get('suffix') );
		$ns = $app->getName().'::'.'com.yooniqueacl.model.'.$model->getTable()->get('_suffix');
    	return $ns;
    }
    
	/**
	 * 
	 * @return unknown_type
	 */
    function _setModelState()
    {
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state = array();
		
        $state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $state['limitstart'] = $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', 0, 'int');
        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.'.$model->getTable()->getKeyName(), 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'ASC', 'word');
        $state['filter']    = $app->getUserStateFromRequest($ns.'.filter', 'filter', '', 'string');
        $state['id']        = JRequest::getVar('id', 'post', JRequest::getVar('id', 'get', '', 'int'), 'int');

        // TODO santize the filter
        // $state['filter']   	= 

    	foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );	
		}
  		return $state;
    }
    
    /**
     * We override parent::getModel because parent::getModel was always creating a new Model instance
     *
     */
	function getModel( $name = '', $prefix = '', $config = array() )
	{
		if ( empty( $name ) ) {
			$name = $this->getName();
		}

		if ( empty( $prefix ) ) {
			$prefix = $this->getName() . 'Model';
		}
		
		$fullname = strtolower( $prefix.$name ); 
		if (empty($this->_models[$fullname]))
		{
			if ( $model = & $this->createModel( $name, $prefix, $config ) )
			{
				// task is a reserved state
//				xdebug_break();
				$model->setState( 'task', $this->task );
	
				// Lets get the application object and set menu information if its available
				$app	= &JFactory::getApplication();
				$menu	= &$app->getMenu();
				if (is_object( $menu ))
				{
					if ($item = $menu->getActive())
					{
						$params	=& $menu->getParams($item->id);
						// Set Default State Data
						$model->setState( 'parameters.menu', $params );
					}
				}
			}
				else 
			{
				$model = new JModelLegacy();
			}
			$this->_models[$fullname] = $model;
		}

		return $this->_models[$fullname];
	}
	
}

