import { Link } from 'react-router-dom'

export default function Home() {
  return (
    <div>
      <div className="p-4 p-md-5 mb-4 bg-white rounded-3 border">
        <div className="container-fluid py-5">
          <h1 className="display-6 fw-semibold">Premium Furniture for Every Space</h1>
          <p className="col-md-8 fs-6 text-muted">Explore our curated collection of quality furniture with robust production and inventory backing. Experience fast order processing and dependable delivery.</p>
          <Link className="btn btn-primary btn-lg" to="/shop">Shop Now</Link>
        </div>
      </div>
      <div className="row g-3">
        <div className="col-12 col-md-4">
          <div className="card h-100 shadow-sm">
            <div className="card-body">
              <h5 className="card-title">Inventory</h5>
              <p className="card-text text-muted">Administer product inventory, SKUs, and stock levels.</p>
              <Link to="/admin/inventory" className="btn btn-outline-primary btn-sm">Go</Link>
            </div>
          </div>
        </div>
        <div className="col-12 col-md-4">
          <div className="card h-100 shadow-sm">
            <div className="card-body">
              <h5 className="card-title">Production</h5>
              <p className="card-text text-muted">Track production stages and improve efficiency.</p>
              <Link to="/admin/production" className="btn btn-outline-primary btn-sm">Go</Link>
            </div>
          </div>
        </div>
        <div className="col-12 col-md-4">
          <div className="card h-100 shadow-sm">
            <div className="card-body">
              <h5 className="card-title">Orders</h5>
              <p className="card-text text-muted">Manage orders, statuses, and fulfillment.</p>
              <Link to="/admin/orders" className="btn btn-outline-primary btn-sm">Go</Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}