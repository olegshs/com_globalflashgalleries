<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

global $globalflash_embedID;
$globalflash_embedID++;

$flashID = "globalflash-gallery-{$this->gallery->id}";
$uniqueFlashID = "{$flashID}-{$globalflash_embedID}";
$xmlURL = $this->xmlURL;

$width = (int)$this->gallery->width;
$height = (int)$this->gallery->height;

$transparent = $this->gallery->wmode == 'transparent';
$bgcolor = '#'.$this->gallery->bgcolor;
if (!$transparent)
	$bgimage = htmlspecialchars($this->gallery->bgimage);

$style = array();

if ( !empty($this->options) )
{
	// Option names are not case sensitive.
	foreach ($this->options as $key => $val) {
		unset($this->options[$key]);
		$this->options[strtolower($key)] = $val;
	}

	if (!empty($this->options['align'])) {
		$style[] = "text-align: {$this->options['align']};";

		switch (strtolower($this->options['align'])) {
			default:
				$margin = '0';
				break;
			case 'center':
				$margin = '0 auto';
				break;
			case 'right':
				$margin = '0 0 0 auto';
				break;
		}
	}

	if (!empty($this->options['width']))
		$width = $this->options['width'];

	if (!empty($this->options['height']))
		$height = $this->options['height'];

	if (!empty($this->options['bgcolor'])) {
		$bgcolor = $this->options['bgcolor'];
		$transparent = false;
	}

	if (isset($this->options['background'])) {
		$bgimage = $this->options['background'];
		$transparent = false;
	}

	if (isset($this->options['bgimage'])) {
		$bgimage = $this->options['bgimage'];
		$transparent = false;
	}

	if (!empty($this->options['style']))
		$style[] = $this->options['style'];
}

$wmode = empty($bgimage) && !$transparent && strlen($bgcolor) == 7 ? 'opaque' : 'transparent';

$style = implode(' ', $style);

$w = is_numeric($width) ? $width.'px' : $width;
$h = is_numeric($height) ? $height.'px' : $height;

$style2 = "width: {$w}; height: {$h}; overflow: hidden;";
if (!$transparent) {
	$style2 .= " background: {$bgcolor}";
	if (!empty($bgimage))
		$style2 .= " url('{$bgimage}') repeat center";
	$style2 .= ';';
}
if (!empty($margin))
	$style2 .= " margin: {$margin};";

?>

<!-- com_globalflashgalleries <?php echo globalflash_version; ?> -->
<div class="<?php echo $flashID; ?> globalflash-gallery"<?php if (!empty($style)) echo " style=\"{$style}\""; ?>>
<div style="<?php echo $style2; ?>">
    <div id="<?php echo $uniqueFlashID; ?>"><?php echo $this->altContent; ?></div>
</div>
</div>
<script type="text/javascript">//<![CDATA[
	altgallery({
		width: '<?php echo $width; ?>',
		height: '<?php echo $height; ?>',
		images: { folder:'<?php echo globalflash_frontendURL.'/images' ?>' },
		config: '<?php echo $xmlURL; ?>',
		configType: 'xml',
		fullscreen: 'never'
	}, '#<?php echo $uniqueFlashID; ?> .globalflash-altcontent');
//]]></script>
<!-- /com_globalflashgalleries -->
