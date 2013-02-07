<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.view');
jimport('joomla.plugin.helper');

if (!function_exists('file_put_contents')) :
function file_put_contents($filename, $data)
{
	$fp = @fopen($filename, 'wb');
	$res = @fwrite($fp, $data);
	@fclose($fp);

	return $res;
}
endif;

class GlobalFlashGalleriesViewXML extends JViewLegacy
{
	var
		$tpl,
		$xmlPath;
	var
		$reduce_images,
		$generate_thumbnails;

	function itemsXml( $items )
	{
		$itemsXml = '';
		foreach ($items as $item)
			$itemsXml .= $this->tpl->parse($this->xmlPath.$this->gallery->type.'-item', $item);

		return $itemsXml;
	}

	function display( $tmpl = null )
	{
		require_once globalflash_frontendDir.DS.'models'.DS.'gallery.php';
		$model = new GlobalFlashGalleriesModelGallery();

		$this->gallery = $model->getData();

		if ($this->gallery->published)
		{
			require_once globalflash_frontendDir.DS.'inc'.DS.'options.class.php';
			$xmlCache = GlobalFlashGalleries_Options::get('galleries.enable_cache') != 0;
			$xmlCachePath = globalflash_tmpDir.DS.'xml'.DS.$this->gallery->id.'.xml';

			if ( $xmlCache && is_file($xmlCachePath) )
			{
				$xml = file_get_contents($xmlCachePath);
			}
			else
			{
				$this->settings =& $model->getSettings();
				$this->items =& $model->getItems();

				require_once globalflash_frontendDir.DS.'inc'.DS.'templates.class.php';
				$this->tpl = new GlobalFlashGalleries_Templates( globalflash_frontendDir.DS.'tpl' );

				JPluginHelper::importPlugin('globalflashgalleries');
				$dispatcher =& JDispatcher::getInstance();

				$legacy = $dispatcher->trigger( 'onGenerateXMLfor'.$this->gallery->type, array('1') );
				$this->xmlPath = empty($legacy[0]) ? 'xml/' : 'xml.legacy/';

				foreach ($this->settings as $name => $value)
					$a[$name] = htmlspecialchars($value);

				$a['imagesFolder'] = globalflash_imagesURL.'/';
				$a['soundsFolder'] = globalflash_frontendURL.'/media/sounds/';
				$a['loader'] = 'true';
				$a['items'] = '';

				$a['multipleAlbums'] = $multipleAlbums = $model->hasMultipleAlbums();

				if ($multipleAlbums)
				{
					$albums = $model->getAlbums();

					$a['albums'] = '';
					foreach ($albums as $key => $album)
					{
						$album->description = htmlspecialchars($album->title);
						$album->icon = '';
						$album->imagesFolder = $a['imagesFolder'];
						$album->thumbnailsFolder = $a['imagesFolder'];
						$album->items = $this->itemsXml($album->items);

						$a['albums'] .= $this->tpl->parse($this->xmlPath.$this->gallery->type.'-album', $album);
					}
				}
				else
				{
					$a['items'] = $this->itemsXml($this->items);
				}

				if ( $this->gallery->type == 'PhotoFlow' && $a['colorScheme'] == 'custom' )
					$a['custom'] = 'true';

				$xml = $this->tpl->parse($this->xmlPath.$this->gallery->type, $a);

				$results = $dispatcher->trigger( 'onGenerateXMLfor'.$this->gallery->type, array(&$xml) );
				if ( !empty($results[0]) )
					$xml = $results[0];

				if ($xmlCache)
				{
					$xmlCacheDir = dirname($xmlCachePath);
					if ( !is_dir($xmlCacheDir) )
					{
						jimport('joomla.filesystem.folder');
						JFolder::create($xmlCacheDir, 0777);
					}

					file_put_contents($xmlCachePath, $xml);
				}
			}

			$this->assignRef('xml', $xml);

			parent::display($tmpl);
		}
	}

}
