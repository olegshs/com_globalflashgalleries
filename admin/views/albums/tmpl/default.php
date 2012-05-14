<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

?>
<div class="com_globalflashgalleries">

<form action="index.php" method="post" name="adminForm">
<?php if ( count($this->items) ) : ?>
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="30"><?php echo JText::_('ID'); ?></th>
			<th width="30"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" /></th>
			<th><?php echo JText::_('Title'); ?></th>
			<th><?php echo JText::_('Description'); ?></th>
			<th width="60"><?php echo JText::_('Size'); ?></th>
			<th width="60"><?php echo JText::_('Created'); ?></th>
			<th width="60"><?php echo JText::_('Modified'); ?></th>
			<th width="120">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php
	$db =& JFactory::getDBO();

	$k = 0;
	for ( $i = 0, $n = count($this->items); $i < $n; $i++ )
	{
		$item =& $this->items[$i];
		$checked = JHTML::_('grid.id', $i, $item->id);
		$link = JRoute::_('index.php?option=com_globalflashgalleries&controller=album&task=edit&cid[]='.$item->id);
		$createGalleryLink = JRoute::_('index.php?option=com_globalflashgalleries&controller=gallery&task=edit&album_id='.$item->id);

		if ( empty($item->description) )
			$item->description = '&nbsp;';

		$db->setQuery("
			SELECT COUNT(*) as count, SUM(`size`) as size
			FROM `#__globalflash_images`
			WHERE
				`album_id` = {$item->id} AND
				`gallery_id` = 0
		");
		$albumInfo = $db->loadObject();
		$count = $albumInfo->count;
		if ($albumInfo->size > 10485760)
			$size = $this->ui->bytesToM($albumInfo->size, ' MB', 1);
		else
			$size = $this->ui->bytesToK($albumInfo->size, ' KB');
?>
		<tr class="<?php echo "row$k"; ?>" valign="middle">
			<td align="center"><?php echo $item->id; ?></td>
			<td align="center"><?php echo $checked; ?></td>
			<td style="font-size:115%;"><strong><a href="<?php echo $link; ?>" title="<?php echo JText::_('Edit Album')." &quot;{$item->title}&quot;"; ?>"><?php echo $item->title; ?></a></strong> (<?php echo $count?>)</td>
			<td><?php echo $item->description; ?></td>
			<td align="right"><?php echo $size; ?></td>
			<td><?php echo JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?></td>
			<td><?php echo JHTML::_('date', $item->modified, JText::_('DATE_FORMAT_LC4')); ?></td>
			<td>
				<div style="margin:0.5em;">
					<a class="<?php if (!$count) echo 'disabled '; ?>button" href="<?php echo $createGalleryLink; ?>" title="<?php echo JText::_('Create a Gallery from the Album')." &quot;{$item->title}&quot;"; ?>"<?php if (!$count) echo ' onclick="return false;"'; ?>><?php echo JText::_('Create Gallery'); ?></a>
				</div>
			</td>
		</tr>
<?php
		$k = 1 - $k;
	}
?>
	</tbody>
	</table>
</div>
<?php else: // count($this->items) ?>
<div style="color:#999;"><?php echo JText::_('No Albums'); ?></div>
<?php endif; // count($this->items) ?>

<input type="hidden" name="option" value="com_globalflashgalleries" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="albums" />
<?php echo JHTML::_('form.token'); ?>

</form>

</div>
