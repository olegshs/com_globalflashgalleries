<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesControllerAlbum extends GlobalFlashGalleriesController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		if ( JRequest::getVar('items-bulk') )
			$this->itemsBulk( JRequest::getVar('action') );
		elseif ( JRequest::getVar('items-bulk2') )
			$this->itemsBulk( JRequest::getVar('action2') );

		if ( !empty($_POST['add_stdupload']) && !empty($_FILES['stdupload_file']) )
			$this->stdUpload();
		elseif ( !empty($_POST['add_directory']) && !empty($_POST['import_directory']) )
			$this->importDirectory();
	}

	function itemsBulk( $action )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		switch ($action)
		{
			case 'delete':
				$imageModel = $this->getModel('image');
				$imageModel->delete();

				$model = $this->getModel('album');
				$model->store();

				$this->save('apply');
				break;
		}
	}

	function display()
	{
		parent::display();
	}

	function edit()
	{
		JRequest::setVar('view', 'album');
		//JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save( $task = NULL )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		if ( empty($task) )
			$task = JRequest::getCmd('task');

		$model = $this->getModel('album');

		if ( $model->store($post) )
			$this->setMessage( JText::_('Album saved') );
		else
			JError::raiseWarning( 500, JText::_('Error saving Album') );

		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_globalflashgalleries&controller=album&task=edit&cid[]='.JRequest::getInt('id');
				break;

			default:
			case 'save':
				$link = 'index.php?option=com_globalflashgalleries&controller=albums';
				break;
		}

		$this->setRedirect($link);
	}

	function cancel()
	{
		$msg = JText::_('Operation cancelled');
		$link = 'index.php?option=com_globalflashgalleries&controller=albums';
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('album');

		if ( $model->delete() )
			$msg = JText::_('Album deleted');
		else
			$msg = JText::_('Error: One or more Albums could not be deleted');

		$this->setRedirect('index.php?option=com_globalflashgalleries&controller=albums', $msg);
	}

	function arrange()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('album');

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

	function stdUpload()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$album_id = JRequest::getInt('id');
		$gallery_id = 0;

		$destDir = globalflash_imagesDir;

		jimport('joomla.filesystem.folder');
		if ( JFolder::exists($destDir) || JFolder::create($destDir, 0777) )
		{
			$db =& JFactory::getDBO();

			$db->setQuery("
				SELECT MAX(`order`)
				FROM `#__globalflash_images`
				WHERE
					`album_id` = {$album_id} AND
					`gallery_id` = 0
			");
			$order = $db->loadResult();

			$uploaded = $this->uploadFiles($destDir);
			foreach ($uploaded as $key => $path)
			{
				if ( !empty($path) )
				{
					$fullPath = $destDir.DS.$path;
					list($width, $height) = $imageSize = getimagesize($fullPath);

					$data = (object)array(
						'id' =>			NULL,
						'album_id' =>	$album_id,
						'gallery_id' =>	$gallery_id,
						'name' =>		$_FILES['stdupload_file']['name'][$key],
						'title' =>		$_POST['stdupload_title'][$key],
						'description'=>	$_POST['stdupload_description'][$key],
						'type' =>		$imageSize['mime'],
						'path' =>		$path,
						'width' =>		$width,
						'height' =>		$height,
						'size' =>		filesize($fullPath),
						'order' =>		++$order,
					);
					$db->insertObject('#__globalflash_images', $data, 'id');
				}
			}
			$this->save('apply');
		}
	}

	function importDirectory()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$album_id = JRequest::getInt('id');
		$gallery_id = 0;

		$dir = JPATH_ROOT. DS. JRequest::getVar('import_directory', 'tmp');
		$copyFunc = JRequest::getVar('import_delete', 0) ? 'rename' : 'copy';
		$recurse = false;

		jimport('joomla.filesystem.folder');
		$files = JFolder::files($dir, '.*\.(gif|jpg|jpeg|png|GIF|JPG|JPEG|PNG)$', $recurse, true);
		if ( is_array($files) && count($files) )
		{
			$destDir = globalflash_imagesDir;

			if ( !(JFolder::exists($destDir) || JFolder::create($destDir, 0777)) )
				return false;

			include_once JPATH_COMPONENT.DS.'inc'.DS.'tools.class.php';
			$tools = new GlobalFlashGalleries_Tools();

			$db =& JFactory::getDBO();

			$db->setQuery("
				SELECT MAX(`order`)
				FROM `#__globalflash_images`
				WHERE
					`album_id` = {$album_id} AND
					`gallery_id` = 0
			");
			$order = $db->loadResult();

			foreach ($files as $file)
			{
				$ext = $tools->fileExt($file);
				$fullPath = $tools->uniqueFile($destDir.DS."%s.{$ext}");
				$path = basename($fullPath);

				if ( $copyFunc($file, $fullPath) )
				{
					list($width, $height) = $imageSize = getimagesize($fullPath);

					$data = (object)array(
						'id' =>			NULL,
						'album_id' =>	$album_id,
						'gallery_id' =>	$gallery_id,
						'name' =>		basename($file),
						'title' =>		'',
						'description'=>	'',
						'type' =>		$imageSize['mime'],
						'path' =>		$path,
						'width' =>		$width,
						'height' =>		$height,
						'size' =>		filesize($fullPath),
						'order' =>		++$order,
					);
					$db->insertObject('#__globalflash_images', $data, 'id');
				}
			}

			$this->save('apply');
		}
	}

	function uploadFiles( $destDir )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		include_once JPATH_COMPONENT.DS.'inc'.DS.'tools.class.php';
		$tools = new GlobalFlashGalleries_Tools();

		if ( !empty($_FILES['stdupload_file']) )
		{
			foreach ( $_FILES['stdupload_file']['name'] as $key => $name )
			{
				if ( !empty($name) )
				{
					$ext = $tools->fileExtByMIME( $_FILES['stdupload_file']['type'][$key] );
					$destNames[$key] = basename( $tools->uniqueFile($destDir."/%s.{$ext}") );
				}
			}
			return $tools->upload('stdupload_file', $destDir, $destNames);
		}
	}

}
