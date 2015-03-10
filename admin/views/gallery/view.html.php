<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesViewGallery extends JViewLegacy
{
	function display( $tpl = null )
	{
		if (globalflash_joomla3)
			JHtml::_('bootstrap.framework');
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet( globalflash_adminURL.'/css/jquery/jquery-ui.css', 'text/css', null, array() );
		$document->addStyleSheet( globalflash_adminURL.'/css/farbtastic/farbtastic.css', 'text/css', null, array() );
		$document->addStyleSheet( globalflash_adminURL.'/css/gallery-settings.css', 'text/css', null, array() );
		$document->addScript( globalflash_adminURL.'/js/swfobject/swfobject.js' );
		if (!globalflash_joomla3) {
			$document->addScript( globalflash_adminURL.'/js/jquery/jquery.js' );
		}
		$document->addScript( globalflash_adminURL.'/js/jquery/jquery-ui.js' );
		$document->addScript( globalflash_adminURL.'/js/farbtastic.js' );
		$document->addScript( globalflash_adminURL.'/js/gallery-settings.js' );

		$this->model =& $this->getModel();

		$gallery =& $this->get('Data');
		$gallery->isNew = $isNew = $gallery->id < 1;
		$gallery->settings =& $this->get('Settings');
		$gallery->settingsInfo =& $this->get('SettingsInfo');

		$title = $isNew ? JText::_('New Gallery') : JText::_('Edit Gallery');
		JToolBarHelper::title( JText::_('Flash Galleries') .": <small>[ {$title} ]</small>", 'edit.png' );

		//JToolBarHelper::preview();

		if ($isNew)
		{
			JToolBarHelper::save();
			JToolBarHelper::cancel();

			$album_id = JRequest::getInt('album_id');

			if ( !empty($album_id) )
			{
				$db =& JFactory::getDBO();
				$db->setQuery("
					SELECT `title`, `description`
					FROM `#__globalflash_albums`
					WHERE `id` = {$album_id}
				");
				$album = $db->loadObject();

				$gallery->title = $album->title;
				$gallery->description = $album->description;
			}
		}
		else
		{
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel('cancel', 'Close');
		}

		JToolBarHelper::help('../galleries.html', true);

		if ( JRequest::getVar('task') == 'add' && empty($gallery->title) )
			$gallery->title = 'New Gallery';

		if ( empty($gallery->width) )
			$gallery->width = 550;

		if ( empty($gallery->height) )
			$gallery->height = 400;

		if ( empty($gallery->bgcolor) )
			$gallery->bgcolor = 'ffffff';

		$this->assignRef('gallery', $gallery);

		$items =& $this->get('Items');
		$this->assignRef('items', $items);

		JPluginHelper::importPlugin('globalflashgalleries');
		$dispatcher =& JDispatcher::getInstance();

		if (!$isNew) {
			$legacy = $dispatcher->trigger( 'onGenerateXMLfor'.$gallery->type, array('1') );
			$settingsPath = empty($legacy[0]) ? 'settings/' : 'settings.legacy/';
			$this->assignRef('settingsPath', $settingsPath);
		}

		$altgallery = $dispatcher->trigger('getJS');
		if (!empty($altgallery[0])) {
			$jsURL = str_replace(DS, '/', preg_replace('/^'.preg_quote(JPATH_ROOT.DS, '/').'(.*)/', JURI::root(true).'/$1', realpath($altgallery[0])));
			$document->addScript($jsURL);
		}
		else
			$document->addScript(globalflash_frontendURL.'/js/altgallery.js');

		$swf = $dispatcher->trigger('getSWFfor'.$gallery->type);
		if (!empty($swf[0]))
			$swfURL = str_replace(DS, '/', preg_replace('/^'.preg_quote(JPATH_ROOT.DS, '/').'(.*)/', JURI::root(true).'/$1', realpath($swf[0])));
		else
			$swfURL = globalflash_frontendURL.'/swf/'.$gallery->type.'.swf';

		$this->assignRef('swfURL', $swfURL);
		$this->assignRef('altContent', $this->model->getAltContent());

		require_once globalflash_adminDir.DS.'inc'.DS.'ui.class.php';
		$ui = new GlobalFlashGalleries_UI();
		$this->assignRef('ui', $ui);

		require_once globalflash_adminDir.DS.'inc'.DS.'templates.class.php';
		$templates = new GlobalFlashGalleries_Templates( globalflash_adminDir.DS.'tpl' );
		$this->assignRef('tpl', $templates);

		parent::display($tpl);
	}

}
