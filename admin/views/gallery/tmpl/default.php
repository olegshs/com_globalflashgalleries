<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

?>
<div class="gallery-edit com_globalflashgalleries">

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
							<label for="gallery-type"><?php echo JText::_('Type'); ?>:</label>
						</td>
						<td>
<?php
						echo $this->ui->comboBox(
							array(
								'3dSlideshow' =>'3D Slideshow',
								'3dWall' =>		'3D Wall Gallery',
								'Art' =>		'Art Gallery',
								'Aura' =>		'Aura Gallery',
								'Box' =>		'Box Gallery',
								'Cubic' =>		'Cubic Gallery',
								'Line' =>		'Line Gallery',
								'PhotoFlow' =>	'PhotoFlow Gallery',
								'Promo' =>		'Promo Gallery',
								'StackPhoto' =>	'Stack Gallery',
								'Zen' =>		'Zen Gallery'
							),
							$this->gallery->type,
							array(
								'id' =>		'gallery-type',
								'name' =>	'type'
							)
						);
?>

						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="gallery-title"><?php echo JText::_('Title'); ?>:</label>
						</td>
						<td>
							<input type="text" class="text" id="gallery-title" name="title" value="<?php echo htmlspecialchars($this->gallery->title); ?>" size="40" maxlength="250" style="font-size:150%; width:95%; height:auto;" />
						</td>
					</tr>
					<tr valign="top">
						<td class="key">
							<label for="gallery-description"><?php echo JText::_('Description'); ?>:</label>
						</td>
						<td>
							<textarea name="description" id="gallery-description" cols="80" rows="5" style="width:95%;"><?php echo htmlspecialchars($this->gallery->description); ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="gallery-width"><?php echo JText::_('Size'); ?>:</label>
						</td>
						<td>
							<input type="text" class="int" id="gallery-width" name="width" title="<?php echo JText::_('Width'); ?>" value="<?php echo $this->gallery->width; ?>" size="5" maxlength="4" />
							&times;
							<input type="text" class="int" id="gallery-height" name="height" title="<?php echo JText::_('Height'); ?>" value="<?php echo $this->gallery->height; ?>" size="5" maxlength="4" />
							px
						</td>
					</tr>
					<tr>
						<td class="key" valign="baseline">
							<label><?php echo JText::_('Background'); ?>:</label>
						</td>
						<td>
							<div style="margin:0 0 0.6em;">
								<input type="hidden" name="wmode" value="opaque" />
								<label>
									<input type="checkbox" id="gallery-transparent" name="wmode" value="transparent"<?php if ($this->gallery->wmode == 'transparent') echo ' checked="checked"'; ?> />
									<span><?php echo JText::_('Transparent'); ?></span>
								</label>
							</div>
							<table id="background-settings" width="100%" cellpadding="4" cellspacing="0" border="0">
								<tr>
									<td style="padding-left:0; white-space:nowrap;">
										<label for="gallery-bgcolor"><?php echo JText::_('Color'); ?>:</label>
									</td>
									<td width="100%">
<?php
										echo $this->ui->input(
											array(
												'type' => 'color'
											),
											'gallery-bgcolor',
											'bgcolor',
											$this->gallery->bgcolor
										);
?>

									</td>
								</tr>
								<tr>
									<td style="padding-left:0; white-space:nowrap;">
										<label for="gallery-bgimage"><?php echo JText::_('Image URL'); ?>:</label>
									</td>
									<td>
<?php
										echo $this->ui->input(
											array(
												'type' => 'image'
											),
											'gallery-bgimage',
											'bgimage',
											htmlspecialchars($this->gallery->bgimage),
											array('before' => '', 'size' => 60, 'style' => 'width:95%;')
										);
?>

									</td>
								</tr>
							</table>
							<div id="background-settings-overlay" style="display:none;"></div>
							<script type="text/javascript">//<![CDATA[
							(function($) {
								$('#gallery-transparent').change(function() {
									var backgroundSettings = $('#background-settings');
									var backgroundSettingsHeight = backgroundSettings.height();
									$('#background-settings-overlay').css({
										display: 'block',
										position: 'relative',
										top: 0 - backgroundSettingsHeight,
										marginBottom: 0 - backgroundSettingsHeight,
										height: backgroundSettingsHeight,
										background: '#fff',
										opacity: 0
									});
									if (this.checked) {
										backgroundSettings.css({ opacity: 0.5 });
									} else {
										$('#background-settings-overlay').css({ display: 'none' });
										backgroundSettings.css({ opacity: 1 });
									}
								});
								$('#gallery-transparent').trigger('change');
							})(jQuery);
							//]]></script>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top"><div style="margin-top:3.3em;"><?php include 'stats.php'; ?></div></td>
		</tr></table>
	</fieldset>

