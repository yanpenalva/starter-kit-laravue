import useLogStore from '@/store/useLogStore';
import { useQuasar } from 'quasar';
import { ref } from 'vue';

export default function useLog() {
  const $q = useQuasar();
  const store = useLogStore();
  const loading = ref(false);
  const pagination = ref({});
  const searchText = ref('');
  const rows = ref([]);
  const errors = ref({});

  const columnMap = {
    id: 'id',
    action: 'eventPt',
    description: 'description',
    executedBy: 'causer',
    affected: 'subject',
    createdAt: 'createdAt',
  };

  const listPage = async (params = {}) => {
    try {
      $q.loading.show();
      loading.value = true;

      await store.list({
        ...params,
        search: searchText.value,
        paginated: 1,
      });

      const response = store.getLogs;

      rows.value = response;

      pagination.value = {
        rowsPerPage: response?.per_page ?? 10,
        page: response?.current_page ?? 1,
        rowsNumber: response?.total ?? 0,
        sortBy: params.column ?? 'id',
        descending: (params.order ?? 'desc') === 'desc',
      };
    } finally {
      loading.value = false;
      $q.loading.hide();
    }
  };

  const handleSearch = async (value) => {
    searchText.value = value;
    await listPage();
  };

  const handleResetSearch = async () => {
    searchText.value = '';
    await listPage();
  };

  const updatePagination = async (pagination) => {
    await listPage({
      column: pagination.sortBy,
      order: pagination.descending ? 'desc' : 'asc',
      page: pagination.page,
      perPage: pagination.rowsPerPage,
    });
  };

  return {
    loading,
    rows,
    pagination,
    searchText,
    errors,
    filter: searchText,
    columnMap,
    listPage,
    handleSearch,
    handleResetSearch,
    updatePagination,
  };
}
