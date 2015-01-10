  <table class="table table-striped">
    <tr>
      <th>バージョン</th>
      <th class="text-center">動作環境</th>
      <th class="text-center">利用者報告</th>
    </tr>
<?php foreach ($hsp_categories as $hsp_category): ?>
	<tr id="hsp_category_<?php echo e($hsp_category->id); ?>">
		<td style="white-space: nowrap;" data-dropdown="true"><span class="<?php echo e($hsp_category->icon); ?>"><span> <?php echo e($hsp_category->name); ?></td>
<?php for ($i = 0; $i < 2; ++$i): ?>
		<td class="text-center">
<?php
	switch ($package_supports[$hsp_category->id][$i]['summary'])
	{
	case Model_Working_Report::StatusUnknown:
		echo ' - ';
		break;
	case Model_Working_Report::StatusSupported:
		echo '<span class="label label-success"><span class="fa fa-check"><span></span>';
		break;
	case Model_Working_Report::StatusPartedSupport:
		echo '<span class="label label-warning"><span class="fa fa-asterisk"><span></span>';
		break;
	case Model_Working_Report::StatusNotSupported:
		echo '<span class="label label-danger"><span class="fa fa-close"><span></span>';
		break;
	}
?>
		</td>
<?php endfor; ?>
	</tr>
<?php foreach ($hsp_specifications[$hsp_category->id] as $hsp_specification): ?>
	<tr id="<?php echo sprintf('hsp_spec_%d_%d', $hsp_category->id, $hsp_specification->id) ?>">
		<td style="white-space: nowrap;">&nbsp;&nbsp;&nbsp; <?php echo e($hsp_specification->version); ?></td>
<?php for ($i = 0; $i < 2; ++$i): ?>
		<td class="text-center">
<?php
	$package_support_details = & $package_supports[$hsp_category->id][$i]['detail'];
	if (!isset($package_support_details[$hsp_specification->id]))
	{
		echo ' - ';
	}
	else
	{
		switch ($package_support_details[$hsp_specification->id])
		{
		case Model_Working_Report::StatusUnknown:
			echo ' - ';
			break;
		case Model_Working_Report::StatusSupported:
			echo '<span class="label label-success"><span class="fa fa-check"><span></span>';
			break;
		case Model_Working_Report::StatusNotSupported:
			echo '<span class="label label-danger"><span class="fa fa-close"><span></span>';
			break;
		}
	}
?>
		</td>
<?php endfor; ?>
    </tr>
<?php endforeach; ?>
<?php endforeach; ?>
  </table>
