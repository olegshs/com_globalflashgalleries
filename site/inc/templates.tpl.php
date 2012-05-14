<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

/*
	{@template}
*/
$m = &$cache['tpl'];
if ( isset($m) || preg_match_all('#'.$qtags[0].'@(.*?)'.$qtags[1].'#', $text, $m_tpl) && $m = $m_tpl )
{
	$keys = array_unique($m[1]);
	foreach ($keys as $key)
		$text = str_replace($m[0], $this->parse($key, $a, false), $text);
}
