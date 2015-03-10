<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class TableImage extends JTable
{
	var
		$id = 0,
		$album_id,
		$gallery_id,
		$type,
		$path,
		$name,
		$title,
		$description,
		$link,
		$target,
		$width,
		$height,
		$size,
		$order;

	function TableImage(&$db)
	{
		parent::__construct('#__globalflash_images', 'id', $db);
	}
}
