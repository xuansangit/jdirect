<?php defined('_JEXEC') or die('Restricted access'); ?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
            <th class='yooniqueacl_groups'>
            <?php echo JText::_( 'Groups' ); ?>
            </th>            
          <td class='right'><?php echo JText::_( 'Filter' ); ?>:</td>
          <td class="input">
            <input type="text" name="search" value="<?php echo htmlspecialchars( $this->search );?>" class="text_area" onchange='submitform("<?php echo htmlspecialchars( JRequest::getCmd( 'task' ) ); ?>")' />
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
                <?php echo JHTML::_('grid.sort', JText::_( 'ID' ), "id", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>                
                <th class="title">
                <?php echo JHTML::_('grid.sort', JText::_( 'Title' ), "title", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
                <?php echo JHTML::_('grid.sort', JText::_( 'Description' ), "description", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
	                <?php echo JText::_( 'Members' ); ?>
                </th>
                <th class="title">
	                <?php echo JText::_( 'Items' ); ?>
                </th>
            </tr>
		</thead>
        <tbody>
					<?php $option	= JRequest::getCmd( 'option' ); ?>
		<?php
        $k = 0;
$option	= JRequest::getCmd( 'option' );
		$database = JFactory::getDBO();
        for ($i=0, $n=count( $this->items ); $i < $n; $i++)
        {
            $r =& $this->items[$i];
            $checked    = JHTML::_( 'grid.id', $i, $r->id );
            $link = JRoute::_( 'index.php?option='.htmlspecialchars( $option ).'&controller='.htmlspecialchars( $this->_name ).'&task=edit&cid[]='. $r->id );

    
        echo "<tr class='row".$k."'>";
			echo "<td>"; echo $i + 1 + $this->pagination->limitstart; echo "</td>";
			echo "<td>$checked</td>";
            echo "<td class='noborder'>"; 
				echo "<a href='".$link."'>$r->id</a>"; 
			echo "</td>";
            echo "<td class='noborder'>"; 
                echo "<a href='".$link."'>"; 
                echo htmlspecialchars( $r->title );
                echo "</a>";
            echo "</td>";
            echo "<td class='noborder'>"; 
                echo htmlspecialchars( $r->description );
            echo "</td>";
            echo "<td class='noborder'>"; 
			     $database->setQuery("SELECT COUNT(*) FROM  " . TABLE_YOONIQUEACL_U2G
			                         ." WHERE `group_id` = '$r->id'");
	    		 $mems = $database->loadResult();
				 echo $mems;
            echo "</td>";
            echo "<td class='noborder'>"; 
			     $database->setQuery("SELECT COUNT(*) FROM  " . TABLE_YOONIQUEACL_G2I
			                         ." WHERE `group_id` = '$r->id'");
	    		 $items = $database->loadResult();
				 echo $items;
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
                
		<input type="hidden" name="option" value="<?php echo htmlspecialchars( $option ); ?>" />
		<input type="hidden" name="controller" value="<?php echo htmlspecialchars( $this->_name ); ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="boxchecked" value="" />
		<input type="hidden" name="filter_order" value="<?php echo htmlspecialchars( $this->filter_order ); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo htmlspecialchars( $this->filter_order_Dir ); ?>" />
        
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>        
