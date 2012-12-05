<?php
/**
 * @copyright   Copyright (c) 2010-2012 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.controller');

if (!function_exists('json_decode'))
{
	require_once globalflash_adminDir.DS.'inc'.DS.'services_json.class.php';
	function json_decode( $json )
	{
		$services_json = new Services_JSON();
		return $services_json->decode($json);
	}
}

class GlobalFlashGalleriesController extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();
	}

	function display()
	{
		$this->addStyleSheets();
		$this->addSubmenu();

		parent::display();
	}

	function updatesAvailable( $a )
	{
		$a === true or exit();

		require_once globalflash_adminDir.DS.'inc'.DS.'options.class.php';

		$now = time();
		if ( GlobalFlashGalleries_Options::get('update.last_check') < $now - GlobalFlashGalleries_Options::get('update.autocheck') * 86400 )
		{
			GlobalFlashGalleries_Options::set('update.last_check', $now);

			$res = file_get_contents("http://flash-gallery.com/update/check.php?format=json&product=com_globalflashgalleries&version=".globalflash_version);
			$update = json_decode($res);
			if ( !empty($update) && $update->status == 'ok' )
			{
				GlobalFlashGalleries_Options::set('update.available', $update->version);
				return $update->version;
			}
		}
		return false;
	}

	function checkForUpdates( $a )
	{
		$a === true or exit();

		require_once globalflash_adminDir.DS.'inc'.DS.'options.class.php';

		if ( GlobalFlashGalleries_Options::get('update.autocheck') && (version_compare(GlobalFlashGalleries_Options::get('update.available'), globalflash_version, '>') || $this->updatesAvailable(true)) )
			JError::raiseNotice(1, "New version of Global Flash Galleries is available. <a href='index.php?option=com_globalflashgalleries&view=checkupdates'>Details &raquo;</a>");
	}

	function addStyleSheets()
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet( JURI::base(true).'/components/com_globalflashgalleries/css/all.css', 'text/css', null, array() );
		$document->addStyleSheet( JURI::base(true).'/components/com_globalflashgalleries/css/icons.css', 'text/css', null, array() );
	}

	function addSubmenu()
	{
		$cName = JRequest::getWord('controller', 'galleries');

		JSubMenuHelper::addEntry(
			JText::_('Galleries'),
			'index.php?option=com_globalflashgalleries&controller=galleries',
			$cName == 'galleries'
		);
		JSubMenuHelper::addEntry(
			JText::_('Albums'),
			'index.php?option=com_globalflashgalleries&controller=albums',
			$cName == 'albums'
		);
		JSubMenuHelper::addEntry(
			JText::_('Options'),
			'index.php?option=com_globalflashgalleries&controller=options',
			$cName == 'options'
		);
	}

}
