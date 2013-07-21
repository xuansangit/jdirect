<?php

/**
 * @version     $Id: controller.php 2013-04-24 14:38:00Z gibiwatch $
 * @package     JMonitoring Client
 * @subpackage  Backend
 * @author      Watchful
 * @authorUrl   http://www.watchful.li
 * @copyright   (c) 2013, Watchful
 */

// No direct access to this file
defined('WATCHFULLI_PATH') or die;

require_once WATCHFULLI_PATH.'/classes/controller.php';
require_once WATCHFULLI_PATH.'/classes/watchfulli.php';

/**
 * General Controller of client component
 */
class watchfulliController extends WatchfulliBaseController
{

    /**
     * display task
     *
     * @param   boolean  $cachable   If true, the view output will be cached 
     * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     * @return  void
     */
    function display($cachable = false, $urlparams = array())
    {
        // set default view if not set
        if ('1.5' == Watchfulli::joomla()->RELEASE)
        {
            JRequest::setVar('view', JRequest::getCmd('view', 'watchfulli'));
        }
        else
        {
            $app = JFactory::getApplication();
            $app->input->set('view', $app->input->get('view', 'watchfulli'));
        }
        // call parent behavior
        parent::display($cachable);
    }

}