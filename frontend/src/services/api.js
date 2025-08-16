import axios from 'axios'
import { tokenStorageKey } from '../utils/authToken'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

export const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem(tokenStorageKey)
  if (token) {
    config.headers = config.headers || {}
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error?.response?.status === 401) {
      // Optional: clear token and redirect; leave for app-level handling
      // localStorage.removeItem(tokenStorageKey)
    }
    return Promise.reject(error)
  }
)

const endpoints = {
  login: '/api/v1/auth/login',
  register: '/api/v1/auth/register',
  logout: '/api/v1/auth/logout',
  me: '/api/v1/auth/me',
  inventory: '/api/v1/inventory',
  production: '/api/v1/production',
  orders: '/api/v1/orders',
  products: '/api/v1/products',
  reports: '/api/v1/reports',
  dashboardMetrics: '/api/v1/dashboard/metrics',
  dashboardCharts: '/api/v1/dashboard/charts',
}

function buildQuery(params = {}) {
  const query = new URLSearchParams()
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      if (Array.isArray(value)) {
        value.forEach((v) => query.append(`${key}[]`, v))
      } else {
        query.append(key, String(value))
      }
    }
  })
  return query.toString() ? `?${query.toString()}` : ''
}

export const AuthAPI = {
  async login(credentials) {
    const { data } = await api.post(endpoints.login, credentials)
    return data
  },
  async register(payload) {
    const { data } = await api.post(endpoints.register, payload)
    return data
  },
  async logout() {
    const { data } = await api.post(endpoints.logout)
    return data
  },
  async me() {
    const { data } = await api.get(endpoints.me)
    return data
  },
}

export const InventoryAPI = {
  async list(params) {
    const { data } = await api.get(`${endpoints.inventory}${buildQuery(params)}`)
    return data
  },
  async getById(id) {
    const { data } = await api.get(`${endpoints.inventory}/${id}`)
    return data
  },
  async create(payload) {
    const { data } = await api.post(endpoints.inventory, payload)
    return data
  },
  async update(id, payload) {
    const { data } = await api.put(`${endpoints.inventory}/${id}`, payload)
    return data
  },
  async remove(id) {
    const { data } = await api.delete(`${endpoints.inventory}/${id}`)
    return data
  },
}

export const ProductionAPI = {
  async list(params) {
    const { data } = await api.get(`${endpoints.production}${buildQuery(params)}`)
    return data
  },
  async getById(id) {
    const { data } = await api.get(`${endpoints.production}/${id}`)
    return data
  },
  async create(payload) {
    const { data } = await api.post(endpoints.production, payload)
    return data
  },
  async update(id, payload) {
    const { data } = await api.put(`${endpoints.production}/${id}`, payload)
    return data
  },
  async updateStage(id, stagePayload) {
    const { data } = await api.patch(`${endpoints.production}/${id}/stage`, stagePayload)
    return data
  },
  async remove(id) {
    const { data } = await api.delete(`${endpoints.production}/${id}`)
    return data
  },
}

export const OrdersAPI = {
  async list(params) {
    const { data } = await api.get(`${endpoints.orders}${buildQuery(params)}`)
    return data
  },
  async my(params) {
    const { data } = await api.get(`${endpoints.orders}/my${buildQuery(params)}`)
    return data
  },
  async getById(id) {
    const { data } = await api.get(`${endpoints.orders}/${id}`)
    return data
  },
  async create(payload) {
    const { data } = await api.post(endpoints.orders, payload)
    return data
  },
  async update(id, payload) {
    const { data } = await api.put(`${endpoints.orders}/${id}`, payload)
    return data
  },
  async changeStatus(id, statusPayload) {
    const { data } = await api.patch(`${endpoints.orders}/${id}/status`, statusPayload)
    return data
  },
  async remove(id) {
    const { data } = await api.delete(`${endpoints.orders}/${id}`)
    return data
  },
}

export const ProductsAPI = {
  async list(params) {
    const { data } = await api.get(`${endpoints.products}${buildQuery(params)}`)
    return data
  },
  async getById(id) {
    const { data } = await api.get(`${endpoints.products}/${id}`)
    return data
  },
}

export const ReportsAPI = {
  async downloadPdf(slug, params) {
    const url = `${endpoints.reports}/${slug}/pdf${buildQuery(params)}`
    const { data } = await api.get(url, { responseType: 'blob' })
    return data
  },
  async downloadExcel(slug, params) {
    const url = `${endpoints.reports}/${slug}/excel${buildQuery(params)}`
    const { data } = await api.get(url, { responseType: 'blob' })
    return data
  },
}

export const DashboardAPI = {
  async getMetrics() {
    const { data } = await api.get(endpoints.dashboardMetrics)
    return data
  },
  async getCharts() {
    const { data } = await api.get(endpoints.dashboardCharts)
    return data
  },
}