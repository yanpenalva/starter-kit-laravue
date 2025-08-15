import { USER_PERMISSION } from '@utils/permissions';

const userRoutes = {
  children: [
    {
      path: '',
      name: 'listUsers',
      component: async () => import('@pages/admin/users/ListPage.vue'),
      meta: {
        requiresAuth: true,
        module: 'Usuários',
        icon: 'people_alt',
        iconColor: '#344955',
        iconBg: '#FFAA30',
        roles: [USER_PERMISSION.LIST],
      },
    },
    {
      path: 'edit/:id',
      name: 'editUsers',
      component: async () => import('@pages/admin/users/EditPage.vue'),
      meta: {
        requiresAuth: true,
        module: 'Usuários',
        icon: 'people_alt',
        iconColor: '#344955',
        iconBg: '#FFAA30',
        roles: [USER_PERMISSION.UPDATE],
      },
    },
    {
      path: 'create',
      name: 'createUsers',
      component: async () => import('@pages/admin/users/CreatePage.vue'),
      meta: {
        requiresAuth: true,
        module: 'Usuários',
        icon: 'people_alt',
        iconColor: '#344955',
        iconBg: '#FFAA30',
        roles: [USER_PERMISSION.CREATE],
      },
    },
    {
      path: 'show/:id',
      name: 'showUsers',
      component: () => import('@pages/admin/users/ShowPage.vue'),
      meta: {
        requiresAuth: true,
        module: 'Usuários',
        icon: 'people_alt',
        iconColor: '#344955',
        iconBg: '#FFAA30',
        roles: [USER_PERMISSION.SHOW],
      },
    },
  ],
};

export default userRoutes;
