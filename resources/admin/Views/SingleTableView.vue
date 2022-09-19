<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import {
  errorMessage,
  getRestError,
  successMessage,
} from "../composables/utils";
import * as rest from "../composables/rest";
import DialogForm from "../components/DialogFormComponent.vue";

const table = ref({});
const columns = ref([]);
const tableRows = ref([]);

const updateID = ref();
const deleteID = ref();
const dialogVisible = ref(false);

const formEl = ref();

const newRow = ref({});

const showAddNew = ref(false);
const showUpdateForm = ref(false);

const table_id = useRoute().params.id;

(async () => {
  try {
    table.value = await rest.GET(`table/${table_id}`);
  } catch (e) {
    errorMessage("Could not get table - " + getRestError(e));
  }

  columns.value = table.value.columns;
})();

const getRows = async () => {
  try {
    tableRows.value = await rest.GET(`tables/${table_id}/rows`);
  } catch (e) {
    errorMessage("Could not get rows - " + getRestError(e));
  }
};

// await getRows();
getRows();

const handleAddNewRow = async (data) => {
  const row = JSON.stringify(data);

  try {
    await rest.POST(`tables/${table_id}/rows`, {
      row,
    });

    showAddNew.value = false;
    newRow.value = {};
    successMessage("Added new row");
    await getRows();
  } catch (e) {
    errorMessage("Couldn't add row - ", getRestError(e));
  }
};

const handleUpdateRow = async (data) => {
  const row = JSON.stringify(data);

  try {
    await rest.PATCH(`rows/${updateID.value}`, {
      row,
    });
    showUpdateForm.value = false;
    newRow.value = {};
    successMessage("Updated row");
    await getRows();
  } catch (e) {
    errorMessage("Couldn't update row. - ", getRestError(e));
  }
};

const currentPage = ref(1);
const pageSize = ref(10);

const rowPage = computed(() => {
  return tableRows.value.slice(
    pageSize.value * (currentPage.value - 1),
    pageSize.value * currentPage.value
  );
});

const handleDelete = (id) => {
  deleteID.value = id;
  dialogVisible.value = true;
};

const handleUpdate = (row) => {
  const { row_id, ...row_columns } = row;
  updateID.value = row_id;
  newRow.value = row_columns;
  showUpdateForm.value = true;
};

const confirmDelete = async () => {
  // try {
  await rest.DELETE(`rows/${deleteID.value}`);
  successMessage("Successfully deleted row " + deleteID.value);
  dialogVisible.value = false;
  await getRows();
  // } catch (e) {
  //   errorMessage("Could not delete row - " + getRestError(e));
  // }
};
</script>

<template>
  <div>
    <el-row justify="space-between" align="bottom">
      <el-col :span="9">
        <el-row class="margin-sm">
          <h2>Table name: {{ table.table_name }}</h2>
        </el-row>
        <el-row class="margin-sm">
          <p>Description: {{ table.table_desc }}</p>
        </el-row>
      </el-col>
      <el-col :span="3">
        <el-button
          class="margin-bottom-sm"
          type="primary"
          @click="showAddNew = true"
        >
          Add new row
        </el-button>
      </el-col>
    </el-row>
    <el-row>
      <el-table :data="rowPage">
        <el-table-column prop="row_id" label="ID" width="50" />
        <el-table-column
          v-for="column in columns"
          :prop="column.value"
          :label="column.label"
        />
        <el-table-column label="Operations" width="140">
          <template #default="{ row }">
            <el-button size="small" @click="handleUpdate(row)">
              Edit
            </el-button>
            <el-button
              size="small"
              type="danger"
              @click="handleDelete(row.row_id)"
            >
              Delete
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-row>
    <el-row justify="end">
      <el-pagination
        v-model:currentPage="currentPage"
        v-model:page-size="pageSize"
        :page-sizes="[10, 20, 30, 40]"
        background
        layout="sizes, total, prev, pager, next"
        :total="tableRows.length"
      />
    </el-row>

    <el-dialog v-model="dialogVisible" title="Tips" width="30%">
      <span> Are you sure you want to delete row with ID {{ deleteID }}? </span>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">Cancel</el-button>
          <el-button type="danger" @click="confirmDelete"> Confirm </el-button>
        </span>
      </template>
    </el-dialog>

    <DialogForm
      :show-dialog="showAddNew"
      dialog-title="Add new row"
      :columns="columns"
      :form-data="newRow"
      @dialog-confirm="handleAddNewRow"
      @dialog-cancel="showAddNew = false"
    />

    <DialogForm
      :show-dialog="showUpdateForm"
      dialog-title="Update row"
      :columns="columns"
      :form-data="newRow"
      @dialog-confirm="handleUpdateRow"
      @dialog-cancel="showUpdateForm = false"
    />
  </div>
</template>

<style lang="scss" scoped>
.margin-sm * {
  margin: 8px 0;
}

.flex-align-center {
  display: flex;
  align-items: center;
}

.no-padding {
  padding: 0;
}

.margin-bottom-sm {
  margin-bottom: 6px;
}
</style>
