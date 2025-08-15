import React from 'react'
import { Form, Button, Card, Container, Alert } from 'react-bootstrap'
import { Formik } from 'formik'
import * as Yup from 'yup'
import { useAuth } from '../hooks/useAuth'
import { useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'

const loginSchema = Yup.object().shape({
  email: Yup.string().email('Invalid email').required('Email is required'),
  password: Yup.string().required('Password is required'),
})

function Login() {
  const { login } = useAuth()
  const navigate = useNavigate()

  const handleSubmit = async (values, { setSubmitting, setErrors }) => {
    try {
      await login(values)
      toast.success('Login successful!')
      navigate('/dashboard')
    } catch (error) {
      const message = error.response?.data?.message || 'Login failed'
      setErrors({ submit: message })
      toast.error(message)
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <Container className="d-flex align-items-center justify-content-center" style={{ minHeight: '100vh' }}>
      <div style={{ maxWidth: '400px', width: '100%' }}>
        <Card>
          <Card.Header className="text-center">
            <h4 className="mb-0">Unick Enterprises ERP</h4>
            <small className="text-muted">Sign in to your account</small>
          </Card.Header>
          <Card.Body>
            <Formik
              initialValues={{ email: '', password: '' }}
              validationSchema={loginSchema}
              onSubmit={handleSubmit}
            >
              {({
                values,
                errors,
                touched,
                handleChange,
                handleBlur,
                handleSubmit,
                isSubmitting,
              }) => (
                <Form onSubmit={handleSubmit}>
                  {errors.submit && (
                    <Alert variant="danger">{errors.submit}</Alert>
                  )}

                  <Form.Group className="mb-3">
                    <Form.Label>Email</Form.Label>
                    <Form.Control
                      type="email"
                      name="email"
                      value={values.email}
                      onChange={handleChange}
                      onBlur={handleBlur}
                      isInvalid={touched.email && errors.email}
                    />
                    <Form.Control.Feedback type="invalid">
                      {errors.email}
                    </Form.Control.Feedback>
                  </Form.Group>

                  <Form.Group className="mb-3">
                    <Form.Label>Password</Form.Label>
                    <Form.Control
                      type="password"
                      name="password"
                      value={values.password}
                      onChange={handleChange}
                      onBlur={handleBlur}
                      isInvalid={touched.password && errors.password}
                    />
                    <Form.Control.Feedback type="invalid">
                      {errors.password}
                    </Form.Control.Feedback>
                  </Form.Group>

                  <Button
                    type="submit"
                    variant="primary"
                    className="w-100"
                    disabled={isSubmitting}
                  >
                    {isSubmitting ? 'Signing in...' : 'Sign In'}
                  </Button>
                </Form>
              )}
            </Formik>

            <div className="mt-4">
              <h6 className="text-center mb-3">Demo Accounts</h6>
              <div className="small text-muted">
                <div><strong>Admin:</strong> admin@unick.test / password</div>
                <div><strong>Planner:</strong> planner@unick.test / password</div>
                <div><strong>Warehouse:</strong> warehouse@unick.test / password</div>
                <div><strong>Production:</strong> prod@unick.test / password</div>
                <div><strong>Customer:</strong> customer@unick.test / password</div>
              </div>
            </div>
          </Card.Body>
        </Card>
      </div>
    </Container>
  )
}

export default Login