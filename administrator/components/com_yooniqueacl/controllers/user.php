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
* 	YooniqueaclControllerUser
*/
// ************************************************************************
class YooniqueaclControllerUser extends YooniqueaclController {
			
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {
		parent::__construct();
					
		// Register Extra tasks
		$this->registerTask( 'list', 'display' );
		$this->registerTask( 'define', 'defineUser' );
		$this->registerTask( 'switch_groups', 'switchUserToGroups' );
	}

	/**
	 * display 
	 * @return void
	 */
	function display() {
		JRequest::setVar( 'view', 'user' );
		JRequest::setVar( 'layout', 'define'  );

		parent::display();

	}

	/**
	 * define
	 * @return void
	 */
	function defineUser() {
		$model = &$this->getModel( 'user' );
		if (!$model->getUser()) {
			// fail
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';
			 $msg = JText::_( 'Invalid' )." ".JText::_( 'User' );
			 $this->setRedirect( $link, $msg, 'notice' );		
		}
		
		JRequest::setVar( 'view', 'user' );
		JRequest::setVar( 'layout', 'define'  );

		parent::display();

	}

	/**
	 * cancel and redirect to main page
	 * @return void
	 */
	function cancel() {
		$link = 'index.php?option='.'com_yooniqueacl'.'&controller=users&task=list';
	    // $msg = JText::_( 'Operation Cancelled );
	    $this->setRedirect( $link );
	}

	/**
	 * switch
	 * @return void
	 */
	function switchUserToGroups() {

		$model = &$this->getModel( 'user' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$user = $model->getUser();

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to enroll'); window.history.go(-1);</script>\n";
			return;
		}
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				if ( $action = $model->switchUserToGroup( $user->id, $id ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=user&task=define&id='.$user->id;
			 $msg = JText::_( 'Switch' )." ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Users' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=user&task=define&id='.$user->id;
			 //$msg = JText::_( 'Swicth' )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg, 'notice' );
		}
				

		
	}
	
	    
}
