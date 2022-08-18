import { createRouter, createWebHashHistory } from "vue-router";
import AllTablesView from "../views/AllTablesView.vue";

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    {
      path: "/",
      name: "All Tables",
      component: AllTablesView,
    },
    {
      path: "/add-new-table",
      name: "Add New Table",
      component: () => import("../views/AddNewTableView.vue"),
    },
    {
      path: "/tables/:id",
      name: "Single Table View",
      component: () => import("../views/SingleTableView.vue"),
      props: true,
    },
  ],
});

export default router;
