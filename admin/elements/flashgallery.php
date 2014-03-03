<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

if (class_exists('JFormFieldList'))	// Joomla 1.6
{
	class JFormFieldFlashGallery extends JFormFieldList
	{
		/**
		 * The field type.
		 *
		 * @var		string
		 */
		var $type = 'FlashGallery';

		/**
		 * Method to get a list of options for a list input.
		 *
		 * @return	array		An array of JHtml options.
		 */
		function getOptions()
		{
			$options = array();

			$db =& JFactory::getDBO();
			$db->setQuery("
				SELECT
					`id`, `title`
				FROM
					`#__globalflash_galleries`
				WHERE
					`published` > 0
				ORDER BY `title`
			");
			$galleries = $db->loadObjectList();

			if ( $galleries !== null && count($galleries) )
			{
				foreach ($galleries as $gallery)
					$options[] = JHtml::_('select.option', $gallery->id, $gallery->title);
			}

			return $options;
		}
	}
}
elseif (class_exists('JElement'))	// Joomla 1.5
{
	class JElementFlashGallery extends JElement
	{
		function fetchElement($name, $value, &$node, $control_name)
		{
			$db =& JFactory::getDBO();

			$db->setQuery("
				SELECT
					`id`, `title`
				FROM
					`#__globalflash_galleries`
				WHERE
					`published` > 0
				ORDER BY `title`
			");
			$options = $db->loadObjectList();

			if ( $options !== null && count($options) )
				return JHTML::_('select.genericlist', $options, "{$control_name}[{$name}]", 'class="inputbox"', 'id', 'title', $value, $control_name.$name);
		}
	}
}
