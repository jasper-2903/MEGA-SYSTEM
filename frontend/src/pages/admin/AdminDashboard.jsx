import { useEffect, useState } from 'react'
import { DashboardAPI } from '../../services/api'
import { Line, Bar, Pie } from 'react-chartjs-2'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  Tooltip,
  Legend,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, ArcElement, Tooltip, Legend)

export default function AdminDashboard() {
  const [metrics, setMetrics] = useState(null)
  const [charts, setCharts] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    let isMounted = true
    async function load() {
      try {
        const [m, c] = await Promise.all([DashboardAPI.getMetrics(), DashboardAPI.getCharts()])
        if (!isMounted) return
        setMetrics(m)
        setCharts(c)
      } catch {
        // noop: could show toast
      } finally {
        if (isMounted) setLoading(false)
      }
    }
    load()
    return () => { isMounted = false }
  }, [])

  const metricCards = [
    { title: 'Inventory Count', value: metrics?.inventoryCount ?? '-', icon: 'bi-box-seam', color: 'primary' },
    { title: 'Orders', value: metrics?.ordersCount ?? '-', icon: 'bi-bag', color: 'success' },
    { title: 'Production Output', value: metrics?.productionOutput ?? '-', icon: 'bi-gear-wide-connected', color: 'warning' },
    { title: 'Sales', value: metrics?.salesTotal ?? '-', icon: 'bi-currency-dollar', color: 'danger' },
  ]

  const lineData = {
    labels: charts?.inventoryTrend?.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [
      {
        label: 'Inventory Trend',
        data: charts?.inventoryTrend?.data || [100, 120, 150, 130, 160, 180],
        borderColor: '#0d6efd',
        backgroundColor: 'rgba(13, 110, 253, 0.2)',
        tension: 0.3,
        fill: true,
      },
    ],
  }

  const barData = {
    labels: charts?.productionEfficiency?.labels || ['Cutting', 'Assembly', 'Finishing', 'QC'],
    datasets: [
      {
        label: 'Efficiency %',
        data: charts?.productionEfficiency?.data || [85, 92, 78, 88],
        backgroundColor: '#198754',
      },
    ],
  }

  const pieData = {
    labels: charts?.orderDistribution?.labels || ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
    datasets: [
      {
        data: charts?.orderDistribution?.data || [12, 19, 7, 30, 3],
        backgroundColor: ['#0d6efd', '#6f42c1', '#198754', '#ffc107', '#dc3545'],
      },
    ],
  }

  return (
    <div className="container-fluid">
      <div className="d-flex align-items-center justify-content-between mb-4">
        <h5 className="mb-0">Dashboard Overview</h5>
      </div>

      {loading && (
        <div className="text-center py-5">
          <div className="spinner-border text-primary" role="status"></div>
        </div>
      )}

      {!loading && (
        <>
          <div className="row g-3 mb-4">
            {metricCards.map((m) => (
              <div key={m.title} className="col-12 col-sm-6 col-lg-3">
                <div className="card shadow-sm h-100">
                  <div className="card-body d-flex align-items-center">
                    <div className={`rounded-circle bg-${m.color} bg-opacity-10 text-${m.color} d-flex align-items-center justify-content-center me-3`} style={{ width: 48, height: 48 }}>
                      <i className={`bi ${m.icon}`}></i>
                    </div>
                    <div>
                      <div className="text-muted small">{m.title}</div>
                      <div className="fs-5 fw-semibold">{m.value}</div>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>

          <div className="row g-3">
            <div className="col-12 col-lg-6">
              <div className="card shadow-sm h-100">
                <div className="card-header bg-white">
                  <strong>Inventory Trends</strong>
                </div>
                <div className="card-body">
                  <Line data={lineData} options={{ responsive: true, maintainAspectRatio: false }} height={240} />
                </div>
              </div>
            </div>
            <div className="col-12 col-lg-6">
              <div className="card shadow-sm h-100">
                <div className="card-header bg-white">
                  <strong>Production Efficiency</strong>
                </div>
                <div className="card-body">
                  <Bar data={barData} options={{ responsive: true, maintainAspectRatio: false }} height={240} />
                </div>
              </div>
            </div>
          </div>

          <div className="row g-3 mt-1">
            <div className="col-12 col-lg-6">
              <div className="card shadow-sm h-100">
                <div className="card-header bg-white">
                  <strong>Order Distribution</strong>
                </div>
                <div className="card-body">
                  <Pie data={pieData} options={{ responsive: true, maintainAspectRatio: false }} height={240} />
                </div>
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  )
}