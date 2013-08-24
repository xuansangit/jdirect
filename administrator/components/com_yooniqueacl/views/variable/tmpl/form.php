<?php defined('_JEXEC') or die('Restricted access'); ?>
		<?php JHTML::_('behavior.tooltip'); ?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">       

			<table class="adminlist">
            <thead>
                <tr>
                    <th colspan="3">
                    <?php echo JText::_( 'Details' ); ?>
                    </th>
                </tr>
            </thead>

			<tbody>
				<tr>
					<td width="20%">
					<?php echo JText::_( 'Option' ); ?>: *
					</td>
					<td>
					<?php if (JText::_( 'Option Tooltip' )) { echo JHTML::_('tooltip', JText::_( 'Option Tooltip' ) ); } ?> 
                    </td>
					<td>
					<input class="text_area" type="text" name="site_option" size="50" maxlength="250" value="<?php echo htmlspecialchars( $this->row->site_option );?>" />
					</td>
				</tr>
				<tr>
					<td width="20%">
					<?php echo JText::_( 'Variable' ); ?>: *
					</td>
					<td>
					<?php if (JText::_( 'Variable Tooltip' )) { echo JHTML::_('tooltip', JText::_( 'Variable Tooltip' ) ); } ?> 
                    </td>
					<td>
					<input class="text_area" type="text" name="variable" size="50" maxlength="250" value="<?php echo htmlspecialchars( $this->row->variable ); ?>" />
					</td>
				</tr>
				<tr>
					<td width="20%">
					<?php echo JText::_( 'Force Integer' ); ?>?
					</td>
					<td>
					<?php if (JText::_( 'Force Integer Tooltip' )) { echo JHTML::_('tooltip', JText::_( 'Force Integer Tooltip' ) ); } ?> 
                    </td>
					<td>
					<?php echo $this->force_integer; ?>
					</td>
				</tr>
            </tbody>
            <tfoot>
            	<tr>
                  <td colspan="3">&nbsp;
					
                  </td>
                </tr>
            </tfoot>
	        </table>

			<p>* <?php echo JText::_( 'REquired Field' ); ?></p>

<?php $option	= JRequest::getCmd( 'option' ); ?>
		<input type="hidden" name="option" value="<?php echo htmlspecialchars( $option );?>" />
		<input type="hidden" name="controller" value="<?php echo htmlspecialchars( $this->_name );?>" />		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo htmlspecialchars( $this->row->id ); ?>" />
		</form>
