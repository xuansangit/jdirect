<?php
/**
 * @version         $Id: classes/send.php 2013-05-10 15:53:00Z gibiwatch $
 * @package         Watchful Client
 * @subpackage      backend
 * @author          Watchful
 * @authorUrl       http://www.watchful.li
 * @copyright       (c) 2013, Watchful
 */
defined('WATCHFULLI_PATH') or die;

require_once dirname(__FILE__) . '/watchfulli.php';
require_once dirname(__FILE__) . '/encrypt.php';
/**
 * 
 */
class watchfulliSend
{
    public $_data;
    public $db;

    /**
     * 
     */
    public function __construct()
    {
        if (!Watchfulli::checkToken())
        {
            $this->_data = array('status' => array('access' => false));
            die(Watchfulli::encodedJson($this->_data));
        }
        $this->db = JFactory::getDBO();
    }

    /**
     * 	Return all client data separated into different array items
     *                  
     * 	@return     array of arrays
     */
    public function getData()
    {
        if (defined('WATCHFULLI_DEBUG'))
            global $debug;
        $params = JComponentHelper::getParams('com_watchfulli');
        $maintenance = $params->get('maintenance', 0) == 1;
        //$jmpluginsexvalues = JRequest::getVar('jmpluginsexvalues');
        $status = array('access' => true, 'maintenance' => $maintenance, 'can_update' => Watchfulli::canUpdate());

        if ('1.5' == Watchfulli::joomla()->RELEASE)
        {
            if (defined('WATCHFULLI_DEBUG'))
                $debug->time['2.1 Before watchfulliSend::getLegacyExtensions'] = time();
            $extensions = $this->getLegacyExtensions();
            if (defined('WATCHFULLI_DEBUG'))
                $debug->time['2.2 After watchfulliSend::getLegacyExtensions'] = time();
        }
        // check for updates, if not in 1.5
        else
        {
            if (defined('WATCHFULLI_DEBUG'))
                $debug->time['2.1 Before truncating #__updates table'] = time();
            if (defined('WATCHFULLI_DEBUG'))
                $debug->time['2.3 Before watchfulliSend::getExtensions'] = time();
            // Get versions info sent from the master
            $extensions_versions = array();
            foreach (json_decode(JRequest::getVar('versions', '[]')) as $item)
            {
                $extensions_versions[$item->realname] = $item->version;
            }
            //compare local extension with given versions info
            $extensions = $this->getExtensions();
            jimport('joomla.updater.updater');
            jimport('joomla.plugin.helper');
            $updater = JUpdater::getInstance();
            foreach ($extensions as &$extensionlist)
            {
                foreach ($extensionlist as $id => $extension)
                {
                    $extension['version'] = str_replace(array("FREE", "PRO"), "", $extension['version']);
                    if (isset($extensions_versions[$extension['realname']]) && ($extensions_versions[$extension['realname']] != $extension['version']))
                    {
                        if (defined('WATCHFULLI_DEBUG'))
                            $debug->time['2.2 Before JUpdater::findUpdates()'] = time();
                        if ($updater->findUpdates(array($id))) { // populate local database
                            $extensionlist[$id]['vUpdate'] = 1;
                            $extensionlist[$id]['updateVersion'] = $extensions_versions[$extension['realname']];
                            $extensionlist[$id]['extId'] = $this->getUpdateId($id);
                        }
                    }
                }
            }
        }
        
        if (defined('WATCHFULLI_DEBUG'))
            $debug->time['2.4 Before building data'] = time();

        $this->_data = array(
            'status' => $status,
            'versions' => $this->getVersions(),
            'filesproperties' => $this->getFilesProperties(),
            'extensions' => $extensions,
            'watchfulliApps' => $this->getApps()
        );

        if (defined('WATCHFULLI_DEBUG'))
            $debug->time['2.4 watchfulliSend::getData end'] = time();

        //format data
        return $this->_data;
    }

