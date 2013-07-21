<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.4.1
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2013 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<script language="javascript" type="text/javascript">
<!--
	function submitimport() {
		var form = document.adminForm;

		form.task.value = 'import';
		form.submit();
	}

	function submitdbace() {
		var form = document.adminForm;

		form.task.value = 'importdbace';
		form.submit();
	}

	function submitdbsh() {
		var form = document.adminForm;

		form.task.value = 'importdbsh';
		form.submit();
	}
	
//-->
</script>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">
<fieldset class="adminform">
<legend><?php echo JText::_('COM_SEF_IMPORT_URLS_FROM_FILE'); ?></legend>
<table class="adminform table">
<tr>
    <th colspan="2">
        <?php echo JText::_('COM_SEF_INFO_IMPORT_FILE_FORMATS'); ?>
        <br />
        <?php echo JText::_('COM_SEF_INFO_IMPORT_FILE_METATAGS'); ?>
    </th>
</tr>
<tr>
    <td width="120">
        <label for="install_package"><?php echo JText::_('COM_SEF_IMPORT_FILE'); ?>:</label>
    </td>
    <td>
        <input class="input_box" id="importfile" name="importfile" type="file" size="57" />
        <input class="button btn btn-primary" type="button" value="<?php echo JText::_('COM_SEF_IMPORT_URLS'); ?>" onclick="submitimport()" />
    </td>
</tr>
</table>
</fieldset>

<?php
if( $this->aceSefPresent || $this->shSefPresent )
{
?>

<fieldset class="adminform">
<legend><?php echo JText::_('COM_SEF_IMPORT_URLS_FROM_DATABASE'); ?></legend>
<table class="adminform">
<tr>
    <th>
        <?php echo JText::_( 'JoomSEF has detected former installation of another SEF component. You can automatically import SEF URLs from it using the following button.' ); ?>
    </th>
</tr>
<tr>
    <td>
    <?php
    if( $this->aceSefPresent )
    {
        ?>
        <input class="button" type="button" value="<?php echo JText::_( 'Import URLs from AceSEF table' ); ?>" onclick="submitdbace()" />
        <?php
    }
    ?>
    <?php
    if( $this->shSefPresent )
    {
        ?>
        <input class="button" type="button" value="<?php echo JText::_( 'Import URLs from sh404SEF table' ); ?>" onclick="submitdbsh()" />
        <?php
    }
    ?>
    </td>
</tr>
</table>
</fieldset>

<?php
}
?>

<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="sefurls" />
</form>
