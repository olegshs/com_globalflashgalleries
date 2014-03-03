<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

if (method_exists('JUtility', 'getToken'))
	$token = JUtility::getToken();
else
	$token = JSession::getFormToken();

?>
<div class="component-options com_globalflashgalleries">

<form id="adminForm" name="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="com_globalflashgalleries" />
	<input type="hidden" name="controller" value="options" />
	<input type="hidden" name="task" value="save" />

	<div class="option">
		<label><span class="title"><?php echo JText::_('Check for updates'); ?>:</span></label>
		<select name="options[update.autocheck]">
			<option value="1"<?php if ($this->options['update.autocheck'] == 1) echo ' selected="selected"'; ?>><?php echo JText::_('Daily'); ?></option>
			<option value="7"<?php if ($this->options['update.autocheck'] == 7) echo ' selected="selected"'; ?>><?php echo JText::_('Weekly'); ?></option>
			<option value="30"<?php if ($this->options['update.autocheck'] == 30) echo ' selected="selected"'; ?>><?php echo JText::_('Monthly'); ?></option>
			<option value="0"<?php if ($this->options['update.autocheck'] == 0) echo ' selected="selected"'; ?>><?php echo JText::_('Never'); ?></option>
		</select>
	</div>

	<div class="option">
		<input type="hidden" name="options[galleries.reduce_images]" value="0" />
		<label><input type="checkbox" class="checkbox" id="option_reduce_images" name="options[galleries.reduce_images]" value="1"<?php if ($this->options['galleries.reduce_images']) echo ' checked="checked"'; ?> /> <span class="title"><?php echo JText::_('Reduce large images'); ?></span></label>
		<div class="description"><?php echo JText::_('Optimize loading speed of the galleries by reducing the images size.'); ?></div>
	</div>

	<div class="option" id="images_quality" style="padding-left:1.8em;">
		<label style="vertical-align: baseline;"><?php echo JText::_('Images quality'); ?>:</label>
		<label><input type="radio" name="options[galleries.images.quality]" value="1"<?php if ($this->options['galleries.images.quality'] == 1) echo ' checked="checked"'; ?> /> <?php echo JText::_('Medium'); ?></label>
		<label><input type="radio" name="options[galleries.images.quality]" value="2"<?php if ($this->options['galleries.images.quality'] == 2) echo ' checked="checked"'; ?> /> <?php echo JText::_('High'); ?></label>
	</div>

	<div class="option">
		<input type="hidden" name="options[galleries.generate_thumbnails]" value="0" />
		<label><input type="checkbox" class="checkbox" id="option_generate_thumbnails" name="options[galleries.generate_thumbnails]" value="1"<?php if ($this->options['galleries.generate_thumbnails']) echo ' checked="checked"'; ?> /> <span class="title"><?php echo JText::_('Generate thumbnails'); ?></span></label>
		<div class="description"><?php echo JText::_('Create a smaller versions of images for faster loading of the galleries.'); ?></div>
	</div>

	<div class="option" id="thumbnails_quality" style="padding-left:1.8em;">
		<label style="vertical-align: baseline;"><?php echo JText::_('Thumbnails quality'); ?>:</label>
		<label><input type="radio" name="options[galleries.thumbnails.quality]" value="1"<?php if ($this->options['galleries.thumbnails.quality'] == 1) echo ' checked="checked"'; ?> /> <?php echo JText::_('Medium'); ?></label>
		<label><input type="radio" name="options[galleries.thumbnails.quality]" value="2"<?php if ($this->options['galleries.thumbnails.quality'] == 2) echo ' checked="checked"'; ?> /> <?php echo JText::_('High'); ?></label>
	</div>

	<div class="option">
		<input type="hidden" name="options[galleries.enable_cache]" value="0" />
		<label><input type="checkbox" class="checkbox" name="options[galleries.enable_cache]" value="1"<?php if ($this->options['galleries.enable_cache']) echo ' checked="checked"'; ?> /> <span class="title"><?php echo JText::_('Enable XML caching'); ?></span></label>
		<div class="description"><?php echo JText::_('XML cache reduces server load.'); ?> <a id="clearXmlCache" href="index.php?option=com_globalflashgalleries&amp;controller=component&amp;task=clearXmlCache&amp;<?php echo $token; ?>=1"><?php echo JText::_('Clear cache'); ?></a></div>
	</div>

	<div class="submit">
		<button class="button" type="submit"><?php echo JText::_('Apply Changes'); ?></button>
	</div>
</form>

<script type="text/javascript">//<![CDATA[
(function($) {
	$('#clearXmlCache').click(function() {
		$(document.body).addClass('wait');
		$.ajax({
			url: this.href,
			success: function() {
				alert("<?php echo JText::_('The cache is cleared.'); ?>");
			},
			complete: function() {
				$(document.body).removeClass('wait');
			}
		});
		return false;
	});

	function createOverlay(element) {
		var id = $(element).attr('id') + '_overlay', height = $(element).height();
		$(element).append('<div id="' + id + '"></div>');
		$('#' + id).css({
			position: 'relative',
			top: 0 - height,
			height: height,
			marginBottom: 0 - height,
			background: '#fff',
			opacity: 0
		});
	}

	function removeOverlay(element) {
		$('#' + $(element).attr('id') + '_overlay').remove();
	}

	$('#option_reduce_images').change(function() {
		if (this.checked) {
			$('#images_quality').css({ opacity: 1 });
			removeOverlay('#images_quality');
		} else {
			$('#images_quality').css({ opacity: 0.5 });
			createOverlay('#images_quality');
		}
	}).change();

	$('#option_generate_thumbnails').change(function() {
		if (this.checked) {
			$('#thumbnails_quality').css({ opacity: 1 });
			removeOverlay('#thumbnails_quality');
		} else {
			$('#thumbnails_quality').css({ opacity: 0.5 });
			createOverlay('#thumbnails_quality');
		}
	}).change();
})(jQuery);
//]]></script>

</div>
