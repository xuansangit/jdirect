<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.4.1
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2013 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$sefConfig =& SEFConfig::getConfig();
if ($sefConfig->useCache) {
    //require(JPATH_ROOT.'/components/com_sef/sef.cache.php');
    $cache =& sefCache::getInstance();
}
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<script type="text/javascript">
<!--
function useRE(el1, el2)
{
    if( !el1 || !el2 ) {
        return;
    }
    
    if( el1.checked && el2.value.substr(0, 4) != 'reg:' ) {
        el2.value = 'reg:' + el2.value;
    }
    else if( !el1.checked && el2.value.substr(0,4) == 'reg:' ) {
        el2.value = el2.value.substr(4);
    }
}

function handleKeyDown(e)
{
    var code;
    code = e.keyCode;
    
    if (code == 13) {
        // Enter pressed
        document.adminForm.submit();
        return false;
    }
    
    return true;
}

function resetFilters()
{
    document.adminForm.filterHitsCmp.value = '0';
    document.adminForm.filterHitsVal.value = '';
    document.adminForm.filterItemid.value = '';
    document.adminForm.filterSEF.value = '';
    document.adminForm.filterReal.value = '';
    document.adminForm.comFilter.value = '';
    
    document.adminForm.submit();
}

function doAction()
{
    var sel = document.getElementById('sef_selection').value;
    var action = document.getElementById('sef_actions').value;
    
    if (action == 'sep') {
        return;
    }
    
    if (sel == 'selected') {
        // Check that there is at least one URL selected
        if (document.adminForm.boxchecked.value == 0) {
            alert('<?php echo JText::_('COM_SEF_MAKE_SELECTION'); ?>');
            return;
        }
    }
    
    // If delete, show warning
    if (action == 'delete') {
        if (!confirm('<?php echo JText::_('COM_SEF_ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_URLS'); ?>')) {
            return;
        }
    }
    
    // Call the action
    document.adminForm.selection.value = sel;
    submitbutton(action);
}
-->
</script>

<fieldset>
    <legend><?php echo JText::_('COM_SEF_FILTERS'); ?></legend>
<table>
    <tr>
        <td width="100%" valign="bottom"></td>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_VIEWMODE') . ':';
            ?>
        </td>
        <td></td>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_HITS') . ':';
            ?>
        </td>
        <?php if( $this->viewmode != 1 ) { ?>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_ITEMID') . ':';
            ?>
        </td>
        <?php } ?>
        <td nowrap="nowrap">
            <?php echo $this->lists['filterSEFRE']; ?>
        </td>
        <td nowrap="nowrap" align="right">
            <?php
            echo (($this->viewmode == 1) ? JText::_('COM_SEF_FILTER_URLS') : JText::_('COM_SEF_FILTER_SEF_URLS')) . ':';
            ?>
        </td>
        <?php if( $this->viewmode != 1 ) { ?>
        <td nowrap="nowrap">
            <?php echo $this->lists['filterRealRE']; ?>
        </td>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_FILTER_REAL_URLS') . ':';
            ?>
        </td>
        <?php } ?>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_COMPONENT') . ':';
            ?>
        </td>
        <?php if ($sefConfig->langEnable) { ?>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_LANGUAGE') . ':';
            ?>
        </td>
        <?php } ?>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?php echo $this->lists['viewmode']; ?>
        </td>
        <td>
            <?php echo $this->lists['hitsCmp']; ?>
        </td>
        <td>
            <?php echo $this->lists['hitsVal']; ?>
        </td>
        <?php if ($this->viewmode != 1) { ?>
        <td>
            <?php echo $this->lists['itemid']; ?>
        </td>
        <?php } ?>
        <td colspan="2">
            <?php echo $this->lists['filterSEF']; ?>
        </td>
        <?php if ($this->viewmode != 1) { ?>
        <td colspan="2">
            <?php echo $this->lists['filterReal']; ?>
        </td>
        <?php } ?>
        <td>
            <?php echo $this->lists['comList']; ?>
        </td>
        <?php if ($sefConfig->langEnable) { ?>
        <td>
            <?php echo $this->lists['filterLang']; ?>
        </td>
        <?php } ?>
        <td>
            <?php echo $this->lists['filterReset']; ?>
        </td>
    </tr>
