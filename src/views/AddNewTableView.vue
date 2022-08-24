<script setup>
import { ref, reactive } from "vue";
import { postAJAX } from "../composable";

const newTable = ref({
  table_name: "",
  columns: [
    {
      label: "",
      value: "",
    },
  ],
});

const formEl = ref();

const addCol = () => {
  newTable.value.columns.push({
    label: "",
    value: "",
  });
};

const onSubmit = async () => {
  formEl.value.validate(async (valid) => {
    if (valid) {
      try {
        const { success } = await postAJAX("add_table", {
          table_name: newTable.value.table_name,
          description: newTable.value.description,
          columns: JSON.stringify(newTable.value.columns),
        });

        if (success) console.log("successfully added table");
        else console.log("couldn't add table");
      } catch (e) {
        console.log(e);
      }
    } else {
      console.log("Form not valid");
    }
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
  description: [
    {
      required: true,
      message: "Please enter a description for the table",
      trigger: "blur",
    },
  ],
});
</script>

<template>
  <el-form
    @submit.prevent="onSubmit"
    :rules="formRules"
    :model="newTable"
    label-width="auto"
    label-position="left"
    ref="formEl"
  >
    <el-form-item prop="table_name" label="Table Name">
      <el-col :span="8">
        <el-input v-model="newTable.table_name" />
      </el-col>
    </el-form-item>
    <el-form-item prop="description" label="Description">
      <el-col :span="8">
        <el-input v-model="newTable.description" />
      </el-col>
    </el-form-item>
    <el-row v-for="(column, index) in newTable.columns" :key="index">
      <el-form-item
        class="inline-form-item"
        :prop="'columns.' + index + '.label'"
        :label="'Column ' + (index + 1)"
        :rules="{
          required: true,
          message: 'Please enter a label for the column',
          trigger: 'blur',
        }"
      >
        <el-input v-model="column.label" placeholder="Label" />
      </el-form-item>
      <el-form-item
        class="inline-form-item"
        :prop="'columns.' + index + '.value'"
        :rules="{
          required: true,
          message: 'Please enter a key for the column',
          trigger: 'blur',
        }"
        label-width="0px"
      >
        <el-input v-model="column.value" placeholder="Key" />
      </el-form-item>
    </el-row>
    <el-form-item>
      <el-button @click="addCol">+ Add column</el-button>
    </el-form-item>
    <el-form-item>
      <el-button native-type="submit" type="primary"> Add table </el-button>
      <router-link :to="{ name: 'All Tables' }">
        <el-button>Cancel</el-button>
      </router-link>
    </el-form-item>
  </el-form>
</template>

<style lang="scss" scoped>
.inline-form-item {
  display: inline-flex;
  vertical-align: middle;
  margin-right: 32px;
  margin-bottom: 32px;
}
</style>
