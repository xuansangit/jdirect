<?php
/**
 * @version   1.2beta-5
 * @date      Sun Apr 15 16:53:32 2012 -0700
 * @package   yoonique ACL
 * @author    yoonique[.]net
 * @copyright Copyright (C) 2012 yoonique[.]net and all rights reserved.
 *
 * based on
 *
 * @version   1.4.17
 * @date      Fri Mar 29 15:34:01 2013 -0700
 * @package   yoonique ACL
 * @author    yoonique[.]net
 * @copyright Copyright (C) 2012 yoonique[.]net and all rights reserved.
 *
 * based on
 *
 * @package	Juga
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
?>

		<?php JHTML::_('behavior.tooltip'); ?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
            <th class='yooniqueacl_config'>
            <?php echo JText::_( 'Configuration' ); ?>
            </th>            
		</tr>
		</table>

		<table width="100%">
        <tbody>
            <tr>
                <td valign="top">
                    <table class="adminlist">
                    <tbody>
                        <tr>
                            <td width="20%" align="right">
								<?php echo JText::_( 'Public Access Group' ); ?>:
                            </td>
                            <td>

                            </td>
                            <td width="80%" >
                            	<?php echo YooniqueaclHelperGroup::getSelectList( "public_yooniqueacl", htmlspecialchars( $this->config->get('public_yooniqueacl', '0') ) ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">
								<?php echo JText::_( 'Default Custom Error URL' ); ?>:
                            </td>
                            <td>

                            </td>
                            <td width="80%" >
		                        <input name="default_ce" type="text" class="text_area" size='50' value="<?php echo htmlspecialchars( $this->config->get('default_ce', "index.php?option=com_users" ) ) ; ?>" />
                            </td>
                        </tr>
                    </tbody>
                    </table>

				</td>
            </tr>
        </tbody>
		</table>

        
    <?php $option	= JRequest::getCmd( 'option' ); ?>
		<input type="hidden" name="option" value="<?php echo htmlspecialchars( $option ); ?>" />
		<input type="hidden" name="controller" value="<?php echo htmlspecialchars( $this->_name ); ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="boxchecked" value="" />
        
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>        