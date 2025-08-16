import React from 'react'
import { Routes, Route, Navigate } from 'react-router-dom'
import { useAuth } from './hooks/useAuth'
import Layout from './components/Layout'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import Products from './pages/Products'
import Materials from './pages/Materials'
import Inventory from './pages/Inventory'
import SalesOrders from './pages/SalesOrders'
import ProductionOrders from './pages/ProductionOrders'
import PurchaseOrders from './pages/PurchaseOrders'
import Reports from './pages/Reports'
import Forecasts from './pages/Forecasts'
import Profile from './pages/Profile'

function App() {
  const { user, loading } = useAuth()

  if (loading) {
    return (
      <div className="loading-spinner">
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    )
  }

  if (!user) {
    return <Login />
  }

  return (
    <Layout>
      <Routes>
        <Route path="/" element={<Navigate to="/dashboard" replace />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/products" element={<Products />} />
        <Route path="/materials" element={<Materials />} />
        <Route path="/inventory" element={<Inventory />} />
        <Route path="/sales-orders" element={<SalesOrders />} />
        <Route path="/production-orders" element={<ProductionOrders />} />
        <Route path="/purchase-orders" element={<PurchaseOrders />} />
        <Route path="/reports" element={<Reports />} />
        <Route path="/forecasts" element={<Forecasts />} />
        <Route path="/profile" element={<Profile />} />
        <Route path="*" element={<Navigate to="/dashboard" replace />} />
      </Routes>
    </Layout>
  )
}

export default App