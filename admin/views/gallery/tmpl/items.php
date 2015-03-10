<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

require_once globalflash_adminDir.DS.'inc'.DS.'image.class.php';

if (method_exists('JUtility', 'getToken'))
	$token = JUtility::getToken();
else
	$token = JSession::getFormToken();


foreach ($this->items as $item)
{
	$editURL = 'index.php?option=com_globalflashgalleries&controller=image&amp;task=edit&amp;cid[]='.$item->id;
	$downloadURL = $editURL;
	$deleteURL = 'index.php?option=com_globalflashgalleries&controller=image&amp;task=remove&amp;cid[]='.$item->id;

	$image = new GlobalFlashGalleries_Image(globalflash_imagesDir.DS.$item->path);
	$thumbnail = $image->thumbnail(0, 120, 120, 0, globalflash_tmpDir.DS.'thumbnails');
	$thumbnailURL = str_replace(DS, '/', preg_replace('/^'.preg_quote(JPATH_ROOT.DS, '/').'(.*)/', JURI::root(true).'/$1', realpath($thumbnail)));
?>
	<div class="item" id="item-<?php echo $item->id; ?>">
		<div class="image" style="overflow:hidden;">
			<div class="title"><?php echo $item->title; ?></div>
			<div><a href="<?php echo $editURL; ?>"><img src="<?php echo $thumbnailURL; ?>" height="120" alt="<?php echo $item->title; ?>" title="" /></a></div>
			<div class="menu">
				<a class="menu-button" href="<?php echo $editURL; ?>"></a>
				<ul class="button">
					<li class="edit"><a href="<?php echo $editURL; ?>"><?php echo JText::_('Edit'); ?></a></li><?php /* ?>
					<li class="download"><a href="<?php echo $downloadURL; ?>"><?php echo JText::_('Download'); ?></a></li><?php */ ?>
					<li class="delete last"><a href="<?php echo $deleteURL; ?>"><?php echo JText::_('Delete'); ?></a></li>
				</ul>
			</div>
		</div>
	</div>

<?php
}
?>
<div class="clr"></div>

<script type="text/javascript">//<![CDATA[
jQuery(document).ready(function($) {
	$('.images .item .delete a').click(function() {
		if ( confirm('<?php echo JText::_("Delete Image?"); ?>') ) {
			$.ajax({
				type: 'post',
				url: this.href + '&format=raw',
				data: {
					'<?php echo $token; ?>': '1'
				}
			});
			$(this).parents('.item').fadeOut(500, function() {
				$(this).remove();
			});
		}
		return false;
	});

	$('.images .items').sortable({
		start: function() {
		},
		stop: function() {
			var ids = $(this).sortable('serialize').replace(/[^&\d]/g, '').replace(/&/g, ',');
			$.ajax({
				type: 'post',
				url: 'index.php?option=com_globalflashgalleries&controller=gallery&task=arrange&format=raw&cid[]=<?php echo (int)$this->gallery->id; ?>',
				data: {
					order: ids,
					'<?php echo $token; ?>': '1'
				}
			});
		},
		cursor:	'move',
		placeholder: 'item-placeholder',
		revert: 200
	});
});
//]]></script>
