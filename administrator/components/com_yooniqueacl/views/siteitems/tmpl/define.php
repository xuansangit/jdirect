<?php defined('_JEXEC') or die('Restricted access'); ?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
            <th class='yooniqueacl_assignusers'>
            <?php echo JText::_( 'Define' )." ".JText::_( 'Site' )." ".JText::_( 'Item' ); ?>
            <br />
                <span class="small">
                  <?php echo $this->item->id; ?>: <?php echo htmlspecialchars( $this->item->title ); ?> (<?php echo htmlspecialchars( $this->item->type ); ?>)<br /> 
                  &nbsp;&nbsp;&bull;&nbsp;&nbsp;<?php echo JText::_( 'Query' ).": " . htmlspecialchars( $this->item->query ); ?>
                </span>            
            </th>            
          <td class='right'><?php echo JText::_( 'Filter' ); ?>:</td>
          <td class="input">
            <input type="text" name="search" value="<?php echo htmlspecialchars( $this->search );?>" class="text_area" onchange='submitform("<?php echo htmlspecialchars( JRequest::getCmd( 'task' ) ); ?>")' />
          </td>
		</tr>
		</table>

		<table class="adminlist">
		<thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
                </th>
                <th class="title">
                <?php echo JHTML::_('grid.sort', JText::_( 'Group' )." ".JText::_( 'ID' ), "id", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>                
                <th class="title">
                <?php echo JHTML::_('grid.sort', JText::_( 'Title' ), "title", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
                <?php echo JHTML::_('grid.sort', JText::_( 'Description' ), "description", $this->filter_order_Dir, $this->filter_order, htmlspecialchars( JRequest::getCmd( 'task' ) ) ); ?>
                </th>
                <th class="title">
	                <?php echo JText::_( 'Access' ); ?>
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
            $link = JRoute::_( 'index.php?option='.$option.'&controller='.$this->_name.'&task=edit&cid[]='. $r->id );

			$database->setQuery("SELECT `item_id` FROM  " . TABLE_YOONIQUEACL_G2I
								." WHERE `group_id` = '$r->id'"
								." AND `item_id` = '".$this->item->id."' ");
			$already = $database->loadResult();

			$img_u 	= $already ? 'tick.png' : 'publish_x.png';
			$alt_u 	= $already ? JText::_( 'Access' ) : JText::_( 'None' );

    
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
				?>
                <img src="../media/com_yooniqueacl/images/<?php echo $img_u;?>" border="0" alt="<?php echo $alt_u; ?>" />
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
		<input type="hidden" name="task" value="<?php echo htmlspecialchars( JRequest::getCmd( 'task' ) ); ?>" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="boxchecked" value="" />
		<input type="hidden" name="filter_order" value="<?php echo htmlspecialchars( $this->filter_order ); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo htmlspecialchars( $this->filter_order_Dir ); ?>" />
        
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>        
