import http from './AxiosClient';
const route = 'api/v1/logs';

const index = async (params) => {
  const { data } = await http.get(route, { params });
  return data;
};

const get = async (id) => {
  const { data } = await http.get(`${route}/${id}`);
  return data;
};

export default {
  index,
  get,
};
