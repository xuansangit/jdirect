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
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
* 	YooniqueaclController
*
*/
// ************************************************************************
class YooniqueaclControllerVariable extends YooniqueaclController {

	var $_type;

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'view', 'view' );
		$this->registerTask( 'new', 'edit' );
		$this->registerTask( 'save', 'save' );
	}

	/**
	 * display 
	 * @return void
	 */
	function display() {
		JRequest::setVar( 'view', 'group' );
		JRequest::setVar( 'layout', 'default'  );
		// JRequest::setVar('hidemainmenu', 1);

		parent::display();

	}

	/**
	 * display the group
	 * @return void
	 */
	function view() {	
		JRequest::setVar( 'view', 'group' );
		JRequest::setVar( 'layout', 'default' );
		// JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit() {
		JRequest::setVar( 'view', 'group' );
		JRequest::setVar( 'layout', 'form' );
		JRequest::setVar( 'hidemainmenu', 1 );

		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save() {
		
		$model = &$this->getModel( 'variable' );
		
		if ( $data = $model->save() ) {
			// success
			$link = 'index.php?option='.'com_yooniqueacl'.'&controller=variables&task=list';
			$msg = JText::_( 'Save' )." ".JText::_( 'Success' );
			$this->setRedirect( $link, $msg );
		} else {
			// fail
			$link = 'index.php?option='.'com_yooniqueacl'.'&controller=variables&task=list';
			$msg = JText::_( 'Save' )." ".JText::_( 'Failed' )." - ".$model->getError() ;
			$this->setRedirect( $link, $msg, 'notice' );
		}
	}

	/**
	 * cancel and redirect to main page
	 * @return void
	 */
	function cancel() {
		$link = 'index.php?option='.'com_yooniqueacl'.'&controller=variables&task=list';
	    $msg = JText::_( 'Operation Cancelled' );
	    $this->setRedirect( $link, $msg, 'notice' );		
	}

}
