<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

?>

<div id="flashSettings" style="visibility:hidden;"><?php

foreach ($this->gallery->settingsInfo as $name => $param)
{
	$a[$name] = $param;
	$a[$name]->input = $this->ui->input( $param->input, $name, "settings[{$name}]", htmlspecialchars($this->gallery->settings[$name]) );
}

echo $this->tpl->parse( $this->settingsPath.$this->gallery->type, $a );

?></div>
