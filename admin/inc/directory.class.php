<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleries_Directory
{
	var
		$name,
		$path;

	function GlobalFlashGalleries_Directory( $name )
	{
		$this->path = $this->name = $name;
		//$this->path = realpath($name);

		if ( !is_dir($this->path) )
		{
			if ( !mkdir($this->path, 0777, true) )
				return false;
		}
	}

	function delete()
	{
		if ( is_dir($this->path) )
			return rmdir($this->path);
		else
			return false;
	}

	function recurse( $function, $pattern = '#.+#', $maxLevel = false, $level = 0, $path = false )
	{
		if ( is_callable($function) )
		{
			if ($path === false)
				$path = $this->path;

			if ( $dir_handle = opendir($path) )
			{
				$level++;
				while ( $name = readdir($dir_handle) )
				{
					if ( $name != '.' && $name != '..' )
					{
						$fPath = $dir. DS. $name;
						if ( is_dir($fPath) && ($maxLevel === false || $level <= $maxLevel) )
							$this->recurse($function, $pattern, $maxLevel, $level, $fPath);
						else
							if ( preg_match($pattern, $name, $m) )
								call_user_func($function, $fPath);
					}
				}
				closedir($dir_handle);
			}
			else
				return false;
		}
		else
			return false;

		return true;
	}

}
