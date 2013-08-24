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
					<?php echo JText::_( 'Title' ) ?>:
					</td>
					<td>
					<?php if (JText::_( 'Title Tooltip' )) { echo JHTML::_('tooltip', JText::_( 'Title Tooltip' ) ); } ?> 
                    </td>
					<td>
					<span id='title-container'>
						<input class="text_area" type="text" name="title" size="50" maxlength="250" value="<?php echo htmlspecialchars( $this->row->title );?>" />
					</span>

					</td>
				</tr>
				<tr>
					<td width="20%">
					<?php echo JText::_( 'Query' ); ?>: *
					</td>
					<td>
					<?php if (JText::_( 'Query Tooltip' )) { echo JHTML::_('tooltip', JText::_( 'Query Tooltip' ) ); } ?> 
                    </td>
					<td>
						<span id='query-container'>
							<input class="text_area" type="text" name="query" size="50" maxlength="250" value="<?php echo htmlspecialchars( $this->row->query );?>" />
						</span>
					</td>
				</tr>
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
					<?php echo JText::_( 'Error URL' ) ?>:
					</td>
					<td>
					<?php if (JText::_( 'Error URL Tooltip' )) { echo JHTML::_('tooltip', JText::_( 'Error URL Tooltip' ) ); } ?> 
                    </td>
					<td>
					<input class="text_area" type="text" name="error_url" size="50" maxlength="250" value="<?php echo htmlspecialchars( $this->row->error_url );?>" />
					</td>
				</tr>
				<tr>
					<td width="20%">
					<?php echo JText::_( 'Error URL' )." ".JText::_( 'Published' ) ?>:
					</td>
					<td>
					<?php if (JText::_( 'Error URL Published Tooltip' )) { echo JHTML::_('tooltip', JText::_( 'Error URL Published Tooltip' ) ); } ?> 
                    </td>
					<td>
					<?php echo $this->errorurl_published; ?>
					</td>
				</tr>
				<tr>
					<td width="20%">
					<?php echo JText::_( 'Exclude Item' ); ?>?
					</td>
					<td>
					<?php if (JText::_( 'Exclude Item Tooltip' )) { echo JHTML::_('tooltip', JText::_( 'Exclude Item Tooltip' ) ); } ?> 
                    </td>
					<td>
					<?php echo $this->excludeitem; ?>
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

			<p>* <?php echo JText::_( 'Required Field' ); ?></p>

<?php $option	= JRequest::getCmd( 'option' ); ?>
		<input type="hidden" name="option" value="<?php echo htmlspecialchars( $option );?>" />
		<input type="hidden" name="controller" value="<?php echo htmlspecialchars( $this->_name );?>" />		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo htmlspecialchars( $this->row->id ); ?>" />
		</form>
