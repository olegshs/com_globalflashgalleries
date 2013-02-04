<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

if ( count($this->items) ) :

require_once globalflash_adminDir.DS.'inc'.DS.'image.class.php';

if (method_exists('JUtility', 'getToken'))
	$token = JUtility::getToken();
else
	$token = JSession::getFormToken();

?>
<script type="text/javascript">//<![CDATA[
	function checkAll(element) {
		jQuery(element).parents('table').find('input[type=checkbox]').attr('checked', element.checked);
	}
//]]></script>

<div style="margin:0 0 4em;">
	<div class="items-bulk" style="margin:1em 0;">
		<label for="items-bulk-action" style="color:#333;"><?php echo JText::_('With selected'); ?>:</label>
		<select id="items-bulk-action" name="action">
			<option></option>
			<option value="delete"><?php echo JText::_('Delete'); ?></option>
		</select>
		<input type="submit" class="small button" name="items-bulk" value="<?php echo JText::_('Go'); ?>">
	</div>

	<table class="images adminlist">
		<thead>
			<tr>
				<th width="30" align="center"><?php echo JText::_('ID'); ?></th>
				<th width="30" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(this);" /></th>
				<th align="center"><?php echo JText::_('Preview'); ?></th>
				<th><?php echo JText::_('Title'); ?></th>
				<th><?php echo JText::_('Description'); ?></th>
				<th width="150">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php
		$k = 0;
		for ( $i = 0, $n = count($this->items); $i < $n; $i++ )
		{
			$item =& $this->items[$i];
			if ( empty($item->title) )
				$item->title =& $item->name;

			if ( empty($item->description) )
				$item->description = '&nbsp;';

			$checked = JHTML::_('grid.id', $i, $item->id );
			$editLink = JRoute::_('index.php?option=com_globalflashgalleries&controller=image&task=edit&cid[]='.$item->id);
			$deleteLink = JRoute::_('index.php?option=com_globalflashgalleries&controller=image&task=remove&cid[]='.$item->id);
			$url = globalflash_imagesURL.'/'.$item->path;

			$image = new GlobalFlashGalleries_Image(globalflash_imagesDir.DS.$item->path);
			$thumbnail = $image->thumbnail(0, 80, 0, 0, globalflash_tmpDir.DS.'thumbnails');
			$thumbnailURL = str_replace( DS, '/', str_replace(JPATH_ROOT.DS, JURI::root(true).'/', realpath($thumbnail)) );
?>
			<tr class="item <?php echo "row$k"; ?>" id="item-<?php echo $item->id; ?>" valign="top">
				<td class="id" align="center"><?php echo $item->id; ?></td>
				<td class="select" align="center"><?php echo $checked; ?></td>
				<td class="preview" width="150" align="center">
					<a href="<?php echo $url; ?>" target="_blank" rel="#overlay-<?php echo $item->id; ?>"><img src="<?php echo $thumbnailURL; ?>" alt="<?php echo $item->title; ?>" height="80" border="0" /></a>
					<div class="white overlay" id="overlay-<?php echo $item->id; ?>" style="display:none;">
						<table width="640" height="480" cellspacing="0" border="0" style="background:none;"><tr style="background:none;"><td align="center" valign="middle" style="padding:0; border:none; background:none;">
							<img src="<?php echo globalflash_adminURL; ?>/images/transparent.gif" alt="<?php echo $item->title; ?>" style="max-width:640px; max-height:480px; margin:0; padding:0; border:none;" />
						</td></tr></table>
						<p class="title"><strong><?php echo $item->title; ?></strong></p>
					</div>
				</td>
				<td style="font-size:115%;"><strong><a class="hasTip" href="<?php echo $editLink; ?>" title="<?php echo JText::_('Edit Image')." &quot;{$item->title}&quot;"; ?>"><?php echo $item->title; ?></a></strong></td>
				<td><?php echo $item->description; ?></td>
				<td align="center">
					<a class="edit" href="<?php echo $editLink; ?>" title=""><?php echo JText::_('Edit'); ?></a>
					<a class="delete" href="<?php echo $deleteLink; ?>" title=""><?php echo JText::_('Delete'); ?></a>
				</td>
			</tr>
<?php
			$k = 1 - $k;
		}
?>
		</tbody>
		<tfoot>
			<tr>
				<th width="30" align="center"><?php echo JText::_('ID'); ?></th>
				<th width="30" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(this);" /></th>
				<th align="center"><?php echo JText::_('Preview'); ?></th>
				<th><?php echo JText::_('Title'); ?></th>
				<th><?php echo JText::_('Description'); ?></th>
				<th width="150">&nbsp;</th>
			</tr>
		</tfoot>
	</table>

	<div class="items-bulk" style="margin:1em 0;">
		<label for="items-bulk-action2" style="color:#333;"><?php echo JText::_('With selected'); ?>:</label>
		<select id="items-bulk-action2" name="action2">
			<option></option>
			<option value="delete"><?php echo JText::_('Delete'); ?></option>
		</select>
		<input type="submit" class="small button" name="items-bulk2" value="<?php echo JText::_('Go'); ?>">
	</div>

	<div style="clear:left;"></div>
</div>

<script type="text/javascript">//<![CDATA[
(function($) {
	$('.images tbody').sortable({
		start: function() {
		},
		stop: function() {
			var ids = $(this).sortable('serialize').replace(/[^&\d]/g, '').replace(/&/g, ',');
			$.ajax({
				type: 'post',
				url: 'index.php?option=com_globalflashgalleries&controller=album&task=arrange&format=raw&cid[]=<?php echo $this->album->id; ?>',
				data: {
					order: ids,
					'<?php echo $token; ?>': '1'
				}
			});
		},
		axis: 'y',
		cursor:	'move',
		handle: '.id, .select',
		revert: 200
	});

	$(".images .item .preview a")
		.overlay({
			effect: 'apple',
			mask: {
				color: '#000000',
				loadSpeed: 500,
				opacity: 0.7
			}
		})
		.click(function() {
			$(this).parents('.preview').find('.overlay img').attr('src', this.href);
			return false;
		});

	$('.images .item a.delete').click(function() {
		if ( confirm('Delete Image?') ) {
			$.ajax({
				type: 'post',
				url: this.href + '&format=raw',
				data: {
					'<?php echo $token; ?>': '1'
				}
			});
			$(this).parents('tr').fadeOut(500, function() {
				var tbody = $(this).parents('tbody');
				$(this).remove();

				var i = 0;
				tbody.find('tr').each(function() {
					$(this).removeClass('row0 row1');
					$(this).addClass('row' + i++ % 2);
				});
			});
		}
		return false;
	});

	$('.items-bulk .button').click(function() {
		return $(this).parent().find('select').val() ? true : false;
	});
})(jQuery);
//]]></script>

<?php
endif; // count($this->items)
