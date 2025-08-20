import useAuthStore from '@/store/useAuthStore';
import usePermissionStore from '@/store/usePermissionStore';
import useRoleStore from '@/store/useRoleStore';
import notify from '@/utils/notify';
import { ROLES } from '@/utils/roles';
import { storeToRefs } from 'pinia';
import { useQuasar } from 'quasar';
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const useRole = () => {
  const $q = useQuasar();
  const router = useRouter();
  const route = useRoute();
  const searchText = ref('');
  const { role, roles, pagination, loading, errors, message, isSuccess } =
    storeToRefs(useRoleStore());
  const store = useRoleStore();
  const permissionStore = usePermissionStore();
  const authStore = useAuthStore();
  const rows = ref([]);
  const selectedPermissionIds = ref([]);
  const initialRoleSetup = ref(false);

  watch(
    () => role.value,
    (newRole) => {
      if (!newRole || initialRoleSetup.value) return;

      const permissions = newRole.permissions || [];

      permissions.forEach((permission) => {
        const permissionId =
          typeof permission === 'object' ? permission.value : permission;

        if (
          typeof permissionId === 'number' &&
          !selectedPermissionIds.value.includes(permissionId)
        ) {
          selectedPermissionIds.value.push(permissionId);
        }
      });

      initialRoleSetup.value = true;
    },
    { immediate: true, deep: true },
  );

  const shouldBlockDeleteRoleUserAuth = (idRoleRow) => {
    return !authStore.getRoles.find(({ id }) => id == idRoleRow);
  };

  const shouldBlockSelectPermission = computed(() => {
    return role.value?.slug === ROLES.ADMINISTRATOR;
  });

  const isProtectedRole = (slugRoleRow) => {
    return slugRoleRow === ROLES.ADMINISTRATOR;
  };

  const handleSearch = async (value) => {
    searchText.value = value;
    await listPage({
      limit: pagination?.rowsPerPage,
      page: pagination?.page,
      order:
        pagination?.descending || pagination?.descending == undefined ? 'desc' : 'asc',
      column: pagination?.sortBy,
    });
  };

  const shouldBlockEditRoleAdmin = (slugRoleRow) => {
    return slugRoleRow === ROLES.ADMINISTRATOR;
  };

  const shouldBlockDeleteProtectedRole = (idRoleRow) => {
    return isProtectedRole(idRoleRow);
  };

  const togglePermission = (permissionId) => {
    if (shouldBlockSelectPermission.value) return;

    const index = selectedPermissionIds.value.indexOf(permissionId);

    index === -1
      ? selectedPermissionIds.value.push(permissionId)
      : selectedPermissionIds.value.splice(index, 1);
  };

  const saveRole = async () => {
    $q.loading.show();

    const params = {
      name: role.value?.name,
      description: role.value?.description,
      permissions: selectedPermissionIds.value.map((id) => ({ value: id })),
    };

    try {
      const hasId = Boolean(route.params.id);
      const action = hasId ? store.update(route.params.id, params) : store.store(params);

      await action;

      const color = isSuccess.value ? 'positive' : 'negative';
      $q.notify({ message: message.value, color, position: 'top-right' });

      isSuccess.value && router.push({ name: 'listRoles' });
    } catch (error) {
      console.error('Erro ao salvar perfil:', error);
    } finally {
      $q.loading.hide();
    }
  };

  const initializeRoleData = async () => {
    store.resetStore();
    await permissionStore.fetchPermissions();
    initialRoleSetup.value = false;
  };

  const cleanupRoleData = () => {
    store.resetStore();
    selectedPermissionIds.value = [];
    initialRoleSetup.value = false;
  };

  async function updatePagination(event) {
    try {
      $q.loading.show();
      pagination.value.descending = event.pagination?.descending;
      pagination.value.sortBy = event.pagination?.sortBy;

      await listPage({
        limit: event.pagination?.rowsPerPage,
        page: event.pagination?.page,
        order:
          event.pagination?.descending || event?.pagination?.descending == undefined
            ? 'desc'
            : 'asc',
        column: event.pagination?.sortBy,
        search: '',
      });
    } finally {
      $q.loading.hide();
    }
  }

  const listPage = async (params = {}) => {
    try {
      $q.loading.show();
      loading.value = true;
      params.search = searchText.value;
      await store.list(params);
      rows.value = store.getRolesRows.roles;
      pagination.value.rowsPerPage = store.getRolesRows.pagination.per_page;
      pagination.value.page = store.getRolesRows.pagination.current_page;
      pagination.value.rowsNumber = store.getRolesRows.pagination.total;
    } finally {
      loading.value = false;
      $q.loading.hide();
    }
  };

  const onEdit = (event) => {
    router.push({
      name: 'editRoles',
      params: {
        id: event.id,
      },
    });
  };

  const onDelete = async (event) => {
    try {
      $q.loading.show();
      await store.destroy(event.id);

      notify('Perfil excluído com sucesso!');
    } finally {
      $q.loading.hide();

      await updatePagination({ ...{ pagination: pagination.value } });
    }
  };

  const isPermissionGroupFullySelected = (permissionGroup) => {
    return permissionGroup.permissions.every((permissionItem) =>
      selectedPermissionIds.value.includes(permissionItem.value),
    );
  };

  const togglePermissionGroup = (permissionGroup) => {
    const allPermissionsSelected = isPermissionGroupFullySelected(permissionGroup);

    permissionGroup.permissions.forEach((permissionItem) => {
      const permissionAlreadySelected = selectedPermissionIds.value.includes(
        permissionItem.value,
      );

      if (!allPermissionsSelected && !permissionAlreadySelected) {
        selectedPermissionIds.value.push(permissionItem.value);
      }

      if (allPermissionsSelected && permissionAlreadySelected) {
        selectedPermissionIds.value = selectedPermissionIds.value.filter(
          (permissionId) => permissionId !== permissionItem.value,
        );
      }
    });
  };

  return {
    role,
    roles,
    pagination,
    loading,
    errors,
    message,
    isSuccess,
    rows,
    selectedPermissionIds,
    shouldBlockSelectPermission,
    shouldBlockDeleteRoleUserAuth,
    shouldBlockEditRoleAdmin,
    shouldBlockDeleteProtectedRole,
    togglePermission,
    saveRole,
    handleSearch,
    initializeRoleData,
    cleanupRoleData,
    updatePagination,
    onEdit,
    onDelete,
    isPermissionGroupFullySelected,
    togglePermissionGroup,
  };
};

export default useRole;
