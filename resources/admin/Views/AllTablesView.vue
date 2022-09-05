<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { ElMessage } from "element-plus";
// import {
//   errorMessage,
//   getAJAX,
//   getXHRError,
//   postAJAX,
//   successMessage,
// } from "../composable";
import CopyIcon from "../Components/CopyIconComponent.vue";

const tables = ref([
  {
    id: 101,
    table_name: "Contacts",
    table_desc: "A table for contacts",
    columns: [
      { label: "Name", value: "name" },
      { label: "Email", value: "email" },
      { label: "Phone", value: "phone" },
      { label: "Address", value: "address" },
    ],
  },
  ,
]);
const deleteID = ref("");
const editID = ref("");
const router = useRouter();
const dialogVisible = ref(false);
const editDialogVisible = ref(false);
const loading = ref(true);
const formEl = ref();

const modifiedTable = ref({});

const getAllTables = async () => {
  // try {
  //   const { success, data } = await getAJAX("get_all_tables");
  //   if (success) {
  //     tables.value = data;
  //     loading.value = false;
  //   } else {
  //     errorMessage("AJAX not successful - " + data);
  //   }
  // } catch (e) {
  //   errorMessage("AJAX failed - " + getXHRError(e));
  // }
  console.log("getAllTables");
};

getAllTables();

const getLabelsList = (columns) => {
  const labels = [];

  for (let col in columns) {
    labels.push(columns[col].label);
  }

  return labels.join(", ");
};

const handleAddNewTable = () => {
  router.push({ name: "Add New Table" });
};

const handleEdit = (id) => {
  editID.value = id;

  const selectedTable = tables.value.filter((table) => table.id == id);
  modifiedTable.value = {
    table_name: selectedTable[0].table_name,
    table_desc: selectedTable[0].table_desc,
  };

  editDialogVisible.value = true;
};

const handleDelete = (id) => {
  deleteID.value = id;
  dialogVisible.value = true;
};

const confirmDelete = async () => {
  // try {
  //   const { success, data } = await postAJAX("delete_table", {
  //     table_id: deleteID.value,
  //   });
  //   if (success) {
  //     successMessage("Successfully deleted table " + deleteID.value);
  //     dialogVisible.value = false;
  //     getAllTables();
  //   } else {
  //     errorMessage("Could not delete table " + data.error);
  //   }
  // } catch (e) {
  //   errorMessage("AJAX failed - " + getXHRError(e));
  // }
  console.log("confirmDelete");
};

const confirmEdit = async () => {
  console.log("confirmEdit");
  formEl.value.validate(async (valid) => {
    if (valid) {
      // try {
      //   const { success, data } = await postAJAX("update_table", {
      //     table_id: editID.value,
      //     ...modifiedTable.value,
      //     // table_name: modifiedTable.value.table_name,
      //     // table_desc: modifiedTable.value.table_desc,
      //   });
      //   if (success) {
      //     successMessage("Successfully updated table " + editID.value);
      //     editDialogVisible.value = false;
      //     getAllTables();
      //   } else {
      //     errorMessage("Could not update table " + data.error);
      //   }
      // } catch (e) {
      //   errorMessage("AJAX failed - " + getXHRError(e));
      // }
      console.log("form valid");
    } else {
      // errorMessage("Please fix the errors in the form");
      console.log("form invalid");
    }
  });
};

const copyShortcode = async (id) => {
  const shortcode = `[custom-datatable id="${id}"]`;
  await navigator.clipboard.writeText(shortcode);

  ElMessage({
    message: "Shortcode copied to clipboard",
    type: "success",
  });
};

const formRules = reactive({
  table_name: [
    {
      required: true,
      message: "Please enter a name for the table",
      trigger: "blur",
    },
  ],
  table_desc: [
    {
      required: true,
      message: "Please enter a description for the table",
      trigger: "blur",
    },
  ],
});
</script>

<template>
  <div>
    <el-row>
      <p class="message">
        Use shortcode <code>[custom-datatable]</code> to render custom table.
        Specify ID with <code>id</code> attribute to render a specific table.
        Example: <code>[custom-datatable id="7"]</code> to render custom table
        with ID 7.
      </p>
    </el-row>
    <el-row justify="space-between">
      <el-col class="flex-align-center" :span="9">
        <h2 class="inline space-after">List of Custom Tables</h2>
        <el-button type="primary" @click="handleAddNewTable">
          Add new table
        </el-button>
      </el-col>
    </el-row>
    <el-row>
      <el-col>
        <el-table :data="tables" style="width: 100%">
          <el-table-column prop="id" label="ID" width="50" />
          <el-table-column prop="table_name" label="Table Name" min-width="16">
            <template #default="{ row }">
              <router-link
                :to="{
                  name: 'Single Table View',
                  params: { id: row.id },
                }"
              >
                {{ row.table_name }}
              </router-link>
            </template>
          </el-table-column>
          <el-table-column
            prop="table_desc"
            label="Description"
            min-width="16"
          />
          <el-table-column prop="columns" label="Colmuns" min-width="18">
            <template #default="{ row }">
              {{ getLabelsList(row.columns) }}
            </template>
          </el-table-column>
          <el-table-column label="Operations" min-width="13">
            <template #default="{ row }">
              <el-button size="small" @click="handleEdit(row.id)">
                Edit
              </el-button>
              <el-button
                size="small"
                type="danger"
                @click="handleDelete(row.id)"
              >
                Delete
              </el-button>
            </template>
          </el-table-column>
          <el-table-column label="Shortcode" min-width="22">
            <template #default="{ row }">
              <code>[custom-datatable id="{{ row.id }}"]</code>
              <el-button
                class="no-padding"
                size="small"
                @click="copyShortcode(row.id)"
              >
                <CopyIcon class="svg-button" />
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-col>
    </el-row>

    <el-dialog v-model="dialogVisible" title="Tips" width="30%">
      <span>
        Are you sure you want to delete table with ID {{ deleteID }}?
      </span>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">Cancel</el-button>
          <el-button type="danger" @click="confirmDelete"> Confirm </el-button>
        </span>
      </template>
    </el-dialog>
    <el-dialog v-model="editDialogVisible" title="Tips" width="30%">
      <el-form
        @submit.prevent="handleEditTable"
        :model="modifiedTable"
        :rules="formRules"
        label-width="auto"
        label-position="left"
        ref="formEl"
      >
        <el-form-item prop="table_name" label="Table Name">
          <el-input v-model="modifiedTable.table_name" />
        </el-form-item>
        <el-form-item prop="table_desc" label="Table Description">
          <el-input type="textarea" v-model="modifiedTable.table_desc" />
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="editDialogVisible = false">Cancel</el-button>
          <el-button type="danger" @click="confirmEdit">Confirm</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<style lang="scss" scoped>
.space-after {
  margin-right: 1rem;
}

.inline {
  display: inline-block;
}

.flex-align-center {
  display: flex;
  align-items: center;
}

.no-padding {
  padding: 0;
}
</style>
