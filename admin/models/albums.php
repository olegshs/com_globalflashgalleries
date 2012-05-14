<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.model');

class GlobalFlashGalleriesModelAlbums extends JModel
{
	/**
	 * Albums data array
	 *
	 * @var array
	 */
	var $_data;

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
		$query = "
			SELECT *
			FROM `#__globalflash_albums`
			ORDER BY `created` ASC
		";

		return $query;
	}

	/**
	 * Retrieves the Albums list
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
		if ( empty($this->_data) )
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}
}
