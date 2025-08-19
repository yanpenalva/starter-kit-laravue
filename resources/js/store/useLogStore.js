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
    getLogs() {
      return this.logs;
    },
    getLog() {
      return this.log;
    },
    getErrors() {
      return this.errors;
    },
  },
  actions: {
    async list(params) {
      const { data } = await service.index(params);
      this.logs = data;
    },

    async consult(id) {
      const { data } = await service.get(id);
      this.log = data;
    },

    clearStore() {
      this.logs = {
        data: [],
        per_page: 10,
        total: 0,
        current_page: 1,
      };
      this.log = null;
      this.errors = null;
    },
  },
});

export default useLogStore;
