<table class="dtmanager-custom-datatable" data-dtmanager_custom_datatable_id="<?php esc_attr_e($table_id); ?>" style="width:100%">
  <thead>
    <tr>
      <?php foreach ($columns as $column) : ?>
        <th data-dtmanager_custom_datatable_column_value="<?php esc_attr_e($column->value); ?>"><?php esc_html_e($column->label); ?></th>
      <?php endforeach ?>
    </tr>
  </thead>
</table>