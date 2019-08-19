<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access'); 


class NotebookViewNotes extends JViewLegacy
{
  protected $items;
  protected $state;
  protected $pagination;


  /**
   * Execute and display a template script.
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  mixed  A string if successful, otherwise an Error object.
   *
   * @see     \JViewLegacy::loadTemplate()
   * @since   3.0
   */
  public function display($tpl = null)
  {
    $this->items = $this->get('Items');
    $this->state = $this->get('State');
    $this->pagination = $this->get('Pagination');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    // Checks for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    $this->addToolBar();
    $this->setDocument();
    $this->sidebar = JHtmlSidebar::render();

    // Displays the template.
    parent::display($tpl);
  }


  /**
   * Add the page title and toolbar.
   *
   * @return  void
   *
   * @since   1.6
   */
  protected function addToolBar() 
  {
    // Displays the view title and the icon.
    JToolBarHelper::title(JText::_('COM_NOTEBOOK_NOTES_TITLE'), 'stack');

    // Gets the allowed actions list
    $canDo = NotebookHelper::getActions();
    $user = JFactory::getUser();

    // The user is allowed to create or is able to create in one of the component categories.
    if($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_notebook', 'core.create'))) > 0) {
      JToolBarHelper::addNew('note.add', 'JTOOLBAR_NEW');
    }

    if($canDo->get('core.edit') || $canDo->get('core.edit.own') || 
       (count($user->getAuthorisedCategories('com_notebook', 'core.edit'))) > 0 || 
       (count($user->getAuthorisedCategories('com_notebook', 'core.edit.own'))) > 0) {
      JToolBarHelper::editList('note.edit', 'JTOOLBAR_EDIT');
    }

    if($canDo->get('core.edit.state')) {
      JToolBarHelper::divider();
      JToolBarHelper::custom('notes.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
      JToolBarHelper::custom('notes.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::divider();
      JToolBarHelper::archiveList('notes.archive','JTOOLBAR_ARCHIVE');
      JToolBarHelper::custom('notes.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
      JToolBarHelper::trash('notes.trash','JTOOLBAR_TRASH');
    }

    // Add a batch button
    if($user->authorise('core.create', 'com_notebook') && $user->authorise('core.edit', 'com_notebook')
       && $user->authorise('core.edit.state', 'com_notebook')) {
      $title = JText::_('JTOOLBAR_BATCH');

      // Instantiate a new JLayoutFile instance and render the batch button
      $layout = new JLayoutFile('joomla.toolbar.batch');

      $dhtml = $layout->render(array('title' => $title));
      JToolbar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'batch');
    }

    // Checks for delete permission.
    if($canDo->get('core.delete') || count($user->getAuthorisedCategories('com_notebook', 'core.delete'))) {
      JToolBarHelper::divider();
      JToolBarHelper::deleteList('', 'notes.delete', 'JTOOLBAR_DELETE');
    }

    if($canDo->get('core.admin')) {
      JToolBarHelper::divider();
      JToolBarHelper::preferences('com_notebook', 550);
    }
  }


  /**
   * Includes possible css and Javascript files.
   *
   * @return  void
   */
  protected function setDocument() 
  {
    $doc = JFactory::getDocument();
    $doc->addStyleSheet(JURI::base().'components/com_notebook/notebook.css');
  }
}


