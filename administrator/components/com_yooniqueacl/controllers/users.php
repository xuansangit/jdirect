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
 * 
 *
 */
class YooniqueaclControllerUsers extends YooniqueaclController {

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {
		
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'enroll_flex', 'enroll_flex' );
		$this->registerTask( 'withdraw_flex', 'withdraw_flex' );
		$this->registerTask( 'withdraw_all', 'withdraw_all' );
		$this->registerTask( 'enroll', 'enroll' );
		$this->registerTask( 'withdraw', 'withdraw' );

	}

	/**
	 * display 
	 * @return void
	 */
	function display()
	{
		JRequest::setVar( 'view', 'users' );
		parent::display();

	}
	
	/**
	 * change value
	 * @return void
	 */
	function enroll_flex() {
		$msg = new stdClass();
		$msg->type 		= '';
		$msg->message 	= '';
		$msg->link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';

		$model = &$this->getModel( strtolower( 'users' ) );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			$msg->type 		= 'notice';
			$msg->message 	= JText::_( 'Select an item' );
			$this->setRedirect( $msg->link, $msg->message, $msg->type );
			return;
		}

		$msg->type 		= 'message';
		$msg->message  .= JText::_( 'Enroll Users' );

		// fire plugins for each item within the model
		$data = $model->enroll_flex( $msg );

		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}

	/**
	 * change value
	 * @return void
	 */
	function withdraw_flex() {
		$msg = new stdClass();
		$msg->type 		= '';
		$msg->message 	= '';
		$msg->link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';

		$model = &$this->getModel( strtolower( 'users' ) );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			$msg->type 		= 'notice';
			$msg->message 	= JText::_( 'Select an item' );
			$this->setRedirect( $msg->link, $msg->message, $msg->type );
			return;
		}

		$msg->type 		= 'message';
		$msg->message  .= JText::_( 'Withdraw Users' );

		// fire plugins for each item within the model
		$data = $model->withdraw_flex( $msg );

		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}

	/**
	 * change value
	 * @return void
	 */
	function withdraw_all() {
		$msg = new stdClass();
		$msg->type 		= '';
		$msg->message 	= '';
		$msg->link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';

		$model = &$this->getModel( strtolower( 'users' ) );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			$msg->type 		= 'notice';
			$msg->message 	= JText::_( 'Select an item' );
			$this->setRedirect( $msg->link, $msg->message, $msg->type );
			return;
		}

		$msg->type 		= 'message';
		$msg->message  .= JText::_( 'Withdraw Users From All' );

		// fire plugins for each item within the model
		$data = $model->withdraw_all( $msg );

		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}

	/**
	 * enroll in selected item
	 * @return void
	 */
	function enroll() {

		$model = &$this->getModel( 'user' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to enroll'); window.history.go(-1);</script>\n";
			return;
		}

		$groups = JRequest::getVar( 'group', array(0), 'post', 'array' );

		if (count( $cids )) {
			foreach ($cids as $id) {
				// group[$row->id]				
				$groupid = $groups[$id];
				if ( $action = $model->addUserToGroup( $id, $groupid ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';
			 $msg = JText::_( 'Add' )." ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Users' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';
			 $msg = JText::_( 'Add' )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
	}

	/**
	 * withdraw from selected item
	 * @return void
	 */
	function withdraw() {

		$model = &$this->getModel( 'user' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to enroll'); window.history.go(-1);</script>\n";
			return;
		}
		
		$groups = JRequest::getVar( 'group', array(0), 'post', 'array' );
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				// group[$row->id]				
				$groupid = $groups[$id];

				if ( $action = $model->removeUserFromGroup( $id, $groupid ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';
			 $msg = JText::_( 'Withdraw' )." ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Users' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';
			 $msg = JText::_( 'Withdraw' )." ".JText::_( 'Users' );
			 $this->setRedirect( $link, $msg );
		}
		
	}	    
}
