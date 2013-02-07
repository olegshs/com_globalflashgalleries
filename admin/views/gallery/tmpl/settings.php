<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

?>

<div id="flashSettings" style="visibility:hidden;"><?php

foreach ($this->gallery->settingsInfo as $name => $param)
{
	$a[$name] = $param;
	$a[$name]->input = $this->ui->input( $param->input, $name, "settings[{$name}]", htmlspecialchars($this->gallery->settings[$name]) );
}

echo $this->tpl->parse( $this->settingsPath.$this->gallery->type, $a );

?></div>

<div id="exportSettings" style="float:left; margin:0 1.5em 0 0;">
	<fieldset style="border:1px solid #ccc;">
		<legend>Export</legend>
		<div style="height:2.5em; padding:0.5em;">
			<a class="button" href="<?php echo JURI::root(true)."/index.php?option=com_globalflashgalleries&amp;view=xml&amp;format=raw&amp;id={$this->gallery->id}"; ?>&amp;download=1" target="_blank" style="display:inline-block;">Export Settings</a>
		</div>
	</fieldset>
</div>
<?php if (function_exists('simplexml_load_file')): ?>
<div id="importSettings" style="float:left; margin:0 1.5em 0 0;">
	<fieldset style="border:1px solid #ccc;">
		<legend>Import</legend>
		<div style="height:2.5em; padding:0.5em;">
			<input type="hidden" name="id" value="<?php echo $this->gallery->id; ?>" />
			<input type="file" name="importSettingsFile" />
			<input type="submit" class="button" name="importSettings" value="Import Settings" style="display:inline-block; vertical-align:baseline;" />
		</div>
	</fieldset>
</div>
<div style="clear:left;"></div>
<?php endif; ?>
