<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

class com_globalflashgalleriesInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	function __constructor($adapter) {
	}
 
	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function preflight($route, $adapter) {
	}
 
	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function postflight($route, $adapter) {
	}
 
	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function install($adapter) {
		$this->update($adapter);
	}
 
	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function update($adapter) {
		$db = JFactory::getDBO();
		
		$db->setQuery("SHOW COLUMNS FROM `#__globalflash_galleries`");
		$columns = $db->loadObjectList();
		
		if (!$this->findColumn($columns, 'bgimage')) {
			$db->setQuery("ALTER TABLE `#__globalflash_galleries` ADD `bgimage` VARCHAR(255) NOT NULL DEFAULT '' AFTER `bgcolor`");
			$db->query();
		}
	}
 
	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	function uninstall($adapter) {
		$defines_php = dirname(__FILE__).'/defines.php';
		if (is_file($defines_php)) {
			include_once $defines_php;

			jimport('joomla.filesystem.folder');
		
			if ( is_dir(globalflash_imagesDir) )
				JFolder::delete(globalflash_imagesDir);
		
			if ( is_dir(globalflash_tmpDir) )
				JFolder::delete(globalflash_tmpDir);
		}
	}
	
	function findColumn($columns, $name)
	{
		foreach ($columns as $column) {
			if ($column->Field == $name)
				return $column;
		}
		return false;
	}
}
