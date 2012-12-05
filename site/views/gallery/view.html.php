<?php
/**
 * @copyright   Copyright (c) 2010-2012 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.view');
jimport('joomla.plugin.helper');

class GlobalFlashGalleriesViewGallery extends JViewLegacy
{
	var $options = array();

	function display( $tpl = null )
	{
		require_once globalflash_frontendDir.DS.'models'.DS.'gallery.php';
		$this->model = new GlobalFlashGalleriesModelGallery();

		$gallery = $this->model->getData();
		$this->assignRef('gallery', $gallery);

		if ($gallery->published)
		{
			$document =& JFactory::getDocument();
			$document->addScript( globalflash_frontendURL.'/js/jquery/jquery.js' );

			JPluginHelper::importPlugin('globalflashgalleries');
			$dispatcher =& JDispatcher::getInstance();

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

			require_once globalflash_frontendDir.DS.'inc'.DS.'options.class.php';
			$xmlCache = GlobalFlashGalleries_Options::get('galleries.enable_cache') != 0;

			if ($xmlCache && is_file($xmlPath = globalflash_tmpDir."/xml/{$gallery->id}.xml")) {
				$modified = strtotime($gallery->modified);
				$xmlURL = globalflash_tmpURL."/xml/{$gallery->id}.xml?{$modified}";
			}
			else {
				$xmlURL = JURI::root(true)."/index.php?option=com_globalflashgalleries&view=xml&format=raw&id={$gallery->id}";
			}

			$this->assignRef('xmlURL', $xmlURL);

			$this->assignRef('altContent', $this->model->getAltContent());

			require_once globalflash_frontendDir.DS.'inc'.DS.'ui.class.php';
			$ui = new GlobalFlashGalleries_UI();
			$this->assignRef('ui', $ui);

			/*
			$document =& JFactory::getDocument();
			$document->addStyleSheet( globalflash_frontendURL.'/css/default.css' );
			*/

			parent::display($tpl);
		}
	}

}
