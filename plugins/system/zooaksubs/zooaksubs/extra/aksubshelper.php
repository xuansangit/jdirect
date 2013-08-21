<?php
/*
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ZL Helper
		The general ZOOlander helper Class for zoo
*/
class AkSubsHelper extends AppHelper {
	
	/**
	 * Gets the level ID out of a level title. If an ID was passed, it simply returns the ID.
	 * If a non-existent subscription level is passed, it returns -1.
	 *
	 * @param $title string|int The subscription level title or ID
	 *
	 * @return int The subscription level ID
	 */
	private static function getId($title)
	{
		static $levels = null;
		
		// Don't process invalid titles
		if(empty($title)) return -1;
		
		// Fetch a list of subscription levels if we haven't done so already
		if(is_null($levels)) {
			$levels = array();
			$list = FOFModel::getTmpInstance('Level','AkeebasubsModel')
					->getList();
					
			if(count($list)) foreach($list as $level) {
				$thisTitle = strtoupper($level->title);
				$levels[$thisTitle] = $level->id;
			}
		}
		
		$title = strtoupper($title);
		if(array_key_exists($title, $levels)) {
			// Mapping found
			return($levels[$title]);
		} elseif( (int)$title == $title ) {
			// Numeric ID passed
			return (int)$title;
		} else {
			// No match!
			return -1;
		}
	}
	
	/**
	 * Checks if a user has a valid, active and payed subscription by that particular ID
	 * 
	 * @param $id int The subscription level ID
	 *
	 * @return bool True if there is such a subscription
	 */
	private static function isTrue($id, $user)
	{
		static $subscriptions = null;
				
		// Don't process empty or invalid IDs
		if(empty($id) || ($id <= 0)) return false;
		
		// Don't process for guests
		if($user->guest) return false;
		
		if(is_null($subscriptions)) {
			$subscriptions = array();
			jimport('joomla.utilities.date');
			$jNow = new JDate();
			$list = FOFModel::getTmpInstance('Subscriptions','AkeebasubsModel')
				->user_id($user->id)
				->expires_from($jNow->toSQL())
				->paystate('C') // and is payed
				//->publish_down($jNow->MySQL())
				->getList();
				
			if(count($list)) foreach($list as $sub) {
				if($sub->enabled)
					if(!in_array($sub->akeebasubs_level_id, $subscriptions))
						$subscriptions[] = $sub->akeebasubs_level_id;
			}
		}
		
		return in_array($id, $subscriptions);
	}
	
