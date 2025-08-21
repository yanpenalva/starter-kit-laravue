import useAuthStore from '@/store/useAuthStore';
import useUserStore from '@/store/useUserStore';
import notify from '@/utils/notify';
import { storeToRefs } from 'pinia';
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';

const useUser = () => {
  const $q = useQuasar();
  const router = useRouter();
  const store = useUserStore();
  const loading = ref(false);
  const pagination = ref({});
  const searchText = ref('');
  const rows = ref([]);
  const errors = ref({});
  const confirmHandleStatus = ref(false);
  const currentRowStatus = ref(null);

  const { user } = storeToRefs(useAuthStore());

  const nameUser = computed(() => user.value?.name);

  const handleSearch = async (value) => {
    searchText.value = value;
    await listPage({
      limit: pagination.value?.rowsPerPage,
      order:
        pagination.value?.descending || pagination.value?.descending === undefined
          ? 'desc'
          : 'asc',
      column: pagination.value?.sortBy,
    });
  };

  const listPage = async (params = {}) => {
    try {
      $q.loading.show();
      loading.value = true;
      params.search = searchText.value;
      await store.list(params);
      rows.value = store.getUsers.data;
      pagination.value.rowsPerPage = store.getUsers.per_page;
      pagination.value.page = store.getUsers.current_page;
      pagination.value.rowsNumber = store.getUsers.total;
    } finally {
      loading.value = false;
      $q.loading.hide();
    }
  };

  const updatePagination = async (event) => {
    pagination.value.descending = event.pagination?.descending;
    pagination.value.sortBy = event.pagination?.sortBy;

    await listPage({
      limit: event.pagination?.rowsPerPage,
      page: event.pagination?.page,
      order:
        event.pagination?.descending || event?.pagination?.descending === undefined
          ? 'desc'
          : 'asc',
      column: event.pagination?.sortBy,
      search: '',
    });
  };

  const onRegister = async (payload) => {
    const { role, ...params } = payload;
    try {
      loading.value = true;
      errors.value = {};
      await store.register({ ...params, role: role?.value });

      notify('Cadastro realizado com sucesso! Um e-mail de confirmação foi encaminhado.');
      router.push({ name: 'login' });
    } catch {
      errors.value = store.getErrors || {};
      const firstError = Object.values(errors.value)?.[0]?.[0] || 'Erro ao cadastrar';
      notify(firstError, 'negative');
    } finally {
      loading.value = false;
    }
  };

  const onVerifyEmail = async (params) => {
    try {
      await store.verifyEmail(params);
      notify('Confirmação de cadastro realizada com sucesso');
    } finally {
      router.push({ name: 'login' });
    }
  };

  const onEdit = (event) => {
    router.push({
      name: 'editUsers',
      params: {
        id: event.id,
      },
    });
  };

  const onDelete = async (row) => {
    try {
      loading.value = true;
      await store.destroy(row.id);
      notify('Usuário excluído com sucesso');
      await listPage({
        limit: pagination.value.rowsPerPage,
        page: pagination.value.page,
      });
    } catch {
      notify('Erro ao excluir usuário', 'negative');
    } finally {
      loading.value = false;
    }
  };

  const onStatus = (payload) => {
    currentRowStatus.value = payload;

    if (payload.value) {
      confirmHandleStatus.value = true;
      return;
    }

    handleStatus(false);
  };

  // Função utilitária para normalizar/whitelist
  const sanitizeUser = (user) => ({
    id: user.id,
    name: user.name,
    email: user.email,
    role: user.role,
    active: !!user.active,
  });

  const replaceUserInRows = (rows, updated) => {
    return rows.map((row) => {
      if (row.id !== updated.id) return row;

      return {
        ...sanitizeUser(updated),
        role: row.role ?? updated.role,
      };
    });
  };

  const handleStatus = async (sendEmail) => {
    try {
      loading.value = true;
      const { data, value } = currentRowStatus.value;

      const updated = await store.update(data.id, {
        active: value ? 1 : 0,
        notify_status: sendEmail ? 1 : 0,
      });

      rows.value = replaceUserInRows(rows.value, updated);

      notify('Situação do usuário atualizada com sucesso!');
    } catch {
      notify('Erro ao atualizar situação do usuário', 'negative');
    } finally {
      loading.value = false;
      confirmHandleStatus.value = false;
    }
  };

  return {
    router,
    handleSearch,
    loading,
    rows,
    pagination,
    updatePagination,
    onRegister,
    onVerifyEmail,
    nameUser,
    errors,
    onEdit,
    onDelete,
    onStatus,
    confirmHandleStatus,
    handleStatus,
  };
};

export default useUser;
