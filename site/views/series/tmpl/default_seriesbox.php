<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |                                                                 
                                                        |_| 				
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		@update number 29 of this MVC
	@build			21st November, 2016
	@created		10th November, 2015
	@package		Sermon Distributor
	@subpackage		default_seriesbox.php
	@author			Llewellyn van der Merwe <https://www.vdm.io/>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	A sermon distributor that links to Dropbox. 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access'); 

// build the list class
$style = $this->params->get('series_list_style');
switch ($style)
{
	case 1:
		// Lines
		$listClass = ' uk-list-line';
	break;
	case 2:
		// Striped
		$listClass = ' uk-list-striped';
	break;
	case 3:
		// Spaced
		$listClass = ' uk-list-space';
	break;
	default:
		// Plain
		$listClass = '';
	break;
}

// build the contrast text option
$contrast = $this->params->get('series_box_contrast');
$contrastClass = '';
if ($contrast)
{
	$contrastClass = 'uk-text-contrast';
}

// set Panal Size
$mediumMain = '1-4';
if (($this->params->get('series_sermon_count') || $this->params->get('series_sermons_download_counter'))
	&& ($this->params->get('series_desc') && $this->series->description))
{
	$mediumDesc = '2-4';
	$mediumMid = '1-4';
}
elseif (($this->params->get('series_sermon_count') || $this->params->get('series_sermons_download_counter')) 
	|| ($this->params->get('series_desc') && $this->series->description))
{
	$mediumDesc = '3-4';
	$mediumMid = '3-4';
}
elseif (!$this->params->get('series_icon'))
{
	$mediumMain = '1-1';
}

?>
<div class="uk-block uk-block-primary">
	<div class="uk-container">
	<h1 class="<?php echo $contrastClass; ?>"><?php echo $this->series->name; ?></h1>
	<div class="uk-grid" data-uk-grid-margin="{target:'.uk-panel'}">
	<div class="uk-width-medium-<?php echo $mediumMain; ?>">
		<div class="uk-panel">
		<?php if ($this->params->get('series_hits')): ?>
			<?php
				$hits_state	= ($this->series->hits > 0) ? true:false;
				$badge_class	= ($hits_state) ? 'uk-badge-success':'uk-badge-warning';
				$badge_icon	= ($hits_state) ? 'check-circle':'minus-circle';
			?>
			<div class="uk-panel-badge uk-badge <?php echo $badge_class; ?>">
				<i class="uk-icon-<?php echo $badge_icon; ?>"></i>
				<?php echo JText::_('COM_SERMONDISTRIBUTOR_HITS'); ?>: <?php echo $this->series->hits; ?>
			</div>
		<?php endif ;?>
		<?php if ($this->params->get('series_icon')): ?>
			<?php $this->series->icon = ($this->series->icon) ? $this->series->icon : $this->params->get('series_default_icon'); ?>
			<?php if ($this->series->icon): ?>
				<img class="uk-thumbnail uk-thumbnail-expand" src="<?php echo $this->series->icon; ?>" alt="<?php echo $this->series->name; ?>">
			<?php endif; ?>
		<?php endif; ?>
		</div>
	</div>
	<?php if ($this->params->get('series_sermon_count') || ($this->params->get('series_sermons_download_counter'))): ?>
		<div class="uk-width-medium-<?php echo $mediumMid; ?>">
			<div class="uk-panel">
				<ul class="uk-list<?php echo $listClass; ?>">
					<?php if ($this->params->get('series_sermon_count')): ?>
						<li class="<?php echo $contrastClass; ?>"><?php echo JText::_('COM_SERMONDISTRIBUTOR_SERMON_COUNT'); ?>: <?php echo $this->sermonTotal; ?></li>
					<?php endif; ?>
					<?php if ($this->params->get('series_sermons_download_counter')): ?>
						<li class="<?php echo $contrastClass; ?>"><?php echo JText::_('COM_SERMONDISTRIBUTOR_TOTAL_DOWNLOADS'); ?>: <?php echo $this->downloadTotal; ?></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
	<?php if ($this->params->get('series_desc') && $this->series->description): ?>
		<div class="uk-width-medium-<?php echo $mediumDesc; ?>">
			<div class="uk-panel <?php echo $contrastClass; ?>">
				<?php echo $this->series->description; ?>
			</div>
		</div>
	<?php endif; ?>
	</div>
	</div>
</div>
