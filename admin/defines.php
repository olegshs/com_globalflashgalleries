<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

if (!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

define( 'globalflash_version',		'0.9.7' );
define( 'globalflash_debug',		false );

$jversion = new JVersion();
$ver = $jversion->getShortVersion();
define( 'globalflash_joomla15',		version_compare($ver, '1.6', '<') ? $ver : false );
define( 'globalflash_joomla3',		version_compare($ver, '3.0', '>=') ? $ver : false );

define( 'globalflash_adminDir',		JPATH_BASE.DS.'components'.DS.'com_globalflashgalleries' );
define( 'globalflash_adminURL',		JURI::base(true).'/components/com_globalflashgalleries' );

define( 'globalflash_frontendDir',	JPATH_ROOT.DS.'components'.DS.'com_globalflashgalleries' );
define( 'globalflash_frontendURL',	JURI::root(true).'/components/com_globalflashgalleries' );

define( 'globalflash_imagesDir',	JPATH_ROOT.DS.'images'.DS.'globalflashgalleries' );
define( 'globalflash_imagesURL',	JURI::root(true).'/images/globalflashgalleries' );

if ( is_writable(JPATH_ROOT.DS.'tmp') )
{
	define( 'globalflash_tmpDir',	JPATH_ROOT.DS.'tmp'.DS.'globalflashgalleries' );
	define( 'globalflash_tmpURL',	JURI::root(true).'/tmp/globalflashgalleries' );
}
else
{
	$app =& JFactory::getApplication();
	define( 'globalflash_tmpDir',		$app->getCfg('tmp_path').DS.'globalflashgalleries' );
	define( 'globalflash_tmpURL',		str_replace(DS, '/', preg_replace('/^'.preg_quote(JPATH_ROOT.DS, '/').'(.*)/', JURI::root(true).'/$1', globalflash_tmpDir)) );
}

if (!class_exists('JControllerLegacy')) {
	jimport('joomla.application.component.controller');
	class JControllerLegacy extends JController {}
}
if (!class_exists('JModelLegacy')) {
	jimport('joomla.application.component.model');
	class JModelLegacy extends JModel {}
}
if (!class_exists('JViewLegacy')) {
	jimport('joomla.application.component.view');
	class JViewLegacy extends JView {}
}
