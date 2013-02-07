<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

?>

<table class="album stats admintable" width="95%" style="border:1px dashed silver; padding:5px 10px;">
<?php if ($this->album->id) : ?>
<tr>
	<th width="33%"><?php echo JText::_('Album ID'); ?>:</th>
	<td>
		<?php echo $this->album->id; ?>
	</td>
</tr>
<?php endif; ?>
<tr>
	<th><?php echo JText::_('Created'); ?>:</th>
	<td>
<?php
		if ( !$this->album->created )
			echo JText::_('New Album');
		else
			echo JHTML::_('date', $this->album->created,  JText::_('DATE_FORMAT_LC2'));
?>
	</td>
</tr>
<tr>
	<th><?php echo JText::_('Modified'); ?>:</th>
	<td>
<?php
		if ( !$this->album->modified )
			echo JText::_('Not modified');
		else
			echo JHTML::_('date', $this->album->modified, JText::_('DATE_FORMAT_LC2'));
?>
	</td>
</tr>
</table>
