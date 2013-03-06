<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesViewGlobalFlashGalleries extends JViewLegacy
{
	function display( $tpl = null )
	{
		JToolBarHelper::title( JText::_('Global Flash Galleries Component').' <small style="color:#999;">[ '.JText::_('Version: ').globalflash_version.' ]</small>', 'cpanel.png' );
		JToolBarHelper::help('../contents.html', true);

		parent::display($tpl);
	}

}
