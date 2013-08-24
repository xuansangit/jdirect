<?php defined('_JEXEC') or die('Restricted access'); ?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
            <th class='yooniqueacl_users'>
            	<?php echo JText::_( 'Users' ); ?>
				<br>				
				<small>
					<?php echo JText::_( 'Flex' ).': '; echo $this->selectList_flex; ?>
				</small>
            </th>            
          <td class='right'><?php echo JText::_( 'Filter' ); ?>:</td>
          <td class="input">
            <input type="text" name="search" value="<?php echo htmlspecialchars( $this->search );?>" class="text_area" onchange='submitform("<?php echo htmlspecialchars( JRequest::getCmd( 'task' ) ); ?>")' />
          </td>
			<td class='right'>
            <?php echo $this->groupsFilterList;  ?>
			</td>
		</tr>
		</table>

		<table class="adminlist table table-striped">
		<thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                	<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" />
                </th>
                <th class="title">
	                <?php echo JHTML::_('grid.sort', JText::_( 'ID' ), "id", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>                
                <th class="title">
					<?php echo JHTML::_('grid.sort', JText::_( 'Name' ), "name", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                    +
                    <?php echo JHTML::_('grid.sort', JText::_( 'Username' ), "username", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
    	            <?php echo JHTML::_('grid.sort', JText::_( 'email' ), "email", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
	                <?php echo JText::_( 'Current' )." ".JText::_( 'Groups' ); ?>
                </th>
                <th class="title">
	                <?php echo JText::_( 'Groups' ); ?>
                    <img onmouseover="this.style.cursor='pointer';" onclick="document.adminForm.toggle.checked=true; jQuery('input:checkbox').attr('checked','checked'); submitform('enroll');" src="../media/com_yooniqueacl/images/add.png" border="0" width="18" height="18" alt="<?php echo JText::_( 'Enroll' ); ?>" title="<?php echo JText::_( 'Enroll' ); ?>" name="<?php echo JText::_( 'Enroll' ); ?>" />
					<img onmouseover="this.style.cursor='pointer';" onclick="document.adminForm.toggle.checked=true; jQuery('input:checkbox').attr('checked','checked'); submitform('withdraw');" src="../media/com_yooniqueacl/images/remove.png" border="0" width="18" height="18" alt="<?php echo JText::_( 'Withdraw' ); ?>" title="<?php echo JText::_( 'Withdraw' ); ?>" name="<?php echo JText::_( 'Withdraw' ); ?>" />
                </th>
                <th class="title">
	    	        <?php echo JText::_( 'Action' ); ?>
                </th>
            </tr>
		</thead>
        <tbody>
					<?php $option	= JRequest::getCmd( 'option' ); ?>
		<?php
        $k = 0;
		$database = &JFactory::getDBO();
        for ($i=0, $n=count( $this->items ); $i < $n; $i++)
        {
            $row =& $this->items[$i];
			$checked    = JHTML::_( 'grid.id', $i, $row->id );
			$userlink = JRoute::_( "index.php?option=com_users&task=user.edit&id=".$row->id );
			$definelink = JRoute::_( 'index.php?option='.htmlspecialchars( $option ).'&controller=user&task=define&id='. $row->id );
			?>
				<tr class="<?php echo "row$k"; ?>">
					<?php
                    echo "<td>"; echo $i + 1 + $this->pagination->limitstart; echo "</td>";
                    echo "<td>$checked</td>";
					?>

					<td>
						<?php echo $row->id; ?>
					</td>
					<td>
						<a href="<?php echo $userlink; ?>" title="<?php echo JText::_( 'Edit' ); ?>">
						<?php echo htmlspecialchars( $row->name ); ?>
						</a>
						<br />
						&nbsp;&nbsp;&bull;&nbsp;&nbsp;
						<a href="<?php echo $userlink; ?>" title="<?php echo JText::_( 'Edit' ); ?>">
						<?php echo htmlspecialchars( $row->username ); ?>
						</a>
					</td>
					<td>
						<a href="<?php echo $userlink; ?>" title="<?php echo JText::_( 'Edit' ); ?>">
						<?php echo htmlspecialchars( $row->email ); ?>
                        </a>
					</td>
                    <td>
                        <?php
                            $database->setQuery("SELECT * FROM  " . TABLE_YOONIQUEACL_GROUPS . ",  " . TABLE_YOONIQUEACL_U2G . " WHERE  " . TABLE_YOONIQUEACL_U2G . ".group_id =  " . TABLE_YOONIQUEACL_GROUPS . ".id AND  " . TABLE_YOONIQUEACL_U2G . ".user_id = '$row->id' "
                                                ." ORDER BY `title` ASC");
                            $user_groups = $database->loadObjectList();
    
                            if ($user_groups) {
                                foreach ($user_groups as $g) {
                                  echo "<a href='index.php?option=com_yooniqueacl&controller=groups&search=$g->title'>$g->title</a><br />";
                                } //endforeach
                            } //endif
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($this->groups) {
                          echo "<select name='group[$row->id]' size='1'>";
                          echo "<option value=''>&nbsp;&nbsp;&nbsp;</option>";
                          foreach ($this->groups as $dbg) {
                            echo "<option value='$dbg->id'>" . htmlspecialchars( $dbg->title ) . "&nbsp;&nbsp;&nbsp;</option>";
                          } // end foreach
                          echo "</select>";
                        } //end if
                        ?>
                    </td>
					<td>
                    	<center>
						<a href='<?php echo $definelink; ?>'>
						<img src="../media/com_yooniqueacl/images/switch_f2.png" border="0" width="18" height="18" alt="<?php echo JText::_( 'Select' ); ?>" title="<?php echo JText::_( 'Select' ); ?>" name="<?php echo JText::_( 'Select' ); ?>" />
						</a>
                        </center>
					</td>
				</tr>
        <?php 
        $k = 1 - $k;
        }
        ?>
        </tbody>
        <tfoot>
        	<tr>
                <td colspan='10'>
                    <div class="pagination">
                    <?php echo $this->pagination->getListFooter(); ?>
                    </div>            
                </td>
            </tr>
        </tfoot>

		</table>
                
		<input type="hidden" name="option" value="<?php echo htmlspecialchars( $option ); ?>" />
		<input type="hidden" name="controller" value="<?php echo htmlspecialchars( $this->_name ); ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="boxchecked" value="" />
		<input type="hidden" name="filter_order" value="<?php echo htmlspecialchars( $this->filter_order ); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo htmlspecialchars( $this->filter_order_Dir ); ?>" />
		</form>        
        
 
