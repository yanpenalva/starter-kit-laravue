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

  const showModal = ref(false);
  const selectedLog = ref(null);

  const validColumns = ['description', 'event', 'causer', 'subject', 'createdAt'];

  const getValidColumn = (column) => {
    return validColumns.includes(column) ? column : 'createdAt';
  };

  const listPage = async (params = {}) => {
    try {
      $q.loading.show();
      loading.value = true;

      const requestParams = {
        search: searchText.value || '',
        paginated: 1,
        page: params.page || pagination.value.page || 1,
        limit: params.limit || pagination.value.rowsPerPage || 10,
        column: getValidColumn(params.column || pagination.value.sortBy || 'createdAt'),
        order: params.order || (pagination.value.descending ? 'desc' : 'asc') || 'desc',
      };

      await store.list(requestParams);

      const response = store.getLogs;
      rows.value = response.data || response || [];
      pagination.value.rowsPerPage = response.meta?.per_page || response.per_page || 10;
      pagination.value.page = response.meta?.current_page || response.current_page || 1;
      pagination.value.rowsNumber = response.meta?.total || response.total || 0;
    } finally {
      loading.value = false;
      $q.loading.hide();
    }
  };

  const handleSearch = async (value) => {
    searchText.value = value ?? '';
    await listPage({
      page: 1,
      limit: pagination.value?.rowsPerPage || 10,
      order: pagination.value?.descending ? 'desc' : 'asc',
      column: pagination.value?.sortBy || 'createdAt',
    });
  };

  const handleResetSearch = async () => {
    searchText.value = '';
    await listPage({
      page: 1,
      limit: pagination.value?.rowsPerPage || 10,
      order: pagination.value?.descending ? 'desc' : 'asc',
      column: pagination.value?.sortBy || 'createdAt',
    });
  };

  const updatePagination = async (event) => {
    pagination.value.descending = event.pagination?.descending;
    pagination.value.sortBy = event.pagination?.sortBy;

    await listPage({
      page: event.pagination?.page || 1,
      limit: event.pagination?.rowsPerPage || 10,
      order: event.pagination?.descending ? 'desc' : 'asc',
      column: event.pagination?.sortBy || 'createdAt',
    });
  };
  const onConsult = (row) => {
    selectedLog.value = row;
    showModal.value = true;
  };

  const closeModal = () => {
    showModal.value = false;
    selectedLog.value = null;
  };

  return {
    loading,
    rows,
    pagination,
    searchText,
    errors,
    filter: searchText,
    listPage,
    handleSearch,
    handleResetSearch,
    updatePagination,
    showModal,
    selectedLog,
    onConsult,
    closeModal,
  };
}
