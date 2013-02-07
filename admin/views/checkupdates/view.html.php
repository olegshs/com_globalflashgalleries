<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.view');

class GlobalFlashGalleriesViewCheckUpdates extends JViewLegacy
{
	function display( $tpl = null )
	{
		$title = JText::_('Upgrade');
		JToolBarHelper::title( JText::_('Global Flash Galleries Component').": <small>[ {$title} ]</small>", 'cpanel.png' );
		//JToolBarHelper::help('../upgrade.html', true);

		require_once globalflash_adminDir.DS.'inc'.DS.'options.class.php';
		GlobalFlashGalleries_Options::set('update.last_check', time());

		$res = file_get_contents("http://flash-gallery.com/update/check.php?format=json&product=com_globalflashgalleries&version=".globalflash_version);
		$update = json_decode($res);

		if ( empty($update) || $update->status != 'ok' )
			$update = false;

		$this->assignRef('update', $update);

		parent::display($tpl);
	}

}
