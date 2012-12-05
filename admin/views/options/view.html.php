<?php
/**
 * @copyright   Copyright (c) 2010-2012 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.view');

class GlobalFlashGalleriesViewOptions extends JViewLegacy
{
	function display( $tpl = null )
	{
		$document =& JFactory::getDocument();
		$document->addScript( globalflash_adminURL.'/js/jquery/jquery.js' );

		$title = JText::_('Options');
		JToolBarHelper::title( JText::_('Flash Galleries').": <small>[ {$title} ]</small>", 'cpanel.png' );
		JToolBarHelper::apply();
		JToolBarHelper::help('../contents.html', true);

		require_once globalflash_adminDir.DS.'inc'.DS.'options.class.php';
		$options = GlobalFlashGalleries_Options::get( GlobalFlashGalleries_Options::defaults() );
		$this->set('options', $options);

		parent::display($tpl);
	}
}
