<?php defined('_JEXEC') or die('Restricted access'); ?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
            <th class='yooniqueacl_variables'>
            <?php echo JText::_( 'Variables' ); ?>
            </th>            
          <td class='right'><?php echo JText::_( 'Filter' ); ?>:</td>
          <td class="input">
            <input type="text" name="search" value="<?php echo htmlspecialchars( $this->search );?>" class="text_area" onchange='submitform("<?php echo htmlspecialchars( JRequest::getCmd( 'task' ) ); ?>")' />
          </td>
          <td class="input">
            <?php echo $this->siteOptions; ?>
          </td>
          <td class="input">
            <?php echo $this->variables; ?>
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
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
                </th>
                <th class="title">
            	    <?php echo JHTML::_('grid.sort', JText::_( 'ID' ), "id", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>                
                <th class="title">
        	        <?php echo JHTML::_('grid.sort', JText::_( 'Option' ), "option", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
    	            <?php echo JHTML::_('grid.sort', JText::_( 'Variable' ), "variable", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
	                <?php echo JHTML::_('grid.sort', JText::_( 'Force Integer' ), "forceinteger", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
            </tr>
		</thead>
        <tbody>
					<?php $option	= JRequest::getCmd( 'option' ); ?>  
		<?php
        $k = 0;
        for ($i=0, $n=count( $this->items ); $i < $n; $i++)
        {
            $r =& $this->items[$i];
            $checked    = JHTML::_( 'grid.id', $i, $r->id );
            $link = JRoute::_( 'index.php?option='.$option.'&controller='.$this->_name.'&task=edit&cid[]='. $r->id );

    
        echo "<tr class='row".$k."'>";
			echo "<td>"; echo $i + 1 + $this->pagination->limitstart; echo "</td>";
			echo "<td>$checked</td>";
            echo "<td class='noborder'>"; 
                echo "<a href='".$link."'>";
                echo htmlspecialchars( $r->id );
                echo "</a>";
            echo "</td>";
            echo "<td class='noborder'>"; 
                echo "<a href='".$link."'>";
                echo htmlspecialchars( $r->site_option );
                echo "</a>";
            echo "</td>";
            echo "<td class='noborder'>"; 
                echo "<a href='".$link."'>";
                echo htmlspecialchars( $r->variable );
                echo "</a>";
            echo "</td>";
            echo "<td class='noborder'>"; ?>
                <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','switch_forceinteger')">
                <?php
                echo "
				<center>
				"; 
					if ($r->force_integer == "0") { 
						echo JHTML::_( "image", "media/com_yooniqueacl/images/publish_x.png", JText::_( 'Not Force Integer' ), " border='0' alt='".JText::_( 'Not Force Integer' )."' title='".JText::_( 'Not Force Integer' )."' name='".JText::_( 'Not Force Integer' )."' " );
						}
					elseif ($r->force_integer == "1") { 
						echo JHTML::_( "image", "media/com_yooniqueacl/images/tick.png", JText::_( 'Force Integer' ), " border='0' alt='".JText::_( 'Force Integer' )."' title='".JText::_( 'Force Integer' )."' name='".JText::_( 'Force Integer' )."' " );
					}
                echo "
				</center>";
				?>
                </a>
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
