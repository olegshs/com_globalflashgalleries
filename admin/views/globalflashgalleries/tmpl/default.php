<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

?>
<div class="com_globalflashgalleries">
<?php

$imagesURL = globalflash_adminURL.'/images';
$upgradeLink = 'index.php?option=com_globalflashgalleries&amp;view=checkupdates';

?>

<div class="component-upgrade">
	<a class="big button" href="<?php echo $upgradeLink; ?>"><?php echo JText::_('Check for Updates'); ?></a>
</div>

<div class="component-galleries">
	<a href="index.php?option=com_globalflashgalleries&amp;controller=galleries"><img src="<?php echo $imagesURL.'/galleries/Art.png' ?>" alt="Art" title="Art Gallery" /></a>
	<a href="index.php?option=com_globalflashgalleries&amp;controller=galleries"><img src="<?php echo $imagesURL.'/galleries/Box.png' ?>" alt="Box" title="Box Gallery" /></a>
	<a href="index.php?option=com_globalflashgalleries&amp;controller=galleries"><img src="<?php echo $imagesURL.'/galleries/Zen.png' ?>" alt="Zen" title="Zen Gallery" /></a>
	<a href="index.php?option=com_globalflashgalleries&amp;controller=galleries"><img src="<?php echo $imagesURL.'/galleries/StackPhoto.png' ?>" alt="StackPhoto" title="Stack Gallery" /></a>
	<a href="index.php?option=com_globalflashgalleries&amp;controller=galleries"><img src="<?php echo $imagesURL.'/galleries/Promo.png' ?>" alt="Promo" title="Promo Gallery" /></a>
	<a href="index.php?option=com_globalflashgalleries&amp;controller=galleries"><img src="<?php echo $imagesURL.'/galleries/PhotoFlow.png' ?>" alt="PhotoFlow" title="PhotoFlow Gallery" /></a>
	<a href="index.php?option=com_globalflashgalleries&amp;controller=galleries"><img src="<?php echo $imagesURL.'/galleries/3dWall.png' ?>" alt="3D Wall" title="3D Wall Gallery" /></a>
	<a href="index.php?option=com_globalflashgalleries&amp;controller=galleries"><img src="<?php echo $imagesURL.'/galleries/Line.png' ?>" alt="Line" title="Line Gallery" /></a>
</div>

<div class="copyright">
	<div><?php echo JText::_('Copyright 2010 Mediaparts Interactive. All rights reserved.'); ?></div>
	<?php /* ?><div class="gpl">
		Global Flash Galleries Component for Joomla! is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.
	</div><?php */ ?>
<?php if ($l10n = JText::_('com_globalflashgalleries.localization')) : ?>
	<div class="l10n"><?php echo $l10n; ?></div>
<?php endif; ?>
</div>

</div>
