<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="search-results<?php echo $this->pageclass_sfx; ?>">
<?php foreach ($this->results as $result) : ?>
	<div class="result-item">
	<div class="result-title">

		<?php if ($result->href) : ?>
			<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) : ?> target="_blank"<?php endif; ?>>
				<?php // $result->title should not be escaped in this case, as it may ?>
				<?php // contain span HTML tags wrapping the searched terms, if present ?>
				<?php // in the title. ?>
				<?php echo $result->title; ?>
			</a>
		<?php else : ?>
			<?php // see above comment: do not escape $result->title ?>
			<?php echo $result->title; ?>
		<?php endif; ?>
		<?php if ($result->section) : ?>
		<div class="result-category">
			<span class="small<?php echo $this->pageclass_sfx; ?>">
				<?php echo $this->escape($result->section); ?>
			</span>
		</div>
	<?php endif; ?>
	</div>

	<div class="result-text">
		<?php echo $result->text; ?>
	</div>
	<?php if ($this->params->get('show_date')) : ?>
		<div class="result-created<?php echo $this->pageclass_sfx; ?>">
			<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?>
		</div>
	<?php endif; ?>
	</div>
<?php endforeach; ?>
</div>
<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
