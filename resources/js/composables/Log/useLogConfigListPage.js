import { hasPermission } from '@utils/hasPermission';
import { LOG_PERMISSION } from '@utils/permissions';
import { ref } from 'vue';

export default function useLogConfigListPage() {
  const ACTION_VIEW = 'view';
  const columns = ref([
    {
      name: 'description',
      label: 'Descrição',
      field: 'description',
      align: 'left',
      sortable: true,
    },
    {
      name: 'causer',
      label: 'Executado por',
      field: 'causer',
      align: 'left',
      sortable: true,
    },
    {
      name: 'subject',
      label: 'Afetado',
      field: 'subject',
      align: 'left',
      sortable: true,
    },
    {
      name: 'createdAt',
      label: 'Data',
      field: 'createdAt',
      align: 'left',
      sortable: true,
    },
    {
      name: 'action',
      label: 'Opções',
      align: 'center',
      field: 'id',
      methods: {
        onConsult: (row) => {
          const hasPermissionValue = hasPermission([LOG_PERMISSION.LIST]);
          const isNotView = row.event !== ACTION_VIEW;
          return hasPermissionValue && isNotView;
        },
      },
    },
  ]);

  return { columns };
}
