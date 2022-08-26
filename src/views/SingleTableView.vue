<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import {
  getAJAX,
  postAJAX,
  successMessage,
  errorMessage,
  getXHRError,
} from "../composable";

const table = ref({});
const columns = ref([]);
const tableRows = ref([]);

const deleteID = ref();
const dialogVisible = ref(false);

const formEl = ref();

const newRow = ref({});

const showAddNew = ref(false);

const table_id = useRoute().params.id;

(async () => {
  try {
    const { success, data } = await getAJAX("get_table", { table_id });
    if (success) table.value = data;
    else errorMessage("Failed to fetch tables - " + data.error);
  } catch (e) {
    errorMessage("AJAX Failed - " + getXHRError(e));
  }

  columns.value = table.value.columns;
})();

const getRows = async () => {
  try {
    const { success, data } = await getAJAX("get_table_rows", { table_id });
    if (success) {
      tableRows.value = data;
    } else errorMessage("Failed to fetch rows - " + data.error);
  } catch (e) {
    errorMessage("AJAX Failed - " + getXHRError(e));
  }
};

await getRows();

const showAddNewForm = () => {
  showAddNew.value = true;
};

const hideAddNewForm = () => {
  showAddNew.value = false;
};

const handleAddNewRow = async () => {
  formEl.value.validate(async (valid) => {
    if (valid) {
      const row = JSON.stringify(newRow.value);

      try {
        const { success, data } = await postAJAX("add_row", {
          table_id,
          row,
        });
        if (success) {
          hideAddNewForm();
          newRow.value = {};
          successMessage("Added new row");
          getRows();
        } else errorMessage("Couldn't add row - " + data.error);
      } catch (e) {
        errorMessage("AJAX failed - ", getXHRError(e));
      }
    } else {
      errorMessage("Please fix the errors in the form");
    }
  });
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

const confirmDelete = async () => {
  try {
    const { success, data } = await postAJAX("delete_row", {
      row_id: deleteID.value,
    });
    if (success) {
      successMessage("Successfully deleted row " + deleteID.value);
      dialogVisible.value = false;
      await getRows();
    } else {
      errorMessage("Could not delete row " + data.error);
    }
  } catch (e) {
    errorMessage("AJAX failed - " + getXHRError(e));
  }
};
</script>

<template>
  <div>
    <el-row>
      <el-col :span="9">
        <el-row class="margin-sm">
          <h2>Table name: {{ table.table_name }}</h2>
        </el-row>
        <el-row class="margin-sm">
          <p>Description: {{ table.table_desc }}</p>
        </el-row>
      </el-col>
    </el-row>
    <el-row justify="space-between">
      <el-button type="primary" @click="showAddNewForm">
        Add new row
      </el-button>
      <el-pagination
        v-model:currentPage="currentPage"
        v-model:page-size="pageSize"
        :page-sizes="[10, 20, 30, 40]"
        background
        layout="sizes, total, prev, pager, next"
        :total="tableRows.length"
      />
    </el-row>
    <el-row>
      <el-col>
        <el-table :data="rowPage">
          <el-table-column prop="row_id" label="ID" />
          <el-table-column
            v-for="column in columns"
            :prop="column.value"
            :label="column.label"
          />
          <el-table-column label="Operations" width="100">
            <template #default="{ row }">
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

    <el-dialog v-model="dialogVisible" title="Tips" width="30%">
      <span> Are you sure you want to delete row with ID {{ deleteID }}? </span>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">Cancel</el-button>
          <el-button type="danger" @click="confirmDelete"> Confirm </el-button>
        </span>
      </template>
    </el-dialog>

    <el-dialog v-model="showAddNew" title="Add new row">
      <el-form
        @submit.prevent="handleAddNewRow"
        :model="newRow"
        label-width="auto"
        label-position="left"
        ref="formEl"
      >
        <el-form-item
          v-for="column in columns"
          :key="column.value"
          :label="column.label"
          :prop="column.value"
          label-width="auto"
          label-position="left"
          :rules="{
            required: true,
            message: column.label + ' can not be empty',
            trigger: 'blur',
          }"
        >
          <el-input v-model="newRow[column.value]" />
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="showAddNew = false">Cancel</el-button>
          <el-button type="primary" @click="handleAddNewRow">
            Confirm
          </el-button>
        </span>
      </template>
    </el-dialog>
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
</style>
