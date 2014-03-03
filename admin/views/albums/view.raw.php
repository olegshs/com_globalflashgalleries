<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesViewAlbums extends JViewLegacy
{
	function display( $tpl = null )
	{
		//$document =& JFactory::getDocument();
		//$document->addScript( globalflash_adminURL.'/js/jquery/jquery.js' );

		$db =& JFactory::getDBO();

		$db->setQuery("
			SELECT *
			FROM `#__globalflash_albums`
			ORDER BY `title`
		");
		$albums = $db->loadObjectList();

		if ($albums)
		{
			foreach ($albums as $key => $album)
			{
				$db->setQuery("
					SELECT *
					FROM `#__globalflash_images`
					WHERE
						`album_id` = '{$album->id}' AND
						`gallery_id` = 0
					ORDER BY `order`
				");
				$images = $db->loadObjectList();

				if ($images)
					$albums[$key]->images = $images;
			}
		}
		$this->assignRef('albums', $albums);

		$gallery_id = JRequest::getInt('gallery_id');
		$gallery = (object)array(
			'id' => $gallery_id
		);
		$this->assignRef('gallery', $gallery);

		$order = JRequest::getVar('order');
		$this->assignRef('order', $order);

		require_once globalflash_adminDir.DS.'inc'.DS.'tools.class.php';
		$tools = new GlobalFlashGalleries_Tools();
		$this->assignRef('tools', $tools);

		parent::display($tpl);
	}
}
