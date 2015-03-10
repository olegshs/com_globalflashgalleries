<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesViewUpgrade extends JViewLegacy
{
	function display( $tpl = null )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$title = JText::_('Upgrade');
		JToolBarHelper::title( JText::_('Global Flash Galleries Component') .": <small>[ {$title} ]</small>", 'cpanel.png' );
		//JToolBarHelper::help('../upgrade.html', true);

		$document = JFactory::getDocument();
		if (!globalflash_joomla3) {
			$document->addScript( globalflash_adminURL.'/js/jquery/jquery.js' );
		}
		$document->addStyleSheet( globalflash_adminURL.'/css/all.css', 'text/css', null, array() );

		$update = (object)array(
			'url' => JRequest::getVar('url')
		);
		$this->assignRef('update', $update);

		parent::display($tpl);
	}

}
