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

		$mainframe = JFactory::getApplication();
		$database = &JFactory::getDBO();

		$stats = array();

		// new today siteitems
			$query = "SELECT COUNT(*) "
			. " FROM  " . TABLE_YOONIQUEACL_ITEMS
			. " WHERE created_datetime >= '".gmdate("Y-m-d H:i:s")."' "
			;
		$database->setQuery( $query );
		$stats["siteitems_today"] = $database->loadResult();

		// unassigned siteitems
			$query = "SELECT COUNT(*) "
			. " FROM " . TABLE_YOONIQUEACL_ITEMS . " AS i "
			. " LEFT JOIN " . TABLE_YOONIQUEACL_G2I . " AS g2i ON i.id = g2i.item_id  "
			. " WHERE 1 "
			. " AND g2i.group_id IS NULL "
			;
		$database->setQuery( $query );
		$stats["siteitems_unassigned"] = $database->loadResult();

		// total siteitems
			$query = "SELECT COUNT(*) "
			. " FROM  " . TABLE_YOONIQUEACL_ITEMS
			;
		$database->setQuery( $query );
		$stats["siteitems_total"] = $database->loadResult();

		// unassigned users
		$query = "SELECT COUNT(*) FROM #__users "
		." LEFT JOIN " . TABLE_YOONIQUEACL_U2G . " ON #__users.id =  " . TABLE_YOONIQUEACL_U2G . ".user_id "
		." WHERE  " . TABLE_YOONIQUEACL_U2G . ".group_id IS NULL "
		;
		$database->setQuery( $query );
		if ($database->getErrorNum()) {
			echo $database->stderr();
		}
		$stats["users_unassigned"] = $database->loadResult();

		// total users
		$query = "SELECT COUNT(*) FROM #__users ";
		$database->setQuery( $query );
		$stats["users_total"] = $database->loadResult();
		?>

<h3>
		<div class="">
			<table class="">

            <?php if ( $num = $stats["siteitems_today"] ) { ?>
			  <tr>
				<td>
					<?php
						$link = "index.php?option=com_yooniqueacl&controller=siteitems&order=createddate&order_dir=desc";
					?>
				</td>
				<td>
					<?php
						echo "<a href='".$link."'>".$num." ".JText::_( 'New Site Items' )."</a>";
					?>
				</td>
			  </tr>
              <?php } ?>

            <?php if ( $num = $stats["siteitems_unassigned"] ) { ?>
			  <tr>
				<td>
					<?php
						$link = "index.php?option=com_yooniqueacl&controller=siteitems&_group=-2";
					?>
				</td>
				<td>
					<?php
						echo "<a href='".$link."'>".$num." ".JText::_( 'Unassigned Items' )."</a>";
					?>
				</td>
			  </tr>
              <?php } ?>

            <?php if ( $num = $stats["siteitems_total"] ) { ?>
			  <tr>
				<td>
					<?php
						$link = "index.php?option=com_yooniqueacl&controller=siteitems&order=title&order_dir=asc&_group=";
					?>
				</td>
				<td>
					<?php
						echo "<a href='".$link."'>".$num." ".JText::_( 'Total Items' )."</a>";
					?>
				</td>
			  </tr>
              <?php } ?>

            <?php if ( $num = $stats["users_unassigned"] ) { ?>
			  <tr>
				<td>
					<?php
						$link = "index.php?option=com_yooniqueacl&controller=users&group_id=-2";
					?>
				</td>
				<td>
					<?php
						echo "<a href='".$link."'>".$num." ".JText::_( 'Unassigned Users' )."</a>";
					?>
				</td>
			  </tr>
              <?php } ?>

            <?php if ( $num = $stats["users_total"] ) { ?>
			  <tr>
				<td>
					<?php
						$link = "index.php?option=com_yooniqueacl&controller=users&group_id=";
					?>
				</td>
				<td>
					<?php
						echo "<a href='".$link."'>".$num." ".JText::_( 'Total Users' )."</a>";
					?>
				</td>
			  </tr>
              <?php } ?>

			</table>
		</div>
</h3>
	<?php

