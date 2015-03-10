<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
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
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	TRUE on success
	 */
	function store( $data )
	{
		$data = JRequest::get('post');

		$data['modified'] = $now = date('Y-m-d H:i:s');
		if ( $data['id'] < 1 )
			$data['created'] = $now;

		$row =& $this->getTable();

		// Bind the form fields to the Galleries table
		if ( !$row->bind($data) )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		// Make sure the Gallery record is valid
		if ( !$row->check() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		// Store to the database
		if ( !$row->store() )
		{
			$this->setError( $row->getErrorMsg() );
			return false;
		}

		$this->setId($row->id);

		$this->clearXmlCache();

		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	TRUE on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cids);

		if ( count($cids) )
		{
			$row =& $this->getTable();

			foreach ($cids as $cid)
			{
				$this->setId($cid);
				$this->getData();
				$items =& $this->getItems();

				if ( $row->delete($cid) )
				{
					$this->_db->setQuery("
						DELETE FROM `#__globalflash_settings`
						WHERE `gallery_id` = {$this->_id}
					");
					$this->_db->query();

					$this->_db->setQuery("
						DELETE FROM `#__globalflash_images`
						WHERE `gallery_id` = {$this->_id}
					");
					$this->_db->query();

					foreach ($items as $item)
					{
						$this->_db->setQuery("
							SELECT COUNT(*)
							FROM `#__globalflash_images`
							WHERE `path` = '{$item->path}'
						");
						$copies = $this->_db->loadResult();

						if ( $copies !== null && $copies == 0 )
							unlink( globalflash_imagesDir.DS.$item->path );
					}

					$this->clearXmlCache();
				}
				else
				{
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
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
		require_once globalflash_adminDir.DS.'inc'.DS.'options.class.php';
		$reduce_images = GlobalFlashGalleries_Options::get('galleries.reduce_images') != 0;
		$generate_thumbnails = GlobalFlashGalleries_Options::get('galleries.generate_thumbnails') != 0;

		require_once globalflash_adminDir.DS.'inc'.DS.'image.class.php';

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
						$t_width = (int)$this->settings['preview.width'];
						$t_height = (int)$this->settings['preview.height'];
					}
					else {
						$t_width = (int)$this->settings['thumbnail.width'];
						$t_height = (int)$this->settings['thumbnail.height'];
					}
					break;

				case 'PhotoFlow':
					$t_width = (int)$this->settings['maxImageWidth'];
					$t_height = 0;
					break;

				case 'StackPhoto':
					$t_width = (int)$this->settings['image.width'];
					$t_height = (int)$this->settings['image.height'];
					break;

				case 'Zen':
					$t_width = (int)$this->settings['iconWidth'];
					$t_height = (int)$this->settings['iconHeight'];
					break;

				default:
					$t_width = (int)$this->settings['thumbnail.width'];
					$t_height = (int)$this->settings['thumbnail.height'];
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

	function addItems( $items )
	{
		if ( !empty($items) )
		{
			if ( !is_array($items) )
				$items = array($items);

			$order = JRequest::getVar('order');
			if ($order == 'before')
			{
				$this->_db->setQuery("
					SELECT MIN(`order`)
					FROM `#__globalflash_images`
					WHERE `gallery_id` = {$this->_id}
				");
				$order = $this->_db->loadResult();
				$orderInc = -1;

				$items = array_reverse($items);
			}
			elseif ($order == 'after')
			{
				$this->_db->setQuery("
					SELECT MAX(`order`)
					FROM `#__globalflash_images`
					WHERE `gallery_id` = {$this->_id}
				");
				$order = $this->_db->loadResult();
				$orderInc = 1;
			}
			else
			{
				$order = (int)$order;
				$orderInc = 1;
			}

			foreach ($items as $item)
			{
				unset($item->id);

				$item->gallery_id = $this->_id;
				$item->order = $order += $orderInc;

				$this->_db->insertObject('#__globalflash_images', $item, 'id');
			}

			$this->clearXmlCache();
		}
	}

	function arrangeItems( $order )
	{
		if ( is_array($order) )
		{
			$ids = "'". implode("', '", $order). "'";

			$this->_db->setQuery("
				SELECT `id`, `order`
				FROM `#__globalflash_images`
				WHERE `id` IN ( {$ids} )
				ORDER BY `id`
			");
			$items = $this->_db->loadObjectList();

			if ($items)
			{
				$this->_db->setQuery("
					SELECT MIN(`order`)
					FROM `#__globalflash_images`
					WHERE `id` IN ( {$ids} )
				");
				$minOrder = (int)$this->_db->loadResult();

				$result = array();
				foreach ($items as $item)
				{
					$item->order = array_search($item->id, $order) + $minOrder;
					$this->_db->updateObject('#__globalflash_images', $item, 'id');
					$result[$item->id] = $item->order;
				}

				$this->clearXmlCache();

				return $result;
			}
		}
		else if ( is_string($order) )
		{
			switch ( strtolower($order) )
			{
				case 'desc':
					$order = 'DESC';
					break;
				default:
					$order = 'ASC';
			}

			$this->_db->setQuery("
				SELECT `id`
				FROM `#__globalflash_images`
				WHERE `gallery_id` = '{$this->_id}'
				ORDER BY `title` {$order}, `name` {$order}
			");
			$items = $this->_db->loadObjectList();

			if ($items)
			{
				$result = array();

				$n = 0;
				foreach ($items as $item)
				{
					$item->order = $n++;
					$this->_db->updateObject('#__globalflash_images', $item, 'id');
					$result[$item->id] = $item->order;
				}

				$this->clearXmlCache();

				return $result;
			}
		}
		return false;
	}

	function getAltContent()
	{
		$items =& $this->getItems();

		$altContent = '';
		if (!empty($items)) {
			foreach ($items as $item) {
				$altContent .= "\n\t\t<li style='display:inline;'><a href='{$item->source}'><img src='{$item->thumbnail}' alt='{$item->description}' /></a></li>";
			}
		}

		return "<div class='globalflash-altcontent' style='list-style:none;'><ul>{$altContent}\n\t</ul></div>";
	}

	function &getSettingsInfo()
	{
		if ( !isset($this->settingsInfo) && !empty($this->_data->type) )
		{
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
				require_once globalflash_adminDir.DS.'inc'.DS.'simplexml.class.php';
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
						'description' => isset($param->description) ? htmlspecialchars(str_replace('"_QQ_"', '"', JText::_((string)$param->description) )) : '',
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

	function saveSettings( $settings )
	{
		$existingSettings = array();

		$query = "
			SELECT `id`, `name`
			FROM `#__globalflash_settings`
			WHERE
				`gallery_id` = '{$this->_id}' AND
				`gallery_type` = '{$this->_data->type}'
		";
		$results = $this->_getList($query);
		if ( count($results) )
		{
			foreach ($results as $res)
				$existingSettings[$res->name] = $res->id;
		}

		foreach ( $settings as $name => $value )
		{
			if ( isset($existingSettings[$name]) )
			{
				$row = (object)array(
					'id' => $existingSettings[$name],
					'value' => trim($value)
				);
				$this->_db->updateObject('#__globalflash_settings', $row, 'id');
			}
			else
			{
				$row = (object)array(
					'gallery_id' => $this->_data->id,
					'gallery_type' => $this->_data->type,
					'name' => $name,
					'value' => trim($value)
				);
				$this->_db->insertObject('#__globalflash_settings', $row, 'id');
			}
		}

		$this->clearXmlCache();
	}

	function clearXmlCache()
	{
		$path = globalflash_tmpDir.DS.'xml'.DS.$this->_id.'.xml';
		if ( is_file($path) )
			unlink($path);
	}

}
