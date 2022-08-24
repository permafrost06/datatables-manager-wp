<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";
import { getAJAX, postAJAX } from "../composable";

const table = ref({});
const columns = ref([]);
const tableRows = ref([]);

const formEl = ref();

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
  try {
    const { success, data } = await getAJAX("get_table_rows", { table_id });
    if (success) {
      tableRows.value = data;
    } else console.log("AJAX not successful", data);
  } catch (e) {
    console.log("AJAX Failed", e);
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

      showAddNew.value = false;
    } else {
      console.log("form not valid");
    }
  });
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
    <el-dialog v-model="showAddNew" title="Shipping address">
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
