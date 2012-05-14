<?php
defined('_JEXEC') or die('Restricted access');	// No direct access

class TableSettings extends JTable
{
	var
		$id = 0,
		$gallery_id = 0,
		$gallery_type,
		$name,
		$value;

	function TableSettings(&$db)
	{
		parent::__construct('#__globalflash_settings', 'id', $db);
	}
}
