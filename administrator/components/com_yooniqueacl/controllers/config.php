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

/**
* 	YooniqueaclControllerConfig
*/
class YooniqueaclControllerConfig extends YooniqueaclController {
			
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {
		parent::__construct();
				
		// Register Extra tasks
		$this->registerTask( 'list', 'display' );
		$this->registerTask( 'save', 'save' );
		$this->registerTask( 'cancel', 'cancel' );
	}

	/**
	 * display 
	 * @return void
	 */
	function display() {
		JRequest::setVar( 'view', 'config' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();

	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save() 
	{
		$model = &$this->getModel( 'config' );
		$msg = new JObject();
		$msg->type 		= '';
		$msg->message 	= '';
		$msg->link = 'index.php?option='.'com_yooniqueacl'.'&controller=config';

		if ( $data = $model->save() ) {
			$msg->link		= 'index.php?option='.'com_yooniqueacl';
			$msg->type 		= 'message';
			$msg->message  .= JText::_( 'Successfully Saved' );

			// fire plugins
			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger( 'onAfterSaveConfig', array( $data, $msg ) );
			
		} else {
			// save failed
			$msg->type = 'notice';			
			$msg->message = JText::_( 'Save Failed' )." - ".$model->getError();
		}
		
		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}

	/**
	 * cancel and redirect to main page
	 * @return void
	 */
	function cancel() {
		$link = 'index.php?option='.'com_yooniqueacl';
	    $msg = JText::_( 'Operation Cancelled' );
	    $this->setRedirect( $link, $msg, 'notice' );		
	}
	    
}