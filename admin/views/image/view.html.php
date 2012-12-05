<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.view');

class GlobalFlashGalleriesViewImage extends JViewLegacy
{
	function display( $tpl = null )
	{
		//$document =& JFactory::getDocument();
		//$document->addStyleSheet( globalflash_adminURL.'/css/icons.css', 'text/css', null, array() );

		$image =& $this->get('Data');
		$isNew = $image->id < 1;

		$title = JText::_('Edit Image');
		JToolBarHelper::title( JText::_('Flash Galleries') .": <small>[ {$title} ]</small>", 'edit.png' );

		//JToolBarHelper::preview();

		if ($isNew)
		{
			JToolBarHelper::save();
			JToolBarHelper::cancel();
		}
		else
		{
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel('cancel', 'Close');
		}

		//JToolBarHelper::help('../images.html', true);

		$this->assignRef('image', $image);

		parent::display($tpl);
	}

}
