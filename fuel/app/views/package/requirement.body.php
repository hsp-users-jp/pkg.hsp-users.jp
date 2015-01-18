<?php $status2disp = array(
		Model_Working_Report::StatusUnknown       => ' - ',
		Model_Working_Report::StatusSupported     => '<span class="label label-success"><span class="fa fa-check"><span></span>', 
		Model_Working_Report::StatusPartedSupport => '<span class="label label-warning"><span class="fa fa-asterisk"><span></span>',
		Model_Working_Report::StatusNotSupported  => '<span class="label label-danger"><span class="fa fa-close"><span></span>' );
      $def2val = array( Model_Working_Report::StatusUnknown => 0,
                        Model_Working_Report::StatusSupported => 1, 
                        Model_Working_Report::StatusPartedSupport => 2,
                        Model_Working_Report::StatusNotSupported => 3 );
?>
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
<?php echo Arr::get($status2disp, $package_supports[$hsp_category->id][$i]['summary'], ' - '); ?>
		</td>
<?php endfor; ?>
	</tr>
<?php foreach ($hsp_specifications[$hsp_category->id] as $hsp_specification): ?>
	<tr id="<?php echo sprintf('hsp_spec_%d_%d', $hsp_category->id, $hsp_specification->id) ?>">
		<td style="white-space: nowrap;">&nbsp;&nbsp;&nbsp; <?php echo e($hsp_specification->version); ?></td>
<?php for ($i = 0; $i < 2; ++$i): ?>
		<td class="text-center">
<?php $package_support_details = & $package_supports[$hsp_category->id][$i]['detail'];
      if (!$i && $is_author && $is_editable): ?>
			<a href="#" id="<?php echo sprintf('hsp_spec_req[%d][%d]',
			                                   $hsp_category->id, $hsp_specification->id) ?>"
			   data-type="radiolist" data-title="動作環境"
			   data-url="<?php echo Uri::create('/package/requirement/'.$package_revision_id); ?>" 
			   data-value="<?php echo isset($package_support_details[$hsp_specification->id])
			                          ? $def2val[$package_support_details[$hsp_specification->id]]
			                          : $def2val[Model_Working_Report::StatusUnknown]; ?>"></a>
		</td>
<?php else: ?>
<?php
	if (!isset($package_support_details[$hsp_specification->id]))
	{
		echo ' - ';
	}
	else
	{
		echo Arr::get($status2disp, $package_support_details[$hsp_specification->id], ' - ');
	}
?>
<?php endif ?>
<?php endfor; ?>
    </tr>
<?php endforeach; ?>
<?php endforeach; ?>
  </table>
