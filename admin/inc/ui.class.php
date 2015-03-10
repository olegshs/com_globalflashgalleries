<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleries_UI
{
	function GlobalFlashGalleries_UI()
	{
		require_once globalflash_adminDir.DS.'inc'.DS.'templates.class.php';
		$this->tpl = new GlobalFlashGalleries_Templates( globalflash_adminDir.DS.'tpl' );
	}

	function xmlElement( $tag, $atts = array(), $inner = false, $quot = '"', $echo = false )
	{
		$out = "<{$tag}";
		if ( !empty($atts) )
		{
			if ( is_object($atts) )
				$atts = get_object_vars($atts);

			foreach ( $atts as $key => $value )
				$out .= " {$key}={$quot}{$value}{$quot}";
		}
		if ( $inner !== false )
			$out .= ">{$inner}</{$tag}>";
		else
			$out .= " />";

		if ($echo) echo $out;
		return $out;
	}

	function comboBox( $items, $selected = '', $atts = array(), $quot = '"', $echo = false )
	{
		$optionsHTML = "\n";
		foreach ($items as $value => $title)
		{
			$optionAtts = array('value' => $value);
			if ( strlen($selected) && $selected == $value )
				$optionAtts['selected'] = 'selected';

			$optionsHTML .= "\t". $this->xmlElement('option', $optionAtts, $title, $quot, false) ."\n";
		}
		return $this->xmlElement('select', $atts, $optionsHTML, $quot, $echo);
	}

	function input( $input, $id, $name, $value = '', $args = array() )
	{
		$tpl = $this->tpl;

		$before = isset($args['before']) ? $args['before'] : null;
		unset($args['before']);

		if ( is_object($input) )
			$inputAtt = $input->attributes();
		else
			$inputAtt = (object)$input;

		switch ( $type = (string)$inputAtt->type )
		{
			case 'checkbox':
				$values = explode('|', (string)$inputAtt->value );

				$a = array(
					'type' => 'checkbox',
					'class' => 'checkbox',
					'id' => $id,
					'name' => $name,
					'value' => $values[0],
				);
				if ($value == $values[0])
					$a['checked'] = 'checked';

				$a = array_merge($a, $args);

				return $this->xmlElement('input', $a);

			case 'textarea':
				$a = array(
					'id' => $id,
					'name' => $name,
					'cols' => 40,
					'rows' => 3,
				);
				$a = array_merge($a, $args);

				return $this->xmlElement('textarea', $a, $value);

			case 'font':
			case 'select':
				$options = '';
				if ($type == 'font')
				{
					$fonts = array(
						'Arial' => 'Arial',
						'Comic Sans MS' => 'Comic Sans MS',
						'Courier New' => 'Courier New',
						'Georgia' => 'Georgia',
						'Tahoma' => 'Tahoma',
						'Times New Roman' => 'Times New Roman',
						'Trebuchet MS' => 'Trebuchet MS',
						'Verdana' => 'Verdana'
					);
					foreach ($fonts as $font => $title)
					{
						$a = array( 'value' => $font, 'style' => "font-family:'{$font}';" );
						if ($font == $value)
							$a['selected'] = 'selected';

						$options .= $this->xmlElement('option', $a, $title);
					}
				}
				else
				{
					foreach ($input->option as $option)
					{
						$optionAtt = $option->attributes();

						$a = array( 'value' => (string)$optionAtt->value );
						if ((string)$optionAtt->value == $value)
							$a['selected'] = 'selected';

						if ( get_class($option) == 'SimpleXMLElement' )
							$optionContent = &$option;
						else {
							$optionContent = $option->content();
							$optionContent = $optionContent->scalar;
						}

						$options .= $this->xmlElement('option', $a, (string)$optionContent);
					}
				}

				$a = array(
					'id' => $id,
					'name' => $name
				);
				$a = array_merge($a, $args);

				return $this->xmlElement('select', $a, $options);

			case 'slider':
				return $tpl->parse(
					'slider',
					array(
						'id' => $id,
						'id2' => str_replace('.', '\\\\.', $id),
						'name' => $name,
						'value' => $value,
						'min' => $inputAtt->min,
						'max' => $inputAtt->max,
						'step' => !empty($inputAtt->step) ? $inputAtt->step : 1,
						'width' => 100
					)
				);

			case 'color':
				return $tpl->parse(
					'color',
					array(
						'id' => $id,
						'id2' => str_replace('.', '\\\\.', $id),
						'name' => $name,
						'value' => $value
					)
				);

			case 'sound':
			case 'image':
			case 'int':
			case 'text':
			default:
				$a = array(
					'type' => 'text',
					'class' => $type,
					'id' => $id,
					'name' => $name,
					'value' => $value
				);
				switch ($type)
				{
					case 'int':
						$a['maxlength'] = 10;
						break;
					case 'sound':
					case 'image':
						if ($before === null)
							$before = '<label class="url" for="'.$id.'">URL</label> ';
				}
				if ( !empty($inputAtt->readonly) )
				{
					$a['readonly'] = 'readonly';
					$a['class'] = 'readonly ' . $a['class'];
					$a['title'] = 'This parameter is defined in the XML file only';
				}
				if ( !empty($inputAtt->disabled) )
				{
					$a['disabled'] = 'disabled';
					$a['class'] = 'disabled ' . $a['class'];
					$a['title'] = 'This parameter is defined in the XML file only';
				}
				$a = array_merge($a, $args);

				return $before . $this->xmlElement('input', $a);
		}
	}

	function js( $script, $echo = false )
	{
		$script =
			"<script type='text/javascript'>//<![CDATA[\n".
			$script.
			"\n//]]></script>\n";

		if ($echo)
			echo $script;

		return $script;
	}

	function redirect( $href )
	{
		if ( !headers_sent() )
			header("Location: {$href}");

		$this->js("location.href = '{$href}';", true);
		echo "<div class='redirect'><a href='{$href}'>". htmlspecialchars($href) ."</a></div>\n";
		exit();
	}

	function flash( $id, $movie, $width = 550, $height = 400, $params = array(), $altContent = false, $echo = false )
	{
		if ($altContent === false || $altContent === null)
			$altContent = '<a href="http://www.adobe.com/go/getflash" rel="nofollow"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a>';

		if ( is_object($params) )
			$params = get_object_vars($params);

		$paramsHTML = '';

		if ( count($params) )
		{
			foreach ($params as $name => $value)
				$paramsHTML .= "\n\t<param name='{$name}' value='{$value}' />";
		}

		$html = "<object
	classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'
	id='{$id}'
	width='{$width}'
	height='{$height}'
>
	<param name='movie' value='{$movie}' />{$paramsHTML}
<!--[if !IE]>-->
	<object
		type='application/x-shockwave-flash'
		data='{$movie}'
		width='{$width}'
		height='{$height}'
		style='outline:none;'
	>{$paramsHTML}
<!--<![endif]-->
	{$altContent}
<!--[if !IE]>-->
	</object>
<!--<![endif]-->
</object>
";

		if ($echo)
			echo $html;

		return $html;
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

	function bytesToM( $size, $units = 'M', $precision = 0 )
	{
		$k = array(
			'' => 1,
			'B' => 1,
			'K' => 1024,
			'M' => 1048576,
			'G' => 1073741824,
			'T' => 1099511627776
		);
		if ( preg_match('/\s*(\w+)\s*/', $units, $m) )
			return round($size / $k[strtoupper($m[1][0])], $precision) . $units;
		else
			return $size;
	}

	function bytesToK( $size, $units = 'K', $precision = 0 )
	{
		return $this->bytesToM($size, $units, $precision);
	}

}
