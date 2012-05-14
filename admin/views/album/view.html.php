<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.view');

class GlobalFlashGalleriesViewAlbum extends JView
{
	function display( $tpl = null )
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet( globalflash_adminURL.'/css/jquery/jquery-ui.css', 'text/css', null, array() );
		$document->addScript( globalflash_adminURL.'/js/jquery/jquery.js' );
		$document->addScript( globalflash_adminURL.'/js/jquery/jquery-ui.js' );
		$document->addScript( globalflash_adminURL.'/js/jquery-tools/overlay.js' );
		$document->addScript( globalflash_adminURL.'/js/jquery-tools/overlay.apple.js' );
		$document->addScript( globalflash_adminURL.'/js/jquery-tools/toolbox.expose.js' );

		$album =& $this->get('Data');
		$album->isNew = $isNew = $album->id < 1;

		$title = $isNew ? JText::_('New Album') : JText::_('Edit Album');
		JToolBarHelper::title( JText::_('Flash Galleries').": <small>[ {$title} ]</small>", 'edit.png' );

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

		JToolBarHelper::help('../albums.html', true);

		if ( $isNew && empty($album->title) )
			$album->title = 'New Album';

		if ( empty($gallery->width) )
			$album->width = 550;

		if ( empty($gallery->height) )
			$album->height = 400;

		$this->assignRef('album', $album);

		$items =& $this->get('Items');
		$this->assignRef('items', $items);

		require_once globalflash_adminDir.DS.'inc'.DS.'ui.class.php';
		$ui = new GlobalFlashGalleries_UI();
		$this->assignRef('ui', $ui);

		parent::display($tpl);
	}

}
