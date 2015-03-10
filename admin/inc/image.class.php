<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

if (!function_exists('memory_get_usage')):
function memory_get_usage( $real_usage = false )
{
	return 0;
}
endif;

if (!function_exists('image_type_to_extension')):
function image_type_to_extension( $imagetype, $include_dot = true )
{
	$prefix = $include_dot ? '.' : '';

	switch ($imagetype)
	{
		case IMAGETYPE_GIF:
			return $prefix.'gif';

		case IMAGETYPE_JPEG:
			return $prefix.'jpeg';

		case IMAGETYPE_PNG:
			return $prefix.'png';
	}
}
endif;

class GlobalFlashGalleries_Image
{
	var
		$path,
		$type,
		$width,
		$height,
		$_image;

	function GlobalFlashGalleries_Image( $path = null )
	{
		if ( $path !== null )
			$this->load($path);
	}

	function load( $path = null )
	{
		$this->_image = false;

		if ( $path !== null )
			$this->path = $path;

		if ( !empty($this->path) && is_file($this->path) )
		{
			if ( $size = getimagesize($this->path) )
			{
				list($this->width, $this->height, $this->type) = $size;

				$memoryNeed = ($this->width * $this->height * 5) + 4194304;
				if ( $this->getFreeMemory() < $memoryNeed )
				{
					$currentLimit = $this->mToBytes( @ini_get('memory_limit') );
					$newLimit = $memoryNeed + memory_get_usage() + 4194304;
					if ( $newLimit > $currentLimit )
						@ini_set('memory_limit', $newLimit);

					if ( $this->getFreeMemory() < $memoryNeed )
						return false;
				}

				$func = 'imagecreatefrom'. image_type_to_extension($this->type, false);
				if ( function_exists($func) )
					$this->_image = $func($this->path);
			}
		}

		return $this->_image !== false;
	}

	function save( $path = null )
	{
		if ( $path === null )
			$path = $this->path;

		if ( !empty($this->path) && $this->_image !== false )
		{
			$func = 'image'. image_type_to_extension($this->type, false);
			if ( function_exists($func) )
				return $func($this->_image, $path);
		}

		return false;
	}

	function resize( $width, $height = 0 )
	{
		if ($this->_image !== false)
		{
			$src_image =& $this->_image;

			$src_width = imagesx($src_image);
			$src_height = imagesy($src_image);

			$dst_width = $width == 0 ? floor($src_width * $height / $src_height) : $width;
			$dst_height = $height == 0 ? floor($src_height * $width / $src_width) : $height;

			$dst_image = imagecreatetruecolor($dst_width, $dst_height);

			if ($this->type == IMAGETYPE_PNG)
			{
				imagesavealpha($dst_image, true);
				imagefill($dst_image, 0, 0, imagecolorallocatealpha($dst_image, 255, 255, 255, 127));
			}

			if ( imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height) )
			{
				imagedestroy($this->_image);
				$this->_image = $dst_image;

				return true;
			}

			imagedestroy($dst_image);
		}
		return false;
	}

	function crop( $width, $height = 0 )
	{
		if ($this->_image !== false)
		{
			$src_image =& $this->_image;

			$src_width = imagesx($src_image);
			$src_height = imagesy($src_image);

			$dst_width = $width == 0 ? $src_width : $width;
			$dst_height = $height == 0 ? $src_height : $height;

			if ( ($width && $dst_width < $src_width) || ($height && $dst_height < $src_height) )
			{
				$src_x = floor( ($src_width - $dst_width) / 2 );
				$src_y = floor( ($src_height - $dst_height) / 2 );

				$dst_image = imagecreatetruecolor($dst_width, $dst_height);

				if ( imagecopy($dst_image, $src_image, 0, 0, $src_x, $src_y, $src_width, $src_height) )
				{
					imagedestroy($this->_image);
					$this->_image = $dst_image;

					return true;
				}

				imagedestroy($dst_image);
			}
		}
		return false;
	}

	function thumbnail( $width, $height = 0, $maxWidth = 0, $maxHeight = 0, $dir = '.', $name = null )
	{
		if ($this->_image !== false)
		{
			$src_width = imagesx($this->_image);
			$src_height = imagesy($this->_image);
			if ( ($width && $width < $src_width) || ($height && $height < $src_height) )
			{
				if ( $name === null )
				{
					preg_match('#^(.*)\.(.*?)$#', basename($this->path), $m);
					$name = "{$m[1]}-{$width}x{$height}.{$m[2]}";
				}

				$path = $dir. DIRECTORY_SEPARATOR. $name;

				if ( !is_file($path) )
				{
					if ( !is_dir($dir) )
					{
						jimport('joomla.filesystem.folder');
						JFolder::create($dir, 0777);
					}

					$imageClass = get_class($this);
					$image = new $imageClass($this->path);

					$image->resize($width, $height);

					if ( $maxWidth > 0 || $maxHeight > 0 )
						$image->crop($maxWidth, $maxHeight);

					$image->save($path);
				}
				return $path;
			}
		}
		return $this->path;
	}

	function getFreeMemory()
	{
		$memory_limit = $this->mToBytes(@ini_get('memory_limit'));
		if ($memory_limit > 0)
			return $memory_limit - memory_get_usage();
		else
			return 33554432;
	}

	function mToBytes( $size )
	{
		$k = array(
			'' => 1,
			'B' => 1,
			'K' => 1024,
			'M' => 1048576,
			'G' => 1073741824,
			'T' => 1099511627776
		);
		if ( preg_match('/([.\d]+)\s*([BKMGT]|)\w*/i', $size, $m) )
			return (int)((float)$m[1] * $k[strtoupper($m[2])]);
		else
			return (int)$size;
	}

}
