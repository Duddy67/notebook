<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

//Note: More information about router here: https://docs.joomla.org/Supporting_SEF_URLs_in_your_component

/**
 * Build the route for the com_notebook component
 *
 * @param	array	An array of URL arguments
 *
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function NotebookBuildRoute(&$query)
{
  $segments = array();

  if(isset($query['view'])) {
    $segments[] = $query['view'];
    unset($query['view']);
  }

  if(isset($query['id'])) {
    $segments[] = $query['id'];
    unset($query['id']);
  }

  if(isset($query['layout'])) {
    unset($query['layout']);
  }

  unset($query['catid']);

  return $segments;
}


/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 */
function NotebookParseRoute($segments)
{
  $vars = array();

  switch($segments[0])
  {
    case 'categories':
	   $vars['view'] = 'categories';
	   break;
    case 'category':
	   $vars['view'] = 'category';
	   $id = explode(':', $segments[1]);
	   $vars['id'] = (int)$id[0];
	   break;
    case 'note':
	   $vars['view'] = 'note';
	   $id = explode(':', $segments[1]);
	   $vars['id'] = (int)$id[0];
	   break;
    case 'form':
	   $vars['view'] = 'form';
	   //Form layout is always set to 'edit'.
	   $vars['layout'] = 'edit';
	   break;
  }

  return $vars;
}

