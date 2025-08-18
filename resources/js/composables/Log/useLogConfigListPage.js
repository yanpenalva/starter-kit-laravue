import { hasPermission } from '@utils/hasPermission';
import { LOG_PERMISSION } from '@utils/permissions';
import { ref } from 'vue';

export default function useLogConfigListPage() {
  const columns = ref([
    { name: 'id', align: 'left', label: 'ID', field: 'id', sortable: true },
    { name: 'eventPt', label: 'Ação', field: 'eventPt', align: 'left', sortable: true },
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
      field: (row) => row.causer?.name ?? '-',
      align: 'left',
      sortable: false,
    },
    {
      name: 'subject',
      label: 'Afetado',
      field: (row) => row.subject?.name ?? '-',
      align: 'left',
      sortable: false,
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
      field: (row) => row.id,
      methods: {
        onConsult: hasPermission([LOG_PERMISSION.LIST]),
      },
    },
  ]);

  return { columns };
}
