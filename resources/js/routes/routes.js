import AccessDeniedPage from '@/pages/errors/AccessDeniedPage.vue';
import NotFoundPage from '@/pages/errors/NotFoundPage.vue';
import adminHomeRoutes from '@/routes/adminRoutes';
import logRoutes from '@/routes/logRoutes';
import publicRoutes from '@/routes/publicRoutes/publicRoutes';
import roleRoutes from '@/routes/roleRoutes';
import userRoutes from '@/routes/userRoutes';
import AdminLayout from '@layouts/AdminLayout.vue';
import MainLayout from '@layouts/MainLayout.vue';

const routes = [
  {
    path: '/',
    name: 'main',
    component: MainLayout,
    children: publicRoutes,
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        ...adminHomeRoutes,
      },
      {
        path: 'users',
        children: userRoutes.children,
      },
      {
        path: 'profiles',
        children: roleRoutes.children,
      },
      {
        path: 'logs',
        children: logRoutes.children,
      },
      {
        path: 'notFound',
        name: 'admin-notFound',
        component: NotFoundPage,
        meta: {
          requiresAuth: true,
          module: 'Error',
        },
      },
      {
        path: 'accessDenied',
        name: 'accessDenied',
        component: AccessDeniedPage,
        meta: {
          requiresAuth: true,
          module: 'Error',
        },
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'notFound',
    component: NotFoundPage,
  },
];

export default routes;
