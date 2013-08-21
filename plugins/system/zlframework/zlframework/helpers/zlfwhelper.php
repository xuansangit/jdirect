<?php
/**
* @package		ZL Framework
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 Class: ZLFW Helper
 The general ZL Framework helper Class for zoo
 */
class zlfwHelper extends AppHelper
{
	/* prefix */
	protected $_prefix;

	/* models */
	protected $_helpers = array();
    
	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// set helper prefix
		$this->_prefix = 'zlfwHelper';
	}

	/*
		Function: get
			Retrieve a helper

		Parameters:
			$name - Helper name
			$prefix - Helper prefix

		Returns:
			Mixed
	*/
	public function get($name, $prefix = null) {
		
		// set prefix
		if ($prefix == null) {
			$prefix = $this->_prefix;
		}
		
		// load class
		$class = $prefix . $name;
		
		$this->app->loader->register($class, 'zlfw:zlfwhelpers/'.strtolower($name).'.php');
		
		// add helper, if not exists
		if (!isset($this->_helpers[$name])) {
			$this->_helpers[$name] = class_exists($class) ? new $class($this->app) : new AppHelper($this->app, $prefix.$name);
		}

		return $this->_helpers[$name];
	}
	
	/*
		Function: __get
			Retrieve a helper

		Parameters:
			$name - Helper name

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->get($name);
	}

	// ======================

	/**
	 * get Enviroment
	 *
	 * @return @object The enviroment parameters
	 *
	 * @since 3.0.6
	 */
	public function getEnviroment()
	{
		$obj = new stdClass;

		// component, eg. zoo
		$obj->component = str_replace('com_', '', $this->app->request->getVar('option'));

		// controller
		$obj->controller = $this->app->request->getVar('controller');

		// task
		$obj->task = $this->app->request->getVar('task');

		// group
		$obj->group = $this->app->request->getVar('group');

		return $obj;
	}

	/**
	 * get The Enviroment
	 *
	 * @return @string An known enviroment in simple string
	 *
	 * @since 3.0.6
	 */
	public function getTheEnviroment()
	{	
		// init vars
		$env = $this->getEnviroment();

		// ZOO
		if ($env->component == 'zoo')
		{
			$path = 'zoo-';
			switch ($env->task) {
				case 'editelements':
				case 'addelement':
					$path .= 'type-edit';
					break;

				case 'assignelements':
					$path .= 'type-assignment';
					break;

				case 'assignsubmission':
					$path .= 'type-assignment-submission';
					break;

				case 'edit':
					$path .= 'item-edit';
					break;

				case 'add':
					if ($env->controller == 'new' && strlen($env->group)) $path .= 'app-config';
					break;

				default:
					if ($env->controller == 'configuration') $path .= 'app-config';
					break;
			}

			return $path;
		}

		// Modules
		if ($env->component == 'advancedmodules' || $env->component == 'modules')
		{
			return 'joomla-module';
		}
	}

	/**
	 * output json content
	 *
	 * @return @json The JSON string with application/json content type
	 *
	 * @since 3.0.10
	 */
	public function outputJSON($json)
	{
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
		header("Content-Transfer-Encoding: binary");
		header("Content-Type: application/json");

		echo $json;

		exit;
	}

	/**
	 * is the Enviroment
	 *
	 * @return @boolean True if enviroment match
	 *
	 * @since 3.0.6
	 */
	public function isTheEnviroment($enviroment)
	{
		return (strpos($this->getTheEnviroment(), $enviroment) === 0);
	}

	/**
	 * Retrieve an plugin object
	 *
	 * @param  string $name The plugin name
	 * @param  string $type The plugin type
	 *
	 * @return Object The requested plugin
	 */
	public function getPlugin($name, $type = 'system')
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__extensions WHERE element LIKE ' . $db->Quote($name) . ' AND folder LIKE ' . $db->Quote($type) . ' LIMIT 1';
		
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function checkPluginOrder($plugin_name, $type = 'system')
	{
		$fw = $this->getPlugin('zlframework', 'system');
		$p = $this->getPlugin($plugin_name, $type);
		// Fix for aksubs
		$paks = $this->getPlugin('aksubs', 'system');

		if ($fw->ordering >= $p->ordering)
		{
			$db = JFactory::getDBO();
			$query = 'UPDATE #__extensions SET ordering = ' . ((int)($fw->ordering) + 1) . ' WHERE extension_id = ' . (int)$p->extension_id;
			if($paks){
				$queryaks = 'UPDATE #__extensions SET ordering = ' . ((int)($p->ordering) + 100) . ' WHERE extension_id = ' . (int)$paks->extension_id;
			}
			
			if($paks){
				$db->setQuery($queryaks);
				$db->query();
			}
			
			$db->setQuery($query);
			return $db->query();
		}

		return true;
	}

	/*
	 Function: checkExt
	 Check if specified Components/Plugins exists and are enabled
	 Params
	 $renderif - extension name. Ex: com_widgetkit
	 */
	public function checkExt($ext = null)
	{
		if (!empty($ext) && $name = str_replace('com_', '', $ext))// it's component
		{
			jimport('joomla.filesystem.file');
			if (JFile::exists(JPATH_ADMINISTRATOR . '/components/' . $ext . '/classes/' . $name . '.php') && JComponentHelper::getComponent($ext, true)->enabled)
			{
				return true;
			}
		}
		else
		if ($this->app->zlfw->getPlugin($ext)->enabled)
		{
			// else should be plugin
			return true;
		}

		return false;
	}

	/*
	 Function: getApplications
	 Returns valid App Objects from ID array

	 Parameters:
	 $apps - array of app objects
	 $all - if TRUE and final result is empty, return All Apps
	 $group - filter by App group

	 */
	public function getApplications($apps, $all = false, $groups = array())
	{
		// init vars
		$table = $this->app->table->application;
		$result = array();

		$apps 		= array_filter((array)($apps));
		$groups 	= array_filter((array)($groups));

		// get apps
		foreach ($apps as $app) {
			if ($app && $app = $table->get($app))
			{
				// filter by App group
				if (empty($groups) || in_array($app->getGroup(), $groups)) {	
					$result[] = $app;
				}
			}
		}

		// if empty get All
		if (empty($result) && $all) {
			foreach ($table->all(array('order' => 'name')) as $app)
			{
				// filter by App group
				if (empty($groups) || in_array($app->getGroup(), $groups)) {	
					$result[] = $app;
				}
			}
		}

		return $result;
	}

	/* depricated name */
	public function getAppsObject($apps, $all = false, $group = null){
		return $this->getApplications($apps, $all, $group);
	}

	/*
	 Function: renderView
	 Renders an Item View

	 Parameters:
	 $item - the Item Object
	 $layoutName - the Item layout
	 $path - path to the renderer parent folder

	 Returns:
	 String - html

	 Ex. of use:
	 echo $this->app->zlfw->renderView($this->_item, 'LAYOUTNAME');
	 */
	public function renderView($item, $layoutName = 'full', $path=null, $view=null)
	{
		// create it, if is not object
		if (!is_object($item))
		{
			$item = $this->app->table->item->get($item);
		}

		if (is_object($item))
		{
			// init vars
			$app  		= $item->getApplication();
			$path 		= $path ? $path : $app->getTemplate()->getPath();
			$renderer 	= $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $path));
			$layoutName = str_replace(".php", "", $layoutName);

			$path = 'item';
			$prefix = 'item.';
			$type = $item->getType()->id;
			if ($renderer->pathExists($path . DIRECTORY_SEPARATOR . $type))
			{
				$path .= DIRECTORY_SEPARATOR . $type;
				$prefix .= $type . '.';
			}

			// set view object
			if(empty($view)){
				$view = new AppView( array('name' => 'item'));
				$view->params = $app->getParams('site');
			}

			return $renderer->render($prefix . $layoutName, array('item' => $item, 'view' => $view));
		}
		else
		{
			return JText::_('Item does not exist.');
		}
	}

	/*
	 Function: renderModule
	 Renders Joomla Module

	 Parameters:
	 $modID - Module ID

	 HTML Styles:
	 table - Wrapped by Table (Column)
	 horz - Wrapped by Table (Horizontal)
	 xhtml - Wrapped by Divs
	 rounded - Wrapped by Multiple Divs
	 none - No wrapping (raw output)

	 Returns:
	 String - html

	 Ex. of use:
	 echo $this->app->zlfw->renderModule($id);
	 */
	public function renderModule($modID = null)
	{

		// get modules
		$modules = $this->app->module->load();

		if ($modID && isset($modules[$modID]))
		{
			if ($modules[$modID]->published)
			{

				$attribs['style'] = 'xhtml';
				$rendered = JModuleHelper::renderModule($modules[$modID], $attribs);

				if (isset($modules[$modID]->params))
				{
					$module_params = $this->app->parameter->create($modules[$modID]->params);
					if ($moduleclass_sfx = $module_params->get('moduleclass_sfx'))
					{
						$html[] = '<div class="' . $moduleclass_sfx . '">';
						$html[] = $rendered;
						$html[] = '</div>';

						return implode("\n", $html);
					}
				}

				return $rendered;
			}
		}

		return null;
	}

	/*
	 Function: renderModulePosition
	 Renders Joomla Module Position

	 Parameters:
	 $position - render the position

	 Returns:
	 String - html

	 Ex. of use:
	 echo $this->app->zlfw->renderModulePosition('POSITION');
	 */
	public function renderModulePosition($position = null)
	{
		// get modules
		$modules = $this->app->module->load();
		$result = array();

		foreach ($modules as $mod)
		{
			if ($mod->position == $position)
			{
				$result[] = $this->renderModule($mod->id);
			}
		}

		if (!empty($result))
		{
			return implode("\n", $result);
		}

		return null;
	}

	/**
	 * Text encryption/decryption
	 *
	 * @param  string $text The text to encrypt
	 * @param  string $action Posible values are 'encrypt' or 'decrypt'
	 *
	 * @return string The encrypted/decrypted text
	 *
	 * @since 3.0.3
	 */
	public function crypt($text, $action)
	{
		$secret = $this->app->system->config->get('secret');
		$key 	= new JCryptKey('simple', $secret, $secret);
		$crypt 	= new JCrypt(null, $key);
		
		return $crypt->$action( $text );
	}

	/**
	 * Password field decryption
	 *
	 * @param  string $pass The encrypted password to decrypt
	 *
	 * @return string The decrypted password
	 *
	 * @since 3.0.3
	 */
	public function decryptPassword($pass)
	{
		$matches = array();
		if (preg_match('/zl-encrypted\[(.*)\]/', $pass, $matches)) {
			return $this->crypt($matches[1], 'decrypt');
		}
		
		// if no valid pass to decrypt, return orig pass
		return $pass;
	}
	

	/*
	 Function: resizeImage
	 Resize and cache image file.

	 Returns:
	 String - image path
	 */
	public function resizeImage($file, $width, $height, $avoid_cropping = null, $unique = null)
	{
		if (is_file($file))
		{
			// init vars
			$width = (int)$width;
			$height = (int)$height;
			$file_info = pathinfo($file);
			$thumbfile = $this->app->path->path('cache:') . '/images/' . $file_info['filename'] . '_' . md5($file . $width . $height . $avoid_cropping . $unique) . '.' . $file_info['extension'];
			$cache_time = 86400;
			// cache time 24h
			$format = '';

			if ($avoid_cropping > 0)
			{
				$file_size = getimagesize($file);
				$format = ($file_size[0] > $file_size[1]) ? 'landscape' : 'portrait';
			}

			// check thumbnail directory
			if (!JFolder::exists(dirname($thumbfile)))
			{
				JFolder::create(dirname($thumbfile));
			}

			// create or re-cache thumbnail
			if ($this->app->imagethumbnail->check() && (!is_file($thumbfile) || ($cache_time > 0 && time() > (filemtime($thumbfile) + $cache_time))))
			{
				$thumbnail = $this->app->imagethumbnail->create($file);

				// without cropping - check cropping condition
				if ($avoid_cropping && $format == 'landscape' && $avoid_cropping != 3 && $width > 0)
				{
					$thumbnail->sizeWidth($width);
					$thumbnail->save($thumbfile);
				}
				else
				if ($avoid_cropping && $format == 'portrait' && $avoid_cropping != 2 && $height > 0)
				{
					$thumbnail->sizeHeight($height);
					$thumbnail->save($thumbfile);

					// with cropping
				}
				else
				if ($width > 0 && $height > 0)
				{
					$thumbnail->setSize($width, $height);
					$thumbnail->save($thumbfile);
				}
				else
				if ($width > 0 && $height == 0)
				{
					$thumbnail->sizeWidth($width);
					$thumbnail->save($thumbfile);
				}
				else
				if ($width == 0 && $height > 0)
				{
					$thumbnail->sizeHeight($height);
					$thumbnail->save($thumbfile);
				}
				else
				{
					if (JFile::exists($file))
					{
						JFile::copy($file, $thumbfile);
					}
				}
				$this->app->zoo->putIndexFile(dirname($thumbfile));
			}

			if (is_file($thumbfile))
			{
				return $thumbfile;
			}

			return $file;

		}
		else
		{
			return false;
		}
	}

	/*
	 Function: applySeparators
	 Separates the passed element values with a separator

	 Parameters:
	 $separated_by - Separator
	 $values - Element values

	 Returns:
	 String
	 */
	public function applySeparators($separated_by, $values, $class = '', $fixHTML = false)
	{
		if (!is_array($values))
		{
			$values = array($values);
		}

		$separator = '';
		$tag = '';
		$enclosing_tag = '';
		if ($separated_by)
		{
			if (preg_match('/separator=\[(.*)\]/U', $separated_by, $result))
			{
				$separator = $result[1];
			}

			// seach the tag in the beginning or in the midle with space before
			if (preg_match('/(^tag|\stag)=\[(.*)\]/U', $separated_by, $result))
			{
				$tag = $result[2]; // as regex has sub patrons the match is in index 2
			}

			if (preg_match('/enclosing_tag=\[(.*)\]/U', $separated_by, $result))
			{
				$enclosing_tag = $result[1];
			}
		}

		// add class to tag or enclosing tag
		if (!empty($class) && preg_match('/<((.*).*)>/U', $tag, $result))
		{
			// If there is enclosing, apply the class to the enclosing and not to the tag itself
			if (!empty($enclosing_tag) && preg_match('/<((.*).*)>/U', $enclosing_tag, $result))
			{
				$enclosing_tag = str_replace('<' . $result[1] . '>', '<' . $result[1] . ' class="' . $class . '">', $enclosing_tag);
			}
			else
			{
				$tag = str_replace('<' . $result[1] . '>', '<' . $result[1] . ' class="' . $class . '">', $tag);
			}
		}

		if (!empty($tag))
		{
			foreach ($values as $key => $value)
			{
				$values[$key] = sprintf($tag, $values[$key]);
			}
		}

		$value = implode($separator, $values);

		if (!empty($enclosing_tag))
			$value = sprintf($enclosing_tag, $value);

		// clean the resultant HTML code
		if ($fixHTML)
			$value = $this->app->zlstring->getFixedHtml($value);

		return $value;
	}

	/*
	 Function: pluploadTranslation
	 Translate Plupload script variables

	 Returns:
	 Translations - json
	 */
	public function pluploadTranslation()
	{
		if (!defined('PLG_ZLFRAMEWORK_PLUPLOAD_SCRIPT_DECLARATION'))
		{
			define('PLG_ZLFRAMEWORK_PLUPLOAD_SCRIPT_DECLARATION', true);

			$translations = array('Select files' => 'PLG_ZLFRAMEWORK_FLP_SELECT_FILES', 'Add files to the upload queue and click the start button.' => 'PLG_ZLFRAMEWORK_FLP_ADD_FILES_TO_QUEUE', 'Upload element accepts only %d file(s) at a time. Extra files were stripped.' => 'PLG_ZLFRAMEWORK_FLP_UPLOAD_ACCEPTS_ONLY', 'Image format either wrong or not supported.' => 'PLG_ZLFRAMEWORK_FLP_WRONG_IMAGE_FORMAT', 'Runtime ran out of available memory.' => 'PLG_ZLFRAMEWORK_FLP_RUNTIME_OUT_OF_MEMORY', 'Resoultion out of boundaries! <b>%s</b> runtime supports images only up to %wx%hpx.' => 'PLG_ZLFRAMEWORK_FLP_RESOLUTION_OUT', 'Filename' => 'PLG_ZLFRAMEWORK_FLP_FILENAME', 'Upload URL might be wrong or doesn\'t exist' => 'PLG_ZLFRAMEWORK_FLP_UPLOAD_URL_WRONG', 'Using runtime: ' => 'PLG_ZLFRAMEWORK_FLP_USING_RUNTIME', 'Status' => 'PLG_ZLFRAMEWORK_FLP_STATUS', 'Size' => 'PLG_ZLFRAMEWORK_FLP_SIZE', 'File: %s' => 'PLG_ZLFRAMEWORK_FLP_FILE', 'Add Files' => 'PLG_ZLFRAMEWORK_FLP_ADD_FILES', 'Stop current upload' => 'PLG_ZLFRAMEWORK_FLP_STOP_CURRENT_UPLOAD', 'Start uploading queue' => 'PLG_ZLFRAMEWORK_FLP_START_CURRENT_UPLOAD', 'Uploaded %d/%d files' => 'PLG_ZLFRAMEWORK_FLP_UPLOADED_FILES', 'N/A' => 'PLG_ZLFRAMEWORK_FLP_NA', 'Drag files here.' => 'PLG_ZLFRAMEWORK_FLP_DRAG_FILES_HERE', 'File extension error.' => 'PLG_ZLFRAMEWORK_FLP_FILE_EXTENSION_ERROR', 'File size error.' => 'PLG_ZLFRAMEWORK_FLP_FILE_SIZE_ERROR', 'Init error.' => 'PLG_ZLFRAMEWORK_FLP_INIT_ERROR', 'HTTP Error.' => 'PLG_ZLFRAMEWORK_FLP_HTTP_ERROR', 'Security error.' => 'PLG_ZLFRAMEWORK_FLP_SECURITY_ERROR', 'Generic error.' => 'PLG_ZLFRAMEWORK_FLP_GENERIC_ERROR', 'File count error.' => 'PLG_ZLFRAMEWORK_FLP_FILE_COUNT_ERROR', 'IO error.' => 'PLG_ZLFRAMEWORK_FLP_IO_ERROR', 'Stop Upload' => 'PLG_ZLFRAMEWORK_FLP_STOP_UPLOAD', 'Start Upload' => 'PLG_ZLFRAMEWORK_FLP_START_UPLOAD', '%d files queued' => 'PLG_ZLFRAMEWORK_FLP_FILES_QUEUED', 'Cancel' => 'PLG_ZLFRAMEWORK_FLP_CANCEL');

			$translations = array_map(array('JText', '_'), $translations);
			$javascript = 'plupload.addI18n(' . json_encode($translations) . ');';

			$this->app->document->addScriptDeclaration($javascript);
		}
	}

	/*
	 Function: filesproTranslation
	 Translate FilesPro script variables

	 Returns:
	 Translations - json
	 */
	public function filesproTranslation()
	{

		if (!defined('PLG_ZLFRAMEWORK_FILESPRO_SCRIPT_DECLARATION'))
		{
			define('PLG_ZLFRAMEWORK_FILESPRO_SCRIPT_DECLARATION', true);

			$translations = array('MyFolder' => 'PLG_ZLFRAMEWORK_FLP_MYFOLDER', 'Upload files into the main folder' => 'PLG_ZLFRAMEWORK_FLP_UPLOAD_MAIN_FOLDER', 'Upload files into this folder' => 'PLG_ZLFRAMEWORK_FLP_UPLOAD_INTO_FOLDER', 'Create a new folder into the main folder' => 'PLG_ZLFRAMEWORK_FLP_NEW_FOLDER', 'Create a new subfolder' => 'PLG_ZLFRAMEWORK_FLP_NEW_SUBFOLDER', 'Input a name for the new folder' => 'PLG_ZLFRAMEWORK_FLP_INPUT_NAME_FOLDER', 'Delete' => 'PLG_ZLFRAMEWORK_FLP_DELETE', 'You are about to delete' => 'PLG_ZLFRAMEWORK_FLP_YOU_ARE_ABOUT_TO_DELETE', 'Do you agree' => 'PLG_ZLFRAMEWORK_FLP_DO_YOU_AGREE', 'Confirm' => 'PLG_ZLFRAMEWORK_FLP_OK', 'Cancel' => 'PLG_ZLFRAMEWORK_FLP_CANCEL');

			$translations = array_map(array('JText', '_'), $translations);
			$javascript = "var filesPro = function() {
								var translations = " . json_encode($translations) . ";
								return {
									translate: function(text) {
										return (typeof translations[text] === 'undefined') ? text : translations[text];
									}
								};
							}();";

			$this->app->document->addScriptDeclaration($javascript);
		}
	}

	/*
		Function: getCurrentLanguage
			retrieves the current language
			
		Return :
			string - the current language in en-GB format or en
	*/
	public static function getCurrentLanguage($url_safe=false)
	{
		$zoo = App::getInstance( 'zoo' );
	
		$current_lang = '';
		if ($zoo->joomla->isVersion('1.5')) {
			$current_lang = JFactory::getLanguage()->_lang;
		} else {
			$current_lang = JFactory::getLanguage()->get('tag');
		}
	
		if ($url_safe) {
			return substr($current_lang, 0, 2);
		}
		return $current_lang;
	}

	/* depricated since zlfw 2.5.6 */
	public function limitText($text, $limit, $etc = false)
	{
		$result = strip_tags($text);
		$etc = $etc ? $etc : '';

		if ($limit > 0 && $limit < strlen($result))
		{
			return substr($result, 0, strrpos(substr($result, 0, $limit), ' ')) . $etc;
		}
		else
		{
			return $result;
		}
	}

	/*
		Function: replaceShortCode
			replace the short codes with the appropiate result
			
		Variables:
			$string - string with shortcodes
			$args - arguments needed for the shortcode execution
			
		Return :
			string
	*/
	public function replaceShortCodes($string, $args=array())
	{
		// expression to search for
		$regex		= '/{\S*}/';
		$matches	= array();

		// find all instances of plugin and put in $matches
		preg_match_all($regex, $string, $matches, PREG_SET_ORDER);

		foreach($matches as $match)
		{
			$string = preg_replace("|$match[0]|", $this->shortCode($match[0], $args), $string, 1);			
		}

		return $string;
	}

	/*
		Function: shortCode

		Variables:
			$shortcode - ex {PHP_MAX_UPLOAD} the length of the output string
			
		Return :
			string
	*/
	public function shortCode($shortcode, $args=array())
	{	
		// extract the arguments
		extract($args, EXTR_OVERWRITE);

		switch ($shortcode) {
			case '{PHP-MAX_UPLOAD}':
				return $this->app->zlfilesystem->getUploadValue();
				break;

			case '{ITEM-URL}':
				return isset($item) ? $this->app->route->item($item) : '';
				break;

			case '{ITEM-NAME}':
				return isset($item) ? $item->name : '';
				break;

			case '{ITEM-ALIAS}':
				return isset($item) ? $item->alias : '';
				break;
			
			default:
				return $shortcode;
				break;
		}
	}

	/*
		Function: renderLayout
			Renders the element using template layout file.

	   Parameters:
            $__layout - layouts template file
	        $__args - layouts template file args

		Returns:
			String - html
	*/
	function renderLayout($__layout, $__args = array())
	{
		// init vars
		if (is_array($__args)) {
			foreach ($__args as $__var => $__value) {
				$$__var = $__value;
			}
		}

		// render layout
		$__html = '';
		ob_start();
		include($__layout);
		$__html = ob_get_contents();
		ob_end_clean();

		return $__html;
	}

	/*
	 Function: loadLibrary
	 load libraries
	 */
	public function loadLibrary($lib)
	{
		switch($lib)
		{
			case 'qtip' :
				// load qTip libraries
				$this->app->document->addStylesheet('zlfw:assets/libraries/qtip/jquery.qtip.min.css');
				$this->app->document->addStylesheet('zlfw:assets/libraries/qtip/jquery.qtip.custom.css');
				$this->app->document->addScript('zlfw:assets/libraries/qtip/jquery.qtip.min.js');
				break;

			case 'frontendui' :
				// load ZL frontend assets
				$this->app->document->addStylesheet('zlfw:assets/css/frontendui.css');
				break;

			case 'bootstrap' :
				// load bootstrap
				$this->app->document->addStylesheet('zlfw:assets/libraries/bootstrap/css/bootstrap-wrapped.min.css');
				$this->app->document->addStylesheet('zlfw:assets/libraries/bootstrap/css/bootstrap-responsive-wrapped.min.css');
				break;
			case 'bootstrap-js' :
				// load bootstrap js
				$this->app->document->addScript('zlfw:assets/libraries/bootstrap/js/bootstrap.min.js');
				break;

			case 'zlux' :
				// load ZL UX library
				$this->app->document->addStylesheet('zlfw:assets/libraries/zlux/zlux.css');
				$this->app->document->addScript('zlfw:assets/libraries/zlux/zlux.js');

				// load dependent assets
				$this->loadLibrary('qtip');
				$this->loadLibrary('bootstrap');

				// init translation
				$this->app->zlfw->filesproTranslation();
				break;

			case 'zlux-front' :
				// load ZL UX frontend library
				$this->app->document->addStylesheet('zlfw:assets/libraries/zlux/zlux-front.css');
				break;
		}
	}

	/*
		Function: getCountryOptions
			Returns well formated Countries options
		
		Parameters:
			$selectable_countries - Element Chosen Countries
	*/
	public function getCountryOptions($selectable_countries)
	{
		$element_options = array();
		$countries = $this->app->country->getIsoToNameMapping();
		$keys = array_flip($selectable_countries);
		$countries = array_intersect_key($countries, $keys);
		foreach ($countries as $key => $country) {
			$element_options[] = array('name' => $country, 'value' => $key);
		}
		return $element_options;
	}

	/*
	 Function: getqtipOptions
	 */
	public function getqtipOptions($element = null, $lparams)
	{
		$result = '';
		if ($lparams->find('qtip._mode') == 'modal')// if MODAL
		{
			$result .= "
			position: {
				at: 'center',
				my: 'center',
				target: $(window),
				effect: false
			},
			hide: false,";
		}
		else// if NOT MODAL
		{
			$result .= "
			position: {
				my: '" . $lparams->find('qtip._my', 'left bottom') . "',
				at: '" . $lparams->find('qtip._at', 'top right') . "'
			},
			hide: {
				event: '" . $lparams->find('qtip._hide_event', 'mouseleave') . "'
				" . ($lparams->find('qtip._hide_delay') ? ', delay: ' . $lparams->find('qtip._hide_delay') : '') . ($lparams->find('qtip._hide_fixed') ? ', fixed: true' : '') . "
			},";
		}

		$result .= "
			show: {
				event: '" . $lparams->find('qtip._show_event', 'mouseenter') . "'
				" . ($lparams->find('qtip._show_solo') ? ', solo: true' : '') . ($lparams->find('qtip._mode') == 'modal' ? ', modal: true' : '') . ($lparams->find('qtip._show_delay') ? ', delay: ' . $lparams->find('qtip._show_delay') : '') . "
			},
			style: {
				classes: '" . $this->getqTipStyle($element, $lparams) . "'
				" . ($lparams->find('qtip._width') ? ', width: ' . $lparams->find('qtip._width') : '') . ($lparams->find('qtip._height') ? ', height: ' . $lparams->find('qtip._height') : '') . "
			}";

		return $result;
	}

	/*
	 Function: getqTipStyle
	 return qtip style string
	 */
	public function getqTipStyle($element = null, $lparams)
	{
		$type = $element->getType();
		$classes = array();

		$classes[] = 'ui-tooltip-default ui-tooltip-' . $element->getElementType() . ' ui-tooltip-custom';
		$classes[] = $lparams->find('qtip._class');
		// additional class
		$classes[] = $lparams->get('qtip_additional_class');
		// custom class on params
		$classes[] = $lparams->find('qtip._iframe') ? 'ui-tooltip-modal ui-tooltip-iframe' : '';
		$classes[] = $lparams->find('qtip._width') ? 'fixed-width' : '';
		$classes[] = $lparams->find('qtip._mode') == 'modal' ? 'ui-tooltip-modal' : '';
		$classes[] = $type->config->find('zl.qtip._style', '') ? 'ui-tooltip-' . $type->config->find('zl.qtip._style') : '';
		$classes[] = $type->config->find('zl.qtip._shadow', '') ? 'ui-tooltip-shadow' : '';
		$classes[] = $type->config->find('zl.qtip._rounded', '') ? 'ui-tooltip-rounded' : '';

		return implode(' ', $classes);
	}

	/*
	 Function: getqTipTitle
	 return qtip title string
	 */
	public function getqTipTitle($element = null, $params)
	{
		$title = '';
		switch ($params->find('layout.qtip._title', ''))
		{
			case 'label' :
				$title = $params->get('altlabel', 0) ? $params->get('altlabel') : $element->config->get('name');
				break;
			case 'itemname' :
				$title = $element->getItem()->name;
				break;
			case 'custom' :
				$title = $params->find('layout.qtip._customtitle');
				break;
			default :
				$params->find('layout.qtip._button') && $title = ' ';
				// workaround - if button on, render empty space as title
				break;
		}
		return $title;
	}

	/*
	 Function: getqTipTrigger
	 return qtip trigger string
	 */
	public function getqTipTrigger($element = null, $params, $el_id)
	{
		$trigger = '';
		if ($params->find('layout.qtip._trigger_render') == '4')
		{
			return $trigger;
		}
		else
		{
			switch ($params->find('layout.qtip._trigger_content', ''))
			{
				case 'label' :
					$trigger = $params->get('altlabel', 0) ? $params->get('altlabel') : $element->config->get('name');
					break;
				case 'custom' :
					$trigger = JText::_($params->find('layout.qtip._cus_tg_text', ''));
					break;
				case 'itemname' :
					$trigger = $element->getItem()->name;
					break;
				default :
					$trigger = JText::_('READ_MORE');
					break;
			}

			// trigger title
			$title = '';
			switch ($params->find('layout.qtip._trigger_title', ''))
			{
				case 'label' :
					$title = $params->get('altlabel', 0) ? $params->get('altlabel') : $element->config->get('name');
					break;
				case 'itemname' :
					$title = $element->getItem()->name;
					break;
				case 'custom' :
					$title = $params->find('layout.qtip._trigger_title_custom');
					break;
			}
			$title = $title ? ' title="' . $title . '"' : '';

			return '<a href="javascript:void(0);"' . $title . ' id="' . $el_id . '" class="modal_plus">' . $trigger . '</a>';
		}
	}

}