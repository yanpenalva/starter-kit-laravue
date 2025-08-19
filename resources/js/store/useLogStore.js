import service from '@/services/LogService';
import { defineStore } from 'pinia';

const useLogStore = defineStore('logs', {
  state: () => ({
    logs: {
      data: [],
      per_page: 10,
      total: 0,
      current_page: 1,
    },
    log: null,
    errors: null,
  }),
  getters: {
    getLogs(state) {
      return state.logs;
    },
    getLog(state) {
      return state.log;
    },
    getErrors(state) {
      return state.errors;
    },
  },
  actions: {
    async list(params) {
      const data = await service.index(params);
      this.logs = data;
    },
    async consult(id) {
      const { data } = await service.get(id);
      this.log = data;
    },
  },
});

export default useLogStore;
