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
			<th width="70"><?php echo JText::_('Type'); ?></th>
			<th><?php echo JText::_('Title'); ?></th>
			<th><?php echo JText::_('Description'); ?></th>
			<th width="40"><?php echo JText::_('Published'); ?></th>
			<th width="60"><?php echo JText::_('Created'); ?></th>
			<th width="60"><?php echo JText::_('Modified'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
	$k = 0;
	for ( $i = 0, $n = count($this->items); $i < $n; $i++ )
	{
		$item =& $this->items[$i];
		$checked = JHTML::_('grid.id',   $i, $item->id );
		$link = JRoute::_('index.php?option=com_globalflashgalleries&controller=gallery&task=edit&cid[]='.$item->id);
		$published = JHTML::_('grid.published', $item, $i);

		if ( empty($item->description) )
			$item->description = '&nbsp;';
?>
		<tr class="<?php echo "row{$k}"; ?>" valign="middle">
			<td align="center"><?php echo $item->id; ?></td>
			<td align="center"><?php echo $checked; ?></td>
			<td style="font-variant:small-caps;"><a href="<?php echo $link; ?>"><?php echo $item->type; ?></a></td>
			<td style="font-size:115%;"><strong><a href="<?php echo $link; ?>" title="<?php echo JText::_('Edit Gallery')." &quot;{$item->title}&quot;"; ?>"><?php echo $item->title; ?></a></strong></td>
			<td><?php echo $item->description; ?></td>
			<td align="center"><?php echo $published; ?></td>
			<td style="white-space:nowrap;"><?php echo JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?></td>
			<td style="white-space:nowrap;"><?php echo JHTML::_('date', $item->modified, JText::_('DATE_FORMAT_LC4')); ?></td>
		</tr>
<?php
		$k = 1 - $k;
	}
?>
	</tbody>
	</table>
</div>
<?php else: // count($this->items) ?>
<div style="color:#999;"><?php echo JText::_('No Galleries'); ?></div>
<?php endif; // count($this->items) ?>
<input type="hidden" name="option" value="com_globalflashgalleries" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="galleries" />
<?php echo JHTML::_('form.token'); ?>

</form>

</div>
