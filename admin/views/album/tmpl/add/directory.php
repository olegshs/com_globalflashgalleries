<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

?>
<div class="name">
	<label for="directory-name"><?php echo JURI::root(); ?></label>
	<input type="text" class="text" id="directory-name" name="import_directory" value="tmp" />
</div>
<div class="notdelete">
	<label>
		<input type="checkbox" name="import_delete" value="1" />
		<span>Delete files after import</span>
	</label>
</div>

<?php echo JHTML::_('form.token'); ?>

<div class="submit">
	<input type="submit" class="big button" name="add_directory" value="<?php echo JText::_('Import'); ?>" />
</div>
