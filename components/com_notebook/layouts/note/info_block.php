<?php
/**
 * @package Note Book
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('JPATH_BASE') or die('Restricted access');
?>

<dl class="article-info muted">

  <?php if($displayData['params']->get('show_author') && !empty($displayData['item']->author )) : ?>
    <dd class="createdby" itemprop="author" itemscope itemtype="http://schema.org/Person">
	<?php $author = '<span itemprop="name">'.$displayData['item']->author.'</span>'; ?>
	<?php echo JText::sprintf('COM_NOTEBOOK_WRITTEN_BY', $author); ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_parent_category') && !empty($displayData['item']->parent_slug)) : ?>
    <dd class="parent-category-name">
      <?php $title = $this->escape($displayData['item']->parent_title); ?>
      <?php if($displayData['params']->get('link_parent_category')) : ?>
	<?php $url = '<a href="' . JRoute::_(NotebookHelperRoute::getCategoryRoute($displayData['item']->parent_slug)).'" itemprop="genre">'.$title.'</a>'; ?>
	<?php echo JText::sprintf('COM_NOTEBOOK_PARENT', $url); ?>
      <?php else : ?>
	<?php echo JText::sprintf('COM_NOTEBOOK_PARENT', '<span itemprop="genre">'.$title.'</span>'); ?>
    <?php endif; ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_category')) : ?>
    <dd class="category-name">
      <?php $title = $this->escape($displayData['item']->category_title); ?>
      <?php if ($displayData['params']->get('link_category') && $displayData['item']->catslug) : ?>
	<?php $url = '<a href="'.JRoute::_(NotebookHelperRoute::getCategoryRoute($displayData['item']->catslug)).'" itemprop="genre">'.$title.'</a>'; ?>
	<?php echo JText::sprintf('COM_NOTEBOOK_CATEGORY', $url); ?>
      <?php else : ?>
	<?php echo JText::sprintf('COM_NOTEBOOK_CATEGORY', '<span itemprop="genre">'.$title.'</span>'); ?>
      <?php endif; ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_publish_date')) : ?>
    <dd class="published">
      <span class="icon-calendar"></span>
      <time datetime="<?php echo JHtml::_('date', $displayData['item']->publish_up, 'c'); ?>" itemprop="datePublished">
	<?php echo JText::sprintf('COM_NOTEBOOK_PUBLISHED_DATE_ON', JHtml::_('date', $displayData['item']->publish_up, JText::_('DATE_FORMAT_LC3'))); ?>
      </time>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_create_date')) : ?>
      <dd class="create">
	<span class="icon-calendar"></span>
	<time datetime="<?php echo JHtml::_('date', $displayData['item']->created, 'c'); ?>" itemprop="dateCreated">
	  <?php echo JText::sprintf('COM_NOTEBOOK_CREATED_DATE_ON', JHtml::_('date', $displayData['item']->created, JText::_('DATE_FORMAT_LC3'))); ?>
	</time>
	</dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_modify_date') && (int)$displayData['item']->modified != 0) : ?>
    <dd class="modified">
      <span class="icon-calendar"></span>
      <time datetime="<?php echo JHtml::_('date', $displayData['item']->modified, 'c'); ?>" itemprop="dateModified">
	<?php echo JText::sprintf('COM_NOTEBOOK_LAST_UPDATED', JHtml::_('date', $displayData['item']->modified, JText::_('DATE_FORMAT_LC3'))); ?>
      </time>
      </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_hits')) : ?>
    <dd class="hits">
    <span class="icon-eye-open"></span>
    <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $displayData['item']->hits; ?>" />
      <?php echo JText::sprintf('COM_NOTEBOOK_NOTE_HITS', $displayData['item']->hits); ?>
    </dd>
  <?php endif; ?>
</dl>

