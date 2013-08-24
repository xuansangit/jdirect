<?php
/**
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
     
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="">
	  <tr valign="top">
	    <td width="650">
            <div id="cpanel">
            <table width="99%" align="center">
			<tr>
            	<td width="20%" valign="top">

	              <div style="float:left;">
	              <div class="icon">
	                <a href="index.php?option=com_yooniqueacl&controller=groups&task=list">
	                    <div class="iconimage">
	                    	<?php $text = JText::_( 'Groups' ); ?>
	                        <img src="<?php echo JURI::root(); ?>media/com_yooniqueacl/images/groups.png" alt="<?php echo $text; ?>" align="middle" name="image" border="0" /></div>
	                    <?php echo $text; ?></a>
	              </div>
	              </div>
				  
		        </td>
            	<td width="20%" valign="top">
				  	
	              <div style="float:left;">
	              <div class="icon">
	                <a href="index.php?option=com_yooniqueacl&controller=variables&task=list">
	                    <div class="iconimage">
	                    	<?php $text = JText::_( 'Variables' ); ?>
	                        <img src="<?php echo JURI::root(); ?>media/com_yooniqueacl/images/variables.png" alt="<?php echo $text; ?>" align="middle" name="image" border="0" /></div>
	                    <?php echo $text; ?></a>
	              </div>
	              </div>

		        </td>
            	<td width="20%" valign="top">
				  	
	              <div style="float:left;">
	              <div class="icon">
	                <a href="index.php?option=com_yooniqueacl&controller=siteitems&task=list">
	                    <div class="iconimage">
	                    	<?php $text = JText::_( 'Items' ); ?>
	                        <img src="<?php echo JURI::root(); ?>media/com_yooniqueacl/images/siteitems.png" alt="<?php echo $text; ?>" align="middle" name="image" border="0" /></div>
	                    <?php echo $text; ?></a>
	              </div>
	              </div>

		        </td>
            	<td width="20%" valign="top">
				  	
	              <div style="float:left;">
	              <div class="icon">
	                <a href="index.php?option=com_yooniqueacl&controller=users&task=list">
	                    <div class="iconimage">
	                    	<?php $text = JText::_( 'Users' ); ?>
	                        <img src="<?php echo JURI::root(); ?>media/com_yooniqueacl/images/users.png" alt="<?php echo $text; ?>" align="middle" name="image" border="0" /></div>
	                    <?php echo $text; ?></a>
	              </div>
	              </div>
				  				  
		        </td>
            	<td width="20%" valign="top">

	              <div style="float:left;">
	              <div class="icon">
	                <a href="index.php?option=com_yooniqueacl&controller=config">
	                    <div class="iconimage">
	                    	<?php $text = JText::_( 'Configuration' ); ?>
	                        <img src="<?php echo JURI::root(); ?>media/com_yooniqueacl/images/config.png" alt="<?php echo $text; ?>" align="middle" name="image" border="0" /></div>
	                    <?php echo $text; ?></a>
	              </div>
	              </div>              

		        </td>
            	<td width="20%" valign="top">

	              <div style="float:left;">
	              <div class="icon">
	                <a href="index.php?option=com_yooniqueacl&controller=statistics">
	                    <div class="iconimage">
	                    	<?php $text = JText::_( 'Statistics' ); ?>
	                        <img src="<?php echo JURI::root(); ?>media/com_yooniqueacl/images/statistics.png" alt="<?php echo $text; ?>" align="middle" name="image" border="0" /></div>
	                    <?php echo $text; ?></a>
	              </div>
	              </div>              

		        </td>
			</tr>
            </table>

            </div>
	    </td>
	    <td>
        

	    </td>
	  </tr>
	  </table>
				
