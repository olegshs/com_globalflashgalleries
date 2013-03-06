<?php
/**
 * @copyright   Copyright (c) 2010-2013 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

class GlobalFlashGalleriesModelGalleries extends JModelLegacy
{
	/**
	 * Galleries data array
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
			FROM `#__globalflash_galleries`
			ORDER BY `created` ASC
		";

		return $query;
	}

	/**
	 * Retrieves the Galleries data
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
		// Lets load the data if it doesn't already exist
		if ( empty($this->_data) )
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}
}
