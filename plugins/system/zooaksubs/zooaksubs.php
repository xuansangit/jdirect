<?php
/*
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgSystemZooAkSubs extends JPlugin {
	
	public $joomla;
	public $app;
	
	/**
	 * onAfterInitialise handler
	 *
	 * Adds ZOO event listeners
	 *
	 * @access public
	 * @return null
	 */
	function onAfterInitialise()
	{
		// Get Joomla instances
		$this->joomla = JFactory::getApplication();
		$jlang = JFactory::getLanguage();
		
		// load default and current language
		$jlang->load('plg_system_zooaksubs', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_system_zooaksubs', JPATH_ADMINISTRATOR, null, true);

		// check dependences
		if (!defined('ZLFW_DEPENDENCIES_CHECK_OK')){
			$this->checkDependencies();
			return; // abort
		}

		// Get the ZOO App instance
		$this->app = App::getInstance('zoo');
		
		// register plugin path
		if ( $path = $this->app->path->path( 'root:plugins/system/zooaksubs/zooaksubs' ) ) {
			$this->app->path->register($path, 'zooaksubs');
		}
		
		// register elements path
		if ( $path = $this->app->path->path( 'zooaksubs:elements' ) ){
			$this->app->path->register( $path, 'elements' );
		}
		
		// register fields - necesary since ZOO 2.5.13
		if ( $path = $this->app->path->path( 'zooaksubs:fields' ) ) {
			$this->app->path->register($path, 'fields');
		}
			
		// register helpers
		if ( $path = $this->app->path->path( 'zooaksubs:extra' ) ) {
			$this->app->path->register($path, 'helpers');

			$this->app->loader->register('AkSubsHelper', 'helpers:aksubshelper.php');
			$this->app->loader->register('AKSHTMLHelper', 'helpers:akshtmlhelper.php');

			// Renderer
			$this->app->loader->register('AkSubsRenderer', 'helpers:aksubsrenderer.php');
		}
		
		// register events
		$this->app->event->dispatcher->connect('item:saved', array($this, 'saveItemAsLevel'));
		$this->app->event->dispatcher->connect('item:deleted', array($this, 'deleteRelatedLevel'));
		$this->app->event->dispatcher->connect('element:configparams', array($this, 'addElementConfig'));
		$this->app->event->dispatcher->connect('element:configform', array($this, 'configForm'));
		$this->app->event->dispatcher->connect('element:beforedisplay', array($this, 'beforeElementDisplay'));
		$this->app->event->dispatcher->connect('element:beforesubmissiondisplay', array($this, 'beforeElementDisplay'));
		$this->app->event->dispatcher->connect('element:download', array($this, 'canDownload'));
		$this->app->event->dispatcher->connect('element:afteredit', array($this, 'afterEdit'));
		$this->app->event->dispatcher->connect('layout:init', array($this, 'initTypeLayouts'));
	}
	
	/*
		Function: initTypeLayouts
			Callback function for the zoo layouts

		Returns:
			void
	*/
	public function initTypeLayouts($event)
	{
		$extensions = (array) $event->getReturnValue();
		
		// add plugin layout
		$extensions[] = array('name' => 'ZOOaksubs Plugin', 'path' => $this->app->path->path('zooaksubs:'), 'type' => 'plugin');
		
		$event->setReturnValue($extensions);
	}

	/**
	 * Save the Item as level
	 */
	function saveItemAsLevel($event)
	{
		$item 	 = $event->getSubject();
		$element = $this->app->aksubs->getSyncElement($item->getElements());
		
		// Is this enabled as a level?
	 	if ( $element && $element->syncItem() ){
			// Save to Akeeba!!
			$this->app->aksubs->saveLevel( $item );
		}
	}
	
	/**
	 * On Item deletion remove relation and associated level
	 */
	function deleteRelatedLevel($event)
	{
		$item = $event->getSubject();

		// search for related level and delete both
		$level_id = $this->app->aksubs->getRelatedLevel($item->id);
		
		if ($level_id) {
			// delete both relation and level
			$this->app->aksubs->deleteLevel($level_id, $item->id);
		}
	}
	
	/**
	 * Add levels parameter to the form
	 */
	public function configForm( $event, $arguments = array() ){
		
		$config = $event['form'];
		
		// add XML params path
		$config->addElementPath($this->app->path->path('zooaksubs:fields'));
	}
	
	/** 
	 * New method for adding params to the element
	 * @since 2.5
	 */
	public function addElementConfig($event)
	{
		// Custom Params File
		$file = $this->app->path->path( 'zooaksubs:element.xml');
		$xml = simplexml_load_file( $file );
		
		// Old params
		$params = $event->getReturnValue();
		// add new params from custom params file
		$params[] = $xml->asXML();

		$event->setReturnValue($params);
	}
	
	/**
	 * Evaluates if the element should be rendered
	 */
	public function beforeElementDisplay($event)
	{
		$item	 = $event->getSubject();
		$element = $event['element'];
		$params  = $event['params'];
		$user    = isset($event['user']) ? $event['user'] : $this->app->user->get();
		
		// get the final overided params
		$akparams = $this->app->aksubs->getFinalParams($element, $params);

		// perform only if evaluate is checked
		if($akparams->get('evaluate'))
		{
			// avoid rendering if result is false
			if(!$this->evaluate($akparams->get('conditions', array()), $akparams->get('match_method', 1), $element, $user)){
				$event['render'] = false;
			}
		}
	}

	/**
	 * Evaluates if the file can be downloaded - it only take in consideration the Config and Item data, not positions
	 */
	public function canDownload($event)
	{
		$element = $event->getSubject();
		$check   = $event['check'];
		$item	 = $element->getItem();
		$user    = isset($event['user']) ? $event['user'] : $this->app->user->get();
		
		// get the final overided params
		$akparams = $this->app->aksubs->getFinalParams($element);

		// perform only if evaluate is checked
		if($akparams->get('evaluate'))
		{
			// avoid rendering if result is false
			if(!$this->evaluate($akparams->get('conditions', array()), $akparams->get('match_method', 1), $element, $user)){
				$event['canDownload'] = false;
			}
		}
	}

	/**
	 * it evaluates the current user access with passed params, used in elements evaluation
	 */
	public function evaluate($conditions, $match_method, $element, $user)
	{
		$render = true; // initially we assume it should be rendered

		// evaluate conditions if assignement is not ignored
		foreach($conditions as $key => $condition) if(strlen($condition['_assignto']) && $condition['_assignto'] != 0)
		{
			$function = 'evaluate'.ucfirst($key);
			if($match_method == 1) // if at least one of conditions NOT met, don't render the element
			{
				if(!$this->app->aksubs->$function($condition, $element, $user))
				{
					// if exclude assignment return oposite
					$render = $condition['_assignto'] == '2' ? true : false;
					break;
				}
			}
			else if($match_method == 0) // if at least one of conditions DO met, render the element
			{
				if($this->app->aksubs->$function($condition, $element, $user))
				{
					// if exclude assignment return oposite
					$render = $condition['_assignto'] == '2' ? false : true;
					break;
				} else {
					$render = $condition['_assignto'] == '2' ? true : false;
				}
			}
		}

		return $render;
	}
	
	/**
	 * add params for after edit
	 */
	public function afterEdit($event)
	{	
		$element 	  = $event->getSubject();
		$config 	  = $element->config;
		$itemoveride  = $config->find('zooaksubs._itemoveride', 0);
		$setOnConfig  = false;
		$enviroment   = $this->app->zlfield->getTheEnviroment();

		if ($element->getElementType() != 'aksubslevelsync' && $itemoveride)
		{	
			// load assets
			$this->app->document->addStyleSheet('zooaksubs:assets/zooaksubs.css');
			$this->app->zlfw->loadLibrary('zlparams');
			$this->app->zlfw->loadLibrary('qtip');

			$html = $event['html']; // set $event['html'] after modifying the html
			$last = array_pop($html); // remove last html (</div>)

				// init var
				$type 	 = $element->getType();
				$params  = $this->app->data->create(array());
				$isCore  = $element->getGroup() == 'Core';
				$fname   = 'zooaksubs';
				$setOnConfig = $config->find($fname.'._evaluate', 0);

				// set arguments
				$arguments = compact('type', 'element', 'config', 'params', 'fname', 'setOnConfig');

				// get json from file
				$json = include($this->app->path->path('fields:zooaksubs.json.php'));

				// set ctrl
				$ctrl = 'elements['.$element->identifier.'][zooaksubs]';

				// render
				$html[] = $this->app->zlfield->render(array($json, $ctrl, array(), '', false, $arguments), false, JText::_('PLG_ZOOAKSUBS_AKSUBS_EVALUATION'), array(), 'zooaksubs');

			$html[] = $last;
			$event['html'] = $html;
		}
	}

	/*
	 *  checkDependencies
	 */
	public function checkDependencies()
	{
		if($this->joomla->isAdmin())
		{
			// if ZLFW not enabled
			if(!JPluginHelper::isEnabled('system', 'zlframework') || !JComponentHelper::getComponent('com_zoo', true)->enabled) {
				$this->joomla->enqueueMessage(JText::_('PLG_ZOOAKSUBS_ZLFW_MISSING'), 'notice');
			} else {
				// load zoo
				require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

				// fix plugins ordering
				$zoo = App::getInstance('zoo');
				$zoo->loader->register('ZlfwHelper', 'root:plugins/system/zlframework/zlframework/helpers/zlfwhelper.php');
				$zoo->zlfw->checkPluginOrder('zooaksubs');
			}
		}
	}
}