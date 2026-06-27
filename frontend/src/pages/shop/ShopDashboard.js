import React, { useEffect, useState } from 'react';
import { Link, Outlet, useLocation, useNavigate } from 'react-router-dom';
import { useSelector } from 'react-redux';
import {
  Box, Drawer, List, ListItem, ListItemButton, ListItemIcon, ListItemText,
  Typography, Grid, Paper, CircularProgress, Divider, Alert,
} from '@mui/material';
import {
  Dashboard, ShoppingCart, ShoppingBag, LocalOffer, Assessment, Campaign,
  Store, Chat,
} from '@mui/icons-material';
import { shopAnalyticsAPI, shopRegAPI } from '../../api/endpoints';

const drawerWidth = 240;

const menuItems = [
  { label: 'Dashboard', icon: <Dashboard />, path: '/shop/dashboard' },
  { label: 'Products', icon: <ShoppingCart />, path: '/shop/products' },
  { label: 'Reservations', icon: <ShoppingBag />, path: '/shop/reservations' },
  { label: 'Offers', icon: <LocalOffer />, path: '/shop/offers' },
  { label: 'Advertisements', icon: <Campaign />, path: '/shop/ads' },
  { label: 'Analytics', icon: <Assessment />, path: '/shop/analytics' },
  { label: 'Messages', icon: <Chat />, path: '/chat' },
  { label: 'Shop Settings', icon: <Store />, path: '/shop/settings' },
];

function ShopLayout() {
  const location = useLocation();
  const navigate = useNavigate();
  const { user } = useSelector((state) => state.auth);

  useEffect(() => {
    if (!user || user.role !== 'shop_owner') navigate('/login');
  }, [user, navigate]);

  return (
    <Box sx={{ display: 'flex' }}>
      <Drawer variant="permanent" sx={{
        width: drawerWidth, flexShrink: 0,
        '& .MuiDrawer-paper': { width: drawerWidth, boxSizing: 'border-box', top: 64 },
      }}>
        <Box sx={{ p: 2 }}>
          <Typography variant="subtitle2" color="text.secondary">SHOP DASHBOARD</Typography>
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

function ShopDashboardContent() {
  const [stats, setStats] = useState(null);
  const [shop, setShop] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      shopAnalyticsAPI.dashboard(),
      shopRegAPI.myShop(),
    ]).then(([analyticsRes, shopRes]) => {
      setStats(analyticsRes.data);
      setShop(shopRes.data.shop);
      setLoading(false);
    }).catch(() => setLoading(false));
  }, []);

  if (loading) return <CircularProgress />;

  if (shop?.status !== 'approved') {
    return (
      <Box>
        <Alert severity={shop?.status === 'pending' ? 'warning' : 'error'} sx={{ mb: 3 }}>
          {shop?.status === 'pending' && 'Your shop is pending approval. Please wait for admin review.'}
          {shop?.status === 'rejected' && `Your shop has been rejected. Reason: ${shop.rejection_reason}`}
          {shop?.status === 'suspended' && 'Your shop has been suspended. Please contact admin.'}
        </Alert>
      </Box>
    );
  }

  const cards = [
    { label: 'Total Views', value: stats?.total_views, color: '#1565C0' },
    { label: 'Total Favorites', value: stats?.total_favorites, color: '#C62828' },
    { label: 'Total Reservations', value: stats?.total_reservations, color: '#E65100' },
    { label: 'Completed Sales', value: stats?.completed_sales, color: '#2E7D32' },
    { label: 'Active Products', value: stats?.active_products, color: '#7B1FA2' },
    { label: 'Sold Products', value: stats?.sold_products, color: '#00695C' },
    { label: 'Shop Rating', value: stats?.shop_rating, color: '#F57F17' },
    { label: 'Total Followers', value: stats?.total_followers, color: '#4527A0' },
  ];

  return (
    <Box>
      <Typography variant="h4" fontWeight={700} gutterBottom>
        Welcome, {shop?.name}
        {shop?.is_verified && ' (Verified)'}
      </Typography>
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

export { ShopLayout, ShopDashboardContent };
export default ShopDashboardContent;
