<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

$flashID = "flash-preview";
$xmlURL = JURI::root(true)."/index.php?option=com_globalflashgalleries&view=xml&format=raw&id={$this->gallery->id}";

$width = (int)$this->gallery->width;
$height = (int)$this->gallery->height;

$transparent = $this->gallery->wmode == 'transparent';
$bgcolor = '#'.$this->gallery->bgcolor;
if (!$transparent)
	$bgimage = htmlspecialchars($this->gallery->bgimage);

$wmode = empty($bgimage) && !$transparent && strlen($bgcolor) == 7 ? 'opaque' : 'transparent';

$w = is_numeric($width) ? $width.'px' : $width;
$h = is_numeric($height) ? $height.'px' : $height;

$style2 = "width: {$w}; height: {$h}; overflow: hidden;";
if (!$transparent) {
	$style2 .= " background: {$bgcolor}";
	if (!empty($bgimage))
		$style2 .= " url('{$bgimage}') repeat center";
	$style2 .= ';';
}

?>

<!-- com_globalflashgalleries <?php echo globalflash_version; ?> -->
<div style="<?php echo $style2; ?>">
<?php
	$this->ui->flash(
		$flashID,
		$this->swfURL,
		'100%',
		'100%',
		array(
			'flashVars' => 'XMLFile='.str_replace('&', '%26', $xmlURL),
			'allowFullScreen' => 'true',
			'allowScriptAccess' => 'always',
			'wmode' => $wmode,
			'bgColor' => $bgcolor,
			'quality' => 'high',
			'swfversion' => '9.0.45.0',
			'expressinstall' => globalflash_frontendURL.'/js/swfobject/expressInstall.swf'
		),
		$this->altContent,
		true
	);
?>
</div>
<script type="text/javascript">//<![CDATA[
	swfobject.registerObject('<?php echo $flashID; ?>', '9.0.45.0', '<?php echo globalflash_adminURL; ?>/js/swfobject/expressInstall.swf');
	swf = swfobject.getObjectById('<?php echo $flashID; ?>');

	altgallery({
		width: '<?php echo $width; ?>',
		height: '<?php echo $height; ?>',
		images: { folder:'<?php echo globalflash_frontendURL.'/images' ?>' },
		config: '<?php echo $xmlURL; ?>',
		configType: 'xml',
		fullscreen: 'never'
	}, '#<?php echo $flashID; ?> .globalflash-altcontent');
//]]></script>
<!-- /com_globalflashgalleries -->
