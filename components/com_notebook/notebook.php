<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); 

// Registers the component helper files. They will be loaded automatically later as soon
// as an helper class is instantiate.
JLoader::register('NotebookHelperRoute', JPATH_SITE.'/components/com_notebook/helpers/route.php');
JLoader::register('NotebookHelperQuery', JPATH_SITE.'/components/com_notebook/helpers/query.php');


$controller = JControllerLegacy::getInstance('Notebook');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();


