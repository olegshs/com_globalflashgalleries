<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

?>
<div class="com_globalflashgalleries">

<div class="component-upgrade">
<?php if ($this->update): ?>
	<p><big><?php echo JText::_('There is a new version available.'); ?></big></p>

<?php
	if ( !empty($this->update->whatsnew) )
	{
		$whatsnew = $this->update->whatsnew;
?>
	<div class="whatsnew">
		<h2>What's new</h2>
		<ul>
<?php
		foreach ($whatsnew as $version => $features) {
?>
			<li>
				<h3><?php echo $version; ?></h3>
				<ul>
<?php
				foreach ($features as $feature) {
?>
					<li><?php echo htmlspecialchars($feature); ?></li>
<?php
				}
?>
				</ul>
			</li>
<?php
	}
?>
		</ul>
	</div>
<?php
	}
?>

	<form action="index.php?option=com_globalflashgalleries" method="post" style="margin:2em 0;">
		<input type="hidden" name="controller" value="component" />
		<input type="hidden" name="task" value="upgrade" />
		<input type="hidden" name="url" value="<?php echo htmlspecialchars($this->update->url); ?>" />
		<?php echo JHTML::_('form.token'); ?>

		<button class="big button"><?php printf(JText::_('Upgrade to version <strong>%s</strong>'), htmlspecialchars($this->update->version)); ?></button>
	</form>
<?php else: ?>
	<p><big><?php echo JText::_('You are using the latest version.'); ?></big></p>
<?php endif; ?>
</div>

</div>
