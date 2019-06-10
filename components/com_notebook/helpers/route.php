<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Note Book Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_notebook
 * @since       1.5
 */
abstract class NotebookHelperRoute
{
  /**
   * Get the note route.
   *
   * @param   integer  $id        The route of the note item.
   * @param   integer  $catid     The category ID.
   * @param   integer  $language  The language code.
   *
   * @return  string  The article route.
   *
   * @since   1.5
   */
  public static function getNoteRoute($id, $catid = 0, $language = 0)
  {
    // Create the link
    $link = 'index.php?option=com_notebook&view=note&id='.$id;

    if((int) $catid > 1) {
      $link .= '&catid='.$catid;
    }

    if($language && $language !== '*' && JLanguageMultilang::isEnabled()) {
      $link .= '&lang='.$language;
    }

    return $link;
  }


  /**
   * Get the category route.
   *
   * @param   integer  $catid     The category ID.
   * @param   integer  $language  The language code.
   *
   * @return  string  The note route.
   *
   * @since   1.5
   */
  public static function getCategoryRoute($catid, $language = 0)
  {
    if($catid instanceof JCategoryNode) {
      $id = $catid->id;
    }
    else {
      $id = (int) $catid;
    }

    if($id < 1) {
      $link = '';
    }
    else {
      $link = 'index.php?option=com_notebook&view=category&id='.$id;

      if($language && $language !== '*' && JLanguageMultilang::isEnabled()) {
	$link .= '&lang='.$language;
      }
    }

    return $link;
  }


  /**
   * Get the form route.
   *
   * @param   integer  $id  The form ID.
   *
   * @return  string  The note route.
   *
   * @since   1.5
   */
  public static function getFormRoute($id)
  {
    return 'index.php?option=com_notebook&task=note.edit&n_id='.(int)$id;
  }
}
