<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
defined('WATCHFULLI_PATH') or die;

require_once WATCHFULLI_PATH . '/classes/controller.php';
require_once WATCHFULLI_PATH . '/classes/actions.php';
 
class watchfulliController extends WatchfulliBaseController
{
  function doUpdate()
  {
    $action = new watchfulliActions();
    $action->doUpdate();
  }
  
  function test()
  {
    $action = new watchfulliActions();
    $action->test();
  }
}
