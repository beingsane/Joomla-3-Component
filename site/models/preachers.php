<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |                                                                 
                                                        |_| 				
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		@update number 17 of this MVC
	@build			11th April, 2016
	@created		5th November, 2015
	@package		Sermon Distributor
	@subpackage		preachers.php
	@author			Llewellyn van der Merwe <https://www.vdm.io/>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	A sermon distributor that links to Dropbox. 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * Sermondistributor Model for Preachers
 */
class SermondistributorModelPreachers extends JModelList
{
	/**
	 * Model user data.
	 *
	 * @var        strings
	 */
	protected $user;
	protected $userId;
	protected $guest;
	protected $groups;
	protected $levels;
	protected $app;
	protected $input;
	protected $uikitComp;

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$this->user		= JFactory::getUser();
		$this->userId		= $this->user->get('id');
		$this->guest		= $this->user->get('guest');
                $this->groups		= $this->user->get('groups');
                $this->authorisedGroups	= $this->user->getAuthorisedGroups();
		$this->levels		= $this->user->getAuthorisedViewLevels();
		$this->app		= JFactory::getApplication();
		$this->input		= $this->app->input;
		$this->initSet		= true; 
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__sermondistributor_preacher as a
		$query->select($db->quoteName(
			array('a.id','a.asset_id','a.name','a.alias','a.icon','a.email','a.website','a.description','a.hits','a.ordering'),
			array('id','asset_id','name','alias','icon','email','website','description','hits','ordering')));
		$query->from($db->quoteName('#__sermondistributor_preacher', 'a'));
		$query->where('a.access IN (' . implode(',', $this->levels) . ')');
		$query->where('a.published = 1');
		$query->order('a.ordering ASC');

		// return the query object
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$user = JFactory::getUser();
		// check if this user has permission to access item
		if (!$user->authorise('site.preachers.access', 'com_sermondistributor'))
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_SERMONDISTRIBUTOR_NOT_AUTHORISED_TO_VIEW_PREACHERS'), 'error');
			// redirect away to the home page if no access allowed.
			$app->redirect(JURI::root());
			return false;
		}  
		// load parent items
		$items = parent::getItems();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_sermondistributor', true);

		// Convert the parameter fields into objects.
		if (SermondistributorHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Make sure the content prepare plugins fire on description.
				$item->description = JHtml::_('content.prepare',$item->description);
				// Checking if description has uikit components that must be loaded.
				$this->uikitComp = SermondistributorHelper::getUikitComp($item->description,$this->uikitComp);
				// set idPreacherSermonB to the $item object.
				$item->idPreacherSermonB = $this->getIdPreacherSermonDcaa_B($item->id);
			}
		} 


		if (SermondistributorHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				if (!SermondistributorHelper::checkArray($item->idPreacherSermonB))
				{
					// remove empty preacher
					unset($items[$nr]);
				}
			}
		}

		// return items
		return $items;
	} 

	/**
	* Method to get an array of Sermon Objects.
	*
	* @return mixed  An array of Sermon Objects on success, false on failure.
	*
	*/
	public function getIdPreacherSermonDcaa_B($id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__sermondistributor_sermon as b
		$query->select($db->quoteName(
			array('b.id'),
			array('id')));
		$query->from($db->quoteName('#__sermondistributor_sermon', 'b'));
		$query->where('b.preacher = ' . $db->quote($id));
		$query->where('b.access IN (' . implode(',', $this->levels) . ')');
		$query->where('b.published = 1');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();

		// check if there was data returned
		if ($db->getNumRows())
		{
			return $db->loadObjectList();
		}
		return false;
	}


	/**
	* Get the uikit needed components
	*
	* @return mixed  An array of objects on success.
	*
	*/
	public function getUikitComp()
	{
		if (isset($this->uikitComp) && SermondistributorHelper::checkArray($this->uikitComp))
		{
			return $this->uikitComp;
		}
		return false;
	}  
}
