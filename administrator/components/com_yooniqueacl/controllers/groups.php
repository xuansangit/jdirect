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
* 	YooniqueaclControllerGroups
*/
// ************************************************************************
class YooniqueaclControllerGroups extends YooniqueaclController {

	// var $_search;
			
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'list', 'display' );
		$this->registerTask( 'edit', 'edit' );
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'remove', 'delete' );
	}

	/**
	 * display 
	 * @return void
	 */
	function display() {
		JRequest::setVar( 'view', 'groups' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();

	}

	/**
	 * display the ticket
	 * @return void
	 */
	function edit() {
		JRequest::setVar( 'view', 'group' );
		JRequest::setVar( 'layout', 'form' );
		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function delete() {

		$model = &$this->getModel( 'group' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
			return;
		}
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				if ( $action = $model->delete( $id ) ) {
					// success
					$success++;
				} else {
					// fail
					$fail++;
				}
			}
		}
		
		if ( $success != count($cids) || intval( $fail ) > 0 ) {
			// fail
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=groups&task=list';
			 $msg = JText::_( 'Delete' )." ". JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=groups&task=list';
			 $msg = JText::_( 'Delete' )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
				

		
	}

	    
}
