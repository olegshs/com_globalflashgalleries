<?php
/**
 * @copyright   Copyright (c) 2010-2014 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

if (method_exists('JUtility', 'getToken'))
	$token = JUtility::getToken();
else
	$token = JSession::getFormToken();

?>
<div class="com_globalflashgalleries">

<div class="component-upgrade">

<div id="upgrade-status"></div>

<script type="text/javascript">
document.body.style.cursor = 'wait';

jQuery(document).ready(function($) {

	function globalflash_upgrade_finish() {
		document.body.style.cursor = '';
	}

	$('#upgrade-status').append('<p><?php echo JText::_("Downloading update from"); ?> <code><?php echo $this->update->url; ?></code></p>');
	$.ajax({
		type: 'post',
		url: 'index.php?format=raw',
		dataType: 'json',
		data: {
			option: 'com_globalflashgalleries',
			controller: 'component',
			task: 'download',
			url: '<?php echo addslashes($this->update->url); ?>',
			'<?php echo $token; ?>': '1'
		},
		success: function(data) {
			if (data.status == 'ok' && data.packagefile && data.extractdir) {
				var
					packagefile = data.packagefile,
					extractdir = data.extractdir;

<?php if (globalflash_joomla15): ?>
				$('#upgrade-status').append('<p><?php echo JText::_("Removing the previous version..."); ?></p>');
				$.ajax({
					type: 'post',
					url: 'index.php?format=raw',
					dataType: 'json',
					data: {
						option: 'com_globalflashgalleries',
						controller: 'component',
						task: 'remove',
						'<?php echo $token; ?>': '1'
					},
					success: function(data) {
						if (data.status == 'ok') {
<?php endif; ?>
							$('#upgrade-status').append('<p><?php echo JText::_("Installing the component..."); ?></p>');
							$.ajax({
								type: 'post',
								url: 'index.php?tmpl=component',
								data: {
									option: 'com_installer',
									task: '<?php echo globalflash_joomla15 ? 'doInstall' : 'install.install'; ?>',
									installtype: 'folder',
									install_directory: extractdir,
									'<?php echo $token; ?>': '1'
								},
								success: function(data) {
									if ( !data.match(/<dt class="error">/) ) {
										$.ajax({
											type: 'post',
											url: 'index.php?format=raw',
											data: {
												option: 'com_globalflashgalleries',
												controller: 'component',
												task: 'cleanupInstall',
												packagefile: packagefile,
												extractdir: extractdir,
												'<?php echo $token; ?>': '1'
											},
											success: function() {
												$('#upgrade-status').append('<p><big><?php echo JText::_("Done."); ?></big></p>');
												setTimeout(function() { location.href='index.php?option=com_globalflashgalleries'; }, 5000);
												globalflash_upgrade_finish();
											}
										});
									} else {
										$('#upgrade-status').append('<div class="error"><?php echo JText::_("Install component error."); ?></div>');
										globalflash_upgrade_finish();
									}
								}
							});
<?php if (globalflash_joomla15): ?>
						} else {
							$('#upgrade-status').append('<div class="error"><?php echo JText::_("Unable to remove the component."); ?></div>');
							globalflash_upgrade_finish();
						}
					}
				});
<?php endif; ?>
			} else {
				$('#upgrade-status').append('<div class="error"><?php echo JText::_("Unable to download the update."); ?></div>');
				globalflash_upgrade_finish();
			}
		}
	});
});
</script>
</div>

</div>
