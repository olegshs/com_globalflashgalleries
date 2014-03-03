<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class TableAlbum extends JTable
{
	var
		$id = 0,
		$title,
		$description,
		$created,
		$created_by,
		$modified,
		$modified_by,
		$order;

	function TableAlbum(&$db)
	{
		parent::__construct('#__globalflash_albums', 'id', $db);
	}
}
