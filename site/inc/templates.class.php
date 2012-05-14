<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleries_Templates
{
	var
		$_dir,
		$_vars,
		$_tags,
		$_templates = array(),
		$_tplCache = array();

	function GlobalFlashGalleries_Templates( $dir = 'tpl', $vars = array(), $tags = '{}' )
	{
		$this->_dir = $dir;
		$this->_tags = $tags;

		if ( is_object($vars) )
			$vars = get_object_vars($vars);

		if ( is_array($vars) )
			$this->_vars = $vars;
	}

	function parse( $templateName, $a = array(), $echo = false, $tags = NULL )
	{
		$out = NULL;

		if ( is_object($a) )
			$a = get_object_vars($a);

		$t = &$this->_templates[$templateName];
		$path = $this->_dir.DS.$templateName;

		$templateFile = $path.'.php';
		if ( is_file($templateFile) )	// Simple PHP templates
		{
			$t = $this->phpTemplate($templateFile, $a);
		}
		else	// Advanced templates
		{
			$templateFile = $path.'.tpl.php';
			if ( is_file($templateFile) )	// Advanced PHP templates
			{
				$t = &$this->_templates[$templateName];
				$t = $this->phpTemplate($templateFile, $a);
			}

			if ( !isset($t) )	// HTML, XML templates
			{
				if ( !file_exists($templateFile) ) $templateFile = $path.'.html';
				if ( !file_exists($templateFile) ) $templateFile = $path.'.xml';
				if ( !file_exists($templateFile) ) $templateFile = $path.'.tpl';
				if ( !file_exists($templateFile) ) $templateFile = $path;
				if ( !file_exists($templateFile) )
				{
					$t = NULL;
					$this->_error( sprintf('Template not found: <strong>%s</strong>', $templateName) );
					return false;
				}
				$t = file_get_contents($templateFile);
			}

			if ( !empty($t) )
			{
				if ( is_array($a) )
				{
					if ( count($this->_vars) )
						$a = array_merge($this->_vars, $a);

					if ( empty($tags) )
						$tags = &$this->_tags;

					$out = $this->_fast($t, $a, $tags, $this->_tplCache[$templateName]);
				}
				else
					$out = &$t;

				if ( $echo === true )
					echo $out;
			}
		}

		return $out;
	}

	function phpTemplate( $source, $a = array() )
	{
		extract($this->_vars);
		extract($a);

		ob_start();
		include $source;
		return ob_get_clean();
	}

	function getElement( $e, $a )
	{
		$p = explode('.', $e);

		$k2 = '';
		foreach ($p as $key)
		{
			if ( is_object($a) ) $a = get_object_vars($a);

			if ( is_array($a) && array_key_exists($key, $a) ) {
				$a = &$a[$key];
				$k2 = '';
			}
			else {
				$k2 .= $key;
				if ( is_array($a) && array_key_exists($k2, $a) ) {
					$a = &$a[$k2];
					$k2 = '';
				}
				else
					$k2 .= '.';
			}
		}
		return is_array($a) ? false : $a;
	}

	function _fast( $text, &$a, &$tags, &$cache )
	{
		$qtags = array( preg_quote($tags[0]), preg_quote($tags[1]) );

		@include 'templates.true.php';
		@include 'templates.if.php';
		//@include 'templates.translate.php';
		@include 'templates.tpl.php';
		@include 'templates.var.php';

		return $text;
	}

	function _error( $message )
	{
		echo "<div class='error'>$message</div>\n";
	}

}
