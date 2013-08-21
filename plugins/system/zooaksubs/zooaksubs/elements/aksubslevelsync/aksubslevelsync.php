<?php
/**
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementAkeebaSubs
*/
class ElementAksubsLevelSync extends ElementPro {


	/*
		Function: hasValue
			Override. Checks if the element's value is set.

	   Parameters:
			$params - AppData render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$default = $this->config->get('default', 0);
		$value = $this->get('value', $default);
		$level = $this->app->aksubs->getRelatedLevel($this->_item->id, true);
		return !empty($value) && !empty($level);
	}
	
	/*
		Function: syncItem
			Override. Checks if the element's value is set.

	   Parameters:
			$params - AppData render parameter

		Returns:
			Boolean - true, on success
	*/
	public function syncItem($params = array()) {
		$default = $this->config->get('default', 0);
		$value = $this->get('value', $default);
		return !empty($value);
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - AppData render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {
		// set default
		$default = $this->config->get('default', 0);
		if ($default != '' && $this->_item != null && $this->_item->id == 0) {
			$this->set('value', $default);
		}
		
		$params = $this->app->data->create($params);
		$level  = $this->app->aksubs->getRelatedLevel($this->_item->id, true);
		
		// render layout
		if ($layout = $this->getLayout('render/'.$params->find('layout._layout', 'default.php'))) {
			return $this->renderLayout($layout, array(
				'params' => $params, 'level' => $level)
			);
		}
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

        if ($layout = $this->getLayout('edit/edit.php')) {
            return $this->renderLayout($layout,
                array(
					
                )
            );
        }
	}

}