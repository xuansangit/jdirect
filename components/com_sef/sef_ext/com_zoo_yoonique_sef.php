<?php
/*
 * @version   2.1.1 Sat Apr 21 19:16:52 2012 -0700
 * @package   yoonique zoo plugin for JoomSEF and sh404sef
 * @author    yoonique[.]net
 * @copyright Copyright (C) yoonique[.]net and all rights reserved.
 * @license   http://www.gnu.org/licenses/gpl.html
 */


defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if (!defined('ZOO_SEF_ALPHAINDEX')) {
	! $pluginParams->get('ZOO_SEF_ALPHAINDEX_SHOW') ? define('ZOO_SEF_ALPHAINDEX', '') : define('ZOO_SEF_ALPHAINDEX',  $pluginParams->get('ZOO_SEF_ALPHAINDEX'));
	! $pluginParams->get('ZOO_SEF_CATEGORY_SHOW') ? define('ZOO_SEF_CATEGORY', '') : define('ZOO_SEF_CATEGORY',  $pluginParams->get('ZOO_SEF_CATEGORY'));
	! $pluginParams->get('ZOO_SEF_FEED_SHOW') ? define('ZOO_SEF_FEED', '') : define('ZOO_SEF_FEED',  $pluginParams->get('ZOO_SEF_FEED'));
	! $pluginParams->get('ZOO_SEF_FRONTPAGE_SHOW') ? define('ZOO_SEF_FRONTPAGE', '') : define('ZOO_SEF_FRONTPAGE',  $pluginParams->get('ZOO_SEF_FRONTPAGE'));
	! $pluginParams->get('ZOO_SEF_ITEM_SHOW') ? define('ZOO_SEF_ITEM', '') : define('ZOO_SEF_ITEM',  $pluginParams->get('ZOO_SEF_ITEM'));
	! $pluginParams->get('ZOO_SEF_SUBMISSION_SHOW') ? define('ZOO_SEF_SUBMISSION', '') : define('ZOO_SEF_SUBMISSION',  $pluginParams->get('ZOO_SEF_SUBMISSION'));
	! $pluginParams->get('ZOO_SEF_MYSUBMISSIONS_SHOW') ? define('ZOO_SEF_MYSUBMISSIONS', '') : define('ZOO_SEF_MYSUBMISSIONS',  $pluginParams->get('ZOO_SEF_MYSUBMISSIONS'));
	! $pluginParams->get('ZOO_SEF_TAG_SHOW') ? define('ZOO_SEF_TAG', '') : define('ZOO_SEF_TAG',  $pluginParams->get('ZOO_SEF_TAG'));
	! $pluginParams->get('ZOO_SEF_PAGE_SHOW') ? define('ZOO_SEF_PAGE', '') : define('ZOO_SEF_PAGE',  $pluginParams->get('ZOO_SEF_PAGE'));
	! $pluginParams->get('ZOO_SEF_DATE') ? define('ZOO_SEF_DATE', '') : define('ZOO_SEF_DATE',  $pluginParams->get('ZOO_SEF_DATE'));
	! $pluginParams->get('ZOO_SEF_DATE_FORMAT') ? define('ZOO_SEF_DATE_FORMAT', '') : define('ZOO_SEF_DATE_FORMAT',  $pluginParams->get('ZOO_SEF_DATE_FORMAT'));
	! $pluginParams->get('ZOO_SEF_ADD_TRAILING_SLASH') ? define('ZOO_SEF_ADD_TRAILING_SLASH', '') : define('ZOO_SEF_ADD_TRAILING_SLASH',  $pluginParams->get('ZOO_SEF_ADD_TRAILING_SLASH'));

	define('ZOO_SEF_SHOW_TYPE',  $pluginParams->get('ZOO_SEF_SHOW_TYPE', 0));
	define('ZOO_SEF_SHOW_PRIMARY',  $pluginParams->get('ZOO_SEF_SHOW_PRIMARY', 1));
	define('ZOO_SEF_SHOW_APP',  $pluginParams->get('ZOO_SEF_SHOW_APP', 1));
	define('ZOO_SEF_SHOW_CATEGORY',  $pluginParams->get('ZOO_SEF_SHOW_CATEGORY', 1));
	define('ZOO_SEF_ITEM_ALIAS',  $pluginParams->get('ZOO_SEF_ITEM_ALIAS', 1));
	define('ZOO_SEF_CATEGORY_ALIAS',  $pluginParams->get('ZOO_SEF_CATEGORY_ALIAS', 1));
}