	/*
	 *	Save the Level
	*/
	public static function saveLevel( $item ){
	
		// Load extra
		$application = $item->getApplication();
		$template = $application->getTemplate();
		$zoo = App::getInstance('zoo');
		$db = $zoo->database;
		
		// Get the values based on the akeebasubs layout
		$renderer = $zoo->renderer->create('aksubs');
		$renderer->addPath( array($zoo->path->path( "zooaksubs:" )) );
		$renderer->setLayout('akeebalevel');
		$renderer->setItem($item);

		$data = array();
		$positions = $renderer->getPositions( 'item.akeebalevel' );
		foreach ($positions['positions'] as $position => $label){
			$data[$position] = $renderer->renderPosition( $position );
			$data[$position] = empty($data[$position]) ? '' : $data[$position];
		}

		// related level
		$level = self::getRelatedLevel($item->id);

		// if price is detected and level does not exist, continue
		if ( !empty($data['level_price']) && !$level ) {
			// and create level on akeeba!!z
			$leveldata = self::getData($item, $data);
			$newlevel = FOFTable::getAnInstance('Level', 'AkeebasubsTable', 
						array(
							'option' => 'com_akeebasubs'
							)
						);
			$newlevel->akeebasubs_level_id = 0;
			$newlevel->reset();
			$saved = $newlevel->save($leveldata);
			
			// add xref relation
			
			// FOF way. Yay!
			$xref = FOFTable::getAnInstance('LevelItemXref', 'AkeebasubsTable', 
					array(
						'tbl' => '#__akeebasubs_levelitemxref',
						'option' => 'com_akeebasubs', 
						'tbl_key' => 'akeebasubs_levelitemxref_id')
						);
			$xrefdata = array();
			$xrefdata['level_id'] = (int)$newlevel->akeebasubs_level_id;
			$xrefdata['item_id'] = (int)$item->id;

			$saved = $saved && $xref->save($xrefdata);
			
			if($saved){
				$level = $newlevel->akeebasubs_level_id;
				JFactory::getApplication()->enqueueMessage( JText::sprintf('PLG_ZOOAKSUBS_CREATION_LEVEL', $level, $level, $item->name, $item->id) );
			} else {
				JFactory::getApplication()->enqueueMessage( JText::_('PLG_ZOOAKSUBS_CREATION_LEVEL_FAILED').$newlevel->getError() .' '.$xref->getError() );
			}
			
		} elseif ( !empty($data['level_price']) ) {
			
			// otherwise if level exist update
			$leveldata = self::getData($item, $data);

			$lev = FOFModel::getTmpInstance('Levels', 'AkeebasubsModel', array('option' => 'com_akeebasubs'))->getTable();
			// Fix when saving multiple items
			// clone, reset, unload to prevent caching of table object
			$lev = clone $lev;
			$lev->reset();
			$lev->akeebasubs_level_id = 0;
			$lev->load((int)$level);
			$saved = $lev->save($leveldata);
			
			if($saved){
				JFactory::getApplication()->enqueueMessage( JText::sprintf('PLG_ZOOAKSUBS_SYNC_LEVEL', $level, $level, $item->name, $item->id) );
			} else{
				JFactory::getApplication()->enqueueMessage( JText::_('PLG_ZOOAKSUBS_SYNC_LEVEL_FAILED').$lev->getError() );
			}
			
		} else {
			JError::raiseNotice( 100, JText::_('PLG_ZOOAKSUBS_ERROR_PRICE_NOT_SET') );
		}
	}
	
	/*
	 *	Return an array with Level data from Item
	*/
	protected static function getData($item, $data)
	{
		return array(
			'title' 		=> (string)$item->getType()->name.' - '.$data['level_name'],
			'slug'			=> $item->app->string->sluggify($data['level_name']),
			'description' 	=> (string)$data['level_description'],
			'duration' 		=> (int)$data['level_duration'],
			'price' 		=> (float)$data['level_price'],
			'ordertext' 	=> (string)$data['level_ordertext'],
			'canceltext' 	=> (string)$data['level_canceltext'],
			'image' 		=> (string)$data['level_image'],
			'enabled'		=> (int)$item->state,
			'only_once' 	=> (int)$data['level_only_once'],
			'recurring' 	=> (int)$data['level_recurring'],
			'notify1' 		=> (int)$data['level_notify1'],
			'notify2' 		=> (int)$data['level_notify2']
		);
	}
	
	/*
	 *	Delete the Level
	*/
	public static function deleteLevel($level_id, $item_id)
	{
		// delete relation
		$row = FOFTable::getAnInstance('LevelItemXref', 'AkeebasubsTable', 
					array(
						'tbl' => '#__akeebasubs_levelitemxref',
						'option' => 'com_akeebasubs', 
						'tbl_key' => 'level_id')
						);
		$row->load((int)$level_id);
		$row->delete();

		// delete akeeba level
		FOFModel::getTmpInstance('Level','AkeebasubsModel')->setId((int)$level_id)->getItem()->delete();

		// raise notice
		JFactory::getApplication()->enqueueMessage( JText::sprintf('PLG_ZOOAKSUBS_LEVEL_DELETED', $level_id, $item_id) );
	}
	
