<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;

$weekdays = $this->weekNames;
?>
<div class="base-calendar">
	<table class="calendar">
		<thead>
			<tr class="month-year-row">				
				<th colspan="7"><?php echo $this->monthName.', '.$this->year; ?></th>				
			</tr>
			<tr class="weekdays-row">
				<?php foreach ($weekdays as $weekDay): ?>
					<th><?php echo $weekDay; ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>			
			<?php echo $this->getTableBody(); ?>			
		</tbody>
	</table>
</div>