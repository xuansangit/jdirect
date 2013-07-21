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
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

if (!class_exists('JViewLegacy')) {
    class JViewLegacy extends JView { }
}

class SEFView extends JViewLegacy
{
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->addTemplatePath($this->_basePath.'/views/templates');
        $this->loadSelectSkin();
    }
    
    protected function tooltip($tooltip, $title = '', $image = 'tooltip.png')
    {
        $tooltip    = str_replace('"', '\\"', htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8'));
        $title      = str_replace('"', '\\"', htmlspecialchars($title, ENT_COMPAT, 'UTF-8'));
        
        $image  = JURI::root(true).'/administrator/components/com_sef/assets/images/'. $image;
        $text   = '<img src="'. $image .'" border="0" alt="'. JText::_('COM_SEF_TOOLTIP') .'"/>';
            
        if ($title) {
            $title .= '::';
        }

        $style = 'style="text-decoration: none; color: #333;"';

        $tip = '<span class="editlinktip hasTip" title="' . $title . $tooltip . '" ' . $style . '>';
        $tip .= $text . '</span>';
        
        return $tip;
    }
    
    protected function renderParams($params, $group)
    {
        $fields = $params->getFieldset($group);
        
        if (count($fields) > 0) {
            echo '<fieldset class="panelform form-horizontal">';
            //echo '<ul class="adminformlist">';
            foreach ($fields as $field) {
                echo '<div class="control-group">';
                //echo '<li>';
                echo '<div class="control-label">'.$field->label.'</div>';
                echo '<div class="controls">'.$field->input.'</div>';
                //echo '</li>';
                echo '</div>';
            }
            //echo '</ul>';
            echo '</fieldset>';
        }
    }
    
    public function showInfoText($str, $adminForm = false)
    {
        $sefConfig =& SEFConfig::getConfig();
        
        $this->assign('infoString', JText::_($str));
        $this->assign('infoShown', $sefConfig->showInfoTexts);
        $this->assign('infoTextClass', $adminForm ? 'class="adminform"' : '');
        
        // Prepare JS variables
        $document =& JFactory::getDocument();
        $js = "var jsInfoTextShown = ".($sefConfig->showInfoTexts ? 'true' : 'false').";\n";
        $js .= "var jsInfoTextHide = '".JText::_('COM_SEF_INFOTEXT_HIDE', true)."';\n";
        $js .= "var jsInfoTextShow = '".JText::_('COM_SEF_INFOTEXT_SHOW', true)."';\n";
        $js .= "var jsInfoTextUrl = '".JURI::root()."administrator/index.php?option=com_sef&controller=config&task=setinfotext';\n";
        $document->addScriptDeclaration($js);
        
        // Load JS
        JHTML::script('administrator/components/com_sef/assets/js/infotexts.js', true);
        
        $prevLayout = $this->setLayout('default');
        echo $this->loadTemplate('infotext');
        $this->setLayout($prevLayout);
    }
    
    public function loadSelectSkin() {
        if (version_compare(JVERSION, '3.0', '>=')) {
            // Convert only select boxes that have size="1" or size is not set
            JHtml::_('formbehavior.chosen', 'select[size="1"], select:not([size])');
        }
    }
}

?>