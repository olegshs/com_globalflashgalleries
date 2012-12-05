<?php
/**
 * @copyright   Copyright (c) 2010-2012 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

define( 'globalflash_version',		'0.8.2' );
define( 'globalflash_debug',		false );

$jversion = new JVersion();
$ver = $jversion->getShortVersion();
define( 'globalflash_joomla15',		version_compare($ver, '1.6', '<') ? $ver : false );

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
	$config =& JFactory::getConfig();
	define( 'globalflash_tmpDir',		$config->getValue('config.tmp_path').DS.'globalflashgalleries' );
	define( 'globalflash_tmpURL',		str_replace(DS, '/', preg_replace('/^'.preg_quote(JPATH_ROOT.DS, '/').'(.*)/', JURI::root(true).'/$1', globalflash_tmpDir)) );
}

if (!class_exists('JControllerLegacy')) {
	class JControllerLegacy extends JController {}
}
if (!class_exists('JModelLegacy')) {
	class JModelLegacy extends JModel {}
}
if (!class_exists('JViewLegacy')) {
	class JViewLegacy extends JView {}
}
