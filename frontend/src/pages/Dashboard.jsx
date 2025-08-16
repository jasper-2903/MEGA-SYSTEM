import React from 'react'
import { Row, Col, Card, Badge } from 'react-bootstrap'
import { useQuery } from '@tanstack/react-query'
import { useAuth } from '../hooks/useAuth'
import { dashboardService } from '../services/dashboardService'

function Dashboard() {
  const { user } = useAuth()

  const { data: dashboardData, isLoading, error } = useQuery({
    queryKey: ['dashboard'],
    queryFn: () => dashboardService.getDashboardData(),
  })

  if (isLoading) {
    return (
      <div className="loading-spinner">
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    )
  }

  if (error) {
    return (
      <div className="alert alert-danger">
        Error loading dashboard data: {error.message}
      </div>
    )
  }

  const data = dashboardData || {}

  const getRoleBasedWidgets = () => {
    const widgets = []

    // Common widgets for all roles
    widgets.push(
      <Col key="welcome" md={12} className="mb-4">
        <Card className="dashboard-card">
          <Card.Body>
            <h4>Welcome, {user?.name}!</h4>
            <p className="text-muted mb-0">
              Role: <Badge bg="primary">{user?.role}</Badge>
            </p>
          </Card.Body>
        </Card>
      </Col>
    )

    // Inventory widgets for warehouse and admin
    if (['admin', 'warehouse'].includes(user?.role)) {
      widgets.push(
        <Col key="inventory" md={3} className="mb-4">
          <Card className="dashboard-card text-center">
            <Card.Body>
              <h3 className="text-primary">{data.inventory?.total_items || 0}</h3>
              <p className="text-muted mb-0">Total Items</p>
            </Card.Body>
          </Card>
        </Col>,
        <Col key="low-stock" md={3} className="mb-4">
          <Card className="dashboard-card text-center">
            <Card.Body>
              <h3 className="text-warning">{data.inventory?.low_stock_items || 0}</h3>
              <p className="text-muted mb-0">Low Stock Items</p>
            </Card.Body>
          </Card>
        </Col>
      )
    }

    // Sales widgets for all roles except production
    if (['admin', 'planner', 'warehouse', 'customer'].includes(user?.role)) {
      widgets.push(
        <Col key="sales" md={3} className="mb-4">
          <Card className="dashboard-card text-center">
            <Card.Body>
              <h3 className="text-success">{data.sales?.total_orders || 0}</h3>
              <p className="text-muted mb-0">Total Orders</p>
            </Card.Body>
          </Card>
        </Col>,
        <Col key="revenue" md={3} className="mb-4">
          <Card className="dashboard-card text-center">
            <Card.Body>
              <h3 className="text-success">${data.sales?.total_revenue?.toLocaleString() || 0}</h3>
              <p className="text-muted mb-0">Total Revenue</p>
            </Card.Body>
          </Card>
        </Col>
      )
    }

    // Production widgets for production and admin
    if (['admin', 'production'].includes(user?.role)) {
      widgets.push(
        <Col key="production" md={3} className="mb-4">
          <Card className="dashboard-card text-center">
            <Card.Body>
              <h3 className="text-info">{data.production?.total_orders || 0}</h3>
              <p className="text-muted mb-0">Production Orders</p>
            </Card.Body>
          </Card>
        </Col>,
        <Col key="completion" md={3} className="mb-4">
          <Card className="dashboard-card text-center">
            <Card.Body>
              <h3 className="text-success">{data.production?.completion_rate || 0}%</h3>
              <p className="text-muted mb-0">Completion Rate</p>
            </Card.Body>
          </Card>
        </Col>
      )
    }

    // Purchase widgets for warehouse and admin
    if (['admin', 'warehouse'].includes(user?.role)) {
      widgets.push(
        <Col key="purchase" md={3} className="mb-4">
          <Card className="dashboard-card text-center">
            <Card.Body>
              <h3 className="text-primary">{data.purchase?.total_orders || 0}</h3>
              <p className="text-muted mb-0">Purchase Orders</p>
            </Card.Body>
          </Card>
        </Col>,
        <Col key="pending" md={3} className="mb-4">
          <Card className="dashboard-card text-center">
            <Card.Body>
              <h3 className="text-warning">{data.purchase?.pending_orders || 0}</h3>
              <p className="text-muted mb-0">Pending Orders</p>
            </Card.Body>
          </Card>
        </Col>
      )
    }

    return widgets
  }

  return (
    <div>
      <h2 className="mb-4">Dashboard</h2>
      
      <Row>
        {getRoleBasedWidgets()}
      </Row>

      {/* Recent Activity */}
      <Row className="mt-4">
        <Col md={6}>
          <Card className="dashboard-card">
            <Card.Header>
              <h5 className="mb-0">Recent Sales Orders</h5>
            </Card.Header>
            <Card.Body>
              {data.recent_sales?.length > 0 ? (
                <div>
                  {data.recent_sales.slice(0, 5).map((order) => (
                    <div key={order.id} className="d-flex justify-content-between align-items-center mb-2">
                      <div>
                        <strong>{order.so_number}</strong>
                        <br />
                        <small className="text-muted">{order.customer_name}</small>
                      </div>
                      <Badge bg={order.status === 'shipped' ? 'success' : 'warning'}>
                        {order.status}
                      </Badge>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-muted">No recent sales orders</p>
              )}
            </Card.Body>
          </Card>
        </Col>

        <Col md={6}>
          <Card className="dashboard-card">
            <Card.Header>
              <h5 className="mb-0">Recent Production Orders</h5>
            </Card.Header>
            <Card.Body>
              {data.recent_production?.length > 0 ? (
                <div>
                  {data.recent_production.slice(0, 5).map((order) => (
                    <div key={order.id} className="d-flex justify-content-between align-items-center mb-2">
                      <div>
                        <strong>{order.wo_number}</strong>
                        <br />
                        <small className="text-muted">{order.product_name}</small>
                      </div>
                      <Badge bg={order.status === 'completed' ? 'success' : 'info'}>
                        {order.status}
                      </Badge>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-muted">No recent production orders</p>
              )}
            </Card.Body>
          </Card>
        </Col>
      </Row>
    </div>
  )
}

export default Dashboard