<?php if (!$this->gallery->isNew): ?>
	<div class="tabs-panel" style="margin:2em 1em; min-width:900px;">
<?php
	if (globalflash_joomla15) {
		jimport('joomla.html.pane');
		$pane =& JPane::getInstance('sliders');
		echo $pane->startPane('pane');
	}
	else
		echo JHtml::_('tabs.start');
?>
<?php
	if (globalflash_joomla15)
		echo $pane->startPanel(JText::_('Images'), 'images-panel');
	else
		echo JHtml::_('tabs.panel', JText::_('Images'), 'images-panel');
?>
		<div class="images" style="padding:1em;">
			<?php /* ?><div class="arrange" style="float:right; margin:5px 30px 0 0;">
				<label for="arrangeOrder"><?php echo JText::_('Arrange by Title'); ?></label>
				<select id="arrangeOrder" name="order">
					<option value="asc">A-Z</option>
					<option value="desc">Z-A</option>
				</select>
				<input type="submit" class="small button" name="arrangeItems" value="OK" style="vertical-align:baseline;" />
			</div><?php */ ?>

			<?php if ( count($this->items) ) : ?>
			<div class="add before" style="margin:5px 0 0 10px;">
				<input type="button" class="button" id="addImages-before-<?php echo $this->gallery->id; ?>" value="<?php echo JText::_('Add Images'); ?>" />
			</div>
			<?php endif; ?>

			<div class="items" id="gallery-items-<?php echo $this->gallery->id; ?>" style="clear:both; margin:0 0 0 -7px;"><?php
				include 'items.php';
			?></div>

			<div class="add after" style="margin:0 0 0 10px;">
				<input type="button" class="button" id="addImages-after-<?php echo $this->gallery->id; ?>" value="<?php echo JText::_('Add Images'); ?>" />
			</div>

			<div style="clear:both;">&nbsp;</div>

			<div class="selectImages" id="selectImages" style="display:none;"></div>

			<script type="text/javascript">//<![CDATA[
				var selectedImagesCount = 0;

				jQuery(document).ready(function($) {
					$('#selectImages').dialog({
						title: '<?php echo JText::_('Select Images'); ?>',
						autoOpen: false,
						modal: true,
						width: 800,
						height: 550,
						resizable: true,
						buttons: {
							'OK': function() {
								if (selectedImagesCount)
									$('#select-images-form').submit();

								$(this).dialog('close');
							}
						}
					});

					$('.com_globalflashgalleries .images .add input').click(function() {
						$('#selectImages')
							.empty()
							.dialog('open')
							.css({ background:'url(components/com_globalflashgalleries/images/loader.gif) no-repeat center' })
							.load(
								'index.php?option=com_globalflashgalleries&view=albums&layout=select&format=raw',
								{
									gallery_id: this.id.match(/(\d+)/)[1],
									order: $(this.parentNode).hasClass('before') ? 'before' : 'after'
								},
								function() {
									$(this).css({ background:'none' });
								}
							);
						return false;
					});
				});
			//]]></script>
		</div>

<?php
	if (globalflash_joomla15)
		echo $pane->endPanel();
?>
<?php
	if (globalflash_joomla15)
		echo $pane->startPanel(JText::_('Settings'), 'settings-pane');
	else
		echo JHtml::_('tabs.panel', JText::_('Settings'), 'settings-panel');
?>
		<div class="settings" style="padding:1em;">
<?php include 'settings.php'; ?>
		</div>
<?php
	if (globalflash_joomla15)
		echo $pane->endPanel();
?>
<?php
	if (globalflash_joomla15)
		echo $pane->endPane();
	else
		echo JHTml::_('tabs.end');
?>
	</div>
<?php endif; ?>

<?php if (globalflash_debug) : ?>
	<div id="debug" style="margin:1em; padding:0.5em; background:#ffc; color:#333;">&nbsp;</div>
<?php endif; ?>

<?php if ( !$this->gallery->isNew ) : ?>
	<fieldset class="adminform">
		<legend><?php echo JText::_('Preview'); ?></legend>
		<div style="padding:1em;"><?php include 'preview.php'; ?></div>
	</fieldset>
<?php endif; ?>

</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_globalflashgalleries" />
<input type="hidden" name="controller" value="gallery" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->gallery->id; ?>" />
<input type="hidden" name="album_id" value="<?php echo JRequest::getInt('album_id'); ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

</div>
