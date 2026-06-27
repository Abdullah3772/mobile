import React, { useEffect, useState } from 'react';
import { Link, Outlet, useLocation, useNavigate } from 'react-router-dom';
import { useSelector } from 'react-redux';
import {
  Box, Drawer, List, ListItem, ListItemButton, ListItemIcon, ListItemText,
  Typography, Grid, Paper, CircularProgress, Divider,
} from '@mui/material';
import {
  Dashboard, Store, Category, BrandingWatermark, ShoppingCart, Star, Campaign,
  Image, LocalShipping, MonetizationOn, Assessment, Report,
} from '@mui/icons-material';
import { adminAPI } from '../../api/endpoints';

const drawerWidth = 260;

const menuItems = [
  { label: 'Dashboard', icon: <Dashboard />, path: '/admin' },
  { label: 'Shops', icon: <Store />, path: '/admin/shops' },
  { label: 'Categories', icon: <Category />, path: '/admin/categories' },
  { label: 'Brands', icon: <BrandingWatermark />, path: '/admin/brands' },
  { label: 'Products', icon: <ShoppingCart />, path: '/admin/products' },
  { label: 'Reviews', icon: <Star />, path: '/admin/reviews' },
  { label: 'Complaints', icon: <Report />, path: '/admin/complaints' },
  { label: 'Announcements', icon: <Campaign />, path: '/admin/announcements' },
  { label: 'Banners', icon: <Image />, path: '/admin/banners' },
  { label: 'Delivery Partners', icon: <LocalShipping />, path: '/admin/delivery-partners' },
  { label: 'Ad Packages', icon: <MonetizationOn />, path: '/admin/ad-packages' },
  { label: 'Analytics', icon: <Assessment />, path: '/admin/analytics' },
];

function AdminLayout() {
  const location = useLocation();
  const navigate = useNavigate();
  const { user } = useSelector((state) => state.auth);

  useEffect(() => {
    if (!user || user.role !== 'super_admin') navigate('/login');
  }, [user, navigate]);

  return (
    <Box sx={{ display: 'flex' }}>
      <Drawer variant="permanent" sx={{
        width: drawerWidth, flexShrink: 0,
        '& .MuiDrawer-paper': { width: drawerWidth, boxSizing: 'border-box', top: 64 },
      }}>
        <Box sx={{ p: 2 }}>
          <Typography variant="subtitle2" color="text.secondary">ADMIN PANEL</Typography>
        </Box>
        <Divider />
        <List>
          {menuItems.map((item) => (
            <ListItem key={item.path} disablePadding>
              <ListItemButton component={Link} to={item.path} selected={location.pathname === item.path}>
                <ListItemIcon>{item.icon}</ListItemIcon>
                <ListItemText primary={item.label} />
              </ListItemButton>
            </ListItem>
          ))}
        </List>
      </Drawer>
      <Box component="main" sx={{ flexGrow: 1, p: 3, ml: `${drawerWidth}px`, minHeight: 'calc(100vh - 64px)' }}>
        <Outlet />
      </Box>
    </Box>
  );
}

function AdminDashboardContent() {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    adminAPI.dashboard().then(res => {
      setStats(res.data);
      setLoading(false);
    });
  }, []);

  if (loading) return <CircularProgress />;

  const cards = [
    { label: 'Total Users', value: stats?.total_users, color: '#1565C0' },
    { label: 'Active Shops', value: stats?.total_shops, color: '#2E7D32' },
    { label: 'Active Products', value: stats?.total_products, color: '#E65100' },
    { label: 'Total Reservations', value: stats?.total_reservations, color: '#7B1FA2' },
    { label: 'Pending Shops', value: stats?.pending_shops, color: '#F57F17' },
    { label: 'Pending Reviews', value: stats?.pending_reviews, color: '#C62828' },
    { label: 'New Users Today', value: stats?.new_users_today, color: '#00695C' },
    { label: 'New Products Today', value: stats?.new_products_today, color: '#4527A0' },
  ];

  return (
    <Box>
      <Typography variant="h4" fontWeight={700} gutterBottom>Admin Dashboard</Typography>
      <Grid container spacing={3}>
        {cards.map((card) => (
          <Grid item xs={12} sm={6} md={3} key={card.label}>
            <Paper sx={{ p: 3, borderLeft: `4px solid ${card.color}` }}>
              <Typography variant="body2" color="text.secondary">{card.label}</Typography>
              <Typography variant="h4" fontWeight={700} sx={{ color: card.color }}>{card.value || 0}</Typography>
            </Paper>
          </Grid>
        ))}
      </Grid>
    </Box>
  );
}

export { AdminLayout, AdminDashboardContent };
export default AdminDashboardContent;
