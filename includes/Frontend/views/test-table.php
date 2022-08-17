<table id="table_id" class="display">
  <thead>
    <tr>
      <th>Column 1</th>
      <th>Column 2</th>
    </tr>
  </thead>
  <tbody>
    <?php for ($i = 1; $i <= 100; $i++) : ?>
      <tr>
        <td>Row <?php esc_html_e($i) ?> Data 1</td>
        <td>Row <?php esc_html_e($i) ?> Data 2</td>
      </tr>
    <?php endfor; ?>
  </tbody>
</table>