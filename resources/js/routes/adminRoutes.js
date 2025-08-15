const adminHomeRoutes = {
  path: 'inicio',
  name: 'adminHome',
  component: async () => import('@pages/admin/AdminHomePage.vue'),
  meta: {
    requiresAuth: true,
    module: 'Tela Inicial',
    icon: 'space_dashboard',
    iconColor: '#344955',
    iconBg: '#FFAA30',
  },
};

export default adminHomeRoutes;
