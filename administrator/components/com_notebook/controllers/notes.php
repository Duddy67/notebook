<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die('Restricted access'); // No direct access to this file.
 

class NotebookControllerNotes extends JControllerAdmin
{
  /**
   * Proxy for getModel.
   * @since 1.6
  */
  public function getModel($name = 'Note', $prefix = 'NotebookModel', $config = array('ignore_request' => true))
  {
    $model = parent::getModel($name, $prefix, $config);
    return $model;
  }
}



