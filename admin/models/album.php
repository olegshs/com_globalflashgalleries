<?php
/**
 * @copyright   Copyright (c) 2010-2012 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');	// No direct access

jimport('joomla.application.component.model');

class GlobalFlashGalleriesModelAlbum extends JModelLegacy
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
	 * Method to set the Album identifier
	 *
	 * @access	public
	 * @param	int Album identifier
	 * @return	void
	 */
	function setId( $id )
	{
		// Set id and wipe data
		$this->_id = $id;
		$this->_data = null;
	}

	/**
	 * Method to get a Album
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if ( empty($this->_data) )
		{
			$query = "
				SELECT *
				FROM `#__globalflash_albums`
				WHERE `id` = {$this->_id}
			";
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		if ( !$this->_data )
		{
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data = (object)array(
				'id' => 0,
				'title' => null,
				'description' => null,
				'created' => null,
				'created_by' => null,
				'modified' => null,
				'modified_by' => null,
				'order' => null
			);
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

		$data['modified'] = $now = date('Y-m-d H:i:s');
		if ( $data['id'] < 1 )
			$data['created'] = $now;

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
		$cids = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cids);

		if ( count($cids) )
		{
			$row =& $this->getTable();
			foreach ($cids as $cid)
			{
				$this->setId($cid);
				$this->getData();
				$items =& $this->getItems();

				if ( $row->delete($cid) )
				{
					$this->_db->setQuery("
						DELETE FROM `#__globalflash_images`
						WHERE
							`album_id` = {$this->_id} AND
							`gallery_id` = 0
					");
					$this->_db->query();

					foreach ($items as $item)
					{
						$this->_db->setQuery("
							SELECT COUNT(*)
							FROM `#__globalflash_images`
							WHERE `path` = '{$item->path}'
						");
						$copies = $this->_db->loadResult();

						if ( $copies !== null && $copies == 0 )
							unlink( globalflash_imagesDir.DS.$item->path );
					}
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

	/**
	 * Retrieves the Images list
	 * @return array Array of objects containing the data from the database
	 */
	function &getItems()
	{
		// Load the data
		if ( empty($this->_items) )
		{
			$query = "
				SELECT *
				FROM `#__globalflash_images`
				WHERE
					`album_id` = {$this->_id} AND
					`gallery_id` = 0
				ORDER BY `order`
			";
			$this->_items = $this->_getList($query);
		}

		return $this->_items;
	}

	function arrangeItems( $order )
	{
		if ( is_array($order) )
		{
			$ids = "'". implode("', '", $order). "'";

			$this->_db->setQuery("
				SELECT `id`, `order`
				FROM `#__globalflash_images`
				WHERE `id` IN ( {$ids} )
				ORDER BY `id`
			");
			$items = $this->_db->loadObjectList();

			if ($items)
			{
				$this->_db->setQuery("
					SELECT MIN(`order`)
					FROM `#__globalflash_images`
					WHERE `id` IN ( {$ids} )
				");
				$minOrder = (int)$this->_db->loadResult();

				$result = array();
				foreach ($items as $item)
				{
					$item->order = array_search($item->id, $order) + $minOrder;
					$this->_db->updateObject('#__globalflash_images', $item, 'id');
					$result[$item->id] = $item->order;
				}

				return $result;
			}
		}
		else if ( is_string($order) )
		{
			switch ( strtolower($order) )
			{
				case 'desc':
					$order = 'DESC';
					break;
				default:
					$order = 'ASC';
			}

			$this->_db->setQuery("
				SELECT `id`
				FROM `#__globalflash_images`
				WHERE `gallery_id` = '{$this->_id}'
				ORDER BY `title` {$order}, `name` {$order}
			");
			$items = $this->_db->loadObjectList();

			if ($items)
			{
				$result = array();

				$n = 0;
				foreach ($items as $item)
				{
					$item->order = $n++;
					$this->_db->updateObject('#__globalflash_images', $item, 'id');
					$result[$item->id] = $item->order;
				}

				return $result;
			}
		}
		return false;
	}

}
