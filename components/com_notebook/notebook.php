<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die('Restricted access'); // No direct access.

require_once JPATH_COMPONENT.'/helpers/route.php';
require_once JPATH_COMPONENT.'/helpers/query.php';


$controller = JControllerLegacy::getInstance('Notebook');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();


