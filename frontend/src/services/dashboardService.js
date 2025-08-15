import api from './authService'

export const dashboardService = {
  async getDashboardData() {
    const response = await api.get('/dashboard')
    return response.data
  },
}