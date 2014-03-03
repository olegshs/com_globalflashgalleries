<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesControllerOptions extends GlobalFlashGalleriesController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');
	}

	function display()
	{
		JRequest::setVar('view', 'options');
		parent::display();
	}

	function save()
	{
		require_once globalflash_adminDir.DS.'inc'.DS.'options.class.php';

		$options = JRequest::getVar('options', array(), '', 'array');
		$options = array_merge(GlobalFlashGalleries_Options::defaults(), $options);
		GlobalFlashGalleries_Options::set($options);

		$this->clearXmlCache();

		$msg = JText::_('Options saved.');
		$this->setRedirect('index.php?option=com_globalflashgalleries&controller=options', $msg);
	}

	function clearXmlCache()
	{
		$xmlCacheDir = globalflash_tmpDir.DS.'xml';
		if (is_dir($xmlCacheDir)) {
			jimport('joomla.filesystem.folder');
			$files = JFolder::files($xmlCacheDir, '\d+\.xml$', false, true);
			if (!empty($files)) {
				foreach ($files as $file)
					unlink($file);
			}
		}
	}

}