	/*
	 *	Retrieve the related AkeebaSubs level
	*/
	public static function getRelatedLevel($item_id, $object = false){
		$row = FOFTable::getAnInstance('LevelItemXref', 'AkeebasubsTable', 
					array(
						'tbl' => '#__akeebasubs_levelitemxref',
						'option' => 'com_akeebasubs', 
						'tbl_key' => 'item_id')
						);
		$row->load((int)$item_id);
		
		if ( $level_id = $row->level_id ){
			return $object ? FOFModel::getTmpInstance('Level','AkeebasubsModel')->setId($level_id)->getItem() : $level_id;
		}
		
		return false;
	}
	
	/*
	 *	Retrieve the related ZOO Item
	*/
	public static function getRelatedItem($level_id, $object = false){
		$row = FOFTable::getAnInstance('LevelItemXref', 'AkeebasubsTable', 
					array(
						'tbl' => '#__akeebasubs_levelitemxref',
						'option' => 'com_akeebasubs', 
						'tbl_key' => 'level_id')
						);
		$row->load((int)$level_id);
		
		if ( $item_id = $row->item_id ){
			return $object ? App::getInstance('zoo')->table->item->get($item_id) : $item_id;
		}
		
		return false;
	}
	
	/**
	 *  Checks if the aksubslevelsync element is present in the Item
	 */
	public static function getSyncElement($elements)
	{
		foreach ($elements as $element){
			if ( strtolower($element->getElementType()) == 'aksubslevelsync'){
				return $element;
			}
		}
		return false;
	}

	/**
	 * Get final params
	 *
	 * @param object $element Element getting evaluated
	 * @param data object $params Element position params
	 *
	 * @return data object Final params
	 *
	 * @since 2.6
	 */
	public function getFinalParams($element, $params=array())
	{
		$config = $element->config;
		$params = $element->app->data->create($params);
		$data   = $element->data();
	
		/* === get params from config === */
		$evaluate 				= $config->find('zooaksubs._evaluate', 0);
		$match_method   		= $config->find('zooaksubs._match_method', 0);
		$conditions				= $config->find('zooaksubs.conditions', array());

		/* === override from item === */
		if ($element->getItem() && isset($data['zooaksubs']['_evaluate']))
		{
			$data = $element->app->data->create($data);

			$evaluate 			= 1;
			$match_method   	= $data->find('zooaksubs._match_method', 0);
			$conditions			= $data->find('zooaksubs.conditions', array());
		} 
		/* === override from position === */
		else if ($evaluate || $params->find('zooaksubs._evaluate', 0))
		{
			$evaluate 			= 1;
			$match_method   	= strlen($params->find('zooaksubs._match_method', '')) ? $params->find('zooaksubs._match_method') : $match_method;		
			$conditions			= $this->mergeArrays($conditions, $params->find('zooaksubs.conditions', array()));
		}

		return $element->app->data->create(array('evaluate' => $evaluate, 'match_method' => $match_method, 'conditions' => $conditions));
	}

	/**
	 * mergeArrays
	 *
	 * @param array $Arr1 Array of values
	 * @param array $Arr1 Array of values
	 *
	 * @return merged array
	 *
	 * @since 2.6
	 */
	public function mergeArrays($Arr1, $Arr2)
	{
	  foreach($Arr2 as $key => $Value)
	  {
	    if(array_key_exists($key, $Arr1) && is_array($Value))
	      $Arr1[$key] = $this->mergeArrays($Arr1[$key], $Arr2[$key]);
	    else
	      $Arr1[$key] = $Value;
	  }
	  return $Arr1;
	}

