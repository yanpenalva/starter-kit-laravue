import { hasPermission } from '@utils/hasPermission';
import { USER_PERMISSION } from '@utils/permissions';
import { ref } from 'vue';

import useAuthStore from '@/store/useAuthStore';
import { storeToRefs } from 'pinia';

export default function useUserConfigListPage() {
  const { user } = storeToRefs(useAuthStore());

  const columns = ref([
    { name: 'id', align: 'left', label: 'ID', field: 'id', sortable: true },
    {
      name: 'name',
      required: true,
      label: 'Nome',
      align: 'left',
      field: 'name',
      format: (val) => `${val}`,
      sortable: true,
    },
    {
      name: 'email',
      label: 'E-mail',
      field: 'email',
      align: 'left',
      sortable: true,
    },
    {
      name: 'role',
      label: 'Perfil',
      align: 'left',
      field: 'role',
      sortable: true,
    },
    {
      name: 'setSituation',
      required: true,
      label: 'Situação',
      align: 'left',
      sortable: true,
      field: (row) => row.active,
      format: (val) => `${val}`,
    },
    {
      name: 'action',
      label: 'Opções',
      align: 'center',
      field: (row) => row.id,
      format: (val) => `${val}`,
      methods: {
        onConsult: () => hasPermission([USER_PERMISSION.SHOW]),
        onEdit: hasPermission([USER_PERMISSION.UPDATE]),
        onDelete: (row) => {
          return row.id !== user.value?.id && hasPermission([USER_PERMISSION.DELETE]);
        },
      },
    },
  ]);

  return {
    columns,
  };
}
