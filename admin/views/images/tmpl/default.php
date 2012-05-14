<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

require_once globalflash_adminDir.DS.'inc'.DS.'image.class.php';

foreach ($this->images as $image)
{
	$imageObject = new GlobalFlashGalleries_Image(globalflash_imagesDir.DS.$image->path);
	$thumbnail = $imageObject->thumbnail(0, 64, 100, 0, globalflash_tmpDir.DS.'thumbnails');
	$thumbnailURL = str_replace( DS, '/', str_replace(JPATH_ROOT.DS, JURI::root(true).'/', $thumbnail) );
?>
	<li>
		<label for="select-image-<?php echo $image->id; ?>">
			<span class="select-image-preview">
				<img src="<?php echo $thumbnailURL; ?>" height="64" alt="<?php echo $image->name; ?>" title="" />
			</span>
			<span class="select-image">
				<input type="checkbox" id="select-image-<?php echo $image->id; ?>" name="images[]" value="<?php echo $image->id; ?>" />
				<small title="<?php echo $image->name; ?>"><?php echo $this->tools->shortFilename($image->name, 20, ''); ?></small>
			</span>
		</label>
	</li>
<?php
}
