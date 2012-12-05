<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

if (!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

require_once dirname(__FILE__).DS.'defines.php';

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
