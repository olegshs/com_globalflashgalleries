<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

?>
<div class="com_globalflashgalleries">

<script type="text/javascript">//<![CDATA[
	jQuery('#selectImages').css({ background:'none' });
	jQuery('#select-images-form').fadeIn(500);
//]]></script>

<form id="select-images-form" action="index.php" method="post">

<div style="margin:1.5em 0 2em 0;"><a class="button" href="index.php?option=com_globalflashgalleries&controller=albums&task=edit"><?php echo JText::_('+ New Album'); ?></a></div>

<ul id="select-albums">
<?php
require_once globalflash_adminDir.DS.'inc'.DS.'image.class.php';

foreach ($this->albums as $album)
{
	$imagesCount = count($album->images);
?>
	<li class="select-album" id="select-album-<?php echo $album->id; ?>">
		<input class="select-album" type="checkbox" name="albums[]" value="<?php echo $album->id; ?>" />
		<span>
			<a class="select-album" href="#select-album-<?php echo $album->id; ?>"><?php echo $album->title; ?></a>
			<span class="album-count">(<?php echo $imagesCount; ?>)</span>
			<small class="edit-album"><a href="index.php?option=com_globalflashgalleries&amp;controller=album&amp;task=edit&amp;cid[]=<?php echo $album->id; ?>"><?php echo JText::_('Edit Album'); ?></a></small>
		</span>

		<ul class="select-album-images" id="select-album-<?php echo $album->id; ?>-images"></ul>
		<div style="clear:left;"></div>
	</li>
<?php
}
?>
</ul>

<div id="selected-images-count" style="position:absolute; bottom:1.5em;"></div>

<input type="hidden"" name="option" value="com_globalflashgalleries" />
<input type="hidden" name="controller" value="gallery" />
<input type="hidden" name="task" value="addImages" />
<input type="hidden" name="id" value="<?php echo $this->gallery->id; ?>" />
<input type="hidden" name="order" value="<?php echo $this->order; ?>">
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">//<![CDATA[
jQuery(document).ready(function($) {

	function loadImages(element, complete) {
		var images = $('#' + $(element).parents('li.select-album').attr('id') + '-images');

		function compl() {
			if ( typeof(complete) == 'function' )
				complete(images);
		}

		if ( !images.html() ) {
			$(document.body).addClass('wait');

			var album_id = images.attr('id').match(/(\d+)-images$/)[1];
			$.ajax({
				url: 'index.php?option=com_globalflashgalleries&view=images&format=raw&album_id=' + album_id,
				success: function(data) {
					images.html(data);

					images.find('.select-image input[type=checkbox]').click(function() {
						updateImagesCount();
					});

					if ($.browser.msie && $.browser.version < 8) {
						images.find('.select-image-preview img').click(function() {
							$(this).parents('label').click();
							updateImagesCount();
						});
					}

					compl();

					$(document.body).removeClass('wait');
				}
			});
		}
		else {
			compl();
		}

		return images;
	}

	function updateImagesCount() {
		selectedImagesCount = 0;
		$('#select-albums .select-image input[type=checkbox]').each(function() {
			if (this.checked)
				selectedImagesCount++;
		});
		if (selectedImagesCount)
			$('#selected-images-count').html('<?php echo JText::_("You have selected"); ?> '+ selectedImagesCount +' '+ (selectedImagesCount > 1 ? '<?php echo JText::_("images."); ?>' : '<?php echo JText::_("image."); ?>'));
		else
			$('#selected-images-count').html('<?php echo JText::_("You have not selected images."); ?>');
	}

	$('#select-albums input.select-album[type=checkbox]').click(function() {
		var checked = this.checked;

		loadImages(this, function(images) {
			images.find('input[type=checkbox]').each(function() {
				this.checked = checked;
			});
			updateImagesCount();
		});
	});

	$('#select-albums a.select-album').toggle(
		function() {
			var images = loadImages(this);
			images.css({ display:'block' });

			return false;
		},
		function() {
			var images = loadImages(this);
			images.css({ display:'none' });

			return false;
		}
	);

});
//]]></script>

</div>
