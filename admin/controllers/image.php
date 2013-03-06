<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesControllerImage extends GlobalFlashGalleriesController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');
	}

	function edit()
	{
		JRequest::setVar('view', 'image');
		//JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save( $task = NULL )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		if ( empty($task) )
			$task = JRequest::getCmd('task');

		$model = $this->getModel('image');

		if ( $model->store($post) )
			$this->setMessage( JText::_('Image saved') );
		else
			JError::raiseWarning( 500, JText::_('Error saving Image') );

		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_globalflashgalleries&controller=image&task=edit&cid[]='.JRequest::getInt('id');
				break;

			default:
			case 'save':
				if ( $gallery_id = JRequest::getInt('gallery_id') )
					$link = 'index.php?option=com_globalflashgalleries&controller=gallery&task=edit&cid[]='.$gallery_id;
				else
					$link = 'index.php?option=com_globalflashgalleries&controller=album&task=edit&cid[]='.JRequest::getInt('album_id');
				break;
		}

		$this->setRedirect($link);
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('image');
		$image = $model->getData();

		if ( $model->delete() )
			$msg = JText::_('Image deleted');
		else
			$msg = JText::_('Error: One or more Images could not be deleted');

		if ( !empty($image->gallery_id) )
			$link = 'index.php?option=com_globalflashgalleries&controller=gallery&task=edit&cid[]='.$image->gallery_id;
		else
			$link = 'index.php?option=com_globalflashgalleries&controller=album&task=edit&cid[]='.$image->album_id;

		$this->setRedirect($link, $msg);
	}

	function cancel()
	{
		$msg = JText::_('Operation cancelled');

		if ( $gallery_id = JRequest::getInt('gallery_id') )
			$link = 'index.php?option=com_globalflashgalleries&controller=gallery&task=edit&cid[]='.$gallery_id;
		else
			$link = 'index.php?option=com_globalflashgalleries&controller=album&task=edit&cid[]='.JRequest::getInt('album_id');

		$this->setRedirect($link, $msg);
	}

}
