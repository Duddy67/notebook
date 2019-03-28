<?php
/**
 * @package Note Book 
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); 
// Allows to keep the tab state identical in edit form after saving.
JHtml::_('behavior.tabstate');


// Checks against the user permissions.
if(!JFactory::getUser()->authorise('core.manage', 'com_notebook')) {
  JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
  return false;
}

// Registers the component helper file. It will be loaded automatically later as soon
// as the helper class is instantiate.
JLoader::register('NotebookHelper', JPATH_ADMINISTRATOR.'/components/com_notebook/helpers/notebook.php');

$controller = JControllerLegacy::getInstance('Notebook');

// Executes the requested task (set in the url).
// If no task is set then the "display' task will be executed.
$controller->execute(JFactory::getApplication()->input->get('task'));

$controller->redirect();

