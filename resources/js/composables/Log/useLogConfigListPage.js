import { hasPermission } from '@utils/hasPermission';
import { LOG_PERMISSION } from '@utils/permissions';
import { ref } from 'vue';

export default function useLogConfigListPage() {
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
      format: (val) => val?.name ?? '-',
      align: 'left',
      sortable: true,
    },
    {
      name: 'subject',
      label: 'Afetado',
      field: 'subject',
      format: (val) => val?.name ?? '-',
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
        onConsult: hasPermission([LOG_PERMISSION.LIST]),
      },
    },
  ]);

  return { columns };
}
