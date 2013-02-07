<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

ob_start();


define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
define( 'JPATH_BASE', realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS) );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

$mainframe =& JFactory::getApplication('site');


require_once dirname(__FILE__).DS.'defines.php';

require_once globalflash_frontendDir.DS.'models'.DS.'gallery.php';
$model = new GlobalFlashGalleriesModelGallery();

$gallery =& $model->getData();
$settings =& $model->getSettings();
$items =& $model->getItems();

require_once globalflash_frontendDir.DS.'inc'.DS.'templates.class.php';
$tpl = new GlobalFlashGalleries_Templates( globalflash_frontendDir.DS.'tpl' );

jimport('joomla.plugin.helper');
JPluginHelper::importPlugin('globalflashgalleries');
$dispatcher =& JDispatcher::getInstance();

$legacy = $dispatcher->trigger( 'onGenerateXMLfor'.$gallery->type, array('1') );
$xmlPath = empty($legacy[0]) ? 'xml/' : 'xml.legacy/';

foreach ($settings as $name => $value)
	$a[$name] = htmlspecialchars($value);

$a['imagesFolder'] = globalflash_imagesURL.'/';
$a['soundsFolder'] = globalflash_frontendURL.'/media/sounds/';
$a['loader'] = 'true';
$a['items'] = '';

$a['multipleAlbums'] = $multipleAlbums = $model->hasMultipleAlbums();

if ($multipleAlbums)
{
	$albums = $model->getAlbums();
	foreach ($albums as $key => $album)
	{
		$album->description = htmlspecialchars($album->title);
		$album->icon = '';
		$album->imagesFolder = $a['imagesFolder'];
		$album->thumbnailsFolder = $a['imagesFolder'];

		$items = '';
		foreach ($album->items as $item)
		{
			$item->source = $item->path;
			$item->thumbnail = '';

			if ( !empty($item->description) )
			{
				if ( !empty($item->title) )
					$item->description = $item->title. '. '. $item->description;
			}
			else
				$item->description = $item->title;

			$item->description = htmlspecialchars($item->description);

			$item->link = htmlspecialchars($item->link);
			$item->target = htmlspecialchars($item->target);

			$items .= $tpl->parse($xmlPath.$gallery->type.'-item', $item);
		}
		$album->items = $items;

		$a['albums'] .= $tpl->parse($xmlPath.$gallery->type.'-album', $album);
	}
}
else
{
	foreach ($items as $item)
	{
		$item->source = $item->path;
		$item->thumbnail = '';

		if ( !empty($item->description) )
		{
			if ( !empty($item->title) )
				$item->description = $item->title. '. '. $item->description;
		}
		else
			$item->description = $item->title;

		$item->description = htmlspecialchars($item->description);

		$item->link = htmlspecialchars($item->link);
		$item->target = htmlspecialchars($item->target);

		$a['items'] .= $tpl->parse($xmlPath.$gallery->type.'-item', $item);
	}
}

if ( $gallery->type == 'PhotoFlow' && $a['colorScheme'] == 'custom' )
	$a['custom'] = 'true';

$xml = $tpl->parse($xmlPath.$gallery->type, $a);


$dispatcher =& JDispatcher::getInstance();
$results = $dispatcher->trigger( 'onGenerateXMLfor'.$gallery->type, array(&$xml) );

if ( !empty($results[0]) )
	$xml = $results[0];


ob_end_clean();

header("Content-Type: text/xml; encoding=utf-8");

$now = gmdate('D, d M Y H:i:s').' GMT';
header("Expires: {$now}");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo $xml;

?>