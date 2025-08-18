import service from '@/services/LogService';
import { useQuasar } from 'quasar';
import { ref } from 'vue';

export default function useLog() {
  const $q = useQuasar();
  const loading = ref(false);
  const rows = ref([]);
  const pagination = ref({
    page: 1,
    rowsPerPage: 10,
    sortBy: 'id',
    descending: true,
  });

  const listPage = async (event = {}) => {
    try {
      $q.loading.show();
      loading.value = true;
      const params = {
        page: event.page ?? pagination.value.page,
        rowsPerPage: event.rowsPerPage ?? pagination.value.rowsPerPage,
        sortBy: event.sortBy ?? pagination.value.sortBy,
        order: (event.descending ?? pagination.value.descending) ? 'desc' : 'asc',
        paginated: true,
        search: event.search ?? '',
      };
      const data = await service.index(params);
      rows.value = data.data;
      pagination.value.page = data.current_page;
      pagination.value.rowsPerPage = data.per_page;
      pagination.value.rowsNumber = data.total;
    } finally {
      loading.value = false;
      $q.loading.hide();
    }
  };

  const viewLog = async (id) => {
    const data = await service.get(id);
    return data;
  };

  return {
    loading,
    rows,
    pagination,
    listPage,
    viewLog,
  };
}
