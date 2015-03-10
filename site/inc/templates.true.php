<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

/*
	{TRUE var} ... {/TRUE}
	{FALSE var} ... {/FALSE}
*/
$m = &$cache['true'];
if ( isset($m) || preg_match_all('#'.$qtags[0].'(TRUE|FALSE)\s+?(.*?)\s*?'.$qtags[1].'(.*?)'.$qtags[0].'/(TRUE|FALSE)'.$qtags[1].'#ms', $text, $m_tags) && $m = $m_tags )
{
	foreach ($m[0] as $i => $val)
	{
		if ( $m[1][$i] == $m[4][$i] )
		{
			switch ( $m[1][$i] )
			{
				case 'TRUE':
					$e = $this->getElement($m[2][$i], $a);
					if ( !empty($e) && strtolower($e) != 'false' )
						$text = str_replace($val, $m[3][$i], $text);
					else
						$text = str_replace($val, '', $text);
					break;

				case 'FALSE':
					$e = $this->getElement($m[2][$i], $a);
					if ( empty($e) || strtolower($e) == 'false' )
						$text = str_replace($val, $m[3][$i], $text);
					else
						$text = str_replace($val, '', $text);
					break;
			}
		}
	}
}
