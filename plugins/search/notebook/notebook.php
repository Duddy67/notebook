<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2019 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


class plgSearchNotebook extends JPlugin
{
  /**
   * Constructor
   *
   * @access      protected
   * @param       object  $subject The object to observe
   * @param       array   $config  An array that holds the plugin configuration
   * @since       1.6
   */
  public function __construct(& $subject, $config)
  {
    parent::__construct($subject, $config);
    $this->loadLanguage();
  }


  // Define a function to return an array of search areas. 
  // Note the value of the array key is normally a language string
  function onContentSearchAreas()
  {
    static $areas = array('notebook' => 'Notes');
    return $areas;
  }


  /**
   * Search content (articles).
   * The SQL must return the following fields that are used in a common display
   * routine: href, title, section, created, text, browsernav.
   *
   * @param   string  $text      Target search string.
   * @param   string  $phrase    Matching option (possible values: exact|any|all).  Default is "any".
   * @param   string  $ordering  Ordering option (possible values: newest|oldest|popular|alpha|category).  Default is "newest".
   * @param   mixed   $areas     An array if the search it to be restricted to areas or null to search all areas.
   *
   * @return  array  Search results.
   *
   * @since   1.6
   */
  public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
  {
    JLoader::register('NotebookHelperRoute', JPATH_SITE.'/components/com_notebook/helpers/route.php');

    $db = JFactory::getDbo();
    $app = JFactory::getApplication();
    $user = JFactory::getUser();
    $groups = implode(',', $user->getAuthorisedViewLevels());
    $tag = JFactory::getLanguage()->getTag();

    // If the array is not correct, return it:
    if(is_array($areas) && !array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
      return array();
    }

    // Retrieve the plugin parameters.
    $sContent  = $this->params->get('search_content', 1);
    $sArchived = $this->params->get('search_archived', 1);
    $limit     = $this->params->def('search_limit', 50);

    $nullDate = $db->getNullDate();
    $date = JFactory::getDate();
    $now = $date->toSql();

    // Use the PHP function trim to delete spaces in front of or at the back of the searching terms.
    $text = trim($text);

    // Return Array when nothing was filled in.
    if($text === '') {
      return array();
    }

    switch ($phrase) {
      case 'exact':
	      $text = $db->quote('%'.$db->escape($text, true).'%', false);
	      $wheres2   = array();
	      $wheres2[] = 'n.title LIKE '.$text;
	      $wheres2[] = 'n.intro_text LIKE '.$text;
	      $wheres2[] = 'n.full_text LIKE '.$text;

	      $relevance[] = ' CASE WHEN '.$wheres2[0].' THEN 5 ELSE 0 END ';
	      $where = '('.implode(') OR (', $wheres2).')';

	      break;

      case 'all':
      case 'any':
      default:
	      $words = explode(' ', $text);
	      $wheres = array();

	      foreach($words as $word) {
		$word = $db->quote('%'.$db->escape($word, true).'%', false);
		$wheres2 = array();
		$wheres2[] = 'LOWER(n.title) LIKE LOWER('.$word.')';
		$wheres2[] = 'LOWER(n.intro_text) LIKE LOWER('.$word.')';
		$wheres2[] = 'LOWER(n.full_text) LIKE LOWER('.$word.')';

		$relevance[] = ' CASE WHEN '.$wheres2[0].' THEN 5 ELSE 0 END ';

		$wheres[] = implode(' OR ', $wheres2);
	      }

	      $where = '('.implode(($phrase === 'all' ? ') AND (' : ') OR ('), $wheres).')';

	      break;

    }

    // Ordering of the results.
    switch($ordering)
    {
      case 'oldest':
	      $order = 'n.created ASC';
	      break;

      case 'popular':
	      $order = 'n.hits DESC';
	      break;

      case 'alpha':
	      $order = 'n.title ASC';
	      break;

      case 'category':
	      $order = 'c.title ASC, n.title ASC';
	      break;

      case 'newest':
      default:
	      $order = 'n.created DESC';
	      break;
    }

    $rows = array();
    $query = $db->getQuery(true);

    // Search notes.
    if($sContent && $limit > 0) {
      $query->clear();

      // SQLSRV changes.
      $case_when  = ' CASE WHEN ';
      $case_when .= $query->charLength('n.alias', '!=', '0');
      $case_when .= ' THEN ';
      $n_id = $query->castAsChar('n.id');
      $case_when .= $query->concatenate(array($n_id, 'n.alias'), ':');
      $case_when .= ' ELSE ';
      $case_when .= $n_id . ' END as slug';

      $case_when1  = ' CASE WHEN ';
      $case_when1 .= $query->charLength('c.alias', '!=', '0');
      $case_when1 .= ' THEN ';
      $c_id = $query->castAsChar('c.id');
      $case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
      $case_when1 .= ' ELSE ';
      $case_when1 .= $c_id . ' END as catslug';

      if(!empty($relevance)) {
	$query->select(implode(' + ', $relevance).' AS relevance');
	$order = ' relevance DESC, '.$order;
      }

      $query->select('n.title AS title, n.created AS created, n.language, n.catid')
	      ->select($query->concatenate(array('n.intro_text', 'n.full_text')) . ' AS text')
	      ->select('c.title AS section, ' . $case_when . ',' . $case_when1 . ', ' . '\'2\' AS browsernav')
	      ->from('#__notebook_note AS n')
	      ->join('INNER', '#__categories AS c ON c.id=n.catid')
	      ->where('('.$where . ') AND n.published=1 AND c.published = 1 AND n.access IN ('.$groups.') '
		      .'AND c.access IN ('.$groups.')'
		      .'AND (n.publish_up = '.$db->quote($nullDate).' OR n.publish_up <= '.$db->quote($now).') '
		      .'AND (n.publish_down = '.$db->quote($nullDate).' OR n.publish_down >= '.$db->quote($now).')')
	      ->group('n.id, n.title, n.created, n.language, n.catid, n.intro_text, n.full_text, c.title, n.alias, c.alias, c.id')
	      ->order($order);

      // Filter by language.
      if($app->isClient('site') && JLanguageMultilang::isEnabled()) {
	$query->where('n.language IN ('.$db->quote($tag).','.$db->quote('*').')')
	      ->where('c.language IN ('.$db->quote($tag).','.$db->quote('*').')');
      }

      $db->setQuery($query, 0, $limit);

      try {
	$rows = $db->loadObjectList();
      }
      catch(RuntimeException $e) {
	$rows = array();
	JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
      }

      foreach($rows as $key => $row) {
	$rows[$key]->href = NotebookHelperRoute::getNoteRoute($row->slug, $row->catid, $row->language);
      }
    }

    return $rows;
  }
}

