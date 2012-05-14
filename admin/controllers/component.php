<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.controller');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

class GlobalFlashGalleriesControllerComponent extends JController
{
	function __construct()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit('Invalid Token');

		parent::__construct();

		$this->registerTask('upgrade', 'upgrade');
		$this->registerTask('download', 'download');
		$this->registerTask('remove', 'remove');
		$this->registerTask('cleanupInstall', 'cleanupInstall');
		$this->registerTask('clearXmlCache', 'clearXmlCache');
	}

	function upgrade()
	{
		JRequest::setVar('view', 'upgrade');
		//JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function download()
	{
		$url = JRequest::getVar('url');
		if (preg_match('#^https?://(www\.|)flash-gallery.com/update/#', $url))
		{
			// Download the package at the URL given
			$p_file = JInstallerHelper::downloadPackage($url);

			// Was the package downloaded?
			if (!$p_file)
				jexit('{ "status": "error", "error": "Unable to download" }');

			$config =& JFactory::getConfig();
			$tmp_dest = $config->getValue('config.tmp_path');

			// Unpack the downloaded package file
			$package = JInstallerHelper::unpack($tmp_dest.DS.$p_file);

			jexit('{ "status": "ok", "packagefile": "'.addslashes($package['packagefile']).'", "extractdir": "'.addslashes($package['extractdir']).'" }');
		}
		else
			jexit('{ "status": "error", "error": "Invalid URL" }');
	}

	function remove()
	{
		$adminDirBackup = globalflash_adminDir.'~';
		$frontendDirBackup = globalflash_frontendDir.'~';

		if ( is_dir($adminDirBackup) )
			JFolder::delete($adminDirBackup);

		if ( is_dir($frontendDirBackup) )
			JFolder::delete($frontendDirBackup);

		rename( globalflash_adminDir, $adminDirBackup );
		rename( globalflash_frontendDir, $frontendDirBackup );

		if ( !is_dir(globalflash_adminDir) && !is_dir(globalflash_frontendDir) )
			jexit('{ "status": "ok" }');
		else
			jexit('{ "status": "error" }');
	}

	function cleanupInstall()
	{
		require_once globalflash_adminDir.DS.'inc'.DS.'options.class.php';
		GlobalFlashGalleries_Options::set('update.available', 0);
		GlobalFlashGalleries_Options::set('update.last_check', time());

		$this->clearXmlCache();

		$packagefile = JRequest::getVar('packagefile');
		$extractdir = JRequest::getVar('extractdir');
		JInstallerHelper::cleanupInstall($packagefile, $extractdir);
	}

	function clearXmlCache()
	{
		$xmlCacheDir = globalflash_tmpDir.DS.'xml';
		if (is_dir($xmlCacheDir)) {
			$files = JFolder::files($xmlCacheDir, '\d+\.xml$', false, true);
			if (!empty($files)) {
				foreach ($files as $file)
					unlink($file);
			}
		}
	}

}
