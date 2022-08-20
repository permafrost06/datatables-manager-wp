<?php
$column_vals = array_map(function ($column) {
  return $column->value;
}, $data['columns']);
?>
<table class="custom-datatable" data-custom_datatable_id="<?php esc_attr_e($data['table_id']); ?>" data-custom_datatable_columns="<?php esc_attr_e(implode(', ', $column_vals)); ?>" style="width:100%">
  <thead>
    <tr>
      <?php foreach ($data['columns'] as $column) : ?>
        <th><?php esc_html_e($column->label) ?></th>
      <?php endforeach ?>
    </tr>
  </thead>
</table>