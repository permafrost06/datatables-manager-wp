<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { ElMessage } from "element-plus";
import "element-plus/es/components/message/style/css";
import { getAJAX } from "../composable";
import CopyIcon from "../components/CopyIconComponent.vue";

const tables = ref([]);
const deleteID = ref("");
const router = useRouter();
const dialogVisible = ref(false);
const loading = ref(true);

const getAllTables = async () => {
  try {
    const { success, data } = await getAJAX("get_all_tables");
    if (success) {
      tables.value = data;
      loading.value = false;
    } else {
      console.log("AJAX not successful", data);
    }
  } catch (e) {
    console.log("AJAX failed", e);
  }
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
  router.push({ name: "Edit Table", params: { id } });
};

const handleDelete = (id) => {
  deleteID.value = id;
  dialogVisible.value = true;
};

const copyShortcode = async (id) => {
  const shortcode = `[custom-datatable id="${id}"]`;
  await navigator.clipboard.writeText(shortcode);

  ElMessage({
    message: "Shortcode copied to clipboard",
    type: "success",
  });
};
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
        <el-table v-loading="loading" :data="tables" style="width: 100%">
          <el-table-column prop="id" label="id" width="40" />
          <el-table-column prop="table_name" label="Table Name" min-width="16">
            <template #default="{ row }">
              <router-link
                :to="{ name: 'Single Table View', params: { id: row.id } }"
              >
                {{ row.table_name }}
              </router-link>
            </template>
          </el-table-column>
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
      <span
        >Are you sure you want to delete contact with ID {{ deleteID }}?</span
      >
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">Cancel</el-button>
          <el-button type="danger" @click="confirmDelete"> Confirm </el-button>
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
