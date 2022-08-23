<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";
import { getAJAX, postAJAX } from "../composable";

const table = ref({});
const columns = ref([]);
const tableRows = ref([]);

const newRow = ref({});

const showAddNew = ref(false);

const table_id = useRoute().params.id;

(async () => {
  try {
    const { success, data } = await getAJAX("get_table", { table_id });
    if (success) table.value = data;
    else console.log("AJAX not successful", data);
  } catch (e) {
    console.log("AJAX Failed", e);
  }

  columns.value = table.value.columns;
})();

const getRows = async () => {
  {
    try {
      const { success, data } = await getAJAX("get_table_rows", { table_id });
      if (success) {
        tableRows.value = data;
      } else console.log("AJAX not successful", data);
    } catch (e) {
      console.log("AJAX Failed", e);
    }
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
  const row = JSON.stringify(newRow.value);

  try {
    const { success } = await postAJAX("add_row", {
      table_id,
      row,
    });
    if (success) {
      hideAddNewForm();
      newRow.value = {};
      console.log("successfully added row");
      getRows();
    } else console.log("couldn't add row");
  } catch (e) {
    console.log("AJAX failed", e);
  }
};
</script>

<template>
  <div>
    <el-row justify="space-between">
      <el-col class="flex-align-center" :span="9">
        <h2 class="inline space-after">{{ table.table_name }}</h2>
        <el-button v-if="!showAddNew" type="primary" @click="showAddNewForm">
          Add new row
        </el-button>
      </el-col>
      <!-- <el-pagination
        v-model:currentPage="currentPage"
        v-model:page-size="pageSize"
        :page-sizes="[10, 20, 30, 40]"
        background
        layout="sizes, total, prev, pager, next"
        :total="contacts.length"
      /> -->
    </el-row>
    <el-row v-if="showAddNew">
      <el-form
        @submit.prevent="handleAddNewRow"
        label-width="auto"
        label-position="left"
      >
        <h3>Add new row</h3>
        <el-row v-for="column in columns" :key="column.value">
          <el-form-item :prop="column.value" :label="column.label">
            <el-input v-model="newRow[column.value]" />
          </el-form-item>
        </el-row>
        <el-form-item>
          <el-button native-type="submit" type="primary">Add row</el-button>
          <el-button @click="hideAddNewForm">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row>
      <el-col>
        <el-table :data="tableRows" style="width: 100%">
          <el-table-column prop="row_id" label="ID" />
          <el-table-column
            v-for="column in columns"
            :prop="column.value"
            :label="column.label"
          />
        </el-table>
      </el-col>
    </el-row>
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
