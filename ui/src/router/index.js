import { createRouter, createWebHashHistory} from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'capture',
    component: () => import(/* webpackChunkName: "capture" */ '../views/CaptureTable.vue'),
  },
  {
    path: '/conversion',
    name: 'conversion',
    component: () => import(/* webpackChunkName: "aggregation" */ '../views/ConversionPage.vue'),
  },
  {
    path: '/aggregation',
    name: 'aggregation',
    component: () => import(/* webpackChunkName: "aggregation" */ '../views/AggregationTable.vue'),
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
