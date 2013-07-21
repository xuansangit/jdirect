<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */

// no direct access
(defined('_JEXEC') or defined('JPATH_PLATFORM')) or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class com_watchfulliInstallerScript
{
	public function postflight($type, $parent)
	{
		if ('uninstall' == $type) return;
		
		$version = new JVersion;
		$pluginFolder = JPATH_ADMINISTRATOR . '/components/com_jmonitoringslave/jmonitoringpluginmonitoring';
		// recreate the folder
		if (JFolder::exists($pluginFolder)) JFolder::delete($pluginFolder);
		JFolder::create($pluginFolder);
		// copy the files
		JFile::copy(
			JPATH_ADMINISTRATOR.'/components/com_watchfulli/jmonitoringpluginmonitoring/jmonitoringpluginmonitoring.php',
			$pluginFolder.'/jmonitoringpluginmonitoring.php'
		);

		$mainframe = JFactory::getApplication();
		$hasfopen = in_array(ini_get('allow_url_fopen'), array('On','1'));
		$key = md5('watch'.$mainframe->getCfg('secret').'fulli');
		// Show the installation results form
		if (version_compare($version->getShortVersion(), '3.0.0', '>=')) {
			$mainframe->enqueueMessage('<strong>Congratulations! You have successfully installed the Watchful.li client.</strong>');
			$mainframe->enqueueMessage('To complete the installation, please copy the following <em>secret key</em> and paste it into the <em>Details</em>. area of the <em>Site Manager</em> at watchful.li');
			$mainframe->enqueueMessage($key);
			$mainframe->enqueueMessage('If you need the locate the <em>secret key</em> again, please select <a href="index.php?option=com_watchfulli">watchfulli</a> from the Components menu.');
			if (!$hasfopen) $mainframe->enqueueMessage("The update functions of your website won't work : allow_url_fopen is disabled.");
      //AdminTools check
      if($this->isAdmintoolsInstalled()){
        $mainframe->enqueueMessage('It seems that Akeeba AdminTools is installed on this site. You should probably change something in your AdminTools settings to get Watchfulli to work. See here: <a hrer="https://watchful.li/support-services/kb/article/whitelisting-watchful-in-your-firewall-or-htaccess-file">https://watchful.li/support-services/kb/article/whitelisting-watchful-in-your-firewall-or-htaccess-file</a>');
      }
			return;
		}
		
		?>
		
		<h2>Congratulations! You have successfully installed the Watchful.li client.</h2>
		<p>To complete the installation, please copy the following <em>secret key</em> and paste it into the <em>Details</em>. area of the <em>Site Manager</em> at watchful.li</p>
		<p><input readonly="readonly" type="text" style="width:250px;" size="55" value="<?php echo $key; ?>" /></p>
		<p>If you need the locate the <em>secret key</em> again, please select <a href="index.php?option=com_watchfulli">watchfulli</a> from the Components menu.</p>
		<?php if ('1.5' != $version->RELEASE && !$hasfopen) : ?>
		<p>The update functions of your website won't work : allow_url_fopen is disabled.</p>
		<?php endif;

    if($this->isAdmintoolsInstalled()) : ?>
    <p><strong>Important</strong>: it seems that <strong>Akeeba AdminTools</strong> is installed on this site.<br />You will have to change its settings to get Watchfulli to work.<br />More info at <a href="https://watchful.li/support-services/kb/article/whitelisting-watchful-in-your-firewall-or-htaccess-file">https://watchful.li/support-services/kb/article/whitelisting-watchful-in-your-firewall-or-htaccess-file</a></p>
    <?php endif;
	}

  public function isAdmintoolsInstalled()
  {
    return( file_exists( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admintools'.DIRECTORY_SEPARATOR.'version.php' ));
  }
}

$version = new JVersion;
if ('1.5' == $version->RELEASE) {
	$script = new com_watchfulliInstallerScript;
	$script->postflight('install', $this);
	// fix manifest
	$base = JPATH_ADMINISTRATOR . '/components/com_watchfulli';
	if (JFile::exists("$base/z.watchfulli.xml")) {
		JFile::copy("$base/z.watchfulli.xml", "$base/com_watchfulli.xml");
	}
	else if (JFile::exists($this->parent->getPath('source') . '/z.watchfulli.xml')) {
		JFile::copy($this->parent->getPath('source') . '/z.watchfulli.xml', "$base/com_watchfulli.xml");
	}
}
