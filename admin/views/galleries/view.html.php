<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.utilities.date');

class GlobalFlashGalleriesViewGalleries extends JViewLegacy
{
	function display( $tpl = null )
	{
		//$document = JFactory::getDocument();
		//$document->addStyleSheet( globalflash_adminURL.'/css/icons.css', 'text/css', null, array() );

		$title = JText::_('Manage Galleries');
		JToolBarHelper::title( JText::_('Flash Galleries').": <small>[ {$title} ]</small>", 'gallery.png' );

		$items = $this->get('Data');
		if ( count($items) )
		{
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::deleteList();
			if (method_exists('JToolBarHelper', 'editListX'))
				JToolBarHelper::editListX();
			else
				JToolBarHelper::editList();
		}
		if (method_exists('JToolBarHelper', 'addNewX'))
			JToolBarHelper::addNewX();
		else
			JToolBarHelper::addNew();

		JToolBarHelper::help('../galleries.html', true);

		$this->assignRef('items', $items);

		parent::display($tpl);
	}

}
