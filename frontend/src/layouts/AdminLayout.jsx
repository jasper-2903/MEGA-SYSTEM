import { Link, NavLink, Outlet } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'

export default function AdminLayout() {
  const { user, logout } = useAuth()

  return (
    <div className="d-flex">
      <nav className="d-flex flex-column flex-shrink-0 p-3 bg-light" style={{ width: 260, minHeight: '100vh' }}>
        <Link to="/admin" className="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
          <span className="fs-5 fw-bold text-primary">Furni Admin</span>
        </Link>
        <hr />
        <ul className="nav nav-pills flex-column mb-auto">
          <li className="nav-item">
            <NavLink to="/admin" end className={({ isActive }) => `nav-link ${isActive ? 'active' : 'link-dark'}`}>
              <i className="bi bi-speedometer2 me-2"></i> Dashboard
            </NavLink>
          </li>
          <li>
            <NavLink to="/admin/inventory" className={({ isActive }) => `nav-link ${isActive ? 'active' : 'link-dark'}`}>
              <i className="bi bi-box-seam me-2"></i> Inventory
            </NavLink>
          </li>
          <li>
            <NavLink to="/admin/production" className={({ isActive }) => `nav-link ${isActive ? 'active' : 'link-dark'}`}>
              <i className="bi bi-gear-wide-connected me-2"></i> Production
            </NavLink>
          </li>
          <li>
            <NavLink to="/admin/orders" className={({ isActive }) => `nav-link ${isActive ? 'active' : 'link-dark'}`}>
              <i className="bi bi-bag-check me-2"></i> Orders
            </NavLink>
          </li>
          <li>
            <NavLink to="/admin/reports" className={({ isActive }) => `nav-link ${isActive ? 'active' : 'link-dark'}`}>
              <i className="bi bi-graph-up-arrow me-2"></i> Reports
            </NavLink>
          </li>
        </ul>
        <hr />
        <div className="dropdown">
          <a href="#" className="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src={`https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=${encodeURIComponent(user?.name || 'Admin')}`} alt="avatar" width="32" height="32" className="rounded-circle me-2" />
            <strong>{user?.name || 'Admin'}</strong>
          </a>
          <ul className="dropdown-menu text-small shadow">
            <li><button className="dropdown-item" onClick={logout}>Sign out</button></li>
          </ul>
        </div>
      </nav>

      <div className="flex-grow-1">
        <nav className="navbar navbar-expand navbar-light bg-white border-bottom">
          <div className="container-fluid">
            <span className="navbar-brand mb-0 h6">Admin Panel</span>
            <div className="d-flex">
              <Link to="/" className="btn btn-outline-secondary btn-sm">
                <i className="bi bi-house me-1"></i> Public Site
              </Link>
            </div>
          </div>
        </nav>
        <main className="container-fluid p-4 bg-body-secondary" style={{ minHeight: 'calc(100vh - 56px)' }}>
          <Outlet />
        </main>
      </div>
    </div>
  )
}