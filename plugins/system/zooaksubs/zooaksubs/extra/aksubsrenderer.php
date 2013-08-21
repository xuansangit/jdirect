<?php
/*
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
 
App::getInstance('zoo')->loader->register('ItemRenderer', 'classes:renderer/item.php');

/*
	Class: TiendaItemRenderer
		The class for rendering items and its assigned positions.
 */
class AkSubsRenderer extends ItemRenderer {
	
	/*
	    Function: checkPosition
	        Check if position generates output.
	
	    Parameters:
	        $position - Position name.
	
	    Returns:
	        Boolean
	 */
	public function checkPosition( $position ){
		
		foreach ( $this->_getConfigPosition( $position ) as $data ){
			if ( $element = $this->_item->getElement($data->element) ){
				return true;
			}
		}
		
		return false;
	}
	
	/*
	    Function: renderPosition
	        Returns the value of the elements in the given position
	
	    Parameters:
	        $position - Position name.
	        $args - Arguments to be passed to into the position scope.
	
	    Returns:
	        the values
	 */
	public function renderPosition( $position, $args = array() ){
		// init vars
		$elements = array();
		
		// render elements
		foreach ($this->_getConfigPosition($position) as $data) {
            if ($element = $this->_item->getElement($data['element'])) {

                // set params
                $params = array_merge($data, $args);

                // check value
                if ($element->hasValue($this->app->data->create($params))) {
                    $elements[] = compact('element', 'params');
                }
            }
        }
		
		$data = array();
		foreach ($elements as $e){
		
			$params  = $this->app->data->create($e['params']);
		
			$type = strtolower( $e['element']->getElementType() );
			$group = strtolower( $e['element']->getGroup() );
			
			if ($group == 'core'){
				switch ($type){
					case 'itemcategory':
						$categories = $this->_item->getRelatedCategories( true );
						$cats = array( );
						foreach ($categories as $c){
							$cats[] = $c->name;
						}
						$data = $cats;
						break;
					case 'itemname':
						$data = $this->_item->name;
						break;
					default:
						$value = $e['element']->render($params);
						if (!empty($value)){
							$data = $value;
						}
						break;
				}
			}
			else
			{
				switch ($type){
					case 'relateditems':
						$data = $e['element']->get('item');
						$data = @$data[0];
						break;
					case 'image':
						if ( strlen($e['element']->get('file')) ){
							$data = JFile::getName($e['element']->get( 'file' ));
						} else {
							$data = '';
						}
						break;
					case 'imagepro':
						$e['element']->rewind(); // rewind to seek allways the first value
						if ( strlen($e['element']->get('file')) ){
							$data = JFile::getName($e['element']->get( 'file' ));
						} else {
							$data = '';
						}
						break;
					case 'checkbox':
					case 'radio':
					case 'select':
					case 'selectpro':
						$data = $e['element']->get('option');
						$data = @$data[0];
						break;
					default:
						$value = $e['element']->render($params);
						if (!empty($value)){
							$data = $value;
						}
						break;
				}
			}
		}
		
		return $data;
	}
	
	public function setLayout($layout){
		$this->_layout = $layout;
	}
	
	public function setItem($item){
		$this->_item = $item;
	}
	
}