    /**
     * Get full list of extensions for Joomla 1.5, separated by type (components,
     * modules, plugins, libraries, other)
     * 
     * @return  array of arrays
     */
    public function getLegacyExtensions()
    {
        $lang = & JFactory::getLanguage();
        $components = $modules = $plugins = array();
        $componentBaseDir = JPATH_ADMINISTRATOR . '/components';
        $pluginsBaseDir = JPATH_ROOT . '/plugins';
        $db = & JFactory::getDBO();

        /*         * ******************************
         * COMPONENTS
         * ****************************** */

        $db->setQuery("SELECT * FROM #__components WHERE iscore != 1 and parent=0");
        $results = $db->loadObjectList();

        foreach ($results as $row)
        {
            $files = glob($componentBaseDir . '/' . $row->option . "/*.xml", GLOB_NOSORT);
            foreach ($files as $file)
            {
                if ($data = JApplicationHelper::parseXMLInstallFile($file))
                {
                    if ($data['authorUrl'] != 'www.joomla.org') //we don't want joomla module
                    {
                        $lang->load($row->option, JPATH_ADMINISTRATOR, 'en-GB', true);
                        $components[] = array(
                            'name' => JText::_($data['name']),
                            'realname' => $row->option,
                            'version' => $data['version'],
                            'authorurl' => $data['authorUrl'],
                            'creationdate' => $data['creationdate']
                        );
                    }
                }
            }
        }

        /*         * ******************************
         * MODULES
         * ****************************** */

        // TODO: this is an incorrect assumption that all installed modules will have db entries
        // so instead we need to parse the modules folder
        $db->setQuery("SELECT * FROM #__modules WHERE module LIKE 'mod_%' AND iscore != 1 GROUP BY module, client_id");
        $results = $db->loadObjectList();
        foreach ($results as $row)
        {
            // path to module directory (admin or site)
            if ($row->client_id == "1")
            {
                $moduleBaseDir = JPATH_ADMINISTRATOR . "/modules";
            }
            else
            {
                $moduleBaseDir = JPATH_SITE . "/modules";
            }

            $files = glob($moduleBaseDir . '/' . $row->module . "/*.xml", GLOB_NOSORT);
            foreach ($files as $file)
            {
                if ($data = JApplicationHelper::parseXMLInstallFile($file))
                {
                    if ($data['authorUrl'] != 'www.joomla.org') //we don't want joomla module
                    {
                        $base_dir = ($row->client_id == "1") ? JPATH_ADMINISTRATOR : JPATH_SITE;
                        $lang->load($row->module, $base_dir, 'en-GB', true);
                        $modules[] = array(
                            'name' => JText::_($data['name']),
                            'realname' => $row->module,
                            'version' => $data['version'],
                            'authorurl' => $data['authorUrl'],
                            'creationdate' => $data['creationdate']
                        );
                    }
                }
            }
        }

        /*         * ******************************
         * PLUGINS
         * ****************************** */
        $db->setQuery("SELECT * FROM #__plugins WHERE iscore != 1");
        $results = $db->loadObjectList();
        foreach ($results as $row)
        {
            $files = glob($pluginsBaseDir . '/' . $row->folder . "/*.xml", GLOB_NOSORT);
            foreach ($files as $file)
            {
                if (preg_match('#\.xml$#i', $file)) // if it's a xml
                {
                    if ($data = JApplicationHelper::parseXMLInstallFile($file))
                    {
                        if ($data['authorUrl'] != 'www.joomla.org' && $row->name == $data['name'])//we don't want joomla plugin
                        {
                            $lang->load(strtolower($data['name']), JPATH_ADMINISTRATOR, 'en-GB', true);
                            $plugins[] = array(
                                'name' => JText::_($data['name']),
                                'realname' => $row->folder,
                                'version' => $data['version'],
                                'authorurl' => $data['authorUrl'],
                                'creationdate' => $data['creationdate']
                            );
                            continue;
                        }
                    }
                }
            }
        }

        return array('components' => $components, 'modules' => $modules, 'plugins' => $plugins);
    }