$dosef = true;

$current_version = $zooapp->zoo->version();
$current_version = substr($current_version,0,3);

if (version_compare($current_version, '2.5') >= 0) {
	$alias = $zooapp->alias;
} else {
	$alias = $zooapp;
}

if (!isset ($task) && isset ($view))
	$task = $view;

if (!isset ($task))
	$task="nothing";

if (isset($Itemid)) {
	$menu =& JSite::getMenu();
	$menuparams = $menu->getParams($Itemid);
//	if (!isset($app_id))
	$app_id = $menuparams->get('application');
	$app_alias = $alias->application->translateIDToAlias((int)$app_id);
	
}

switch ($task) {
	case 'frontpage':
		$title[] = $app_alias;
		$title[] = ZOO_SEF_FRONTPAGE;
		$title[] = isset ($page) ? ZOO_SEF_PAGE.$page : '';
		shRemoveFromGETVarsList('page');
		shRemoveFromGETVarsList('layout');
		break;

	case 'category':
		if (!isset($category_id)&&isset($Itemid))
			$category_id = intval($menuparams->get('category'));
		if (isset($category_id))
			$category = $zooapp->table->category->get($category_id);

		if (!isset($category)) {
				$dosef = false;
				$title = null;
				break;
		}

		if (ZOO_SEF_SHOW_APP) {
			$app_alias = $alias->application->translateIDToAlias($category->application_id);
			$title[] = $app_alias;
		}
		$title[] = ZOO_SEF_CATEGORY;
		if (ZOO_SEF_CATEGORY_ALIAS) {
			$title[] = $alias->category->translateIDToAlias((int)$category_id);
		} else {
			$title[] = $zooapp->string->sluggify($category->name);
		}
		$title[] = isset ($page) ? ZOO_SEF_PAGE.$page : '';
		shRemoveFromGETVarsList('category_id');
		shRemoveFromGETVarsList('page');
		shRemoveFromGETVarsList('layout');
		break;

	case 'alphaindex':
		$app_alias = $alias->application->translateIDToAlias((int)$app_id);
		if (ZOO_SEF_SHOW_APP)
			$title[] = $app_alias;
		$title[] = ZOO_SEF_ALPHAINDEX;
		$title[] = $alpha_char;
		$title[] = isset ($page) ? ZOO_SEF_PAGE.$page : '';
		shRemoveFromGETVarsList('page');
		shRemoveFromGETVarsList('alpha_char');
		shRemoveFromGETVarsList('app_id');
		break;

	case 'tag':
		$app_alias = $alias->application->translateIDToAlias((int)$app_id);
		if (ZOO_SEF_SHOW_APP)
			$title[] = $app_alias;
		$title[] = ZOO_SEF_TAG;
		$title[] = $tag;
		$title[] = isset ($page) ? ZOO_SEF_PAGE.$page : '';
		shRemoveFromGETVarsList('tag');
		shRemoveFromGETVarsList('page');
		shRemoveFromGETVarsList('app_id');
		break;

	case 'item':
		if (!isset($item_id))
			$item_id = intval($menuparams->get('item_id', 0));
		$item = $zooapp->table->item->get($item_id);

		if (!isset($item)) {
				$dosef = false;
				$title = null;
				break;
		}

		if (ZOO_SEF_SHOW_APP) {
			$app_alias = $alias->application->translateIDToAlias($item->application_id);
			$title[] = $app_alias;
		}
		if (ZOO_SEF_SHOW_TYPE) {
			$title[] = $item->getType()->identifier;
		}
		$title[] = ZOO_SEF_ITEM;
		if (in_array($item->getType()->identifier,explode (",", ZOO_SEF_DATE))) {
			$language = & JFactory::getLanguage();
			$oldLang = $language->setLanguage(JComponentHelper::getParams('com_languages')->get('site', 'en-GB'));
//			$language->load();
			$tzoffset = JFactory::getConfig()->getValue('config.offset');
			$date = $zooapp->date->create($item->created, $tzoffset);
			foreach (explode ("/",ZOO_SEF_DATE_FORMAT) as $format) {
				if(strpos($format, '%i') !== false) {
					$format = str_replace('%i', sprintf("%03d",$item_id), $format);
				}
				$title[] = $zooapp->string->sluggify($date->toFormat($format));
			}
			$language->setLanguage($oldLang);
//			$language->load();
		} else {

			if (ZOO_SEF_SHOW_PRIMARY && $item->getPrimaryCategoryId()) {
				if (ZOO_SEF_CATEGORY_ALIAS) {
					$title[] = $alias->category->translateIDToAlias((int)$item->getPrimaryCategoryId());
				} else {
					$category = $zooapp->table->category->get($item->getPrimaryCategoryId());
					$title[] = $zooapp->string->sluggify($category->name);
				}
			}
			if (isset($category_id)&&ZOO_SEF_SHOW_CATEGORY) {
				if (ZOO_SEF_CATEGORY_ALIAS) {
					$title[] = $alias->category->translateIDToAlias((int)$category_id);
				} else {
					$category = $zooapp->table->category->get($category_id);
					$title[] = $zooapp->string->sluggify($category->name);
				}
			}
		}


		if (ZOO_SEF_ITEM_ALIAS) {
			$title[] = $alias->item->translateIDToAlias((int)$item_id);
		} else {
			$title[] = $zooapp->string->sluggify($item->name);
		}
		shRemoveFromGETVarsList('item_id');
		shRemoveFromGETVarsList('category_id');
		shRemoveFromGETVarsList('layout');
		break;

	case 'feed':
		$app_alias = $alias->application->translateIDToAlias((int)$app_id);
		$title[] = $app_alias;
		$title[] = ZOO_SEF_FEED;
		$title[] = $type;
		$title[] = $alias->category->translateIDToAlias((int)$category_id);
		shRemoveFromGETVarsList('app_id');
		shRemoveFromGETVarsList('category_id');
		shRemoveFromGETVarsList('type');
		break;

	case 'submission':
		$title[] = $app_alias;
		if (!isset($submission_id))
			$submission_id = intval($menuparams->get('submission'));
		switch ($layout) {
		case 'submission';
			$title[] = ZOO_SEF_SUBMISSION;
			if (!isset($type_id))
				$type_id = $menuparams->get('type');
			$title[] = $alias->submission->translateIDToAlias((int) $submission_id);
			$title[] = $type_id;
			if (isset($submission_hash))
				$title[] = $submission_hash;
			if (isset($item_id))
				$title[] = $alias->item->translateIDToAlias((int)$item_id);
			shRemoveFromGETVarsList('redirect');
			shRemoveFromGETVarsList('submission_id');
			shRemoveFromGETVarsList('submission_hash');
			shRemoveFromGETVarsList('type_id');
			shRemoveFromGETVarsList('item_id');
			shRemoveFromGETVarsList('layout');
			break;
		case 'mysubmissions';
			$title[] = ZOO_SEF_SUBMISSION;
			$title[] = $submission_hash;
			$title[] = $alias->submission->translateIDToAlias((int) $submission_id);
			$title[] = isset ($page) ? ZOO_SEF_PAGE.$page : '';
			shRemoveFromGETVarsList('submission_hash');
			shRemoveFromGETVarsList('page');
			shRemoveFromGETVarsList('layout');
			shRemoveFromGETVarsList('submission_id');
			break;
		default:
			break;
		}
		break;

	default:
	$dosef = false;
	$title = null;
	break;
}
