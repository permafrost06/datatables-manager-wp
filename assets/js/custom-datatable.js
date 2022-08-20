// jQuery(document).ready(function () {
//   jQuery(".custom-datatable").DataTable({
//     ordering: false,
//     searching: false,
//     processing: true,
//     serverSide: true,
//     ajax: {
//       url: customDatatableAjax.ajax_url,
//       data: function (d) {
//         d.action = "dtm_get_datatable_rows";
//         d._ajax_nonce = customDatatableAjax.nonce;
//         d.table_id = "3";
//       },
//     },
//     columns: [
//       { data: "name" },
//       { data: "email" },
//       { data: "phone" },
//       { data: "address" },
//     ],
//   });
// });

jQuery(document).ready(function () {
  jQuery(".custom-datatable").each((idx, elem) => {
    const table_id = jQuery(elem).data("custom_datatable_id");
    const columns = jQuery(elem)
      .data("custom_datatable_columns")
      .split(", ")
      .map((col) => {
        return {
          data: col,
        };
      });

    jQuery(elem).DataTable({
      ordering: false,
      searching: false,
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
      columns: columns,
    });
  });
});