    /**
     * Get full list of extensions, separated by type (components, modules, 
     * plugins, libraries, other)
     * 
     * @return  array of arrays
     */
    function getExtensions()
    {
        if ('1.5' == Watchfulli::joomla()->RELEASE)
        {
            return $this->getLegacyExtensions();
        }
        jimport('joomla.utilities.xmlelement');
        libxml_use_internal_errors(true); // Disable libxml errors and allow to fetch error information as needed
        $lang = & JFactory::getLanguage();

        $components = $modules = $plugins = $libraries = $other = array();

        $db = & JFactory::getDBO();
        $db->setQuery('SELECT name,type,element,folder,client_id, e.extension_id,
    @updId := IFNULL((SELECT update_id FROM #__updates as u WHERE u.extension_id = e.extension_id LIMIT 0,1), 0) AS updateId,
    IFNULL((SELECT version FROM #__updates as u WHERE u.extension_id = e.extension_id LIMIT 0,1), 0) AS updateVersion,
    IF(@updId > 0,1,0) as vUpdate
    FROM #__extensions as e');
        $rows = $db->loadObjectList();
        //sort data
        foreach ($rows as $row)
        {
            $xml->name = $xml->version = $xml->authorUrl = $xml->creationDate = '';
            switch ($row->type)
            {
                case 'component':
                    $componentBaseDir = ($row->client_id == '1') ? JPATH_ADMINISTRATOR . '/components' : JPATH_SITE . '/components';
                    $files = glob($componentBaseDir . '/' . $row->element . "/*.xml", GLOB_NOSORT);
                    if (count($files) > 0)
                    {
                        foreach ($files as $file)
                        {
                            if ($xml = simplexml_load_file($file))
                            {
                                //we don't want joomla component and we check if it's an install xml
                                if ($xml->authorUrl != 'www.joomla.org' && ($xml->getName() == 'install' || $xml->getName() == 'extension'))
                                {
                                    $base_dir = ($row->client_id == '1') ? JPATH_ADMINISTRATOR : JPATH_SITE;
                                    $lang->load($row->element, $base_dir, 'en-GB', true);

                                    $components[$row->extension_id] = array(
                                        'name' => (string) JText::_($xml->name),
                                        'realname' => (string) $row->element,
                                        'version' => (string) $xml->version,
                                        'authorurl' => (string) $xml->authorUrl,
                                        'creationdate' => (string) $xml->creationDate,
                                        'vUpdate' => (string) $row->vUpdate,
                                        'updateVersion' => (string) $row->updateVersion,
                                        'updateServer' => (string) $xml->updateservers->server,
                                        'extId' => (string) $row->updateId
                                    );
                                }
                            }
                        }
                    }
                    // search for LiveUpdate config file
                    if ($updateServer = $this->getLiveUpdateServer($componentBaseDir."/".$row->element)) {
                        $components[$row->extension_id]['updateServer'] = $updateServer;
                    }
                    // search for Akeeba variants (Core/Pro/etc...)
                    if ($akeebaVariant = $this->getAkeebaVariant($componentBaseDir."/".$row->element,$row->element)) {
                        $components[$row->extension_id]['akeebaVariant'] = $akeebaVariant;
                    }
                    break;

                case 'module':
                    $moduleBaseDir = ($row->client_id == '1') ? JPATH_ADMINISTRATOR . '/modules' : JPATH_SITE . '/modules';
                    $files = glob($moduleBaseDir . '/' . $row->element . "/*.xml", GLOB_NOSORT);
                    if (count($files) > 0)
                    {
                        foreach ($files as $file)
                        {
                            if ($xml = simplexml_load_file($file, 'JXMLElement'))
                            {
                                //we don't want joomla component and we check if it's an install xml
                                if ($xml->authorUrl != 'www.joomla.org' && ($xml->getName() == 'install' || $xml->getName() == 'extension'))
                                {
                                    $base_dir = ($row->client_id == '1') ? JPATH_ADMINISTRATOR : JPATH_SITE;
                                    $lang->load($row->element, $base_dir, 'en-GB', true);

                                    $modules[$row->extension_id] = array(
                                        'name' => (string) JText::_($xml->name),
                                        'realname' => (string) $row->element,
                                        'version' => (string) $xml->version,
                                        'authorurl' => (string) $xml->authorUrl,
                                        'creationdate' => (string) $xml->creationDate,
                                        'vUpdate' => (string) $row->vUpdate,
                                        'updateVersion' => (string) $row->updateVersion,
                                        'updateServer' => (string) $xml->updateservers->server,
                                        'extId' => (string) $row->updateId
                                    );
                                }
                            }
                        }
                    }
                    break;

                case 'plugin':
                    jimport('joomla.filesystem.folder');
                    $base = JPATH_ROOT . '/plugins/' . $row->folder . '/' . $row->element;
                    if (!JFolder::exists($base))
                    {
                        $base = JPATH_ROOT . '/plugins/' . $row->folder;
                    }
                    $files = glob($base . '/' . $row->element . '*.xml', GLOB_NOSORT);
                    if (count($files) > 0)
                    {
                        foreach ($files as $file)
                        {
                            if ($xml = simplexml_load_file($file, 'JXMLElement'))
                            {
                                //we don't want joomla component and we check if it's an install xml
                                if ($xml->authorUrl != 'www.joomla.org' && ($xml->getName() == 'install' || $xml->getName() == 'extension'))
                                {
                                    $lang->load('plg_' . $row->folder . '_' . $row->element, JPATH_ADMINISTRATOR, 'en-GB');
                                    $plugins[$row->extension_id] = array(
                                        'name' => (string) JText::_($xml->name),
                                        'realname' => (string) 'plg_' . $row->folder . '_' . $row->element,
                                        'version' => (string) $xml->version,
                                        'authorurl' => (string) $xml->authorUrl,
                                        'creationdate' => (string) $xml->creationDate,
                                        'vUpdate' => (string) $row->vUpdate,
                                        'updateVersion' => (string) $row->updateVersion,
                                        'updateServer' => (string) $xml->updateservers->server,
                                        'extId' => (string) $row->updateId
                                    );
                                }
                            }
                        }
                    }
                    break;

                case 'library':
                    jimport('joomla.filesystem.folder');
                    $base = JPATH_ROOT . '/administrator/manifests/libraries';
                    $files = glob($base . '/' . $row->element . '*.xml', GLOB_NOSORT);
                    if (count($files) > 0)
                    {
                        foreach ($files as $file)
                        {
                            if ($xml = simplexml_load_file($file, 'JXMLElement'))
                            {
                                //we don't want joomla component and we check if it's an install xml
                                if ($xml->authorUrl != 'www.joomla.org' && ($xml->getName() == 'install' || $xml->getName() == 'extension'))
                                {
                                    $libraries[$row->extension_id] = array(
                                        'name' => (string) JText::_($xml->name),
                                        'realname' => (string) $row->element,
                                        'version' => (string) $xml->version,
                                        'authorurl' => (string) $xml->authorUrl,
                                        'creationdate' => (string) $xml->creationDate,
                                        'vUpdate' => (string) $row->vUpdate,
                                        'updateVersion' => (string) $row->updateVersion,
                                        'updateServer' => (string) $xml->updateservers->server,
                                        'extId' => (string) $row->updateId
                                    );
                                }
                            }
                        }
                    }
                    break;

                default:
                    if ($row->name && $row->vUpdate == 1 && $row->name != 'files_joomla')
                    {
                        $other[$row->extension_id] = array(
                            'name' => (string) $row->name,
                            'realname' => (string) $row->name,
                            'version' => '0',
                            'authorurl' => (string) $row->type,
                            'creationdate' => '',
                            'vUpdate' => (string) $row->vUpdate,
                            'updateVersion' => (string) $row->updateVersion,
                            'extId' => (string) $row->updateId
                        );
                    }
                    break;
            }
        }
        return array('components' => $components, 'modules' => $modules, 'plugins' => $plugins, 'libraries' => $libraries, 'other' => $other);
    }

    /**
     * Get Joomla and system versions
     * 
     * @return string
     */
    public function getVersions()
    {
        $morevalues = array();
        $version = new JVersion();
        if ('1.5' == Watchfulli::joomla()->RELEASE)
        {
            $upd->version = $upd->jUpdate = null;
        }
        else
        {
            $this->db->setQuery('SELECT IFNULL(update_id,0) AS jUpdate, version FROM #__updates WHERE name = "Joomla"');
            $upd = $this->db->loadObject();
        }

        //some versions
        $morevalues['j_version'] = $version->getShortVersion();
        $morevalues['jUpdate'] = $upd->jUpdate;
        $morevalues['jUpd_version'] = $upd->version;
        $morevalues['php_version'] = phpversion();
        $morevalues['mysql_version'] = $this->db->getVersion();
        //server
        if (isset($_SERVER['SERVER_SOFTWARE']))
        {
            $serverSoft = $_SERVER['SERVER_SOFTWARE'];
        }
        else if (($sf = getenv('SERVER_SOFTWARE')))
        {
            $serverSoft = $sf;
        }
        else
        {
            $serverSoft = 'NOT_FOUND';
        }

        $morevalues['server_version'] = $serverSoft;
        return $morevalues;
    }

    /**
     * Get data for some important system files
     * 
     * @return string
     */
    function getFilesProperties()
    {
        $filesProperties = array();
        //files to check
        $files = array(
            JPATH_ROOT . '/index.php',
            JPATH_CONFIGURATION . '/configuration.php',
            JPATH_ROOT . '/administrator/index.php',
            JPATH_ROOT . '/.htaccess',
        );

        //searching the current template name
        $this->db->setQuery('SELECT DISTINCT template, client_id FROM #__template_styles WHERE template != "joomla_admin"');
        $currentsTmpl = $this->db->loadObjectList();
        if (!empty($currentsTmpl))
        {
            foreach ($currentsTmpl as $tmpl)
            {
                if ($tmpl->client_id == 0 && is_dir(JPATH_ROOT . '/templates/' . $tmpl->template))
                {
                    $files[] = JPATH_ROOT . '/templates/' . $tmpl->template . '/index.php';
                }
                if ($tmpl->client_id == 1 && is_dir(JPATH_ROOT . '/administrator/templates/' . $tmpl->template))
                {
                    $files[] = JPATH_ROOT . '/administrator/templates/' . $tmpl->template . '/index.php';
                }
            }
        }
        foreach ($files as $file)
        {
            // if the file exists
            if (file_exists($file))
            {
                $fp = fopen($file, 'r');
                $fstat = fstat($fp);
                fclose($fp);
                $checksum = md5_file($file);
            }
            elseif ($file != JPATH_ROOT . '/.htaccess')
            { //If not, we say that the file can't be found
                $checksum = $fstat['size'] = $fstat['mtime'] = 'NOT_FOUND';
            }
            $file = array('rootpath' => $file, 'size' => $fstat['size'], 'modificationtime' => $fstat['mtime'], 'checksum' => $checksum);
            $filesProperties[] = $file;
        }
        return $filesProperties;
    }

    /**
     * Get all data from Watchfulli plugins (apps)
     * 
     * @return array
     */
    public function getApps()
    {
        $oldPluginsValue = JRequest::getVar('jmpluginsexvalues');
        jimport('joomla.plugin.helper');
        JPluginHelper::importPlugin('watchfulliApps');
        $dispatcher = JDispatcher::getInstance();
        if ('1.5' == Watchfulli::joomla()->RELEASE)
        {
            $plugins = $dispatcher->trigger('appMainProgram', array($oldPluginsValue));

            foreach ($plugins as $keyP => $plugin)
            {
                foreach ($plugin as $keyV => $value)
                {
                    if ($keyV == "params" || $keyV == "_subject")
                    {
                        unset($plugins[$keyP]->$keyV);
                    }
                }
            }
            return $plugins;
        }
        else
        {
            return $dispatcher->trigger('appMainProgram', $oldPluginsValue);
        }
    }

    /**
     * Get the update id (if present) for a given extension id
     * 
     * @param   int $extension_id
     * @return  int (0 if not found)
     */
    public function getUpdateId($extension_id)
    {
        $update_id = 0;
        $query = "SELECT update_id FROM #__updates WHERE extensions_id = $extension_id";
        $this->db->setQuery($query);
        if ($result = $this->db->loadResult())
        {
            $update_id = $result;
        }
        return $update_id;
    }
    
    /**
     * Get the LiveUpdate server URL from config file
     * 
     * @param string    $component_path
     * @return string   the update server
     * @return boolean  false if not found
     */
    private function getLiveUpdateServer($component_path)
    {
        if (!file_exists($component_path."/liveupdate/config.php")) {
            return false;
        }

        // Parse the file to get the variable. I tried getting an instance of 
        // the object and use getUpdateURL() but I had many troubles
        if ($fh = fopen($component_path."/liveupdate/config.php", "r")) {
          $result = array();
          while ($line = fgets($fh)) {
            if (preg_match('/var \$_updateURL\s*=\s*(\'|\")([^\'\"]*)/',$line,$result)) {
              return $result[2];
            }
          }
        }
        return false;
    }
    
    /**
     * Get the Akeeba variant (Core/Pro)
     * 
     * @param string    $component_path
     * @return mixed    the variant string or false if not found
     */
    private function getAkeebaVariant($component_path,$extensions_name)
    {
        if (!file_exists($component_path."/version.php")) {
            return false;
        }
        
        require_once $component_path."/version.php";
        switch($extensions_name)
        {
            case 'com_admintools':
                return ADMINTOOLS_PRO ? 'Pro' : 'Core';
                break;
            case 'com_akeeba':
                return AKEEBA_PRO ? 'Pro' : 'Core';
                break;
            case 'com_akeebasubs':
                return AKEEBASUBS_PRO ? 'Pro' : 'Core';
                break;
            case 'com_ars':
                return 'Core';
                break;
            case 'com_ats':
                return 'Pro';
                break;
        }

        return false;
    }

}
