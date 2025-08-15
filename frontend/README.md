# React + Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Babel](https://babeljs.io/) for Fast Refresh
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/) for Fast Refresh

## Expanding the ESLint configuration

If you are developing a production application, we recommend using TypeScript with type-aware lint rules enabled. Check out the [TS template](https://github.com/vitejs/vite/tree/main/packages/create-vite/template-react-ts) for information on how to integrate TypeScript and [`typescript-eslint`](https://typescript-eslint.io) in your project.

# Frontend

## Setup

1. Install dependencies

```bash
npm install
```

2. Configure environment

Create a `.env` file in the project root:

```
VITE_API_BASE_URL=http://localhost:8000
```

3. Run the app

```bash
npm run dev
```

Open the printed URL.

## Build

```bash
npm run build && npm run preview
```

## Notes
- Requires Laravel backend with Sanctum and the following endpoints (paths can be adjusted in `src/services/api.js`).
- Role-based routing assumes `user.role` equals `admin` or `customer` from `/api/auth/me` or login responses.
