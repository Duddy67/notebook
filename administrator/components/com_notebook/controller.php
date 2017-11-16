<?php
/**
 * @package Note Book 
 * @copyright Copyright (c) 2016 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */


defined('_JEXEC') or die; // No direct access.

jimport('joomla.application.component.controller');


class NotebookController extends JControllerLegacy
{
  public function display($cachable = false, $urlparams = false) 
  {
    require_once JPATH_COMPONENT.'/helpers/notebook.php';

    //Display the submenu.
    NotebookHelper::addSubmenu($this->input->get('view', 'notes'));

    //Set the default view.
    $this->input->set('view', $this->input->get('view', 'notes'));

    //Display the view.
    parent::display();
  }
}


