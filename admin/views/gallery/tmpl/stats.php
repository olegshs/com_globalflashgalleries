<?php
/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

?>

<table class="gallery stats admintable" width="95%" style="border:1px dashed silver; padding:5px 10px;">
<?php if ($this->gallery->id) : ?>
<tr>
	<th width="33%"><?php echo JText::_('Gallery ID'); ?>:</th>
	<td>
		<?php echo $this->gallery->id; ?>
	</td>
</tr>
<?php endif; ?>
<tr>
	<th><?php echo JText::_('State'); ?>:</th>
	<td>
		<?php echo $this->gallery->published > 0 ? JText::_('Published') : JText::_('Unpublished'); ?>
	</td>
</tr>
<tr>
	<th><?php echo JText::_('Created'); ?>:</th>
	<td>
<?php
		if ( !$this->gallery->created )
			echo JText::_('New Gallery');
		else
			echo JHTML::_('date', $this->gallery->created,  JText::_('DATE_FORMAT_LC2'));
?>
	</td>
</tr>
<tr>
	<th><?php echo JText::_('Modified'); ?>:</th>
	<td>
<?php
		if ( !$this->gallery->modified )
			echo JText::_('Not modified');
		else
			echo JHTML::_('date', $this->gallery->modified, JText::_('DATE_FORMAT_LC2'));
?>
	</td>
</tr>
</table>
