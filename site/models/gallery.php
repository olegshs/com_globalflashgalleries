<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesModelGallery extends JModelLegacy
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid', 0, '', 'array');
		$id = (int)$array[0];

		if ( empty($id) )
			$id = JRequest::getInt('id');

		$this->setId($id);
	}

	/**
	 * Method to set the Gallery identifier
	 *
	 * @access	public
	 * @param	int Gallery identifier
	 * @return	void
	 */
	function setId( $id )
	{
		// Set id and wipe data
		$this->_id = $id;
		$this->_data = null;
	}

	/**
	 * Method to get a Gallery
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if ( empty($this->_data) )
		{
			$query = "
				SELECT *
				FROM `#__globalflash_galleries`
				WHERE `id` = {$this->_id}
			";
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		if ( !$this->_data )
		{
			$this->_data = (object)array(
				'id' => 0,
				'name' => null,
				'title' => null,
				'description' => null,
				'type' => null,
				'width' => null,
				'height' => null,
				'wmode' => null,
				'bgcolor' => null,
				'bgimage' => null,
				'created' => null,
				'created_by' => null,
				'modified' => null,
				'modified_by' => null,
				'published' => null,
				'order' => null
			);
		}

		return $this->_data;
	}

	/**
	 * Retrieves the Images list
	 * @return array Array of objects containing the data from the database
	 */
	function &getItems()
	{
		// Load the data
		if ( empty($this->_items) && $this->_id )
		{
			$query = "
				SELECT *
				FROM `#__globalflash_images`
				WHERE
					`gallery_id` = {$this->_id}
				ORDER BY `order`
			";
			$this->_items = $this->_getList($query);
			$this->_items = $this->processItems($this->_items);
		}
		return $this->_items;
	}

	function processItems( $items )
	{
		require_once globalflash_frontendDir.DS.'inc'.DS.'options.class.php';
		$reduce_images = GlobalFlashGalleries_Options::get('galleries.reduce_images') != 0;
		$generate_thumbnails = GlobalFlashGalleries_Options::get('galleries.generate_thumbnails') != 0;

		require_once globalflash_frontendDir.DS.'inc'.DS.'image.class.php';

		if ($reduce_images)
		{
			$quality = GlobalFlashGalleries_Options::get('galleries.images.quality');

			$i_width = (int)($this->_data->width * $quality);
			$i_height = (int)($this->_data->height * $quality);

			if ($i_width > $i_height)
				$i_height = 0;
			else
				$i_width = 0;
		}

		if ($generate_thumbnails)
		{
			$this->getSettings();

			switch ($this->_data->type)
			{
				case 'Art':
					if ($this->settings['preview.usePreview'] == 'true') {
						$t_width = $this->settings['preview.width'];
						$t_height = $this->settings['preview.height'];
					}
					else {
						$t_width = $this->settings['thumbnail.width'];
						$t_height = $this->settings['thumbnail.height'];
					}
					break;

				case 'Cubic':
					$t_width = $this->_data->width / 3.5;
					$t_height = $this->_data->height / 3.0;
					break;

				case 'PhotoFlow':
					$t_width = $this->settings['maxImageWidth'];
					$t_height = 0;
					break;

				case 'StackPhoto':
					$t_width = $this->settings['image.width'];
					$t_height = $this->settings['image.height'];
					break;

				case 'Zen':
					$t_width = $this->settings['iconWidth'];
					$t_height = $this->settings['iconHeight'];
					break;

				default:
					if (!empty($this->settings['thumbnail.width']) && !empty($this->settings['thumbnail.height'])) {
						$t_width = $this->settings['thumbnail.width'];
						$t_height = $this->settings['thumbnail.height'];
					}
					else {
						$t_width = $this->_data->width;
						$t_height = $this->_data->height;
					}
					break;
			}

			$quality = GlobalFlashGalleries_Options::get('galleries.thumbnails.quality');

			$t_width = (int)($t_width * $quality);
			$t_height = (int)($t_height * $quality);

			if ($t_width > $t_height)
				$t_height = 0;
			else
				$t_width = 0;
		}

		foreach ($items as $key => $item)
		{
			$item->source = globalflash_imagesURL.'/'.$item->path;
			if ( $reduce_images )
			{
				$image = new GlobalFlashGalleries_Image(globalflash_imagesDir.DS.$item->path);
				$thumbnail = $image->thumbnail($i_width, $i_height, 0, 0, globalflash_tmpDir.DS.'images');
				$thumbnailURL = str_replace( DS, '/', str_replace(JPATH_ROOT.DS, JURI::root(true).'/', realpath($thumbnail)) );

				$item->source = $thumbnailURL;
			}

			$item->thumbnail = $item->source;
			if ( $generate_thumbnails && ($t_width || $t_height) )
			{
				$image = new GlobalFlashGalleries_Image(globalflash_imagesDir.DS.$item->path);
				$thumbnail = $image->thumbnail($t_width, $t_height, 0, 0, globalflash_tmpDir.DS.'thumbnails');
				$thumbnailURL = str_replace( DS, '/', str_replace(JPATH_ROOT.DS, JURI::root(true).'/', realpath($thumbnail)) );

				$item->thumbnail = $thumbnailURL;
			}

			if ( !empty($item->description) )
			{
				if ( !empty($item->title) )
					$item->description = "{$item->title}. {$item->description}";
			}
			else
				$item->description = $item->title;

			$item->description = htmlspecialchars($item->description, ENT_QUOTES);

			if (isset($this->settings['lightbox.useLightbox']) && $this->settings['lightbox.useLightbox'] == 'true' && (empty($item->link) || $this->settings['lightbox.overrideLinks'] == 'true'))
			{
				$source = globalflash_imagesURL.'/'.$item->path;
				$item->link = "javascript:altbox('{$source}',{images:{folder:'".globalflash_frontendURL."/images/'}});";
				$item->target = "_self";
			}
			else
			{
				$item->link = htmlspecialchars($item->link);
				$item->target = htmlspecialchars($item->target);
			}

			$items[$key] = $item;
		}

		return $items;
	}

	function &getAlbums()
	{
		if ( empty($this->_albums) )
		{
			$query = "
				SELECT `album_id`
				FROM `#__globalflash_images`
				WHERE
					`gallery_id` = {$this->_id}
				GROUP BY `album_id`
				ORDER BY `order`
			";
			$albums = $this->_getList($query);

			foreach ($albums as $album)
			{
				$query = "
					SELECT *
					FROM `#__globalflash_albums`
					WHERE
						`id` = {$album->album_id}
				";
				$this->_db->setQuery($query);
				$album = $this->_db->loadObject();

				$query = "
					SELECT *
					FROM `#__globalflash_images`
					WHERE
						`gallery_id` = {$this->_id} AND
						`album_id` = {$album->id}
					ORDER BY `order`
				";
				$album->items = $this->_getList($query);
				$album->items = $this->processItems($album->items);

				$this->_albums[$album->id] = $album;
			}
		}
		return $this->_albums;
	}

	function hasMultipleAlbums()
	{
		switch ($this->_data->type)
		{
			case 'Zen':
				return true;

			default:
				return false;
		}
	}

	function getAltContent()
	{
		$items =& $this->getItems();

		$altContent = '';
		if (!empty($items)) {
			foreach ($items as $item)
				$altContent .= "\n\t\t<li><a href='{$item->source}'><img src='{$item->thumbnail}' alt='{$item->description}' /></a></li>";
		}

		return "<div class='globalflash-altcontent'><ul>{$altContent}\n\t</ul></div>";
	}

	function &getSettingsInfo()
	{
		if ( !isset($this->settingsInfo) && !empty($this->_data->type) )
		{
			jimport('joomla.plugin.helper');
			JPluginHelper::importPlugin('globalflashgalleries');
			$dispatcher =& JDispatcher::getInstance();
			$legacy = $dispatcher->trigger( 'onGenerateXMLfor'.$this->_data->type, array('1') );

			if ( empty($legacy[0]) )
				$xmlPath = globalflash_frontendDir.DS.'settings.xml'.DS.$this->_data->type.'.xml';
			else
				$xmlPath = globalflash_frontendDir.DS.'settings.xml.legacy'.DS.$this->_data->type.'.xml';

			if ( function_exists('simplexml_load_file') )
			{
				$settingsInfo = simplexml_load_file($xmlPath);

				$groups = &$settingsInfo->group;
				$params = &$settingsInfo->param;
			}
			else
			{
				require_once globalflash_frontendDir.DS.'inc'.DS.'simplexml.class.php';
				$simplexml = new simplexml();
				$settingsInfo = $simplexml->xml_load_file($xmlPath);

				if ( !is_array($settingsInfo->group) )
					$groups = array( $settingsInfo->group );
				else
					$groups = $settingsInfo->group;

				if ( !is_array($settingsInfo->param) )
					$params = array( $settingsInfo->param );
				else
					$params = $settingsInfo->param;
			}

			foreach ($groups as $group)
			{
				$groupAtt = $group->attributes();
				foreach ($group->items->param as $param)
				{
					if ( is_object($param) )
						$paramAtt = $param->attributes();
					else
						$paramAtt = (object)$param;

					$name = (string)$groupAtt->name.'.'.(string)$paramAtt->name;

					$this->settingsInfo[$name] = (object)array(
						'name' => $name,
						'title' => htmlspecialchars( JText::_((string)$param->title) ),
						'description' => isset($param->description) ? htmlspecialchars( JText::_((string)$param->description) ) : '',
						'default' => (string)$paramAtt->default,
						'input' => $param->input
					);
				}
			}

			foreach ($params as $param)
			{
				if ( is_object($param) )
					$paramAtt = $param->attributes();
				else
					$paramAtt = (object)$param;

				$name = (string)$paramAtt->name;

				$this->settingsInfo[$name] = (object)array(
					'name' => $name,
					'title' => htmlspecialchars( JText::_((string)$param->title) ),
					'description' => isset($param->description) ? htmlspecialchars( JText::_((string)$param->description) ) : '',
					'default' => (string)$paramAtt->default,
					'input' => $param->input
				);
			}
		}
		return $this->settingsInfo;
	}

	function &getDefaultSettings()
	{
		if ( !isset($this->defaultSettings) )
		{
			$settingsInfo =& $this->getSettingsInfo();

			foreach ($settingsInfo as $param)
				$this->defaultSettings[$param->name] = $param->default;
		}
		return $this->defaultSettings;
	}

	function &getSettings()
	{
		if ( !isset($this->settings) && !empty($this->_data->type) )
		{
			$this->settings =& $this->getDefaultSettings();

			$query = "
				SELECT *
				FROM `#__globalflash_settings`
				WHERE
					`gallery_id` = '{$this->_id}' AND
					`gallery_type` = '{$this->_data->type}'
			";
			$settings = $this->_getList($query);

			if ( count($settings) )
			{
				foreach ($settings as $param)
					$this->settings[$param->name] = $param->value;
			}
		}
		return $this->settings;
	}

}
