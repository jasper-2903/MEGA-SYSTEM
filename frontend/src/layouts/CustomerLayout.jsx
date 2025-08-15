import { Link, NavLink, Outlet } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'

export default function CustomerLayout() {
  const { user, isAuthenticated, logout } = useAuth()

  return (
    <div className="min-vh-100 d-flex flex-column">
      <nav className="navbar navbar-expand-lg bg-white border-bottom">
        <div className="container">
          <Link className="navbar-brand fw-semibold text-primary" to="/">Furni</Link>
          <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span className="navbar-toggler-icon"></span>
          </button>
          <div className="collapse navbar-collapse" id="navbarContent">
            <ul className="navbar-nav me-auto mb-2 mb-lg-0">
              <li className="nav-item">
                <NavLink to="/" end className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>Home</NavLink>
              </li>
              <li className="nav-item">
                <NavLink to="/shop" className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>Shop</NavLink>
              </li>
              <li className="nav-item">
                <NavLink to="/my-orders" className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>My Orders</NavLink>
              </li>
            </ul>
            <div className="d-flex align-items-center gap-2">
              <NavLink to="/cart" className="btn btn-outline-primary btn-sm"><i className="bi bi-cart3 me-1"></i> Cart</NavLink>
              {isAuthenticated ? (
                <>
                  <span className="small text-muted">Hi, {user?.name || 'Customer'}</span>
                  <button onClick={logout} className="btn btn-outline-secondary btn-sm">Logout</button>
                </>
              ) : (
                <>
                  <NavLink to="/login" className="btn btn-primary btn-sm">Login</NavLink>
                  <NavLink to="/register" className="btn btn-outline-primary btn-sm">Register</NavLink>
                </>
              )}
            </div>
          </div>
        </div>
      </nav>
      <main className="flex-grow-1 bg-body-secondary">
        <div className="container py-4">
          <Outlet />
        </div>
      </main>
      <footer className="bg-white border-top small py-3">
        <div className="container d-flex justify-content-between">
          <span>© {new Date().getFullYear()} Furni</span>
          <a href="#" className="text-decoration-none">Privacy</a>
        </div>
      </footer>
    </div>
  )
}