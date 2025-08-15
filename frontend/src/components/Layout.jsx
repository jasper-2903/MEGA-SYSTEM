import React, { useState } from 'react'
import { Navbar, Nav, Container, Offcanvas } from 'react-bootstrap'
import { useAuth } from '../hooks/useAuth'
import Sidebar from './Sidebar'

function Layout({ children }) {
  const { user, logout } = useAuth()
  const [sidebarShow, setSidebarShow] = useState(false)

  const handleLogout = () => {
    logout()
  }

  return (
    <div className="d-flex">
      {/* Sidebar */}
      <div className="d-none d-md-block">
        <Sidebar />
      </div>

      {/* Mobile Sidebar */}
      <Offcanvas show={sidebarShow} onHide={() => setSidebarShow(false)}>
        <Offcanvas.Header closeButton>
          <Offcanvas.Title>Menu</Offcanvas.Title>
        </Offcanvas.Header>
        <Offcanvas.Body>
          <Sidebar />
        </Offcanvas.Body>
      </Offcanvas>

      {/* Main Content */}
      <div className="flex-grow-1">
        {/* Navbar */}
        <Navbar bg="white" expand="lg" className="border-bottom">
          <Container fluid>
            <Navbar.Toggle
              aria-controls="sidebar"
              onClick={() => setSidebarShow(true)}
              className="d-md-none"
            />
            <Navbar.Brand href="/" className="me-auto">
              Unick Enterprises ERP
            </Navbar.Brand>
            
            <Nav className="ms-auto">
              <Nav.Link href="/profile" className="d-flex align-items-center">
                <i className="bi bi-person-circle me-2"></i>
                {user?.name}
              </Nav.Link>
              <Nav.Link onClick={handleLogout} className="text-danger">
                <i className="bi bi-box-arrow-right me-2"></i>
                Logout
              </Nav.Link>
            </Nav>
          </Container>
        </Navbar>

        {/* Page Content */}
        <div className="main-content">
          {children}
        </div>
      </div>
    </div>
  )
}

export default Layout