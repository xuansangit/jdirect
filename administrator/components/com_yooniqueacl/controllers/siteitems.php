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
class YooniqueaclControllerSiteitems extends YooniqueaclController {
			
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
		$this->registerTask( 'define', 'defineItem' );
		$this->registerTask( 'switch_publish', 'switchPublish' );
		$this->registerTask( 'switch_inclusion', 'switchInclude' );	
		$this->registerTask( 'switch_groups', 'switchItemToGroups' );
	}

	/**
	 * display 
	 * @return void
	 */
	function display() {
		JRequest::setVar( 'view', 'siteitems' );
		JRequest::setVar( 'layout', 'default'  );

		parent::display();

	}

	/**
	 * display the ticket
	 * @return void
	 */
	function edit() {
		JRequest::setVar( 'view', 'siteitem' );
		JRequest::setVar( 'layout', 'form' );
		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function delete() {

		$model = &$this->getModel( 'siteitem' );
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Delete')." ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Delete' )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
	}


	/**
	 * change value
	 * @return void
	 */
	function enroll_flex() {
		$msg = new stdClass();
		$msg->type 		= '';
		$msg->message 	= '';
		$msg->link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';

		$model = &$this->getModel( strtolower( 'siteitems' ) );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			$msg->type 		= 'notice';
			$msg->message 	= JText::_( 'Select an item' );
			$this->setRedirect( $msg->link, $msg->message, $msg->type );
			return;
		}

		$msg->type 		= 'message';
		$msg->message  .= JText::_( 'Enroll Items' );

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
		$msg->link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';

		$model = &$this->getModel( strtolower( 'siteitems' ) );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			$msg->type 		= 'notice';
			$msg->message 	= JText::_( 'Select an item' );
			$this->setRedirect( $msg->link, $msg->message, $msg->type );
			return;
		}

		$msg->type 		= 'message';
		$msg->message  .= JText::_( 'Withdraw Items' );

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
		$msg->link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';

		$model = &$this->getModel( strtolower( 'siteitems' ) );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			$msg->type 		= 'notice';
			$msg->message 	= JText::_( 'Select an item' );
			$this->setRedirect( $msg->link, $msg->message, $msg->type );
			return;
		}

		$msg->type 		= 'message';
		$msg->message  .= JText::_( 'Withdraw Items From All Groups' );

		// fire plugins for each item within the model
		$data = $model->withdraw_all( $msg );

		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}

	/**
	 * enroll in selected item
	 * @return void
	 */
	function enroll() {

		$model = &$this->getModel( 'siteitems' );
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
				if ( $action = YooniqueaclHelperItem::addToGroup( $id, $groupid ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Add' )." ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Add' )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
	}

	/**
	 * withdraw from selected item
	 * @return void
	 */
	function withdraw() {

		$model = &$this->getModel( 'siteitems' );
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

				if ( $action = YooniqueaclHelperItem::removeFromGroup( $id, $groupid ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Withdraw' )." ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Withdraw'  )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
		
	}

	/**
	 * enroll ce
	 * @return void
	 */
	function enroll_ce() {
		
		$success = ""; $fail = "";
		$model = &$this->getModel( 'siteitems' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to enroll'); window.history.go(-1);</script>\n";
			return;
		}
		
		$flex = $model->getField( 'flex_ce_url', '' );
		if ($flex) {
			if (count( $cids )) {
				foreach ($cids as $id) {
					if ( $action = $model->addCEToItem( $id, $flex ) ) {
						// success
						$success++;
					} else {
						// fail
						$fail++;
					}
				}
			}
		} else {
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Invalid Flex CE URL' );
			 $this->setRedirect( $link, $msg, 'notice' );
			 return;			
		}
		
		if ( intval($success) != count($cids) || intval( $fail ) > 0 ) {
			// fail
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Add' )." ".JText::_( 'Flex' )." CE ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Add' )." ".JText::_( 'Flex' )." CE ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
				

		
	}

	/**
	 * withdraw ce
	 * @return void
	 */
	function withdraw_ce() {

		$model = &$this->getModel( 'siteitems' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to enroll'); window.history.go(-1);</script>\n";
			return;
		}
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				if ( $action = $model->removeCEFromItem( $id ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Withdraw' )." CE ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Withdraw' )." CE ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
				

		
	}

	/**
	 * publish ce
	 * @return void
	 */
	function publish_ce() {

		$model = &$this->getModel( 'siteitems' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to enroll'); window.history.go(-1);</script>\n";
			return;
		}
			
		if (count( $cids )) {
			foreach ($cids as $id) {
				if ( $action = $model->publishCE( $id, '1' ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Publish' )." CE ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Publich' )." CE ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
				

		
	}

	/**
	 * unpublish ce
	 * @return void
	 */
	function unpublish_ce() {

		$model = &$this->getModel( 'siteitems' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to enroll'); window.history.go(-1);</script>\n";
			return;
		}
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				if ( $action = $model->publishCE( $id, '0' ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Unpublish' )." CE ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Unpublish' )." CE ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg );
		}
				

		
	}

	/**
	 * switchPublish
	 * @return void
	 */
	function switchPublish() {

		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$database = JFactory::getDBO();

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
			return;
		}
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				// grab current setting
				$database->setQuery("SELECT `error_url_published` FROM  " . TABLE_YOONIQUEACL_ITEMS
									." WHERE `id` = '$id' ");
				$old = $database->loadResult();
				if ($old == "1") { $new = 0; } else { $new = 1; }
	
				  $query = "UPDATE  " . TABLE_YOONIQUEACL_ITEMS
					."\n SET `error_url_published` = '$new' "
					." WHERE `id` = '$id' "
					;
					$database->setQuery( $query );
					if (!$database->query()) {
						// fail
						 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
						 $msg = JText::_( 'Update' )." ".JText::_( 'Failed' );
						 $this->setRedirect( $link, $msg, 'notice' );
					} else {
						// success
						 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
						 // $msg = JText::_( 'Delete' )." ".JText::_( 'Success' );
						 $this->setRedirect( $link );
					}

			}
		}
		
			
	}

	/**
	 * switchInclude
	 * @return void
	 */
	function switchInclude() {

		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$database = JFactory::getDBO();

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
			return;
		}
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				// grab current setting
				$database->setQuery("SELECT `item_exclude` FROM  " . TABLE_YOONIQUEACL_ITEMS
									." WHERE `id` = '$id' ");
				$old = $database->loadResult();
				if ($old == "1") { $new = 0; } else { $new = 1; }
	
				  $query = "UPDATE  " . TABLE_YOONIQUEACL_ITEMS
					."\n SET `item_exclude` = '$new' "
					." WHERE `id` = '$id' "
					;
					$database->setQuery( $query );
					if (!$database->query()) {
						// fail
						 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
						 $msg = JText::_( 'Update' )." ".JText::_( 'Failed' );
						 $this->setRedirect( $link, $msg, 'notice' );
					} else {
						// success
						 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
						 // $msg = JText::_( 'Delete' )." ".JText::_( 'Success' );
						 $this->setRedirect( $link );
					}

			}
		}
		
			
	}
	
	/**
	 * define
	 * @return void
	 */
	function defineItem() {
		$model = &$this->getModel( 'siteitems' );
		$groups = &$this->getModel( 'groups' );
		if (!$model->getItem()) {
			// fail
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=list';
			 $msg = JText::_( 'Invalid' )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg, 'notice' );		
		}
		
		JRequest::setVar( 'view', 'siteitems' );
		JRequest::setVar( 'layout', 'define'  );

		parent::display();

	}

	/**
	 * switch
	 * @return void
	 */
	function switchItemToGroups() {

		$model = &$this->getModel( 'siteitems' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$item = $model->getItem();

		if (!is_array( $cids ) || count( $cids ) < 1) {
			echo "<script> alert('Select an item to enroll'); window.history.go(-1);</script>\n";
			return;
		}
		
		if (count( $cids )) {
			foreach ($cids as $id) {
				if ( $action = $model->switchItemToGroup( $item->id, $id ) ) {
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
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=define&id='.$item->id;
			 $msg = JText::_( 'Switch' )." ".JText::_( 'Failed' )." - ".$fail." ".JText::_( 'Items' );
			 $this->setRedirect( $link, $msg, 'notice' );
		} else {
			// success
			 $link = 'index.php?option='.'com_yooniqueacl'.'&controller=siteitems&task=define&id='.$item->id;
			 //$msg = JText::_( 'Switch' )." ".JText::_( 'Success' );
			 $this->setRedirect( $link, $msg, 'notice' );
		}
				

		
	}
	
	
}
