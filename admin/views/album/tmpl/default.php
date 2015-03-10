<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

?>
<div class="album-edit com_globalflashgalleries">

<script type="text/javascript">
	jQuery.fn.tooltip = function() {};
</script>

<form id="adminForm" name="adminForm" action="index.php" method="post" enctype="multipart/form-data">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Details'); ?></legend>

		<table width="100%"><tr>
			<td width="67%">
				<table class="admintable" width="95%">
					<tr>
						<td class="key" width="120">
							<label for="album-title"><?php echo JText::_('Title'); ?>:</label>
						</td>
						<td>
							<input type="text" class="text" id="album-title" name="title" value="<?php echo htmlspecialchars($this->album->title); ?>" size="75" maxlength="250" style="font-size:150%; width:95%; height:auto;" />
						</td>
					</tr>
					<tr valign="top">
						<td class="key">
							<label for="album-description"><?php echo JText::_('Description'); ?>:</label>
						</td>
						<td>
							<textarea name="description" id="album-description" cols="80" rows="5" style="width:95%;"><?php echo htmlspecialchars($this->album->description); ?></textarea>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top"><div style="margin-top:0.5em;"><?php include 'stats.php'; ?></div></td>
		</tr></table>
	</fieldset>

<?php if (!$this->album->isNew) : ?>
	<fieldset class="adminform" style="margin:1.5em 1em;">
		<legend><?php echo JText::_('Images'); ?></legend>
		<div style="padding:1em;">
			<div id="album-items"><?php include 'items.php'; ?></div>

			<div id="album-add_items" style="display:none;">
				<ul>
					<!-- <li><a href="#add_items-swfupload"><?php //echo JText::_('Flash Uploader'); ?></em></a></li> -->
					<li><a href="#add_items-upload"><?php echo JText::_('Browser Uploader'); ?></a></li>
					<!-- <li><a href="#add_items-archive"><?php //echo JText::_('Upload Archive'); ?></a></li> -->
					<!-- <li><a href="#add_items-media"><?php //echo JText::_('Import from Media'); ?></a></li> -->
					<li><a href="#add_items-directory"><?php echo JText::_('Import from Directory'); ?></a></li>
					<!-- <li><a href="#add_items-url"><?php //echo JText::_('Import from URLs'); ?></a></li> -->
				</ul>

				<!-- <div class="swfupload tab" id="add_items-swfupload"><?php
					//include 'add/swfupload.php';
				?></div> -->

				<div class="stdupload tab" id="add_items-upload"><?php
					include 'add/upload.php';
				?></div>

				<!-- <div class="archive tab" id="add_items-archive"><?php
					//include 'add/archive.php';
				?></div> -->

				<!-- <div class="media tab" id="add_items-media"><?php
					//include 'add/media.php';
				?></div> -->

				<div class="directory tab" id="add_items-directory"><?php
					include 'add/directory.php';
				?></div>

				<!-- <div class="url tab" id="add_items-url"><?php
					//include 'add/url.php';
				?></div> -->
			</div>
			<script type="text/javascript">//<![CDATA[
				jQuery(document).ready(function($) {
					$('#album-add_items').tabs({
						fx: { opacity: 'toggle', duration: 'fast' }
					}).show();;
				});
			//]]></script>
		</div>
	</fieldset>
<?php endif; // !$this->album->isNew ?>
</div>

<input type="hidden" name="option" value="com_globalflashgalleries" />
<input type="hidden" name="controller" value="albums" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="id" value="<?php echo $this->album->id; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

</div>
