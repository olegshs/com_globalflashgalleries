<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleries_Options
{
	function defaults()
	{
		return array(
			'update.autocheck' => 7,
			'galleries.reduce_images' => 1,
			'galleries.images.quality' => 2,
			'galleries.generate_thumbnails' => 1,
			'galleries.thumbnails.quality' => 2,
			'galleries.enable_cache' => 1
		);
	}

	function get( $name, $default = null )
	{
		if ( is_array($name) )
		{
			$options = array();
			foreach ($name as $key => $value)
			{
				if ( is_int($key) )
					$options[$value] = self::get($value);
				else
					$options[$key] = self::get($key, $value);
			}

			return $options;
		}
		else
		{
			global $_globalflashgalleries_options;

			if ( !isset($_globalflashgalleries_options[$name]) )
			{
				$db =& JFactory::getDBO();
				
				if (globalflash_joomla15)
					$e_name = $db->getEscaped($name);
				else
					$e_name = $db->escape($name);
				
				$db->setQuery("SELECT `value` FROM `#__globalflash_options` WHERE `name` = '{$e_name}'");
				$value = $db->loadResult();
				if ($value === null && $default === null)
				{
					$defaults = self::defaults();
					$_globalflashgalleries_options[$name] = isset($defaults[$name]) ? $defaults[$name] : null;
				}
				else
					$_globalflashgalleries_options[$name] = $value === null ? $default : $value;
			}

			return $_globalflashgalleries_options[$name];
		}
	}

	function set( $name, $value )
	{
		if ( is_array($name) )
		{
			$options = array();
			foreach ($name as $key => $value)
				$options[$key] = self::set($key, $value);

			return $options;
		}
		else
		{
			global $_globalflashgalleries_options;

			$db =& JFactory::getDBO();
			if (globalflash_joomla15) {
				$e_name = $db->getEscaped($name);
				$e_value = $db->getEscaped($value);
			}
			else {
				$e_name = $db->escape($name);
				$e_value = $db->escape($value);
			}

			$db->setQuery("SELECT `id` FROM `#__globalflash_options` WHERE `name` = '{$e_name}'");
			$id = (int)$db->loadResult();
			if ($id)
			{
				$db->setQuery("UPDATE `#__globalflash_options` SET `value` = '{$e_value}' WHERE `id` = {$id}");
				$db->query();
			}
			else
			{
				$db->setQuery("INSERT INTO `#__globalflash_options` (`name`, `value`) VALUES ('{$e_name}', '{$e_value}')");
				$db->query();
			}

			return $_globalflashgalleries_options[$name] = $value;
		}
	}

}
