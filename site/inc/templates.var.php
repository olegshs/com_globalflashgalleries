<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

/*
	{var}
	{$var}
*/
$m = &$cache['vars'];
if ( /*isset($m) ||*/ preg_match_all('#'.$qtags[0].'([\w_]+[\w\d\._]*?)'.$qtags[1].'#', $text, $m_vars) && $m = $m_vars )
{
	$keys = array_unique($m[1]);
	foreach ($keys as $key)
	{
		if ( ($value = array_key_exists($key, $a) ? $a[$key] : false) !== false || (($value = $this->getElement($key, $a)) !== false) )
			$text = str_replace($tags[0].$key.$tags[1], (string)$value, $text);
	}
}
