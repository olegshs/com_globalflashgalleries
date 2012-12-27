<?php
/**
 * @copyright   Copyright (c) 2010-2012 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

?>
<div class="com_globalflashgalleries">

<form id="adminForm" name="adminForm" action="index.php" method="post">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Details'); ?></legend>

		<table class="admintable">
			<tr>
				<td class="key" width="120">
					<label for="image-name"><?php echo JText::_('File Name'); ?>:</label>
				</td>
				<td>
					<input type="text" class="readonly text" id="image-name" name="" value="<?php echo htmlspecialchars($this->image->name); ?>" readonly="readonly" maxlength="250" style="width:600px;" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="image-title"><?php echo JText::_('Title'); ?>:</label>
				</td>
				<td>
					<input type="text" class="text" id="image-title" name="title" value="<?php echo htmlspecialchars($this->image->title); ?>" maxlength="250" style="font-size:150%; width:600px;" />
				</td>
			</tr>
			<tr valign="top">
				<td class="key">
					<label for="image-description"><?php echo JText::_('Description'); ?>:</label>
				</td>
				<td>
					<textarea name="description" id="image-description" cols="80" rows="5" style="width:600px;"><?php echo htmlspecialchars($this->image->description); ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="image-link"><?php echo JText::_('Link'); ?>:</label>
				</td>
				<td>
					<input type="text" class="text" id="image-link" name="link" value="<?php echo htmlspecialchars($this->image->link); ?>" maxlength="250" style="width:600px;" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="image-target"><?php echo JText::_('Target'); ?>:</label>
				</td>
				<td>
					<label><input type="radio" name="target" value="_blank"<?php if ($this->image->target == '_blank') : ?> checked="checked"<?php endif; ?> /> <span>New window (_blank)</span></label>
					<label style="margin-left:1em;"><input type="radio" name="target" value="_self"<?php if ($this->image->target == '_self') : ?> checked="checked"<?php endif; ?> /> <span>Same window (_self)</span></label>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="image-width"><?php echo JText::_('Size'); ?>:</label>
				</td>
				<td>
					<span title="<?= JText::_('Width') ?>"><?php echo $this->image->width; ?></span>
					&times;
					<span title="<?= JText::_('Height') ?>"><?php echo $this->image->height; ?></span>
					px
				</td>
			</tr>
		</table>
	</fieldset>

	<div style="margin:2em 1em; padding:1em; overflow:scroll; background:#ccc; border:1px inset #eee;">
		<img src="<?php echo globalflash_imagesURL; ?>/<?php echo $this->image->path; ?>" alt="<?php echo empty($this->image->title) ? $this->image->name : $this->image->title; ?>" title="" />
	</div>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_globalflashgalleries" />
<input type="hidden" name="controller" value="image" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="album_id" value="<?php echo $this->image->album_id; ?>" />
<input type="hidden" name="gallery_id" value="<?php echo $this->image->gallery_id; ?>" />
<input type="hidden" name="id" value="<?php echo $this->image->id; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

</div>
