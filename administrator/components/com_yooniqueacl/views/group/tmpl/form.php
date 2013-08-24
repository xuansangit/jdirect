<?php defined('_JEXEC') or die('Restricted access'); ?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">       

			<table class="adminlist">
            <thead>
                <tr>
                    <th colspan="2">
                    <?php echo JText::_( 'Details' ); ?>
                    </th>
                </tr>
            </thead>

			<tbody>
                <tr>
                  <td class='title'><?php echo JText::_( 'Title' ); ?>: *</td>
                    <td class="input">
                    <input name="title" type="text" class="text_area" size='50' value="<?php echo htmlspecialchars( $this->row->title ); ?>" />
                    </td>
                </tr>
                <tr>
                  <td class='title'><?php echo JText::_( 'Description' ); ?>:</td>
                    <td class="input">
                    <textarea name="description" class="text_area" cols="40" rows="10" style="width:500px" width="500px" ><?php echo htmlspecialchars( $this->row->description ); ?></textarea>
                    </td>
                </tr>
            </tbody>
            <tfoot>
            	<tr>
                  <td colspan="2">&nbsp;
					
                  </td>
                </tr>
            </tfoot>
	        </table>

			<p>* <?php echo JText::_( 'Required Field' ); ?></p>

<?php $option	= JRequest::getCmd( 'option' ); ?>
			
		<input type="hidden" name="option" value="<?php echo htmlspecialchars( $option );?>" />
		<input type="hidden" name="controller" value="<?php echo htmlspecialchars( $this->_name );?>" />		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo htmlspecialchars( $this->row->id ); ?>" />
		</form>
