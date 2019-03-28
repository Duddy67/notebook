<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access to this file.
defined('_JEXEC') or die('Restricted access'); 


class NotebookModelNotes extends JModelList
{
  /**
   * Constructor.
   *
   * @param   array  $config  An optional associative array of configuration settings.
   *
   * @see     \JModelLegacy
   * @since   1.6
   */
  public function __construct($config = array())
  {
    if(empty($config['filter_fields'])) {
      $config['filter_fields'] = array('id', 'n.id',
				       'title', 'n.title', 
				       'alias', 'n.alias',
				       'created', 'n.created', 
				       'created_by', 'n.created_by',
				       'published', 'n.published', 
			               'access', 'n.access', 'access_level',
				       'user', 'user_id',
				       'ordering', 'n.ordering', 'tm.ordering', 'tm_ordering',
				       'language', 'n.language',
				       'hits', 'n.hits',
				       'catid', 'n.catid', 'category_id',
				       'tag'
				      );
    }

    parent::__construct($config);
  }


  /**
   * Method to auto-populate the model state.
   *
   * This method should only be called once per instantiation and is designed
   * to be called on the first call to the getState() method unless the model
   * configuration flag to ignore the request is set.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @param   string  $ordering   An optional ordering field.
   * @param   string  $direction  An optional direction (asc|desc).
   *
   * @return  void
   *
   * @since   1.6
   */
  protected function populateState($ordering = null, $direction = null)
  {
    // Initialise variables.
    $app = JFactory::getApplication();
    $session = JFactory::getSession();

    // Adjust the context to support modal layouts.
    if($layout = JFactory::getApplication()->input->get('layout')) {
      $this->context .= '.'.$layout;
    }

    // Get the state values set by the user.
    $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    $access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access');
    $this->setState('filter.access', $access);

    $userId = $app->getUserStateFromRequest($this->context.'.filter.user_id', 'filter_user_id');
    $this->setState('filter.user_id', $userId);

    $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
    $this->setState('filter.published', $published);

    $categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
    $this->setState('filter.category_id', $categoryId);

    $language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language');
    $this->setState('filter.language', $language);

    $tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag');
    $this->setState('filter.tag', $tag);

    // List state information.
    parent::populateState('n.title', 'asc');

    // Force a language
    $forcedLanguage = $app->input->get('forcedLanguage');

    if(!empty($forcedLanguage)) {
      $this->setState('filter.language', $forcedLanguage);
      $this->setState('filter.forcedLanguage', $forcedLanguage);
    }
  }


  /**
   * Method to get a store id based on the model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param   string  $id  An identifier string to generate the store id.
   *
   * @return  string  A store id.
   *
   * @since   1.6
   */
  protected function getStoreId($id = '')
  {
    // Compile the store id.
    $id .= ':'.$this->getState('filter.search');
    $id .= ':'.$this->getState('filter.access');
    $id .= ':'.$this->getState('filter.published');
    $id .= ':'.$this->getState('filter.user_id');
    $id .= ':'.$this->getState('filter.category_id');
    $id .= ':'.$this->getState('filter.language');

    return parent::getStoreId($id);
  }


  /**
   * Method to get a \JDatabaseQuery object for retrieving the data set from a database.
   *
   * @return  \JDatabaseQuery  A \JDatabaseQuery object to retrieve the data set.
   *
   * @since   1.6
   */
  protected function getListQuery()
  {
    //Create a new JDatabaseQuery object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);
    $user = JFactory::getUser();

    // Select the required fields from the table.
    $query->select($this->getState('list.select', 'n.id,n.title,n.alias,n.created,n.published,n.catid,n.hits,'.
				   'n.access,n.ordering,n.created_by,n.checked_out,n.checked_out_time,n.language'))
	  ->from('#__notebook_note AS n');

    // Get the user name.
    $query->select('us.name AS user')
	  ->join('LEFT', '#__users AS us ON us.id = n.created_by');

    // Join over the users for the checked out user.
    $query->select('uc.name AS editor')
	  ->join('LEFT', '#__users AS uc ON uc.id=n.checked_out');

    // Join over the categories.
    $query->select('ca.title AS category_title')
	  ->join('LEFT', '#__categories AS ca ON ca.id = n.catid');

    // Join over the language
    $query->select('lg.title AS language_title')
	  ->join('LEFT', $db->quoteName('#__languages').' AS lg ON lg.lang_code = n.language');

    // Join over the asset groups.
    $query->select('al.title AS access_level')
	  ->join('LEFT', '#__viewlevels AS al ON al.id = n.access');

    // Filter by component category.
    $categoryId = $this->getState('filter.category_id');
    if(is_numeric($categoryId)) {
      $query->where('n.catid = '.(int)$categoryId);
    }
    elseif(is_array($categoryId)) {
      JArrayHelper::toInteger($categoryId);
      $categoryId = implode(',', $categoryId);
      $query->where('n.catid IN ('.$categoryId.')');
    }

    // Filter by title search.
    $search = $this->getState('filter.search');
    if(!empty($search)) {
      if(stripos($search, 'id:') === 0) {
	$query->where('n.id = '.(int) substr($search, 3));
      }
      else {
	$search = $db->Quote('%'.$db->escape($search, true).'%');
	$query->where('(n.title LIKE '.$search.')');
      }
    }

    // Filter by access level.
    if($access = $this->getState('filter.access')) {
      $query->where('n.access='.(int) $access);
    }

    // Filter by access level on categories.
    if(!$user->authorise('core.admin')) {
      $groups = implode(',', $user->getAuthorisedViewLevels());
      $query->where('n.access IN ('.$groups.')');
      $query->where('ca.access IN ('.$groups.')');
    }

    // Filter by publication state.
    $published = $this->getState('filter.published');
    if(is_numeric($published)) {
      $query->where('n.published='.(int)$published);
    }
    elseif($published === '') {
      $query->where('(n.published IN (0, 1))');
    }

    // Filter by user.
    $userId = $this->getState('filter.user_id');
    if(is_numeric($userId)) {
      $type = $this->getState('filter.user_id.include', true) ? '= ' : '<>';
      $query->where('n.created_by'.$type.(int) $userId);
    }

    // Filter by language.
    if($language = $this->getState('filter.language')) {
      $query->where('n.language = '.$db->quote($language));
    }

    // Filter by a single tag.
    $tagId = $this->getState('filter.tag');

    if(is_numeric($tagId)) {
      $query->where($db->quoteName('tagmap.tag_id').' = '.(int)$tagId)
	    ->join('LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap').
		   ' ON '.$db->quoteName('tagmap.content_item_id').' = '.$db->quoteName('n.id').
		   ' AND '.$db->quoteName('tagmap.type_alias').' = '.$db->quote('com_notebook.note'));
    }

    // Add the list to the sort.
    $orderCol = $this->state->get('list.ordering', 'n.title');
    $orderDirn = $this->state->get('list.direction'); // asc or desc

    $query->order($db->escape($orderCol.' '.$orderDirn));

    return $query;
  }
}

