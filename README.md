# datatables-manager-wp

A wordpress plugin to add and manage custom datatables.

The admin menu of the plugin is available at the "Settings" menu in the admin panel as "Datatables Manager".
Here, the admin can see all the tables in the plugin's database and can add, modify, or remove custom datatables as needed.

Shortcode `custom-datatable` can be used to display a table.
An argument `id` needs be passed with the ID of the desired table.
Example: `[custom-datatable id="23"]` will display the table with id "23".

## Instrtuctions

Run `npm run build:dev` to generate dev bundle.

Run `npm run watch` to generate dev bundle watch src folder for changes.

Run `npm run watch:hot` to run webpack-dev-server and get HMR.

Run `npm run build` to generate production bundle.

## Export plugin as `.zip`

Run `npm run export` to generate production bundle and compress the plugin into an archive that can be uploaded and installed in wordpress.
