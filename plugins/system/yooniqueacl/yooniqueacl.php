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

/** Import library dependencies */
jimport('joomla.plugin.plugin');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.environment.browser' );

class plgSystemYooniqueacl extends JPlugin {

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgSystemYooniqueacl(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
	}


	/**
	 *
	 * @return unknown_type
	 */
	function onAfterRoute()
	{
		$application = JFactory::getApplication();

		if ($application->isAdmin()) {
			return;
		}

		// First check that Yooniqueacl is installed, and if not, then return
		if ( !JFile::exists( JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/helpers/yooniqueacl.php' ) ) {
			return;
		}

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

		if (!class_exists("YooniqueaclConfigFile")) return;

		// get the user
		$user = JFactory::getUser();

		// set the site & query
		$uri =& JURI::getInstance();
		$option	= JRequest::getCmd( 'option' );
		if (!$option) {
			return;
		}
		$option_exist = JComponentHelper::isEnabled($option, True);
		if (!$option_exist) {
			return;
		}
		if ($application->isAdmin()) {
			$site = 'administrator';
			$parsed_url = YooniqueaclHelper::getQueryFromRequest( 'administrator' );
		} else {
			$site = 'site';
			$parsed_url = YooniqueaclHelper::getQueryFromRequest( 'site' );
		}

		$query = $uri->buildQuery( $parsed_url );


		$details = new stdClass();
		$details->user 	= $user;
		$details->query = $query;
		$details->site 	= $site;

		$result = $this->_checkAccessRights( $details );

		$access = @$result->access;

			$debug = $this->params->get( 'debug', '0' );
			if ($debug) {
				if ($user->id == $this->params->get( 'debugid', '0' )) {
					$debugid = $user->id;
					$debugAccess = $result;
					$cleanquery = YooniqueaclHelper::_cleanQuery( $query, $site );
					$thisMsg = "YooniqueaclSystemPlugin **Access for Userid {$debugid}: ";
					$thisMsg.= $debugAccess->access ? JText::_("Yes")." " : JText::_("No")." ";
					$thisMsg.= "**Site: {$site} ";
					$thisMsg.= "**URL Query: {$query} ";
					$thisMsg.= "**Site Item Query: {$cleanquery->query} ";
					JError::raiseNotice( 'errorMessage', $thisMsg );
				}
			}

		// Allow restrictions to be disabled on either/both sides of site
		$app = &JFactory::getApplication();
		if ($app->isAdmin()) {
			$adminside = $this->params->get( 'adminside', '0');
			if (!$adminside) {
				$access = true;
			}
		} elseif ($app->isSite()) {
			$frontend = $this->params->get( 'frontend', '0');
			if (!$frontend) {
				$access = true;
			}
		}

		// if no access, fire an error page
		if ( !$access ) {
			// append joomla base url if relative path is used
			if (!preg_match('/^http/', $result->ce_url)) {
				$result->ce_url	= JURI::root() . $result->ce_url;
			}
			// Add this to params page
			$displayErrorMessage = $this->params->get( 'displayErrorMessage', '1' );
			$errorMessage = $this->params->get( 'errorMessage', 'You are not authorized to access the requested resource' );
			if ($displayErrorMessage) {
				$app->redirect( $result->ce_url, JText::_( $errorMessage ), 'message' );
			} else {
				$app->redirect( $result->ce_url );
			}
			return;
		}
	}

	function _checkAccessRights( $details ) {
		// use config to determine $success default value--whitelist=true, blacklist=false
		$success = false;
		$args = array();

		$return = new stdClass();
		$return->access 	= $success;			// Access Boolean
		$return->error 		= false;			// Error Boolean
		$return->errorMsg 	= 'DEFAULT';		// Error Message
		$return->ce_url 	= JURI::root(); 	// Error URL for Site Item

		$config = &YooniqueaclConfig::getInstance();
		$defaultCustomErrorUrl = $config->get( 'default_ce', JURI::root() );
		$defaultAdminCustomErrorUrl = $config->get( 'admin_default_ce', JURI::root() );

		// prepare arguments
		$args['user']	= $details->user;
		$args['site']	= $details->site;

		// clean the query
		$cleanquery = YooniqueaclHelper::_cleanQuery( $details->query, $details->site );
		if (!empty($cleanquery->error)) {
			$return->error = true;
			$return->errorMsg = $cleanquery->errorMsg;
			return $return;
		}

		$args['query']	= $cleanquery->query;
		$args['option']	= $cleanquery->option;

		// now send the data to yooniqueacl::rightsCheck, which checks permissions
		$access = YooniqueaclHelper::rightsCheck( $args );

		// if error_url_published, set redirect there
		if ( (isset($access->error_url_published)) && (intval($access->error_url_published) == '1') && ($access->error_url) )
		{
			// set redirect to custom error URL
			$return->ce_url = $access->error_url;
		} else {
			// set redirect to default error URL
			if ($details->site == 'site') {
				$return->ce_url = $defaultCustomErrorUrl;
			} else {
				$return->ce_url = $defaultAdminCustomErrorUrl;
			}
		}

		// if user has access
		if (isset($access->access) && $access->access) {
			$return->access = true;
			$return->errorMsg = '';
		}

		return $return;
	}

}

