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
* 	YooniqueaclControllerVariables
*/
// ************************************************************************
class YooniqueaclControllerVariables extends YooniqueaclController {

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
		$this->registerTask( 'switch_autoadd', 'switchAutoAdd' );
		$this->registerTask( 'switch_forceinteger', 'switchForceInteger' );
	}

	/**
	 * display 
	 * @return void
	 */
	function display() {
		JRequest::setVar( 'view', 'variables' );
		JRequest::setVar( 'layout', 'default'  );
		parent::display();
	}

	/**
	 * display the ticket
	 * @return void
	 */
	function edit() {
		JRequest::setVar( 'view', 'variable' );
		JRequest::setVar( 'layout', 'form' );
		parent::display();
	}

	/**
	 * delete
	 * @return void
	 */
	function delete() {

		$model = &$this->getModel( 'variable' );
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=variables&task=list';
			 $msg = JText::_( 'Delete' )." ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=variables&task=list';
			 $msg = JText::_( 'Delete' )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
			
	}


	/**
	 * switchForceInteger
	 * @return void
	 */
	function switchForceInteger() {

		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$database = JFactory::getDBO();

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
			return;
		}
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				// grab current setting
				$database->setQuery("SELECT `force_integer` FROM  " . TABLE_YOONIQUEACL_VARIABLES
									." WHERE `id` = '$id' ");
				$old = $database->loadResult();
				if ($old == "1") { $new = 0; } else { $new = 1; }
	
				  $query = "UPDATE  " . TABLE_YOONIQUEACL_VARIABLES
					."\n SET `force_integer` = '$new' "
					." WHERE `id` = '$id' "
					;
					$database->setQuery( $query );
					if (!$database->query()) {
						// fail
						 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=variables&task=list';
						 $msg = JText::_( 'Update' )." ".JText::_( 'Failed' );
						 $this->setRedirect( $link, $msg, 'notice' );
					} else {
						// success
						 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=variables&task=list';
						 // $msg = JText::_( 'Delete' )." ".JText::_( 'Success' );
						 $this->setRedirect( $link );
					}

			}
		}
		
			
	}

	   
}
