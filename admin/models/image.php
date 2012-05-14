<?php
/**
 * @copyright   Copyright (c) 2010 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.model');

class GlobalFlashGalleriesModelImage extends JModel
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid', 0, '', 'array');
		$this->setId( (int)$array[0] );
	}

	/**
	 * Method to set the Image identifier
	 *
	 * @access	public
	 * @param	int Image identifier
	 * @return	void
	 */
	function setId( $id )
	{
		// Set id and wipe data
		$this->_id = $id;
		$this->_data = null;
	}

	/**
	 * Method to get an Image
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if ( empty($this->_data) )
		{
			$query = "
				SELECT *
				FROM `#__globalflash_images`
				WHERE `id` = {$this->_id}
			";
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		if ( !$this->_data )
		{
			$this->_data = new stdClass();
			$this->_data->id = 0;
		}

		return $this->_data;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	TRUE on success
	 */
	function store()
	{
		$data = JRequest::get('post');

		$row =& $this->getTable();

		// Bind the form fields to the Galleries table
		if ( !$row->bind($data) )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		// Make sure the Gallery record is valid
		if ( !$row->check() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		// Store the web link table to the database
		if ( !$row->store() )
		{
			$this->setError( $row->getErrorMsg() );
			return false;
		}

		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	TRUE on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(), 'default', 'array' );
		JArrayHelper::toInteger($cids);

		if ( count($cids) )
		{
			$row =& $this->getTable();

			foreach ($cids as $cid)
			{
				$row->load($cid);
				if ( $row->delete($cid) )
				{
					$this->_db->setQuery("
						SELECT COUNT(*)
						FROM `#__globalflash_images`
						WHERE `path` = '{$row->path}'
					");
					$copies = $this->_db->loadResult();

					if ( $copies !== null && $copies == 0 )
						unlink( globalflash_imagesDir.DS.$row->path );
				}
				else
				{
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}

}
