<?php
/**
 * @package Note Book 
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die('Restricted access'); // No direct access.


class NotebookController extends JControllerLegacy
{
  public function display($cachable = false, $urlparams = false) 
  {
    //Display the submenu.
    NotebookHelper::addSubmenu($this->input->get('view', 'notes'));

    //Set the default view.
    $this->input->set('view', $this->input->get('view', 'notes'));

    //Display the view.
    parent::display();
  }
}


