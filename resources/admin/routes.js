import AllTablesView from "./views/AllTablesView.vue";
import AddNewTableView from "./views/AddNewTableView.vue";
import SingleTableView from "./views/SingleTableView.vue";

export default [
  {
    path: "/",
    name: "All Tables",
    component: AllTablesView,
    meta: {
      active: "dashboard",
    },
  },
  {
    path: "/add-new-table",
    name: "Add New Table",
    component: () => AddNewTableView,
  },
  {
    path: "/tables/:id",
    name: "Single Table View",
    component: () => SingleTableView,
    props: true,
  },
];
