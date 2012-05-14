<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

$max_file_uploads = ($max_file_uploads = ini_get('max_file_uploads')) ? (int)$max_file_uploads : 20;
$upload_max_filesize = $this->ui->bytesToM( $this->ui->mToBytes(ini_get('upload_max_filesize')), ' MB' );
$post_max_size = $this->ui->bytesToM( $this->ui->mToBytes(ini_get('post_max_size')), ' MB' );

function GlobalFlashGalleries_stdUploadItem($id, $class = '', $style = '')
{
?>
		<div class="item <?php echo $class; ?>" id="stdupload_item-<?php echo $id; ?>" style="<?php echo $style; ?>"><div class="inside"><table>
			<tr valign="top">
				<th><strong><label for="stdupload_file-id-<?php echo $id; ?>"><?php echo JText::_('Select File'); ?></label></strong></th>
				<td><input type="file" class="file" id="stdupload_file-id-<?php echo $id; ?>" name="stdupload_file[]" /></td>
			</tr>
			<tr valign="top">
				<th><label for="stdupload_title-id-<?php echo $id; ?>"><?php echo JText::_('Title'); ?></label></th>
				<td><input type="text" class="title text" id="stdupload_title-id-<?php echo $id; ?>" name="stdupload_title[]" /></td>
			</tr>
			<tr valign="top">
				<th><label for="stdupload_description-id-<?php echo $id; ?>"><?php echo JText::_('Description'); ?></label></th>
				<td><textarea id="stdupload_description-id-<?php echo $id; ?>" name="stdupload_description[]" rows="2"></textarea></td>
			</tr>
		</table></div></div>
<?php
}

?>

<div id="stdupload">
	<div id="stdupload_items">
<?php
		GlobalFlashGalleries_stdUploadItem(0, '', 'display:none;');
		GlobalFlashGalleries_stdUploadItem(1);
		for ( $uploadItemID = 2; $uploadItemID <= 5; $uploadItemID++ )
			GlobalFlashGalleries_stdUploadItem($uploadItemID, 'nojs');
?>
	</div>
	<div class="add">
		<input type="button" class="button" value="<?php echo JText::_('+ One More File'); ?>" disabled="disabled" />
	</div>
	<div class="limits">
		<?php JText::printf('You can upload up to %s files at once, maximum file size is %s, total size up to %s.', $max_file_uploads, $upload_max_filesize, $post_max_size); ?>
	</div>
</div>

<?php echo JHTML::_('form.token'); ?>

<div class="submit" style="border-top:1px dotted #999;">
	<input type="submit" class="big button" name="add_stdupload" value="<?php echo JText::_('Start Upload'); ?>" />
</div>
<div style="clear:left;"></div>

<script type="text/javascript">//<![CDATA[
(function($) {
	var
		currentID = 1,
		itemsCount = 1,
		maxItemsCount = <?php echo $max_file_uploads ?>;

	function initItem(element) {
		$('.cancel-upload', element).click(function() {
			removeItem(this);
			return false;
		});
		$('.file', element).change(function() {
			changeItem(this);
		});
	}

	function addItem() {
		if (itemsCount < maxItemsCount) {
			currentID++;
			itemsCount++;

			var item = $('#stdupload_items #stdupload_item-0').clone();
			item
				.attr( 'id', 'stdupload_item-' + currentID )
				.html( item.html().replace(/-id-0/g, '-id-' + currentID) )
				.appendTo('#stdupload_items')
				.show(500);

			initItem(item);
		}
	};

	function removeItem(element) {
		itemsCount--;
		$(element).parents('.item').fadeOut(500, function() {
			$(this).remove();
		});
	}

	var changedItems = new Array();
	function changeItem(element) {
		var id = element.id.match(/\d+/);

		document.getElementById('stdupload_title-id-' + id).value =
			(element.value.match(/^(.*[\/\\]|)(.*)\.(.*?)$/)[2])
			.replace(/[_-]/g, ' ')
			.replace(/\s+/g, ' ')
			.replace(/^\s\s*/, '')
			.replace(/\s\s*$/, '');

		if (changedItems[id] == undefined) {
			changedItems[id] = true;
			if ( $(element).parents('.item').next('.item').length == 0 )
				addItem();
		}
	}

	$('#stdupload_items .nojs').remove();
	$('#stdupload_items .item').prepend('<div class="ui-state-default ui-corner-all" style="position:absolute; z-index:1; right:3.5em; top:1em; padding:0;"><a class="cancel-upload ui-icon ui-icon-close" title="Cancel Upload" href="#"></a></div>');

	initItem('#stdupload_items .item');

	$('#stdupload .add .button')
		.click(function() {
			addItem();
		})
		.removeAttr('disabled');

})(jQuery);
//]]></script>
