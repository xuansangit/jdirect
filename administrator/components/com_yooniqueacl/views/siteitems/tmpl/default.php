<?php defined('_JEXEC') or die('Restricted access'); ?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
            <th class='yooniqueacl_siteitems'>
				<small>
					<?php echo JText::_( 'Flex Group' ).': '; echo $this->selectList_flex; ?>
				</small>
				<p>
                <small>
                  <?php echo JText::_( 'Flex CE URL' ); ?>: <input type="text" name="flex_ce_url" class="text_area" size="35" value="<?php echo htmlspecialchars( JRequest::getVar( 'flex_ce_url' ) ); ?>">
                </small>
                </p>
            </th>            
          <td class='right'><?php echo JText::_( 'Filter' ); ?>:</td>
          <td class="input">
            <input type="text" name="search" value="<?php echo htmlspecialchars( $this->search );?>" class="text_area" onchange='submitform("<?php echo htmlspecialchars( JRequest::getCmd( 'task' ) ); ?>")' />
          </td>
          <td class="input">
            <?php echo $this->siteOptions; ?>
          </td>
          <td class="input">
            <?php echo $this->groupsFilterList; ?>
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
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
                </th>
                <th class="title">
	                <?php echo JHTML::_('grid.sort', JText::_( 'Site Item' ), "title", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                    +
	                <?php echo JHTML::_('grid.sort', JText::_( 'Created' ), "created", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>                
                <th class="title">
    	            <?php echo JHTML::_('grid.sort', JText::_( 'Include' ), "included", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
	                <?php echo JText::_( 'Current' )." ".JText::_( 'Groups' ); ?>
                </th>
                <th class="title">
	                <?php echo JText::_( 'Groups' ); ?>
                    <img onmouseover="this.style.cursor='pointer';" onclick="document.adminForm.toggle.checked=true; jQuery('input:checkbox').attr('checked','checked'); submitform('enroll');" src="../media/com_yooniqueacl/images/add.png" border="0" width="18" height="18" alt="<?php echo JText::_('Enroll');; ?>" title="<?php echo JText::_('Enroll'); ?>" name="<?php echo JText::_('Enroll');; ?>" />
					<img onmouseover="this.style.cursor='pointer';" onclick="document.adminForm.toggle.checked=true; jQuery('input:checkbox').attr('checked','checked'); submitform('withdraw');" src="../media/com_yooniqueacl/images/remove.png" border="0" width="18" height="18" alt="<?php echo JText::_('Withdraw');; ?>" title="<?php echo JText::_('Withdraw'); ?>" name="<?php echo JText::_('Withdraw');; ?>" />
                </th>
            </tr>
		</thead>
        <tbody>
					<?php $option	= JRequest::getCmd( 'option' ); ?>  
		<?php
        $k = 0;
		$database = JFactory::getDBO();
        for ($i=0, $n=count( $this->items ); $i < $n; $i++)
        {
            $r =& $this->items[$i];
            $checked    = JHTML::_( 'grid.id', $i, $r->id );
			//xdebug_break();
            $link = JRoute::_( 'index.php?option='.$option.'&controller='.$this->_name.'&task=edit&cid[]='. $r->id );
			$definelink = JRoute::_( 'index.php?option='.$option.'&controller='.$this->_name.'&task=define&id='. $r->id );

    
        echo "<tr class='row".$k."'>";
			echo "<td>"; echo $i + 1 + $this->pagination->limitstart; echo "</td>";
			echo "<td>$checked</td>";
            echo "<td class='noborder'>"; 
                echo "<a href='".$link."'>"; 
                echo htmlspecialchars( $r->title );
                echo "</a>";
            echo "</td>";
            echo "<td class='noborder'>"; ?>
                <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','switch_inclusion')">
                <?php
                echo "
				<center>
				"; 			
					if ($r->item_exclude == "1") { 
						echo JHTML::_( "image", "media/com_yooniqueacl/images/publish_x.png", JText::_( 'Excluded' ), " border='0' alt='".JText::_( 'Excluded' )."' title='".JText::_( 'Excluded' )."' name='".JText::_( 'Excluded' )."' " );
						}
					elseif ($r->item_exclude == "0") { 
						echo JHTML::_( "image", "media/com_yooniqueacl/images/tick.png", JText::_( 'Included' ), " border='0' alt='".JText::_( 'Included' )."' title='".JText::_( 'Included' )."' name='".JText::_( 'Included' )."' " );
					}
                echo "
				</center>";
				?>
                </a>
                <?php
            echo "</td>";
            echo "<td class='noborder'>"; 
						$database->setQuery("SELECT * FROM  " . TABLE_YOONIQUEACL_GROUPS . ",  " . TABLE_YOONIQUEACL_G2I
											." WHERE  " . TABLE_YOONIQUEACL_G2I . ".group_id =  " . TABLE_YOONIQUEACL_GROUPS . ".id "
											." AND  " . TABLE_YOONIQUEACL_G2I . ".item_id = '$r->id' "
											." ORDER BY `title` ASC");
						$item_groups = $database->loadObjectList();

						if ($item_groups) {
							foreach ($item_groups as $g) {
							  echo htmlspecialchars( $g->title ) . "<br />";
							} //endforeach
						} //endif
						
						echo "[";
						echo "<a href='$definelink;'>";
						echo JText::_( "Select Groups" );
						echo "</a>";
						echo "]";
						
            echo "</td>";
            echo "<td class='noborder'>"; 
                        if ($this->groups) {
                          echo "<select name='group[$r->id]' size='1'>";
                          echo "<option value=''>&nbsp;&nbsp;&nbsp;</option>";
                          foreach ($this->groups as $dbg) {
                            echo "<option value='$dbg->id'>" . htmlspecialchars( $dbg->title ) . "&nbsp;&nbsp;&nbsp;</option>";
                          } // end foreach
                          echo "</select>";
                        } //end if
            echo "</td>";
        echo "</tr>";
				echo "<tr class='row".$k."'>";
					echo "<td class='topnowrap' colspan='2'>";
					echo "</td>";
					echo "<td colspan='12' class='yooniqueacl_faded'>"; 
						?>
						<div id="description_<?php echo $r->id; ?>" class="description">
                        	<?php 
							echo "<div class='yooniqueacl_paddedl'>";
														
							// show the query
							if ($r->query) { 
								// show the error url published status
								echo "<strong>".JText::_( 'Query' )."</strong>: ";
								echo htmlspecialchars( $r->query );
								echo "<br />"; ?>
							<?php 
							}

							// show the date created
								echo "<strong>".JText::_( 'Modified' )."</strong>: ";
							if ($r->created_datetime != '0') { 
								echo JHTML::_('date', $r->created_datetime, "D, d M Y"); 
							}

							echo "<br />";
							
							// show the error url published status
							echo JText::_( 'Error URL' )." ".JText::_( 'Published' ).":"; ?>
                                <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','switch_publish')">
                                <?php 
                                if ($r->error_url_published == "0") { 
                                    echo JHTML::_( "image", "media/com_yooniqueacl/images/publish_x.png", JText::_( 'Unpublished' ), " border='0' alt='".JText::_( 'Unpublished' )."' title='".JText::_( 'Unpublished' )."' name='".JText::_( 'Unpublished' )."' " );
                                    }
                                elseif ($r->error_url_published == "1") { 
                                    echo JHTML::_( "image", "media/com_yooniqueacl/images/tick.png", JText::_( 'Published' ), " border='0' alt='".JText::_( 'Published' )."' title='".JText::_( 'Published' )."' name='".JText::_( 'Published' )."' " );
                                }
								?>
                                </a>
							<?php 							
							if ($r->error_url_published) { 
								echo htmlspecialchars( $r->error_url ); ?>
							<?php 
							}							
							                         
							echo "</div>";				  
							?>
						</div>
					<?php
					echo "</td>";
				echo "</tr>";		
		
        $k = 1 - $k;
        }
		
		if (!$this->items) {
			echo "<tr class='row".$k."'>";
				echo "<td class='topnowrap' colspan='10'>";
				echo JText::_( 'None' );
				echo "</td>";
			echo "</tr>";		
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
                <?php $option	= JRequest::getCmd( 'option' ); ?>
		<input type="hidden" name="option" value="<?php echo htmlspecialchars( $option ); ?>" />
		<input type="hidden" name="controller" value="<?php echo htmlspecialchars( $this->_name ); ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="boxchecked" value="" />
		<input type="hidden" name="filter_order" value="<?php echo htmlspecialchars( $this->filter_order ); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo htmlspecialchars( $this->filter_order_Dir ); ?>" />
        
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>        
