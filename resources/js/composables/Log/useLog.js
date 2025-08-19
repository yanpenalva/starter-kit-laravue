import useLogStore from '@/store/useLogStore';
import { useQuasar } from 'quasar';
import { ref } from 'vue';

export default function useLog() {
  const $q = useQuasar();
  const store = useLogStore();
  const loading = ref(false);
  const pagination = ref({
    sortBy: 'id',
    descending: true,
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 0,
  });
  const searchText = ref('');
  const rows = ref([]);
  const errors = ref({});

  const columnResolver = new Map([
    ['id', 'id'],
    ['eventPt', 'event'],
    ['description', 'description'],
    ['causer', 'causer'],
    ['subject', 'subject'],
    ['createdAt', 'created_at'],
  ]);

  const resolveColumn = (sortBy) => {
    return columnResolver.has(sortBy) ? columnResolver.get(sortBy) : 'id';
  };

  const listPage = async (params = {}) => {
    try {
      $q.loading.show();
      loading.value = true;

      const sortBy = params.column ?? pagination.value.sortBy ?? 'id';
      const order = params.order ?? (pagination.value.descending ? 'desc' : 'asc');
      const page = params.page ?? pagination.value.page ?? 1;
      const limit = params.limit ?? pagination.value.rowsPerPage ?? 10;

      const column = resolveColumn(sortBy);

      await store.list({
        search: searchText.value,
        paginated: 1,
        page,
        limit,
        column,
        order,
      });

      const response = store.getLogs;

      rows.value = response.data ?? [];
      pagination.value = {
        rowsPerPage: response.per_page ?? limit,
        page: response.current_page ?? page,
        rowsNumber: response.total ?? 0,
        sortBy,
        descending: order === 'desc',
      };
    } finally {
      loading.value = false;
      $q.loading.hide();
    }
  };

  const handleSearch = async (value) => {
    searchText.value = value ?? '';
    await listPage({ page: 1 });
  };

  const handleResetSearch = async () => {
    searchText.value = '';
    await listPage({ page: 1 });
  };

  const updatePagination = async (event) => {
    const paginationEvent = event?.pagination ?? event;
    await listPage({
      column: paginationEvent?.sortBy,
      order: paginationEvent?.descending ? 'desc' : 'asc',
      page: paginationEvent?.page,
      limit: paginationEvent?.rowsPerPage,
    });
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
  };
}
