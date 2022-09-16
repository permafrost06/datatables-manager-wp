import { ElMessage } from "element-plus";

export const successMessage = (message) => {
  ElMessage({
    message,
    type: "success",
  });
};

export const errorMessage = (message) => {
  ElMessage({
    message,
    type: "error",
  });
};

export const getRestError = (e) => {
  if (e.responseJSON)
    return e.responseJSON.code + " - " + e.responseJSON.message;
  else return e.responseText;
};
