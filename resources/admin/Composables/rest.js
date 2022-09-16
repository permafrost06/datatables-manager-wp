const request = (method, route, data = {}) => {
  const url = `${window.fluentFrameworkAdmin.rest.url}/${route}`;

  const headers = { "X-WP-Nonce": window.fluentFrameworkAdmin.rest.nonce };

  if (["PUT", "PATCH", "DELETE"].indexOf(method.toUpperCase()) !== -1) {
    headers["X-HTTP-Method-Override"] = method;
    method = "POST";
  }

  return window.jQuery.ajax({
    url: url,
    type: method,
    data: data,
    headers: headers,
  });
};

export const GET = (route, data = {}) => {
  return request("GET", route, data);
};
export const POST = (route, data = {}) => {
  return request("POST", route, data);
};
export const DELETE = (route, data = {}) => {
  return request("DELETE", route, data);
};
export const PUT = (route, data = {}) => {
  return request("PUT", route, data);
};
export const PATCH = (route, data = {}) => {
  return request("PATCH", route, data);
};
