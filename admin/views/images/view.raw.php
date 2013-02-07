<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.view');

class GlobalFlashGalleriesViewImages extends JViewLegacy
{
	function display( $tpl = null )
	{
		//$document =& JFactory::getDocument();
		//$document->addScript( globalflash_adminURL.'/js/jquery/jquery.js' );

		$db =& JFactory::getDBO();

		$album_id = JRequest::getInt('album_id');
		$album = (object)array(
			'id' => $album_id
		);
		$this->assignRef('album', $album);

		$db->setQuery("
			SELECT *
			FROM `#__globalflash_images`
			WHERE
				`album_id` = '{$album->id}' AND
				`gallery_id` = 0
			ORDER BY `order`
		");
		$images = $db->loadObjectList();
		$this->assignRef('images', $images);

		require_once globalflash_adminDir.DS.'inc'.DS.'tools.class.php';
		$tools = new GlobalFlashGalleries_Tools();
		$this->assignRef('tools', $tools);

		parent::display($tpl);
	}

}
