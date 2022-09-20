jQuery(document).ready(function () {
  jQuery(".dtmanager-custom-datatable").each((idx, elem) => {
    const el = elem;
    elem = jQuery(elem);
    const table_id = elem.data("dtmanager_custom_datatable_id");

    const columns = [];
    elem.find("th").each((idx, thEl) => {
      const column_value = jQuery(thEl).data(
        "dtmanager_custom_datatable_column_value"
      );
      columns.push({ data: column_value });
    });

    elem.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: customDatatableAjax.ajax_url,
        data: function (d) {
          d.action = "dtm_get_datatable_rows";
          d._ajax_nonce = customDatatableAjax.nonce;
          d.table_id = table_id;
        },
      },
      columns,
    });
  });
});
