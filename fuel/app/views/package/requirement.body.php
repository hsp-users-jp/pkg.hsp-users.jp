  <table class="table table-striped">
    <tr>
      <th>バージョン</th>
      <th class="text-center">動作環境</th>
      <th class="text-center">利用者報告</th>
    </tr>
<?php foreach ($hsp_categories as $hsp_category): ?>
	<tr id="hsp_category_<?php echo e($hsp_category->id); ?>">
		<td colspan="3" style="white-space: nowrap;"><span class="<?php echo e($hsp_category->icon); ?>"><span> <?php echo e($hsp_category->name); ?></td>
	</tr>
<?php foreach ($hsp_specifications[$hsp_category->id] as $hsp_specification): ?>
	<tr id="<?php echo sprintf('hsp_spec_%d_%d', $hsp_category->id, $hsp_specification->id) ?>">
		<td style="white-space: nowrap;">&nbsp;&nbsp;&nbsp; <?php echo e($hsp_specification->version); ?></td>
<?php for ($i = 0; $i < 2; ++$i): ?>
		<td class="text-center">
<?php
	if (!isset($package_supports[$hsp_category->id][$i][$hsp_specification->id]))
	{
		echo ' - ';
	}
	else
	{
		$package_support = & $package_supports[$hsp_category->id][$i][$hsp_specification->id];
		switch ($package_support->status)
		{
		case Model_Working_Report::StatusUnknown:
			echo ' -* ';
			break;
		case Model_Working_Report::StatusSupported:
			echo '<span class="label label-success"><span class="fa fa-check"><span></span>';
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