</table>
<?php
// Links to homepage
if ($this->viewmode == 4) { ?>
<table width="100%">
    <tr>
        <td nowrap="nowrap" align="right">
        <input type="button" value="<?php echo JText::_('COM_SEF_CREATE_LINKS_TO_HOMEPAGE'); ?>" onclick="submitbutton('createlinks');" />
        </td>
    </tr>
</table>
<?php } ?>
</fieldset>

<table class="adminlist table table-striped">
<thead>
    <tr>
    	<?php
    	if($this->viewmode!=6) {
    		?> 
        <th width="5">
            <?php echo JText::_('COM_SEF_NUM'); ?>
        </th>
	        <?php
	    	}
    	?>
        <th width="20">
            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
        </th>
        <th class="title" width="40px">
            <?php
            if($this->viewmode!=6) { 
            	echo JHTML::_('grid.sort', 'COM_SEF_HITS', 'cpt', $this->lists['filter_order'] == 'cpt' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']);
            } else {
            	echo JText::_('COM_SEF_HITS');
            } 
            ?>
        </th>
        <th class="title">
            <?php
            if ($this->viewmode == 1) {
                echo JHTML::_('grid.sort', 'COM_SEF_DATE_ADDED', 'dateadd', $this->lists['filter_order'] == 'dateadd' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']);
            } else {
            	if($this->viewmode!=6) {
                	echo JHTML::_('grid.sort', 'COM_SEF_SEF_URL', 'sefurl', $this->lists['filter_order'] == 'sefurl' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']);
            	} else {
            		echo JText::_('COM_SEF_URL');
            	}
            }
            ?>
        </th>
        <th class="title">
            <?php
            if ($this->viewmode == 1) {
                echo JHTML::_('grid.sort', 'COM_SEF_URL', 'sefurl', $this->lists['filter_order'] == 'sefurl' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']);
            } else {
            	if($this->viewmode!=6) {
                	echo JHTML::_('grid.sort', 'COM_SEF_REAL_URL', 'origurl', $this->lists['filter_order'] == 'origurl' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']);
            	} else {
            		echo JText::_('COM_SEF_REAL_URL');
            	}
            }
            ?>
        </th>
		<?php if ($this->trace) { ?>
	        <th class="title" width="40px">
		        <?php echo JText::_('COM_SEF_TRACE'); ?>
	        </th>
		<?php } ?>        
		<?php 
		if ($this->viewmode != 1) { ?>
			<th class="title" width="55px">
			    <?php
			    if($this->viewmode!=6) { 
			    	echo JHTML::_('grid.sort', 'COM_SEF_ENABLED', 'enabled', $this->lists['filter_order'] == 'enabled' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']);
			    } else {
			    	echo JText::_('COM_SEF_ENABLED');
			    } 
			    ?>
	        </th>
			<th class="title" width="50px">
			    <?php
			    if($this->viewmode!=6) {
			    	echo JHTML::_('grid.sort', 'COM_SEF_SEF', 'sef', $this->lists['filter_order'] == 'sef' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']);
			    } else {
			    	echo JText::_('COM_SEF_SEF');
			    } 
			    ?>
	        </th>
	        <?php
	        if($this->viewmode!=6) {
	        	?>
				<th class="title" width="50px">
		        	<?php echo JHTML::_('grid.sort', 'COM_SEF_LOCKED', 'locked', $this->lists['filter_order'] == 'locked' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']); ?>
		        </th>
				<th class="title" width="50px">
		        	<?php echo JHTML::_('grid.sort', 'COM_SEF_ACTIVE', 'priority', $this->lists['filter_order'] == 'priority' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']); ?>
		        </th>
		        <?php if ($sefConfig->useCache) { 
		        	?>
					<th class="title" width="50px">
			        	<?php echo JText::_('COM_SEF_CACHED'); ?>
			        </th>     
					<?php 
				}
	        }
	        ?>
	        <th>
	        <?php echo JText::_('COM_SEF_HOST'); ?>
	        </th>
	        <?php
		} 
		?>
    </tr>
</thead>
<?php
	$colspan = 5;
	if ($this->viewmode != 1) {
	    $colspan += 5;
	    if ($sefConfig->useCache) $colspan++;
	}
	if ($this->trace )$colspan++;
?>
<?php
if($this->viewmode!=6) {
	?>
	<tfoot>
	    <tr>
	        <td colspan="<?php echo $colspan; ?>">
	            <?php echo $this->pagination->getListFooter(); ?>
	        </td>
	    </tr>
	</tfoot>
	<?php
}
?>
<tbody>
    <?php
    $k = 0;
    //for ($i=0, $n=count( $rows ); $i < $n; $i++) {
    foreach (array_keys($this->items) as $i) {
        $row = &$this->items[$i];
        ?>
        <tr class="<?php echo 'row'. $k; ?>">
        	<?php
        	if($this->viewmode!=6) {
        		?>
	            <td align="center">
	                <?php echo $this->pagination->getRowOffset($i); ?>
	            </td>
	            <?php
        	}
        	?>
            <td>
                <?php echo JHTML::_('grid.id', $i, $this->viewmode!=6?$row->id:$row->sefurl ); ?>
            </td>
            <td>
                <?php echo $row->cpt; ?>
            </td>
            <td style="text-align:left;">
                <?php if ($this->viewmode == 1 ) {
                    echo $row->dateadd;
                } else { ?>
                	<?php
                	if($this->viewmode!=6) {
                		?>
	                    <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>', 'edit')">
	                    <?php echo strlen($row->sefurl)?$row->sefurl:JText::_('COM_SEF_HOMEPAGE');?>
	                    </a>
	                    <?php
                	} else {
                		echo "<b>".(strlen($row->sefurl)?$row->sefurl:JText::_('COM_SEF_HOMEPAGE'))."</b>";		
                	}
                } ?>
            </td>
            <td style="text-align:left;">
                <?php if ($this->viewmode == 1 ) { ?>
                    <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>', 'edit')">
                    <?php echo $row->sefurl; ?>
                    </a>
                <?php } else {
                    echo htmlspecialchars($row->origurl . ($row->Itemid == '' ? '' : (strpos($row->origurl, '?') ? '&' : '?') . 'Itemid='.$row->Itemid ) );
                } ?>
            </td>
            <?php if ($this->trace) : ?>
            <td style="text-align: center;">
            	<?php echo $this->tooltip(nl2br($row->trace), JText::_('COM_SEF_TRACE_INFORMATION'));?></td>
            </td>
            <?php endif; ?>
            <?php
            if( $this->viewmode != 1 ) {
                ?>
                <td style="text-align: center;">
                    <?php
                    if( $row->enabled ) {
                        ?>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('COM_SEF_DISABLE').'::'.JText::_('COM_SEF_TT_DISABLED_URL'); ?>">
                        <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'disable')">
                        <?php echo JHTML::_('image','admin/tick.png', JText::_('COM_SEF_ENABLED'), array('border' => 0), true); ?>
                        </a>
                        </span>
                        <?php
                    }
                    else {
                        ?>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('COM_SEF_ENABLE').'::'.JText::_('COM_SEF_TT_ENABLED_URL'); ?>">
                        <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'enable')">
                        <?php echo JHTML::_('image','admin/publish_x.png', JText::_('COM_SEF_DISABLED'), array('border' => 0), true); ?>
                        </a>
                        </span>
                        <?php
                    }
                    ?>
                </td>
                <td style="text-align: center;">
                    <?php
                    if( $row->sef ) {
                        ?>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('COM_SEF_DONT_SEF').'::'.JText::_('COM_SEF_TT_SEF_REAL_URL'); ?>">
                        <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'sefdisable')">
                        <?php echo JHTML::_('image','admin/tick.png', JText::_('COM_SEF_SEF'), array('border' => 0), true); ?>
                        </a>
                        </span>
                        <?php
                    }
                    else {
                        ?>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('COM_SEF_SEF').'::'.JText::_('COM_SEF_TT_SEF_SEF_URL'); ?>">
                        <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'sefenable')">
                        <?php echo JHTML::_('image','admin/publish_x.png', JText::_('COM_SEF_DONT_SEF'), array('border' => 0), true); ?>
                        </a>
                        </span>
                        <?php
                    }
                    ?>
                </td>
                <?php
                if($this->viewmode!=6) {
                	?>
	                <td style="text-align: center;">
	                    <?php
	                    if( $row->locked ) {
	                        ?>
	                        <span class="editlinktip hasTip" title="<?php echo JText::_('COM_SEF_UNLOCK').'::'.JText::_('COM_SEF_TT_UNLOCKED_URLS'); ?>">
	                        <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'unlock')">
	                        <?php echo JHTML::_('image','admin/checked_out.png', JText::_('COM_SEF_LOCKED'), array('border' => 0), true); ?>
	                        </a>
	                        </span>
	                        <?php
	                    }
	                    else {
	                        ?>
	                        <span class="editlinktip hasTip" title="<?php echo JText::_('COM_SEF_LOCK').'::'.JText::_('COM_SEF_TT_LOCKED_URLS'); ?>">
	                        <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'lock')">
	                        <?php echo JHTML::_('image','admin/publish_x.png', JText::_('COM_SEF_UNLOCKED'), array('border' => 0), true); ?>
	                        </a>
	                        </span>
	                        <?php
	                    }
	                    ?>
	                </td>
	                <td style="text-align: center;">
	                    <?php
	                    if( $row->priority == 0 ) {
	                        ?>
	                        <span class="hasTip" title="<?php echo JText::_('COM_SEF_TT_ACTIVE_LINK'); ?>">
	                        <?php echo JHTML::_('image','admin/tick.png', JText::_('COM_SEF_ACTIVE'), array('border' => 0), true); ?>
	                        </span>
	                        <?php
	                    }
	                    else {
	                        $img = ($row->priority == 100 ? 'publish_r.png' : 'publish_g.png');
	                        ?>
	                        <span class="editlinktip hasTip" title="<?php echo JText::_('COM_SEF_TT_MAKE_ACTIVE'); ?>">
	                        <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'setActive')">
	                        <?php echo JHTML::_('image','admin/'.$img, JText::_('COM_SEF_TT_NOT_ACTIVE'), array('border' => 0), true); ?>
	                        </a>
	                        </span>
	                        <?php
	                    }
	                    ?>
	                </td>
                <?php
	                if ($sefConfig->useCache) {
	                    ?>
	                    <td style="text-align: center;">
	                    <?php
	                    //if( $cache->getNonSefUrl($row->sefurl) !== false ) {
	                    if( $cache->getSefUrl($row->origurl,$row->Itemid) !== false ) {
	                        ?>
	                        <span class="hasTip" title="<?php echo JText::_('COM_SEF_CACHED').'::'.JText::_('COM_SEF_TT_IN_CACHE'); ?>">
	                        <?php echo JHTML::_('image','admin/tick.png', JText::_('COM_SEF_CACHED'), array('border' => 0), true); ?>
	                        </span>
	                        <?php
	                    }
	                    else {
	                        ?>
	                        <span class="hasTip" title="<?php echo JText::_('COM_SEF_TT_NOT_CACHED').'::'.JText::_('COM_SEF_TT_NOT_IN_CACHE'); ?>">
	                        <?php echo JHTML::_('image','admin/publish_x.png', JText::_('COM_SEF_TT_NOT_CACHED'), array('border' => 0), true); ?>
	                        </span>
	                        <?php
	                    }
	                    ?>
	                    </td>
	                    <?php
	                }
	            }
	            ?>
	            <td>
	            <?php echo $row->host; ?>
	            </td>
	            <?php
            }
            ?>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
</tbody>
</table>

<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="sefurls" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['filter_order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['filter_order_Dir']; ?>" />
<input type="hidden" name="selection" value="selected" />
</form>
