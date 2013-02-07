<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

/*
	{IF expr} ... {/IF}
*/
$m = &$cache['if'];
if ( isset($m) || preg_match_all('#'.$qtags[0].'(IF)\s+?(.*?)\s*?'.$qtags[1].'(.*?)'.$qtags[0].'/(IF)'.$qtags[1].'#ms', $text, $m_tags) && $m = $m_tags )
{
	foreach ($m[0] as $i => $val)
	{
		if ( $m[1][$i] == $m[4][$i] )
		{
			switch ( $m[1][$i] )
			{
				case 'IF':
					if ( preg_match_all('#([!]{0,1}[$]{0,1}[\w_]+[\w\d\._]*|[\W]+)\s*#', $m[2][$i], $vars) )
					{
						$result = false;
						$prevOp = 'or';

						foreach ($vars[1] as $varName)
						{
							if ( $varName[0] == '!' )
							{
								$not = true;
								$varName = substr($varName, 1);
							}
							else
								$not = false;

							switch ($varName)
							{
								case '++ ':
									$prevOp = 'and';
									break;

								case '|| ':
									$prevOp = 'or';
									break;

								default:
									if ( $varName[0] == '$' )
										$varValue = $this->display( substr($varName, 1) );
									else
										$varValue = $this->getElement($varName, $a);

									switch ($prevOp)
									{
										case 'or':
											$result = $result || (!empty($varValue) xor $not);
											break;

										case 'and':
										default:
											$result = $result && (!empty($varValue) xor $not);
											break;
									}
							}
						}
						if ( $result )
							$text = str_replace($val, $m[3][$i], $text);
						else
							$text = str_replace($val, '', $text);
					}
					break;
			}
		}
	}
}
