import { ElMessage } from "element-plus";

export const getAJAX = async (action, payload = {}) => {
  const prefix = "dtm";
  payload.action = `${prefix}_${action}`;
  payload._ajax_nonce = datatablesMgrAdmin.nonce;

  return await jQuery.get(datatablesMgrAdmin.ajax_url, payload);
};

export const postAJAX = async (action, payload = {}) => {
  const prefix = "dtm";
  payload.action = `${prefix}_${action}`;
  payload._ajax_nonce = datatablesMgrAdmin.nonce;

  return await jQuery.post(datatablesMgrAdmin.ajax_url, payload);
};

export const getSetting = async (option) => {
  const { data } = await postAJAX("get_setting", {
    option_name: option,
  });

  return data;
};

export const updateSetting = async (option, value) => {
  const { success } = await postAJAX("update_setting", {
    option_name: option,
    option_value: value,
  });

  return success;
};

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

export const getXHRError = (xhr) => {
  const res = JSON.parse(xhr.responseText);
  const err_msg = res.data.message ? res.data.message : res.data.error;

  return err_msg;
};
