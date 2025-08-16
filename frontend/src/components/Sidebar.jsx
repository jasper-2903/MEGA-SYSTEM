import React from 'react'
import { Nav } from 'react-bootstrap'
import { useLocation, Link } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'

function Sidebar() {
  const location = useLocation()
  const { user } = useAuth()

  const isActive = (path) => location.pathname === path

  const getMenuItems = () => {
    const items = [
      {
        path: '/dashboard',
        label: 'Dashboard',
        icon: 'bi bi-speedometer2',
        roles: ['admin', 'planner', 'warehouse', 'production', 'customer'],
      },
    ]

    // Admin and Planner can see catalog management
    if (['admin', 'planner'].includes(user?.role)) {
      items.push(
        {
          path: '/products',
          label: 'Products',
          icon: 'bi bi-box',
          roles: ['admin', 'planner'],
        },
        {
          path: '/materials',
          label: 'Materials',
          icon: 'bi bi-tools',
          roles: ['admin', 'planner'],
        }
      )
    }

    // Warehouse and Admin can see inventory
    if (['admin', 'warehouse'].includes(user?.role)) {
      items.push({
        path: '/inventory',
        label: 'Inventory',
        icon: 'bi bi-boxes',
        roles: ['admin', 'warehouse'],
      })
    }

    // Sales orders for all roles except production
    if (['admin', 'planner', 'warehouse', 'customer'].includes(user?.role)) {
      items.push({
        path: '/sales-orders',
        label: 'Sales Orders',
        icon: 'bi bi-cart',
        roles: ['admin', 'planner', 'warehouse', 'customer'],
      })
    }

    // Production orders for production and admin
    if (['admin', 'production'].includes(user?.role)) {
      items.push({
        path: '/production-orders',
        label: 'Production Orders',
        icon: 'bi bi-gear',
        roles: ['admin', 'production'],
      })
    }

    // Purchase orders for warehouse and admin
    if (['admin', 'warehouse'].includes(user?.role)) {
      items.push({
        path: '/purchase-orders',
        label: 'Purchase Orders',
        icon: 'bi bi-truck',
        roles: ['admin', 'warehouse'],
      })
    }

    // Reports for admin and planner
    if (['admin', 'planner'].includes(user?.role)) {
      items.push({
        path: '/reports',
        label: 'Reports',
        icon: 'bi bi-graph-up',
        roles: ['admin', 'planner'],
      })
    }

    // Forecasts for admin and planner
    if (['admin', 'planner'].includes(user?.role)) {
      items.push({
        path: '/forecasts',
        label: 'Forecasts',
        icon: 'bi bi-calendar-check',
        roles: ['admin', 'planner'],
      })
    }

    return items.filter(item => item.roles.includes(user?.role))
  }

  return (
    <div className="sidebar" style={{ width: '250px' }}>
      <div className="p-3">
        <h5 className="text-white mb-4">Menu</h5>
        <Nav className="flex-column">
          {getMenuItems().map((item) => (
            <Nav.Link
              key={item.path}
              as={Link}
              to={item.path}
              className={isActive(item.path) ? 'active' : ''}
            >
              <i className={item.icon}></i>
              {item.label}
            </Nav.Link>
          ))}
        </Nav>
      </div>
    </div>
  )
}

export default Sidebar