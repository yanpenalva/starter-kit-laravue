import { LOG_PERMISSION } from '@utils/permissions';

const logRoutes = {
  children: [
    {
      path: '',
      name: 'listLogs',
      component: async () => import('@pages/admin/logs/ListPage.vue'),
      meta: {
        requiresAuth: true,
        module: 'Histórico de Registros',
        icon: 'history',
        iconColor: '#344955',
        iconBg: '#FFAA30',
        roles: [LOG_PERMISSION.LIST],
      },
    },
  ],
};

export default logRoutes;
