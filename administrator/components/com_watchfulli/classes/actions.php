<?php
/**
 * @version         $Id: classes/actions.php 2013-05-23 15:13:00Z gibiwatch $
 * @package         Watchful Client
 * @subpackage      backend
 * @author          Watchful
 * @authorUrl       http://www.watchful.li
 * @copyright       Copyright (c) 2012-2013 watchful.li
 */
defined('WATCHFULLI_PATH') or die;

require_once dirname(__FILE__) . '/watchfulli.php';
class watchfulliActions
{

    //Constructor does security checks
    function watchfulliActions()
    {
        if (!Watchfulli::checkToken())
            die;
    }

    function test()
    {
        die("<~ok~>");
    }

    function doUpdate()
    {
        $app = JFactory::getApplication();
        
        // get parameters from request
        $extParams = JRequest::getVar('extParams');
        if ($extParams)
        {
            $extParams = json_decode($extParams);
            if (is_object($extParams))
            {
                if (isset($extParams->update_url))
                {
                    $this->update_url = $extParams->update_url;
                }
                if (isset($extParams->package_name))
                {
                    $this->package_name = $extParams->package_name;
                }
            }
        }
        
        
        $id = $app->input->get('extId', 0);
        if (!$id) $app->close($this->doInstall()); // No update ID, try normal install

        jimport('joomla.updater.update');
        jimport('joomla.database.table');

        $updaterow = JTable::getInstance('update');
        $updaterow->load($id);

        if (!$updaterow->update_id)
        {
            $app->close("COM_JMONITORING_CANT_FIND_UPDATE_RECORD");
        }

        $update = new JUpdate;

        if ($update->loadFromXML($updaterow->detailsurl))
        {
            if (!$this->update_url) {
              if(isset($update->get('downloadurl')->_data)) {
                  $this->update_url = $update->downloadurl->_data;
              }
              else {
                  $app->close("COM_JMONITORING_CANT_GET_UPDATE_URL");
              }
            }

            $p_file = JInstallerHelper::downloadPackage($this->update_url);

            // Was the package downloaded?
            if (!$p_file)
            {
                $app->close("COM_JMONITORING_CANT_DOWNLOAD_UPDATE");
            }

            $config = JFactory::getConfig();
            $tmp_dest = $config->get('tmp_path');

            // Rename the file with custom name
            if (isset($this->package_name))
            {
              JFile::move($tmp_dest.'/'.$p_file, $tmp_dest.'/'.$this->package_name);
              $p_file = $this->package_name;
            }

            // Unpack the downloaded package file
            $package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

            // Get an installer instance
            $installer = JInstaller::getInstance();
            $update->set('type', $package['type']);

            $thisApp = &JFactory::getApplication();

            // fix for Gantry (and others, presumably)
            if (!class_exists('JAdministratorHelper'))
            {
                require_once JPATH_ADMINISTRATOR . '/includes/helper.php';
            }
            if (!class_exists('JAdministrator'))
            {
                require_once JPATH_ADMINISTRATOR . '/includes/application.php';
            }
            // Gantry assumes that the install will ALWAYS happen using JAdministrator and not JSite
            // so let's try to replace the site instance with a new one (at least for the install process)
            if (!($thisApp instanceof JAdministrator))
            {
                JFactory::$application = new WatchfulliApplication;
            }

            // Install the package
            if (!($installer->update($package['dir']) || $this->checkInstall($id)))
            {
                $app->close("COM_JMONITORING_CANT_INSTALL_UPDATE"); // There was an error updating the package
            }

            // replace application
            JFactory::$application = $thisApp;

            // Quick change
            $this->type = $package['type'];

            // Cleanup the install files
            if (!is_file($package['packagefile']))
            {
                $config = JFactory::getConfig();
                $package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
            }

            JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
            $ver = $updaterow->version;
            $updaterow->delete($id);
            ob_clean();
            die("ok_" . $ver);
        }
        $app->close("COM_JMONITORING_CANT_GET_UPDATE");

    }

    /**
     * Manually check if the update has been completed successfully
     * This is required because some of installer scripts do not return a clear
     * true / false message
     * 
     * @param int $id update id
     * @return bool true if update is ok
     */

    public function checkInstall($id)
    {
        // If the Id is no longer in the updater table, we can guess that the install went fine
        jimport('joomla.database.table');
        $updaterow = JTable::getInstance('update');
        if (!$updaterow->load($id))
            return true;
        $updated_version = str_replace(array('FREE', 'PRO'), '', $updaterow->version);

        // Get current version from #__extensions and compare it with the one in the #__updates
        $extension = JTable::getInstance('extension');
        $extension->load($updaterow->extension_id);
        $current_version = json_decode($extension->manifest_cache)->version;
        $current_version = str_replace(array('FREE', 'PRO'), '', $current_version);

        // If current version is equal or better, return true
        if (version_compare($current_version, $updated_version, '>='))
        {
            return true;
        }
    }
    
    /**
     * With this function we just install a package with the passed URL
     */
    public function doInstall()
    {
        if (!$this->update_url) {
            return("COM_JMONITORING_CANT_GET_UPDATE_URL");
        }
        
        $file = JInstallerHelper::downloadPackage($this->update_url);
        if (!$file) {
            return("COM_JMONITORING_CANT_DOWNLOAD_UPDATE");
        }
        
        $package = $this->unpackFile($file);
        if (!$package) {
            return("COM_JMONITORING_CANT_UNPACK_UPDATE");
        }
        
        $installer = JInstaller::getInstance();
        if (!$installer->install($package['dir']))
        {
            return("COM_JMONITORING_CANT_INSTALL_UPDATE"); // There was an error updating the package
        }
        
        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
        ob_clean();
        die("ok_" . $ver);        
    }
    
    /** 
     * Unpack a given file
     * 
     * @param string $file   the name of the file to unpack
     * @return object   a package object
     */
    public function unpackFile($file)
    {
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path',JPATH_SITE.'/tmp');

        // Rename the file with custom name if present
        if (isset($this->package_name))
        {
            JFile::move($tmp_path.'/'.$file, $tmp_path.'/'.$this->package_name);
            $file = $this->package_name;
        }
        $package = JInstallerHelper::unpack($tmp_path . '/' . $file);
        return $package;
    }
}

if (!class_exists('JAdministrator'))
{
    require_once JPATH_ADMINISTRATOR . '/includes/application.php';
}

class WatchfulliApplication extends JAdministrator
{
    public $_name = 'Administrator';

    public function redirect($url)
    {
        return true;
    }

}
