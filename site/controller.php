<?php
/**
 * @copyright   Copyright (c) 2010-2012 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.controller');

class GlobalFlashGalleriesController extends JControllerLegacy
{
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	function display()
	{
		$view = JRequest::getVar('view');
		if ( empty($view) )
			JRequest::setVar('view', 'gallery');

		parent::display();
	}

}
