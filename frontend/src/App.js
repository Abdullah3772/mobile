import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Provider } from 'react-redux';
import { ThemeProvider, CssBaseline } from '@mui/material';
import { HelmetProvider } from 'react-helmet-async';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

import store from './store/store';
import theme from './utils/theme';
import Navbar from './components/layout/Navbar';
import Footer from './components/layout/Footer';

// Public Pages
import HomePage from './pages/public/HomePage';
import ProductsPage from './pages/public/ProductsPage';
import ProductDetailPage from './pages/public/ProductDetailPage';
import ShopsPage from './pages/public/ShopsPage';
import ShopDetailPage from './pages/public/ShopDetailPage';
import ComparePage from './pages/public/ComparePage';
import WishlistPage from './pages/public/WishlistPage';
import ReservationsPage from './pages/public/ReservationsPage';
import ChatPage from './pages/public/ChatPage';
import NotificationsPage from './pages/public/NotificationsPage';

// Auth Pages
import LoginPage from './pages/auth/LoginPage';
import RegisterPage from './pages/auth/RegisterPage';

// Admin Pages
import { AdminLayout, AdminDashboardContent } from './pages/admin/AdminDashboard';
import AdminShops from './pages/admin/AdminShops';
import AdminCategories from './pages/admin/AdminCategories';

// Shop Pages
import { ShopLayout, ShopDashboardContent } from './pages/shop/ShopDashboard';
import ShopProducts from './pages/shop/ShopProducts';
import ShopReservations from './pages/shop/ShopReservations';
import ShopOffers from './pages/shop/ShopOffers';
import ShopRegisterPage from './pages/shop/ShopRegisterPage';

function App() {
  return (
    <Provider store={store}>
      <ThemeProvider theme={theme}>
        <HelmetProvider>
          <CssBaseline />
          <Router>
            <Navbar />
            <Routes>
              {/* Public Routes */}
              <Route path="/" element={<HomePage />} />
              <Route path="/products" element={<ProductsPage />} />
              <Route path="/products/:slug" element={<ProductDetailPage />} />
              <Route path="/shops" element={<ShopsPage />} />
              <Route path="/shops/:slug" element={<ShopDetailPage />} />
              <Route path="/compare" element={<ComparePage />} />

              {/* Auth Routes */}
              <Route path="/login" element={<LoginPage />} />
              <Route path="/register" element={<RegisterPage />} />

              {/* User Routes */}
              <Route path="/wishlist" element={<WishlistPage />} />
              <Route path="/reservations" element={<ReservationsPage />} />
              <Route path="/chat" element={<ChatPage />} />
              <Route path="/notifications" element={<NotificationsPage />} />
              <Route path="/register-shop" element={<ShopRegisterPage />} />

              {/* Admin Routes */}
              <Route path="/admin" element={<AdminLayout />}>
                <Route index element={<AdminDashboardContent />} />
                <Route path="shops" element={<AdminShops />} />
                <Route path="categories" element={<AdminCategories />} />
              </Route>

              {/* Shop Routes */}
              <Route path="/shop" element={<ShopLayout />}>
                <Route path="dashboard" element={<ShopDashboardContent />} />
                <Route path="products" element={<ShopProducts />} />
                <Route path="reservations" element={<ShopReservations />} />
                <Route path="offers" element={<ShopOffers />} />
              </Route>
            </Routes>
            <Footer />
            <ToastContainer position="bottom-right" />
          </Router>
        </HelmetProvider>
      </ThemeProvider>
    </Provider>
  );
}

export default App;
