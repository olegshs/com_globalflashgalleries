<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesControllerGallery extends GlobalFlashGalleriesController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('addImages', 'addImages');
		$this->registerTask('arrange', 'arrange');

		if (isset($_POST['importSettings']))
			$this->importSettings();
	}

	function importSettings() {
		if (is_uploaded_file($_FILES['importSettingsFile']['tmp_name'])) {
			$tmpPath = globalflash_tmpDir.DS.rand().'.xml';
			if (move_uploaded_file($_FILES['importSettingsFile']['tmp_name'], $tmpPath)) {
				$data = simplexml_load_file($tmpPath);
				if ($data->settings) {
					$settings = array();
					foreach ($data->settings as $object) {
						foreach ($object as $element) {
							$name = $element->getName();
							foreach ($element as $option => $value) {
								$settings["{$name}.{$option}"] = (string)$value;
							}
							$attributes = $element->attributes();
							foreach ($attributes as $option => $value) {
								$settings["{$name}.{$option}"] = (string)$value;
							}
							if (trim((string)$element))
								$settings[$name] = (string)$element;
						}
					}
					if (!empty($settings)) {
						$model = $this->getModel('gallery');
						$model->getData();
						$model->saveSettings($settings);
					}
				}
			}
		}
		$this->setRedirect('index.php?option=com_globalflashgalleries&controller=gallery&task=edit&cid[]='.JRequest::getInt('id'));
	}

	function edit()
	{
		JRequest::setVar('view', 'gallery');
		//JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$task = JRequest::getCmd('task');
		$model = $this->getModel('gallery');
		$gallery = $model->getData();

		// Saving Gallery Properties
		$data = JRequest::get('post');
		if ( $model->store($data) )
		{
			$this->setMessage( JText::_('Gallery saved') );

			$model->getData();

			$newGalleryType = JRequest::getString('type', '', 'post');
			if ( $newGalleryType == $gallery->type )
			{
				// Saving Gallery Settings
				$settings = JRequest::getVar('settings', null, 'post', 'array');
				$settingsInfo = $model->getSettingsInfo();
				foreach ($settingsInfo as $param)
				{
					if ( !isset($settings[$param->name]) && (string)$param->input['type'] == 'checkbox' )
					{
						$values = explode( '|', (string)$param->input['value'] );
						$settings[$param->name] = $values[1];
					}
				}
				$model->saveSettings($settings);
			}

			// Adding Images from the Album
			$album_id = JRequest::getInt('album_id');
			if ( !empty($album_id) )
			{
				$albumModel = $this->getModel('album');
				$albumModel->setId($album_id);
				$albumItems = $albumModel->getItems();

				$model->addItems($albumItems);
			}
		}
		else
			JError::raiseWarning( 500, JText::_('Error saving Gallery') );

		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_globalflashgalleries&controller=gallery&task=edit&cid[]='.JRequest::getInt('id');
				break;

			default:
			case 'save':
				$link = 'index.php?option=com_globalflashgalleries&controller=galleries';
				break;
		}

		$this->setRedirect($link);
	}

	function cancel()
	{
		$msg = JText::_('Operation cancelled');
		$this->setRedirect('index.php?option=com_globalflashgalleries&controller=galleries', $msg);
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$this->setRedirect('index.php?option=com_globalflashgalleries&controller=galleries');

		$db = JFactory::getDBO();

		$task = JRequest::getCmd('task');
		$publish = (int)($task == 'publish');

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		$cids = implode(',', $cid);
		$n = count($cid);

		$query = "
			UPDATE `#__globalflash_galleries`
			SET `published` = {$publish}
			WHERE `id` IN ( {$cids} )
		";
		$db->setQuery($query);
		if ( !$db->query() )
			return JError::raiseWarning( 500, $db->getError() );

		$this->setMessage(
			$n > 1
			? JText::sprintf($publish ? '%d Galleries published' : '%d Galleries unpublished', $n)
			: JText::_($publish ? 'Gallery published' : 'Gallery unpublished')
		);
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('gallery');

		if ( $model->delete() )
		{
			$msg = JText::_('Gallery deleted');
		}
		else
			$msg = JText::_('Error: One or more Galleries could not be deleted');

		$this->setRedirect('index.php?option=com_globalflashgalleries&controller=galleries', $msg);
	}

	function addImages()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$images = JRequest::getVar('images', array(), 'post', 'array');
		JArrayHelper::toInteger($images);

		if ( count($images) )
		{
			$db = JFactory::getDBO();

			$items = array();
			foreach ($images as $image_id)
			{
				$db->setQuery("
					SELECT *
					FROM `#__globalflash_images`
					WHERE `id` = {$image_id}
				");
				$item = $db->loadObject();
				if ($item !== null)
					$items[] = $item;
			}

			$model = $this->getModel('gallery');
			$model->addItems($items);
		}

		JRequest::setVar('task', 'apply');
		$this->save();
	}

	function arrange()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('gallery');

		$order = JRequest::getVar('order');
		if ( preg_match_all('/(\d+)/', $order, $m) )
		{
			$ids = $m[1];
			JArrayHelper::toInteger($ids);

			$model->arrangeItems($ids);
		}
		else
		{
			$model->arrangeItems($order);
		}
	}

}
