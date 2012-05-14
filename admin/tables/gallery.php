<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class TableGallery extends JTable
{
	var
		$id = 0,
		$name,
		$title,
		$description,
		$type,
		$width,
		$height,
		$wmode,
		$bgcolor,
		$bgimage,
		$created,
		$created_by,
		$modified,
		$modified_by,
		$published = 1,
		$order;

	function TableGallery(&$db)
	{
		parent::__construct('#__globalflash_galleries', 'id', $db);
	}
}
