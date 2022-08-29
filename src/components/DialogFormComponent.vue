<script setup>
import { ref, reactive } from "vue";
import { errorMessage } from "../composable";

const props = defineProps({
  showDialog: {
    type: Boolean,
    default: false,
  },
  dialogTitle: {
    type: String,
    default: "Dialog Form",
  },
  columns: {
    default: reactive([
      {
        value: "",
        label: "",
      },
    ]),
  },
  formData: {
    default: reactive({}),
  },
  confirmText: {
    type: String,
    default: "Confirm",
  },
});

const emit = defineEmits(["dialog-confirm", "dialog-cancel"]);

const formEl = ref();

const handleCancel = () => {
  emit("dialog-cancel");
};

const handleConfirm = () => {
  formEl.value.validate((valid) => {
    if (valid) emit("dialog-confirm", props.formData);
    else errorMessage("Please fix the errors in the form");
  });
};
</script>

<template>
  <el-dialog
    :before-close="handleCancel"
    v-model="props.showDialog"
    :title="props.dialogTitle"
  >
    <el-form
      @submit.prevent="handleConfirm"
      :model="props.formData"
      label-width="auto"
      label-position="left"
      ref="formEl"
    >
      <el-form-item
        v-for="column in props.columns"
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
        <el-input v-model="props.formData[column.value]" />
      </el-form-item>
      <el-button native-type="submit" style="display: none" />
    </el-form>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="handleCancel">Cancel</el-button>
        <el-button type="primary" @click="handleConfirm">
          {{ props.confirmText }}
        </el-button>
      </span>
    </template>
  </el-dialog>
</template>
