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

  const listPage = async (params = {}) => {
    try {
      $q.loading.show();
      loading.value = true;

      const sortBy = params.column ?? pagination.value.sortBy ?? 'createdAt';
      const order =
        params.order ?? (pagination.value.descending ? 'desc' : 'asc') ?? 'desc';
      const page = params.page ?? pagination.value.page ?? 1;
      const limit = params.limit ?? pagination.value.rowsPerPage ?? 10;

      await store.list({
        search: searchText.value,
        paginated: 1,
        page,
        limit,
        column: sortBy,
        order,
      });

      const response = store.getLogs;

      rows.value = response.data ?? [];

      pagination.value = {
        page: response.current_page ?? page,
        rowsPerPage: response.per_page ?? limit,
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
    await listPage({
      page: 1,
      column: pagination.value?.sortBy ?? 'createdAt',
      order: pagination.value?.descending ? 'desc' : 'asc',
    });
  };

  const handleResetSearch = async () => {
    searchText.value = '';
    await listPage({
      page: 1,
      column: pagination.value?.sortBy ?? 'createdAt',
      order: pagination.value?.descending ? 'desc' : 'asc',
    });
  };

  const updatePagination = async (event) => {
    const paginationEvent = event?.pagination ?? event;

    const sortBy = paginationEvent?.sortBy ?? 'createdAt';
    const order = paginationEvent?.descending ? 'desc' : 'asc';
    const page = paginationEvent?.page ?? 1;
    const limit = paginationEvent?.rowsPerPage ?? 10;

    console.log(sortBy, order, page, limit);

    pagination.value = {
      sortBy,
      descending: order === 'desc',
      page: event.current_page ?? page,
      rowsPerPage: event.per_page ?? limit,
      rowsNumber: event.total ?? 0,
    };

    await listPage({ page, limit, column: sortBy, order });
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
