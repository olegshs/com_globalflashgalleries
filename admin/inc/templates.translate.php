<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

/*
	{`String to translate`}
*/
$m = &$cache['translate'];
if ( isset($m) || preg_match_all('#'.$qtags[0].'`(.*?)`'.$qtags[1].'#', $text, $m_translate) && $m = $m_translate )
{
	$strings = array_unique($m[1]);
	foreach ($strings as $key => $string)
		$text = str_replace($m[0][$key], JText::_($string), $text);
}
