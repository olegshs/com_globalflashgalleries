<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.view');
jimport('joomla.utilities.date');

class GlobalFlashGalleriesViewAlbums extends JViewLegacy
{
	function display( $tpl = null )
	{
		$title = JText::_('Manage Albums');
		JToolBarHelper::title( JText::_('Flash Galleries').": <small>[ {$title} ]</small>", 'album.png' );

		$items =& $this->get('Data');

		if ( count($items) )
		{
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

		JToolBarHelper::help('../albums.html', true);

		$this->assignRef('items', $items);

		require_once globalflash_adminDir.DS.'inc'.DS.'ui.class.php';
		$ui = new GlobalFlashGalleries_UI();
		$this->assignRef('ui', $ui);

		parent::display($tpl);
	}
}
