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

// no direct access
defined('_JEXEC') or die('Restricted access');


class TableWord extends JTable
{
    /** @var int */
    var $id = null;
    /** @var string */
    var $word = null;

    /**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
    function TableWord(& $db)
    {
        parent::__construct('#__sefwords', 'id', $db);
    }

    function check()
    {
        //initialize
        $this->_error = null;
        $this->word = trim($this->word);

        // check for valid word
        if ($this->word == '') {
            $this->_error .= JText::_('COM_SEF_ERROR_EMPTY_WORD');
            return false;
        }
        
        if (is_null($this->_error)) {
            // check for existing words
            $this->_db->setQuery("SELECT id FROM #__sefwords WHERE `word` LIKE " . $this->_db->Quote($this->word));
            $xid = intval($this->_db->loadResult());
            if ($xid && $xid != intval($this->id)) {
                $this->_error = JText::_('COM_SEF_ERROR_WORD_EXISTS');
                return false;
            }
            
            return true;
        } else {
            return false;
        }
    }
}
?>