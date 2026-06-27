import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useSelector, useDispatch } from 'react-redux';
import {
  AppBar, Toolbar, Typography, Button, IconButton, Badge, Menu, MenuItem,
  Box, InputBase, Avatar, Divider, Drawer, List, ListItem, ListItemIcon,
  ListItemText, ListItemButton, useMediaQuery, useTheme,
} from '@mui/material';
import {
  Search, FavoriteBorder, Notifications, Chat, Menu as MenuIcon,
  Person, Store, Dashboard, Logout, CompareArrows, ShoppingBag,
  Home, Category, PhoneAndroid,
} from '@mui/icons-material';
import { logout } from '../../store/slices/authSlice';

function Navbar() {
  const { user } = useSelector((state) => state.auth);
  const { compareList } = useSelector((state) => state.cart);
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down('md'));
  const [anchorEl, setAnchorEl] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [drawerOpen, setDrawerOpen] = useState(false);

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      navigate(`/products?search=${encodeURIComponent(searchQuery)}`);
    }
  };

  const handleLogout = () => {
    dispatch(logout());
    setAnchorEl(null);
    navigate('/');
  };

  const getDashboardLink = () => {
    if (!user) return '/login';
    if (user.role === 'super_admin') return '/admin';
    if (user.role === 'shop_owner') return '/shop/dashboard';
    return '/profile';
  };

  return (
    <>
      <AppBar position="sticky" sx={{ bgcolor: 'primary.main' }}>
        <Toolbar sx={{ maxWidth: 1400, width: '100%', mx: 'auto' }}>
          {isMobile && (
            <IconButton color="inherit" onClick={() => setDrawerOpen(true)} edge="start">
              <MenuIcon />
            </IconButton>
          )}

          <Typography
            variant="h5"
            component={Link}
            to="/"
            sx={{ textDecoration: 'none', color: 'white', fontWeight: 800, mr: 3, letterSpacing: -0.5 }}
          >
            SmartDeals.lk
          </Typography>

          {!isMobile && (
            <Box component="form" onSubmit={handleSearch} sx={{
              display: 'flex', alignItems: 'center', bgcolor: 'rgba(255,255,255,0.15)',
              borderRadius: 2, px: 2, flex: 1, maxWidth: 500, mr: 2,
            }}>
              <Search sx={{ color: 'rgba(255,255,255,0.7)', mr: 1 }} />
              <InputBase
                placeholder="Search phones, brands, models..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                sx={{ color: 'white', flex: 1, '& ::placeholder': { color: 'rgba(255,255,255,0.7)' } }}
              />
            </Box>
          )}

          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, ml: 'auto' }}>
            {!isMobile && (
              <>
                <Button color="inherit" component={Link} to="/products" size="small">Products</Button>
                <Button color="inherit" component={Link} to="/shops" size="small">Shops</Button>
              </>
            )}

            <IconButton color="inherit" component={Link} to="/compare">
              <Badge badgeContent={compareList.length} color="secondary">
                <CompareArrows />
              </Badge>
            </IconButton>

            {user ? (
              <>
                <IconButton color="inherit" component={Link} to="/wishlist">
                  <FavoriteBorder />
                </IconButton>
                <IconButton color="inherit" component={Link} to="/notifications">
                  <Notifications />
                </IconButton>
                <IconButton color="inherit" component={Link} to="/chat">
                  <Chat />
                </IconButton>
                <IconButton onClick={(e) => setAnchorEl(e.currentTarget)} color="inherit">
                  <Avatar sx={{ width: 32, height: 32, bgcolor: 'secondary.main', fontSize: 14 }}>
                    {user.name?.charAt(0).toUpperCase()}
                  </Avatar>
                </IconButton>
                <Menu anchorEl={anchorEl} open={Boolean(anchorEl)} onClose={() => setAnchorEl(null)}>
                  <MenuItem disabled>
                    <Typography variant="body2" fontWeight={600}>{user.name}</Typography>
                  </MenuItem>
                  <Divider />
                  <MenuItem onClick={() => { setAnchorEl(null); navigate(getDashboardLink()); }}>
                    <ListItemIcon><Dashboard fontSize="small" /></ListItemIcon>
                    Dashboard
                  </MenuItem>
                  <MenuItem onClick={() => { setAnchorEl(null); navigate('/profile'); }}>
                    <ListItemIcon><Person fontSize="small" /></ListItemIcon>
                    Profile
                  </MenuItem>
                  <MenuItem onClick={() => { setAnchorEl(null); navigate('/reservations'); }}>
                    <ListItemIcon><ShoppingBag fontSize="small" /></ListItemIcon>
                    My Reservations
                  </MenuItem>
                  {user.role === 'customer' && (
                    <MenuItem onClick={() => { setAnchorEl(null); navigate('/register-shop'); }}>
                      <ListItemIcon><Store fontSize="small" /></ListItemIcon>
                      Register Shop
                    </MenuItem>
                  )}
                  <Divider />
                  <MenuItem onClick={handleLogout}>
                    <ListItemIcon><Logout fontSize="small" /></ListItemIcon>
                    Logout
                  </MenuItem>
                </Menu>
              </>
            ) : (
              <>
                <Button color="inherit" component={Link} to="/login" size="small">Login</Button>
                <Button variant="contained" color="secondary" component={Link} to="/register" size="small">
                  Register
                </Button>
              </>
            )}
          </Box>
        </Toolbar>
      </AppBar>

      <Drawer open={drawerOpen} onClose={() => setDrawerOpen(false)}>
        <Box sx={{ width: 280 }}>
          <Box sx={{ p: 2, bgcolor: 'primary.main', color: 'white' }}>
            <Typography variant="h6" fontWeight={800}>SmartDeals.lk</Typography>
          </Box>
          <Box component="form" onSubmit={(e) => { handleSearch(e); setDrawerOpen(false); }} sx={{ p: 2 }}>
            <InputBase
              placeholder="Search..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              sx={{ border: '1px solid #ddd', borderRadius: 1, px: 2, py: 0.5, width: '100%' }}
            />
          </Box>
          <List>
            <ListItem disablePadding>
              <ListItemButton component={Link} to="/" onClick={() => setDrawerOpen(false)}>
                <ListItemIcon><Home /></ListItemIcon>
                <ListItemText primary="Home" />
              </ListItemButton>
            </ListItem>
            <ListItem disablePadding>
              <ListItemButton component={Link} to="/products" onClick={() => setDrawerOpen(false)}>
                <ListItemIcon><PhoneAndroid /></ListItemIcon>
                <ListItemText primary="Products" />
              </ListItemButton>
            </ListItem>
            <ListItem disablePadding>
              <ListItemButton component={Link} to="/shops" onClick={() => setDrawerOpen(false)}>
                <ListItemIcon><Store /></ListItemIcon>
                <ListItemText primary="Shops" />
              </ListItemButton>
            </ListItem>
            <ListItem disablePadding>
              <ListItemButton component={Link} to="/categories" onClick={() => setDrawerOpen(false)}>
                <ListItemIcon><Category /></ListItemIcon>
                <ListItemText primary="Categories" />
              </ListItemButton>
            </ListItem>
          </List>
        </Box>
      </Drawer>
    </>
  );
}

export default Navbar;
