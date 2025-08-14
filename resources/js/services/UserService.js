import http from './AxiosClient';
const route = 'api/v1/users';

const index = async (params) => {
  const { data } = await http.get(route, {
    params,
  });
  return data;
};

const get = async (id) => {
  const { data } = await http.get(`${route}/${id}`);
  return data;
};

const store = async (params) => {
  const { data } = await http.post(route, params);
  return data;
};

const update = async (id, params) => {
  const { data } = await http.put(`${route}/${id}`, params);
  return data;
};

const destroy = async (id) => {
  return await http.delete(`${route}/${id}`);
};

const register = async (params) => {
  const { data } = await http.post(`${route}/register`, params);
  return data;
};

const verifyEmail = async (params) => {
  const { data } = await http.get(`${route}/verify`, {
    params,
  });
  return data;
};

export default {
  index,
  store,
  update,
  get,
  destroy,
  register,
  verifyEmail,
};
