<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

if ( !headers_sent() )
{
	header("Content-Type: text/xml; encoding=utf-8");

	if (JRequest::getVar('download')) {
		header("Content-Disposition: attachment; filename=\"config.xml\"");
	}

	$now = gmdate('D, j M Y H:i:s').' GMT';
	header("Expires: {$now}");
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}

echo $this->xml;

exit();
