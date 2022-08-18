<script setup>
import { ref } from "vue";
import { postAJAX } from "../composable";

const table_name = ref("");

const columns = ref([]);

const addCol = () => {
  columns.value.push({
    value: "",
    label: "",
  });
};

const onSubmit = async () => {
  try {
    const { success } = await postAJAX("add_table", {
      table_name: table_name.value,
      columns: JSON.stringify(columns.value),
    });

    if (success) console.log("successfully added table");
    else console.log("couldn't add table");
  } catch (e) {
    console.log(e);
  }
};
</script>

<template>
  <el-form @submit.prevent="onSubmit" label-width="100px">
    <el-form-item prop="table_name" label="Table Name">
      <el-col :span="8">
        <el-input v-model="table_name" />
      </el-col>
    </el-form-item>
    <el-form-item
      :label="'Column ' + (index + 1)"
      v-for="(column, index) in columns"
      :key="index"
    >
      <el-col :span="4">
        <el-input v-model="column.label" placeholder="Label" />
      </el-col>
      <el-col :span="4">
        <el-input v-model="column.value" placeholder="Key" />
      </el-col>
    </el-form-item>
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
