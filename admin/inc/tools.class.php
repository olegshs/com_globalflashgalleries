<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleries_Tools
{
	function randString( $length = 8, $chars = '[0-9][a-z][A-Z]' )
	{
		$chars = str_replace('[0-9]', '0123456789', $chars);
		$chars = str_replace('[a-z]', 'abcdefghijklmnopqrstuvwxyz', $chars);
		$chars = str_replace('[A-Z]', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', $chars);
		$n_chars = strlen($chars) - 1;

		srand();

		if ( is_array($length) )
		{
			$length = rand($length[0], $length[1]);
		}

		$string = '';
		for ($i = 0; $i < $length; $i++)
		{
			$string .= $chars[rand(0, $n_chars)];
		}

		return $string;
	}

	function uniqueFile( $format = '%s', $length = 8, $chars = '[a-z][0-9]' )
	{
		do {
			$path = sprintf( $format, $this->randString($length, $chars) );
		} while ( file_exists($path) );

		return $path;
	}

	function fileExt( $path )
	{
		$path_info = pathinfo($path);
		return strtolower($path_info['extension']);
	}

	function fileMIME( $path )
	{
		$ext = $this->fileExt($path);
		switch ( strtolower($ext) )
		{
			case 'png':
				return 'image/png';

			case 'gif':
				return 'image/gif';

			case 'jpg':
			case 'jpeg':
				return 'image/jpeg';

			case 'swf':
				return 'application/x-shockwave-flash';

			default:
				return 'application/octet-stream';
		}
	}

	function fileExtByMIME( $mime )
	{
		switch ($mime)
		{
			case 'image/png':
				return 'png';

			case 'image/gif':
				return 'gif';

			case 'image/jpeg':
				return 'jpg';

			case 'application/x-shockwave-flash':
				return 'swf';

			default:
				return '';
		}
	}

	function shortFilename( $filename, $maxLength = 20, $dots = '..' )
	{
		if ( strlen($filename) > $maxLength )
		{
			if ( preg_match('#^(.*[\\/]|)(.*)(\..*)$#', $filename, $m) )
				return substr( $m[2], 0, $maxLength - strlen($m[3]) - strlen($dots) ). $dots. $m[3];
		}
		return $filename;
	}

	function upload( $name, $destDir, $destName = NULL )
	{
		$result = false;

		if ( !empty($_FILES[$name]) )
		{
			$f = &$_FILES[$name];

			$uploadErrors = array(
				UPLOAD_ERR_OK =>		'There is no error, the file uploaded with success.',
				UPLOAD_ERR_INI_SIZE =>	'The uploaded file exceeds the upload_max_filesize directive in php.ini',
				UPLOAD_ERR_FORM_SIZE =>	'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
				UPLOAD_ERR_PARTIAL =>	'The uploaded file was only partially uploaded.',
				UPLOAD_ERR_NO_FILE =>	'No file was uploaded.',
				UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
				UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
				UPLOAD_ERR_EXTENSION =>	'File upload stopped by extension.',
			);

			if ( !is_array($f['name']) )
			{
				if ( $f['error'] == UPLOAD_ERR_OK && is_uploaded_file($f['tmp_name']) )
				{
					$destName = empty($destName) ? $f['name'] : $destName;
					if ( move_uploaded_file($f['tmp_name'], $destDir .'/'. $destName) )
						$result = $destName;
					else
						$result = false;
				}
				else
					$this->error( $f['name'] .' : '. __($uploadErrors[$f['error']], $plugin->name) );
			}
			else
			{
				foreach ( $f['name'] as $key => $name )
				{
					if ( $f['error'][$key] == UPLOAD_ERR_OK && is_uploaded_file($f['tmp_name'][$key]) )
					{
						$destName[$key] = empty($destName[$key]) ? $f['name'][$key] : $destName[$key];
						if ( move_uploaded_file($f['tmp_name'][$key], $destDir .'/'. $destName[$key]) )
							$result[$key] = $destName[$key];
						else
							$result[$key] = false;
					}
					else
					{
						if ( !empty($f['name'][$key]) )
							$this->error( $f['name'][$key] .' : '. __($uploadErrors[$f['error'][$key]], $plugin->name) );
					}
				}
			}
		}
		return $result;
	}

	function error( $message )
	{
		print "<div class='error'>$message</div>\n";
	}

}
