<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Note Book Component Category Tree
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_notebook
 * @since       1.6
 */
class NotebookCategories extends JCategories
{
  public function __construct($options = array())
  {
    $options['table'] = '#__notebook_note';
    $options['extension'] = 'com_notebook';

    /* IMPORTANT: By default publish parent function invoke a field called "state" to
     *            publish/unpublish (but also archived, trashed etc...) an item.
     *            Since our field is called "published" we must informed the 
     *            JCategories publish function in setting the "statefield" index of the 
     *            options array
    */
    $options['statefield'] = 'published';

    parent::__construct($options);
  }
}
