import { createRouter, createWebHashHistory} from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'capture',
    component: () => import(/* webpackChunkName: "capture" */ '../components/CaptureTable.vue'),
  },
  {
    path: '/settings',
    name: 'settings',
    component: () => import(/* webpackChunkName: "settings" */ '../views/SettingsPage.vue'),
  },
  {
    path: '/audit',
    name: 'audit',
    component: () => import(/* webpackChunkName: "audit" */ '../views/AuditPage.vue'),
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  //history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
