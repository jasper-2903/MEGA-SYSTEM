import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import { ToastContainer } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'

import { AuthProvider } from './contexts/AuthContext'
import ProtectedRoute from './components/ProtectedRoute'

import AdminLayout from './layouts/AdminLayout'
import CustomerLayout from './layouts/CustomerLayout'

import AdminDashboard from './pages/admin/AdminDashboard'
import AdminPlaceholder from './pages/admin/AdminPlaceholder'
import Login from './pages/auth/Login'
import Register from './pages/auth/Register'
import Home from './pages/home/Home'

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route element={<CustomerLayout />}> 
            <Route index element={<Home />} />
            <Route path="shop" element={<AdminPlaceholder title="Shop - Product Listing" />} />
            <Route path="cart" element={<AdminPlaceholder title="Shopping Cart" />} />
            <Route path="my-orders" element={<AdminPlaceholder title="My Orders" />} />
          </Route>

          <Route element={<ProtectedRoute roles={["admin"]} />}> 
            <Route path="/admin" element={<AdminLayout />}> 
              <Route index element={<AdminDashboard />} />
              <Route path="inventory" element={<AdminPlaceholder title="Inventory" />} />
              <Route path="production" element={<AdminPlaceholder title="Production" />} />
              <Route path="orders" element={<AdminPlaceholder title="Orders" />} />
              <Route path="reports" element={<AdminPlaceholder title="Reports" />} />
            </Route>
          </Route>

          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />

          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
        <ToastContainer position="top-right" autoClose={2500} hideProgressBar newestOnTop closeOnClick pauseOnHover theme="colored" />
      </BrowserRouter>
    </AuthProvider>
  )
}

export default App
