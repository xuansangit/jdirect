<?php
/*
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

	// init vars
	$fname	 			= 'zooaksubs';
	$config  			= $element->config;
	$isCore  			= $element->getGroup() == 'Core';
	$evaluated 			= $config->find($fname.'._evaluate', 0);
	$overrided    		= isset($params) ? $params->find($fname.'._evaluate', 0) : 0;
	$conditions 		= array();
	$label_evaluate 	= 'PLG_ZOOAKSUBS_EVALUATE';
	$help_evaluate 		= 'PLG_ZOOAKSUBS_EVALUATE_DESC';


	// change evaluate field text if overiding
	if ($evaluated && $enviroment == 'type-positions')
	{		
		$label_evaluate = 'PLG_ZLFRAMEWORK_OVERRIDE';
		$help_evaluate = '';
	}

	/*
	 * ========== Akeeba Subs Levels ==========
	 */
	$levels = array();
	if($cache = $this->app->zlfield->cache->get('zooaksubs.akeeba-subs-levels'))
	{
		$levels = $cache;
	}
	else
	{
		// Let's add the Special "Item As Level" option if Element present
		$levels = array();
		if ($this->app->aksubs->getSyncElement($type->getElements())) $levels['PLG_ZOOAKSUBS_ITEM_AS_LEVEL'] = 'itemaslevel';
		
		/* Levels */ // get levels tree list and populate array
		$list = FOFModel::getTmpInstance('Level', 'AkeebasubsModel')->enabled(1)->getList();
		foreach ($list as $lv) $levels[$lv->title] = $lv->akeebasubs_level_id;

		// store the value on cache var
		$this->app->zlfield->cache->set('zooaksubs.akeeba-subs-levels', $levels);
	}

	$conditions[] =
	'"levels_fieldset": {
		"type":"fieldset",
		"control":"levels",
		"fields": {
			"_assignto":{
				"type":"radio",
				"label":"PLG_ZOOAKSUBS_LEVELS",
				"help":"PLG_ZOOAKSUBS_LEVELS_DESC||PLG_ZLFRAMEWORK_ACC_ASSIGN_DESC",
				"default":"0",
				"class":"special",
				"specific":{
					"options":{
						"PLG_ZLFRAMEWORK_ACC_SELECTION":"1",
						"PLG_ZLFRAMEWORK_ACC_EXCLUDE_SELECTION":"2",
						"PLG_ZLFRAMEWORK_ACC_IGNORE":"0"
					}
				},
				"dependents":"levels_params > 1 OR 2"
				'.($evaluated && $enviroment == 'type-positions' ? ',
				"state": {
					"init_state":"0",
					"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
				},
				"data_from_config":"'.($params->find($fname.'.conditions.levels._assignto_state') ? 0 : 1).'"' : '').'
			},
			"levels_params": {
				"type":"wrapper",
				"fields": {
					"_levels":{
						"type":"select",
						"label":"PLG_ZOOAKSUBS_LEVELS",
						"help":"PLG_ZOOAKSUBS_LEVELS_LEVELS_DESC",
						"specific": {
							"options": '.json_encode($levels).',
							"multi":"true"
						}
						'.($evaluated && $enviroment == 'type-positions' ? ',
						"state": {
							"init_state":"0",
							"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
						},
						"data_from_config":"'.($params->find($fname.'.conditions.levels._levels_state') ? 0 : 1).'"' : '').'
					},
					"_mode":{
						"type":"radio",
						"label":"PLG_ZLFRAMEWORK_ACC_MODE",
						"help":"PLG_ZLFRAMEWORK_ACC_MODE_DESC",
						"specific": {
							"options": {
								"PLG_ZLFRAMEWORK_AND":"1",
								"PLG_ZLFRAMEWORK_OR":"0"
							}
						},
						"default":"0"
						'.($evaluated && $enviroment == 'type-positions' ? ',
						"state": {
							"init_state":"0",
							"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
						},
						"data_from_config":"'.($params->find($fname.'.conditions.levels._mode_state') ? 0 : 1).'"' : '').'
					},
					"_user":{
						"type":"radio",
						"label":"PLG_ZOOAKSUBS_USER",
						"help":"PLG_ZOOAKSUBS_USER_DESC",
						"specific": {
							"options": {
								"PLG_ZOOAKSUBS_USER":"0",
								"PLG_ZOOAKSUBS_AUTHOR":"1"
							}
						},
						"default":"0"
						'.($evaluated && $enviroment == 'type-positions' ? ',
						"state": {
							"init_state":"0",
							"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
						},
						"data_from_config":"'.($params->find($fname.'.conditions.levels._user_state') ? 0 : 1).'"' : '').'
					}
				}
			}
		}
	}';


	/*
	 * ========== Levels Packages ==========
	 */
	$conditions[] =
	'"packages_fieldset": {
		"type":"fieldset",
		"control":"packages",
		"fields": {
			"_assignto":{
				"type":"radio",
				"label":"PLG_ZOOAKSUBS_PACKAGES",
				"help":"PLG_ZOOAKSUBS_PACKAGES_DESC||PLG_ZLFRAMEWORK_ACC_ASSIGN_DESC",
				"default":"0",
				"class":"special",
				"specific":{
					"options":{
						"PLG_ZLFRAMEWORK_ACC_SELECTION":"1",
						"PLG_ZLFRAMEWORK_ACC_EXCLUDE_SELECTION":"2",
						"PLG_ZLFRAMEWORK_ACC_IGNORE":"0"
					}
				},
				"dependents":"packages_params > 1 OR 2"
				'.($evaluated && $enviroment == 'type-positions' ? ',
				"state": {
					"init_state":"0",
					"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
				},
				"data_from_config":"'.($params->find($fname.'.conditions.packages._assignto_state') ? 0 : 1).'"' : '').'
			},
			"packages_params": {
				"type":"wrapper",
				"fields": {
					"_packages":{
						"type":"elements",
						"label":"PLG_ZOOAKSUBS_PACKAGES_RI",
						"help":"PLG_ZOOAKSUBS_PACKAGES_RI_DESC",
						"specific": {
							"multi":"true",
							"elements":"relateditemspro relateditems",
							"types":"'.$type->id.'",
							"apps":"'.$type->getApplication()->getGroup().'"
						}
						'.($evaluated && $enviroment == 'type-positions' ? ',
						"state": {
							"init_state":"0",
							"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
						},
						"data_from_config":"'.($params->find($fname.'.conditions.packages._levels_state') ? 0 : 1).'"' : '').'
					},
					"_mode":{
						"type":"radio",
						"label":"PLG_ZLFRAMEWORK_ACC_MODE",
						"help":"PLG_ZLFRAMEWORK_ACC_MODE_DESC",
						"specific": {
							"options": {
								"PLG_ZLFRAMEWORK_AND":"1",
								"PLG_ZLFRAMEWORK_OR":"0"
							}
						},
						"default":"0"
						'.($evaluated && $enviroment == 'type-positions' ? ',
						"state": {
							"init_state":"0",
							"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
						},
						"data_from_config":"'.($params->find($fname.'.conditions.packages._mode_state') ? 0 : 1).'"' : '').'
					},
					"_user":{
						"type":"radio",
						"label":"PLG_ZOOAKSUBS_USER",
						"help":"PLG_ZOOAKSUBS_USER_DESC",
						"specific": {
							"options": {
								"PLG_ZOOAKSUBS_USER":"0",
								"PLG_ZOOAKSUBS_AUTHOR":"1"
							}
						},
						"default":"0"
						'.($evaluated && $enviroment == 'type-positions' ? ',
						"state": {
							"init_state":"0",
							"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
						},
						"data_from_config":"'.($params->find($fname.'.conditions.packages._user_state') ? 0 : 1).'"' : '').'
					}
				}
			}
		}
	}';


	/*
	 * ========== JSON ==========
	 */
	return
	'{"fields": {
	
		'.($enviroment == 'type-edit' ? '
		"_itemoveride":{
			"type": "checkbox",
			"label": "PLG_ZOOAKSUBS_ITEM_OVERRIDE",
			"help": "PLG_ZOOAKSUBS_ITEM_OVERRIDE_DESC",
			"specific":{
				"label":"JYES"
			}
		},' : '').'
		
		"_evaluate":{
			"type": "checkbox",
			"label": "'.$label_evaluate.'",
			"help": "'.$help_evaluate.'",
			"specific":{
				"label":"JYES"
			},
			"dependents": "_options_wrapper > 1"
		},

		"_options_wrapper":{
			"type": "wrapper",
			"fields": {

				'./* conditions matching method */'
				"_match_method":{
					"type":"radio",
					"label":"PLG_ZLFRAMEWORK_ACC_MATCHING_METHOD",
					"help":"PLG_ZLFRAMEWORK_ACC_MATCHING_METHOD_DESC",
					"default":"0",
					"specific": {
						"options": {
							"PLG_ZLFRAMEWORK_ALL":"1",
							"PLG_ZLFRAMEWORK_ANY":"0"
						}
					}
					'.($evaluated && $enviroment == 'type-positions' ? ',
					"state": {
						"init_state":"0",
						"label":"PLG_ZLFRAMEWORK_OVERRIDE_THIS_FIELD"
					},
					"data_from_config":"'.($params->find($fname.'._match_method_state') ? 0 : 1).'"' : '').'
				},

				'./* conditions */'
				"conditions_wrapper": {
					"type":"control_wrapper",
					"control":"conditions",
					"fields": {
						'.implode(',', $conditions).'
					}
				}
				
			}
		}
		
	}}';

?>