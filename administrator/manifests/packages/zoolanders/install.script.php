<?php

class pkg_zoolandersInstallerScript {

	public function install($parent) {}

	public function uninstall($parent) {}

	public function update($parent) {}

	public function preflight($type, $parent)
	{
		// check dependencies if not uninstalling
		if($type != 'uninstall' && !$this->checkRequirements($parent)){
			Jerror::raiseWarning(null, $this->_error);
			return false;
		}

		// load config, necesary for some packages
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');
	}

	public function postflight($type, $parent, $results)
	{
		$extensions = array();
		foreach($results as $result) {
			$extensions[] = (object) array('name' => $result['name'], 'status' => $result['result'], 'message' => $result['result'] ? ($type == 'update' ? 'Updated' : 'Installed').' successfully' : 'NOT Installed');
		}

		// display extension installation results
		self::displayResults($extensions, 'Extensions', 'Extension');
	}

	/**
	 * check general requirements
	 * @version 1.1
	 *
	 * @return  boolean  True on success
	 */
	protected function checkRequirements($parent)
	{
		// get joomla release
		$joomla_release = new JVersion();
		$joomla_release = $joomla_release->getShortVersion();

		// manifest file minimum joomla version
		$min_joomla_release = $parent->get( "manifest" )->attributes()->version;

		/*
		 * abort if the current Joomla! release is older
		 */
		if( version_compare( (string)$joomla_release, (string)$min_joomla_release, '<' ) ) {
			$this->_error = "Joomla! v{$min_joomla_release} or higher required, please update it and retry the installation.";
			return false;
		}

		/*
		 * make sure ZOO exist, is enabled
		 */
		if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php')
			|| !JComponentHelper::getComponent('com_zoo', true)->enabled) {
			$this->_error = "ZOOlanders Extensions relies on <a href=\"http://www.yootheme.com/zoo\" target=\"_blank\">ZOO</a>, be sure is installed and enabled before retrying the installation.";
			return false;
		}

		// and up to date
		$zoo_manifest = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_zoo/zoo.xml');
		$min_zoo_release = $parent->get( "manifest" )->attributes()->zooversion;

		if( version_compare((string)$zoo_manifest->version, (string)$min_zoo_release, '<') ) {
			$this->_error = "ZOO v{$min_zoo_release} or higher required, please update it and retry the installation.";

			return false;
		}

		/*
		 * make sure ZLFW exist, is enabled
		 */
		if($min_zlfw_release = $parent->get( "manifest" )->attributes()->zlfwversion)
		{
			if (!JFile::exists(JPATH_ROOT.'/plugins/system/zlframework/zlframework.php')
					|| !JPluginHelper::isEnabled('system', 'zlframework')) {
				$this->_error = "ZOOlanders Extensions relies on <a href=\"https://www.zoolanders.com/extensions/zl-framework\" target=\"_blank\">ZL Framework</a>, be sure is installed and enabled before retrying the installation.";
				return false;
			}

			// and up to date
			$zlfw_manifest = simplexml_load_file(JPATH_ROOT.'/plugins/system/zlframework/zlframework.xml');

			if( version_compare((string)$zlfw_manifest->version, (string)$min_zlfw_release, '<') ) {
				$this->_error = "<a href=\"https://www.zoolanders.com/extensions/zl-framework\" target=\"_blank\">ZL Framework</a> v{$min_zlfw_release} or higher required, please update it and retry the installation.";
				return false;
			}
		}

		return true;
	}

	protected function displayResults($result, $name, $type) {

?>

		<h3><?php echo JText::_($name); ?></h3>
		<table class="adminlist table table-bordered table-striped" width="100%">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_($type); ?></th>
					<th width="60%"><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach ($result as $i => $ext) : ?>
					<tr class="row<?php echo $i++ % 2; ?>">
						<td class="key"><?php echo $ext->name; ?></td>
						<td>
							<?php $style = $ext->status ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
							<span style="<?php echo $style; ?>"><?php echo JText::_($ext->message); ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

<?php

	}

}