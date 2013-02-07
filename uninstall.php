<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

include_once dirname(__FILE__).DS.'defines.php';

if ( is_dir(globalflash_imagesDir) )
	JFolder::delete(globalflash_imagesDir);

if ( is_dir(globalflash_tmpDir) )
	JFolder::delete(globalflash_tmpDir);
