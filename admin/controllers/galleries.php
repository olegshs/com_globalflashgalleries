<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

require_once globalflash_adminDir.DS.'controllers'.DS.'gallery.php';

class GlobalFlashGalleriesControllerGalleries extends GlobalFlashGalleriesControllerGallery
{
	function display()
	{
		$this->checkForUpdates(true);

		JRequest::setVar('view', 'galleries');
		parent::display();
	}

}
