<?php
/*
 * @version   1.5.0 Sun Sep 16 03:15:57 2012 -0700
 * @package   yoonique download element
 * @author    yoonique[.]net
 * @credits   based on Yootheme's download element
 * @copyright Copyright (C) yoonique[.]net and all rights reserved.
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

// register ElementFile class
App::getInstance('zoo')->loader->register('ElementFile', 'elements:file/file.php');

/*
	Class: ElementDownload
		The file download element class
*/
class ElementYooniquedownload extends ElementFile implements iSubmittable, iSubmissionUpload {

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		// set defaults
		$this->config->set('secret', $this->app->system->config->getValue('config.secret'));

		// set callbacks
		$this->registerCallback('download');
		$this->registerCallback('reset');
		$this->registerCallback('files');
	}

	/*
		Function: getSize
			Gets the download file size.

		Returns:
			String - Download file with KB/MB suffix
	*/
	public function getSize() {
		return $this->app->filesystem->formatFilesize($this->get('size', 0));
	}

	/*
		Function: getSize
			Gets the download file size.

		Returns:
			String - Download file with KB/MB suffix
	*/
	function isDownloadLimitReached() {
		return ($limit = $this->get('download_limit')) && $this->get('hits', 0) >= $limit;
	}

	/*
		Function: getLink
			Gets the link to the download.

		Returns:
			String - link
	*/
	public function getLink() {

		// init vars
		$download_mode = $this->config->get('download_mode');

		$access_level	 = $this->get('access_level');

		if(!$this->accessAllowed($access_level)) {
				$redirectUrl = $_SERVER['REQUEST_URI'];
				$redirectUrl = base64_encode($redirectUrl);
				$redirectUrl = '&amp;return='.$redirectUrl;
				if(JVersion::isCompatible('1.7.0')) {
					return 'index.php?option=com_users&amp;view=login' . $redirectUrl;
				} else {
					return 'index.php?option=com_user&amp;view=login' . $redirectUrl;
				}
		}

		// create download link
		$query = array('task' => 'callelement', 'format' => 'raw', 'item_id' => $this->_item->id, 'element' => $this->identifier, 'method' => 'download');

		if ($download_mode == 1) {
			return $this->app->link($query);
		} else if ($download_mode == 2) {
			$query['args[0]'] = $this->filecheck();
			return $this->app->link($query);
		} else {
			return $this->get('file');
		}

	}

	/*
		Function: render
			Renders the element.

	   Parameters:
   			$params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// init vars
		$params = $this->app->data->create($params);
		$file = $this->get('file');
		$filename = basename($file);
		$access_level	 = $this->get('access_level');

		$access_level = $this->accessAllowed($access_level);

		// render layout
		if ($layout = $this->getLayout()) {
			return $this->renderLayout($layout,
				array(
					'file' => $file,
					'filename' => $filename,
					'size' => $this->getSize(),
					'hits' => (int) $this->get('hits', 0),
					'download_name' => $this->app->string->str_ireplace('{filename}', $filename, $params->get('download_name', '')),
					'download_link' => $this->getLink(),
					'filetype' => $this->getExtension(),
					'display' => $params->get('display', null),
					'limit_reached' => $this->isDownloadLimitReached(),
					'download_limit' => $this->get('download_limit'),
					'access_level' => $access_level
				)
			);
		}
	}

	/*
		Function: download
			Download the file.

		Returns:
			Binary - File data
	*/
	public function download($check = '') {

		// init vars
		$filepath = $this->app->path->path('root:'.$this->get('file'));
		$download_mode = $this->config->get('download_mode');

		// check limit
		if ($this->isDownloadLimitReached()) {
			header('Content-Type: text/html');
			echo JText::_('Download limit reached!');
			return;
		}
		$access_level	 = $this->get('access_level');

		$user = JFactory::getUser();

		if(!$this->accessAllowed($access_level)) {
			JError::raiseError(500, JText::_('Unable to access item'));
			return;
		}

		// trigger on download event
		$canDownload = true;
		$this->app->event->dispatcher->notify($this->app->event->create($this, 'element:download', array('check' => $check, 'canDownload' => &$canDownload)));

		if ($canDownload) {

			// output file
			if ($download_mode == 1 && is_readable($filepath) && is_file($filepath)) {
				$this->set('hits', $this->get('hits', 0) + 1);
				$this->app->filesystem->output($filepath);
			} else if ($download_mode == 2 && $this->filecheck() == $check && is_readable($filepath) && is_file($filepath)) {
				$this->set('hits', $this->get('hits', 0) + 1);
				$this->app->filesystem->output($filepath);
			} else {
				header('Content-Type: text/html');
				echo JText::_('Invalid file!');
			}

			// save item
			$this->app->table->item->save($this->getItem());

		}

	}

	/*
	   Function: filecheck
	       Get the file check string.

	   Returns:
	       String - md5(file + secret + date)
	*/
	public function filecheck() {
		return md5($this->get('file').$this->config->get('secret').date('Y-m-d'));
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit(){

		// create info
		$info[] = JText::_('Size').': '.$this->getSize();
		$info[] = JText::_('Hits').': '.(int)$this->get('hits', 0);
		$info   = ' ('.implode(', ', $info).')';

        if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout,
                array(
					'info' => $info,
                    'hits' => $this->get('hits', 0)
                )
            );
        }

	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		parent::loadAssets();
		$this->app->document->addScript('elements:download/assets/js/download.js');
	}

	public function reset() {

		$this->set('hits', 0);

		//save item
		$this->app->table->item->save($this->getItem());

		return $this->edit();
	}

	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$data - array

		Returns:
			Void
	*/
	public function bindData($data = array()) {
		parent::bindData($data);

		// add size to data
		$filepath = $this->app->path->path('root:'.$this->get('file'));
		if (is_readable($filepath) && is_file($filepath)) {
			$this->set('size', sprintf('%u', filesize($filepath)));
		} else {
			$this->set('size', 0);
		}
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {

        // get params
        $trusted_mode = $params->get('trusted_mode');

        // init vars
        $upload = $this->get('file');

        if (empty($upload) && $trusted_mode) {
            $upload = $this->get('upload');
        }

        // is uploaded file
        $upload = is_array($upload) ? '' : $upload;

        // build upload select
        $lists = array();
        if ($trusted_mode) {
            $options = array($this->app->html->_('select.option', '', '- '.JText::_('Select File').' -'));
            if (!empty($upload) && !$this->_inUploadPath($upload)) {
                $options[] = $this->app->html->_('select.option', $upload, '- '.JText::_('No Change').' -');
            }
			foreach ($this->app->path->files('root:'.$this->_getUploadPath()) as $file) {
                $options[] = $this->app->html->_('select.option', $this->_getUploadPath().'/'.$file, basename($file));
            }
            $lists['upload_select'] = $this->app->html->_('select.genericlist', $options, $this->getControlName('upload'), 'class="upload"', 'value', 'text', $upload);
        }

        if (!empty($upload)) {
            $upload = basename($upload);
        }

        if ($layout = $this->getLayout('submission.php')) {
            return $this->renderLayout($layout,
                compact('lists', 'upload', 'trusted_mode')
            );
        }

	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {

        // init vars
        $trusted_mode = $params->get('trusted_mode');

        // get old file value
		$old_file = $this->get('file');

        $file = '';
        // get file from select list
        if ($trusted_mode && $file = $value->get('upload')) {

            if (!$this->_inUploadPath($file) && $file != $old_file) {
                throw new AppValidatorException(sprintf('This file is not located in the upload directory.'));
            }

            if (!JFile::exists($file)) {
                throw new AppValidatorException(sprintf('This file does not exist.'));
            }

        // get file from upload
        } else {

            try {

                // get the uploaded file information
                $userfile = $value->get('userfile', null);

				// get legal extensions
				$extensions = array_map(create_function('$ext', 'return strtolower(trim($ext));'), explode(',', $this->config->get('upload_extensions', 'png,jpg,doc,mp3,mov,avi,mpg,zip,rar,gz')));

				//get legal mime types
				$legal_mime_types = $this->app->data->create(array_intersect_key($this->app->filesystem->getMimeMapping(), array_flip($extensions)))->flattenRecursive();

				// get max upload size
				$max_upload_size = $this->config->get('max_upload_size', '512') * 1024;
				$max_upload_size = empty($max_upload_size) ? null : $max_upload_size;

				// validate
                $file = $this->app->validator
						->create('file', array('mime_types' => $legal_mime_types, 'max_size' => $max_upload_size))
						->addMessage('mime_types', 'Uploaded file is not of a permitted type.')
						->clean($userfile);

            } catch (AppValidatorException $e) {

                if ($e->getCode() != UPLOAD_ERR_NO_FILE) {
                    throw $e;
                }

                if (!$trusted_mode && $old_file && $value->get('upload')) {
                    $file = $old_file;
                }

            }

        }

        if ($params->get('required') && empty($file)) {
            throw new AppValidatorException('Please select a file to upload.');
        }

        $download_limit = (string) $this->app->validator
				->create('integer', array('required' => false), array('number' => 'The Download Limit needs to be a number.'))
				->clean($value->get('download_limit'));

		return compact('file', 'download_limit');
	}

	/*
		Function: doUpload
			Does the actual upload during submission

		Returns:
			void
	*/
    public function doUpload() {

        // get the uploaded file information
        if (($userfile = $this->get('file')) && is_array($userfile)) {

            // get file name
            $ext = $this->app->filesystem->getExtension($userfile['name']);
            $base_path = JPATH_ROOT . '/' . $this->_getUploadPath() . '/';
            $file = $tmp = $base_path . $userfile['name'];
            $filename = basename($file, '.'.$ext);

            $i = 1;
            while (JFile::exists($tmp)) {
                $tmp = $base_path . $filename . '-' . $i++ . '.' . $ext;
            }
            $file = $this->app->path->relative($tmp);

            if (!JFile::upload($userfile['tmp_name'], $file)) {
                throw new AppException('Unable to upload file.');
            }

            $this->set('file', $file);

        }
    }

    protected function _inUploadPath($image) {
        return $this->_getUploadPath() == dirname($image);
    }

    protected function _getUploadPath() {
        return trim(trim($this->config->get('upload_directory', 'images/zoo/uploads/')), '\/');
    }

	protected function accessAllowed ($access_level) {

		$params = array();
		if(!JVersion::isCompatible('2.5')) {
			$params['UserTypes'] = null;
			$params['UserTypes']->selection = $access_level;
			$params['UserTypes']->assignment = 'include';
		} else {
			$params['UserGroupLevels'] = null;
			$params['UserGroupLevels']->selection = $access_level;
			$params['UserGroupLevels']->assignment = 'include';
		}

		return $this->passAll( $params, 'or');

	}

	protected function passAll( &$params, $match_method = 'and', $article = 0 )
	{
		if ( empty( $params ) ) {
			return 1;
		}

		$pass = ( $match_method == 'and' ) ? 1 : 0;
		foreach ( array ('UserTypes','UserGroupLevels') as $type ) {
			if ( isset( $params[$type] ) ) {
//				$this->initParams( $params[$type], $type );
				$func = 'pass'.$type;
				if ( ( $pass && $match_method == 'and' ) || ( !$pass && $match_method == 'or' ) ) {
					if ( $params[$type]->assignment == 'all' ) {
						$pass = 1;
					} else if ( $params[$type]->assignment == 'none' ) {
						$pass = 0;
					} else {
						$pass = $this->$func( $params[$type]->params, $params[$type]->selection, $params[$type]->assignment, $article );
					}
				}
			}
		}
		return $pass;
	}

	/**
	 * passUserGroupLevels
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 * @return <bool>
	 */
	function passUserGroupLevels( &$params, $selection = array(), $assignment = 'all' )
	{
		$user =& JFactory::getUser();
		$groups = $user->getAuthorisedGroups();

		return $this->passSimple( $groups, $selection, $assignment );
	}

	function passUserTypes( &$params, $selection = array(), $assignment = 'all' )
	{
		$user =& JFactory::getUser();

		if ( !is_array( $selection ) ) {
			if ( !( strpos( $selection, '|' ) === false ) ) {
				$selection = explode( '|', $selection );
			} else {
				$selection = explode( ',', $selection );
			}
		}

		return $this->passSimple( $user->get( 'usertype' ), $selection, $assignment );
	}
	/**
	 * passUsers
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 * @return <bool>
	 */
	function passUserNames( &$params, $selection = array(), $assignment = 'all' )
	{
		$user =& JFactory::getUser();

		return $this->passSimple( $user->get( 'username' ), $selection, $assignment );
	}

	/**
	 * passSimple
	 * @param <string> $value
	 * @param <array> $selection
	 * @param <string> $assignment
	 * @return <bool>
	 */
	function passSimple( $values = '', $selection = array(), $assignment = 'all' )
	{
		if (!$values) return;

		if ( !is_array( $values ) ) {
			$values = explode( ',', $values );
		}
		if ( !is_array( $selection ) ) {
			if ( !( strpos( $selection, '|' ) === false ) ) {
				$selection = explode( '|', $selection );
			} else {
				$selection = explode( ',', $selection );
			}
		}

		$pass = 0;
		foreach ( $values as $value ) {
			if ( in_array( $value, $selection ) ) {
				$pass = 1;
				break;
			}
		}

		if ( $pass ) {
			return ( $assignment == 'include' );
		} else {
			return ( $assignment == 'exclude' );
		}
	}

}
