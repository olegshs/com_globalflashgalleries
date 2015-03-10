<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

function findColumn( $columns, $name )
{
	foreach ($columns as $column) {
		if ($column->Field == $name)
			return $column;
	}
	return false;
}

$db = JFactory::getDBO();

$db->setQuery("SHOW COLUMNS FROM `#__globalflash_galleries`");
$columns = $db->loadObjectList();

if ( !findColumn($columns, 'bgimage') )
{
	$db->setQuery("ALTER TABLE `#__globalflash_galleries` ADD `bgimage` VARCHAR(255) NOT NULL DEFAULT '' AFTER `bgcolor`");
	$db->query();
}