	/**
	 * evaluateLevels
	 *
	 * @param array $params Array of options
	 * @param object $element Element object being rendered
	 *
	 * @return boolean
	 *
	 * @since 2.6
	 */
	public function evaluateLevels($params, $element, $user)
	{
		// if no levels selected allow access
		if(empty($params['_levels'])) return true;

		// if selected set author as user
		if (isset($params['_user']) && $params['_user']) {
			$user = $this->app->user->get($element->getItem()->created_by);
		}

		// if user not valid, deny access
		if (empty($user)) return false;

		// init vars
		$levels = $params['_levels'];
		$mode   = $params['_mode'];

		// "Use Item as Level", get related Level ID if the Sync element is present and enabled
		if(in_array('itemaslevel', $levels))
		{
			$item = $element->getItem();
			$syncEl = $element->app->aksubs->getSyncElement($item->getElements());
			if ($syncEl && $syncEl->get('value') == 1) {
				$levels[0] = self::getRelatedLevel( $item->id );
			} else {
				// sync is disabled, remove option
				unset($levels[0]);
			}
		}

		// evaluate
		$render = false;  
		if ($mode==1) // if AND
		{
			foreach($levels as $key => $level){			
				if(self::isTrue($level, $user)){
					unset($levels[$key]);
				}
			}
			
			// if empty means user have sub for all selected levels
			if(empty($levels)){
				$render = true;
			}
		}
		if ($mode==0) // if OR
		{
			foreach($levels as $level){
				if(self::isTrue($level, $user)) {
					$render = true;
				}
			}
		}

		return $render;
	}

	/**
	 * evaluatePackages
	 *
	 * @param array $params Array of options
	 * @param object $element Element object being rendered
	 *
	 * @return boolean
	 *
	 * @since 2.6
	 */
	public function evaluatePackages($params, $element, $user)
	{
		// if no packages selected allow access
		if(empty($params['_packages'])) return true;

		// if selected set author as user
		if (isset($params['_user']) && $params['_user']) {
			$user = $this->app->user->get($element->getItem()->created_by);
		}

		// if user not valid, deny access
		if (empty($user)) return false;

		// init vars
		$levels   = array();
		$packages = $params['_packages'];
		$mode     = $params['_mode'];

		// get Levels from packages
		foreach ($packages as $package){
			if ($element = $element->getItem()->getElement( $package ))
			{
				if($element->getElementType() == 'relateditems')
				{
					if($element->data()) foreach(array_shift($element->data()) as $item_id){
						$levels[] = self::getRelatedLevel( $item_id );	
					}
				}
				else if($element->getElementType() == 'relateditemspro')
				{
					foreach($element->getRelatedItems() as $item){
						$levels[] = self::getRelatedLevel( $item->id );	
					}
				}
			}
		}

		// evaluate
		$render = false; 
		if(!empty($levels)){
			if ($mode==1) // if AND
			{
				foreach($levels as $key => $level){			
					if(self::isTrue($level, $user)){
						unset($levels[$key]);
					}
				}
				
				// if empty means user have sub for all selected levels
				if(empty($levels)){
					$render = true;
				}
			}
			if ($mode==0) // if OR
			{
				foreach($levels as $level){
					if(self::isTrue($level, $user)) {
						$render = true;
					}
				}
			}
		}

		return $render;
	}
	
	
	/* Subscriptions
	==========================================================================================================*/

	/*
	 *	get/set subsription params
	 */
	public static function subParam($sub, $param, $value = null)
	{
		$zoo	  = App::getInstance('zoo'); // important
		$sub 	  = is_object($sub) ? $sub : FOFModel::getTmpInstance('Subscriptions','AkeebasubsModel')->setId($sub)->getItem();
		$params	  = (array)json_decode($sub->params, true);
 		$myParams = $zoo->data->create(array_key_exists('plg_zooaksubs', $params) ? $params['plg_zooaksubs'] : array());
	
		// set the value param
		if(!empty($value)) {
			$myParams[$param] = $value;
			$params['plg_zooaksubs'] = $myParams;
			$sub->params = json_encode($params);
			$sub->save(array());
		} else {
			// retrieve the param
			return $myParams->get($param, '');
		}
	}

}