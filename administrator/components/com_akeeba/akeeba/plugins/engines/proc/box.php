<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

class AEPostprocBox extends AEAbstractPostproc
{
	private $apiKey = 'dm8jnek5us1uin7q8plbov6jer2zylf7';

	public function __construct()
	{
		parent::__construct();

		$this->can_delete = true;
		$this->can_download_to_browser = true;
		$this->can_download_to_file = false;
	}

	public function processPart($absolute_filename)
	{
		$config = AEFactory::getConfiguration();

		$filename = basename($absolute_filename);
		$this->remote_path = $config->get('engine.postproc.box.folderid') . '/' . $filename;

		AEUtilLogger::WriteLog(_AE_LOG_DEBUG, "Box.net -- Starting upload of ".$filename);

		$box = AEUtilBox::getInstance($this->apiKey);
		$box->auth_token = $config->get('engine.postproc.box.token');
		AEUtilLogger::WriteLog(_AE_LOG_DEBUG, "Box.net -- Token = ".$box->auth_token);

		$file = new Box_Client_File($absolute_filename, $filename);
		$file->attr('folder_id', $config->get('engine.postproc.box.folderid'));
		$result = $box->upload($file, array('skiprename' => 1));

		if($result !== 'upload_ok') {
			$this->setWarning($result);
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Deletes a remote file
	 * @param $path string Absolute path to the file we're deleting
	 * @return bool|int False on failure, true on success, 1 if more work is required
	 */
	public function delete($path)
	{
		$file_id = $this->_findFile($path);

		if (empty($file_id))
		{
			return false;
		}

		$res = $box->delete($file_id);

		if ($res != true)
		{
			$this->setError($file_id . ' -- ' . $res);
			return false;
		}

		return true;
	}

	public function downloadToBrowser($remotePath)
	{
		$file_id = $this->_findFile($remotePath);

		if (empty($file_id))
		{
			return false;
		}

		$config = AEFactory::getConfiguration();
		$url = 'https://www.box.net/api/1.0/download/' . $config->get('engine.postproc.box.token') . '/' . $file_id;

		return $url;
	}

	/**
	 * Opens the OAuth window
	 *
	 * @param array $params Not used :)
	 *
	 * @return boolean False on failure, redirects on success
	 */
	public function oauthOpen($params = array())
	{
		$box = AEUtilBox::getInstance($this->apiKey);
		$res = $box->get('get_ticket',array('api_key' => $this->apiKey));
		if($res['status'] === 'get_ticket_ok') {
			$ticket = $res['ticket'];
			$session = JFactory::getSession();
			$session->set('box.ticket', $ticket, 'akeeba');
			JFactory::getApplication()->redirect('https://www.box.net/api/1.0/auth/'.$ticket);
		}
		else {
			echo $res['status'];
			return false;
		}
	}

	/**
	 * Fetches the authentication token from Box.net, after you've run the first
	 * step of the OAuth process.
	 *
	 * @return array
	 */
	public function getauth()
	{
		$box = AEUtilBox::getInstance($this->apiKey);
		$session = JFactory::getSession();
		$ticket = $session->get('box.ticket', null, 'akeeba');
		$res = $box->get('get_auth_token',array('api_key' => $this->apiKey, 'ticket' => $ticket));
		if($res['status'] == 'get_auth_token_ok') {
			return array(
				'error'	=> '',
				'token'	=> $res['auth_token']
			);
		} else {
			return array(
				'error'	=> $res['status'],
				'token'	=> ''
			);
		}
	}

	/**
	 * Fetches the folder tree, for use in rendering a drop-down menu in the GUI
	 *
	 * @return array
	 */
	public function gettree()
	{
		$box = AEUtilBox::getInstance($this->apiKey);
		$config = AEFactory::getConfiguration();
		$box->auth_token = $config->get('engine.postproc.box.token');
		$res = $box->folder(0, array('params' => array('nofiles','nozip','simple')));
		if($res instanceof Box_Client_Folder) {
			$ret = array();
			$this->_parseFolders($res, '', $ret);
			return $ret;
		} else {
			return array();
		}
	}

	/**
	 * Parses the recursive tree of folders and generates a flat list of folders
	 *
	 * @param Box_Client_Folder $folder The folder to parse
	 * @param string $path Current path to here
	 * @param array $ret The flat structure
	 */
	private function _parseFolders(Box_Client_Folder $folder, $path, &$ret)
	{
		$id = $folder->attr('id');
		$fullpath = (empty($path)?'':$path.'/').$folder->attr('name');
		if($id != 0) {
			$ret[$id] = $fullpath;
		}

		if(!empty($folder->folder)) {
			foreach($folder->folder as $subfolder) {
				$this->_parseFolders($subfolder, $fullpath, $ret);
			}
		}
	}

	/**
	 * Finds a file in Box.net and returns the file ID
	 *
	 * @param string $path
	 * @return null|integer
	 */
	private function _findFile($path)
	{
		// Get the folder ID and filename
		list($folder_id, $filename) = explode('/', $path, 2);

		// List the folder and try to file the file
		$config = AEFactory::getConfiguration();
		$box = AEUtilBox::getInstance($this->apiKey);
		$box->auth_token = $config->get('engine.postproc.box.token');
		$listing = $box->folder($folder_id);

		$file_id = null;

		if ($listing instanceof Box_Client_Folder)
		{
			if (!empty($listing->file))
			{
				foreach($listing->file as $file)
				{
					if ($file->attr('file_name', '') == $filename)
					{
						$file_id = $file->attr('id');
						break;
					}
				}
			}
		}
		else
		{
			$this->setError('The directory no longer exists');
			return null;
		}

		if (empty($file_id))
		{
			$this->setError('File not found in Box.net');
			return null;
		}

		return $file_id;
	}
}