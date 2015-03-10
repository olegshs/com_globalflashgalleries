<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

error_reporting(0);

require_once dirname(__FILE__).'/defines.php';

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require specific controller if requested
if ( $controller = JRequest::getWord('controller') )
{
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if ( file_exists($path) )
		require_once $path;
	else
		$controller = '';
}

// Create the controller
$classname	= 'GlobalFlashGalleriesController'.$controller;
$controller	= new $classname();

// Perform the Request task
$controller->execute( JRequest::getVar('task') );

// Redirect if set by the controller
$controller->redirect();
