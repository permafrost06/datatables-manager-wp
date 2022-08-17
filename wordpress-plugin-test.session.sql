--@block
SELECT * FROM wp_options WHERE option_name LIKE "datatab%";

--@block
CREATE TABLE IF NOT EXISTS `wp_datatables_tables` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `table_name` varchar(32) NOT NULL,
  `columns` varchar(255) NOT NULL
);

--@block
CREATE TABLE IF NOT EXISTS `wp_contacts_manager_table` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL
);

--@block
SET foreign_key_checks = 0;
DROP TABLE IF EXISTS wp_datatables_tables;
DROP TABLE IF EXISTS wp_datatables_tabledata;
SET foreign_key_checks = 1;

--@block
DROP TABLE `{$wpdb->prefix}contacts_manager_table`;

--@block
CREATE TABLE IF NOT EXISTS `wp_datatables_tabledata` (
  `field_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `table_id` int NOT NULL,
  `column_name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  FOREIGN KEY (table_id) REFERENCES wp_datatables_tables(id)
);

--@block
INSERT INTO wp_datatables_tables (table_name, columns)
VALUES (
  'Cars',
  '{
    "col1": {
      "name": "model",
      "label": "Model"
    },
    "col2": {
      "name": "manufacturer",
      "label": "Manufacturer"
    },
    "col3": {
      "name": "engine_type",
      "label": "Engine Type"
    }
  }'
);

--@block
TRUNCATE TABLE wp_datatables_tablerows;

--@block
INSERT INTO wp_datatables_tablerows (table_id, row)
VALUES (
  1,
  "{
    'model': 'McLaren F1 GTR',
    'manufacturer': 'McLaren',
    'engine_type': 'BMW V12'
  }"
);

INSERT INTO wp_datatables_tablerows (table_id, row)
VALUES (
  1,
  "{
    'model': 'Ferrari F50',
    'manufacturer': 'Ferrari',
    'engine_type': 'Tipo V12'
  }"
);

INSERT INTO wp_datatables_tablerows (table_id, row)
VALUES (
  1,
  "{
    'model': 'Ford GT40',
    'manufacturer': 'Ford',
    'engine_type': 'Ford V8'
  }"
);

INSERT INTO wp_datatables_tablerows (table_id, row)
VALUES (
  1,
  "{
    'model': 'Supra 2019',
    'manufacturer': 'Toyota',
    'engine_type': 'BMW V6'
  }"
);

--@block
SELECT * FROM wp_datatables_tabledata
INNER JOIN wp_datatables_tables
ON wp_datatables_tabledata.table_id = wp_datatables_tables.id;

--@block
SELECT * FROM wp_datatables_tablerows;

--@block
SELECT * FROM wp_datatables_tablerows WHERE table_id = 1;
