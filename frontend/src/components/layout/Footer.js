import React from 'react';
import { Link } from 'react-router-dom';
import { Box, Container, Grid, Typography, IconButton, Divider } from '@mui/material';
import { Facebook, Twitter, Instagram, YouTube, Phone, Email, LocationOn } from '@mui/icons-material';

function Footer() {
  return (
    <Box sx={{ bgcolor: '#1a1a2e', color: 'white', mt: 6, pt: 6, pb: 3 }}>
      <Container maxWidth="lg">
        <Grid container spacing={4}>
          <Grid item xs={12} sm={6} md={3}>
            <Typography variant="h5" fontWeight={800} gutterBottom>SmartDeals.lk</Typography>
            <Typography variant="body2" color="grey.400" sx={{ mb: 2 }}>
              Sri Lanka's Trusted Verified Mobile Marketplace.
              Buy from verified phone shops with confidence.
            </Typography>
            <Box sx={{ display: 'flex', gap: 1 }}>
              <IconButton size="small" sx={{ color: 'grey.400' }}><Facebook /></IconButton>
              <IconButton size="small" sx={{ color: 'grey.400' }}><Twitter /></IconButton>
              <IconButton size="small" sx={{ color: 'grey.400' }}><Instagram /></IconButton>
              <IconButton size="small" sx={{ color: 'grey.400' }}><YouTube /></IconButton>
            </Box>
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <Typography variant="h6" gutterBottom>Quick Links</Typography>
            {[
              { label: 'All Products', to: '/products' },
              { label: 'Verified Shops', to: '/shops' },
              { label: 'Hot Deals', to: '/products?sort_by=discount' },
              { label: 'Compare Phones', to: '/compare' },
              { label: 'Register Your Shop', to: '/register-shop' },
            ].map((link) => (
              <Typography key={link.to} variant="body2" sx={{ mb: 1 }}>
                <Box component={Link} to={link.to} sx={{ color: 'grey.400', textDecoration: 'none', '&:hover': { color: 'white' } }}>
                  {link.label}
                </Box>
              </Typography>
            ))}
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <Typography variant="h6" gutterBottom>Categories</Typography>
            {['Smartphones', 'Accessories', 'Tablets', 'Laptops', 'Gaming Devices', 'Spare Parts'].map((cat) => (
              <Typography key={cat} variant="body2" sx={{ mb: 1 }}>
                <Box component={Link} to={`/products?category=${cat.toLowerCase()}`} sx={{ color: 'grey.400', textDecoration: 'none', '&:hover': { color: 'white' } }}>
                  {cat}
                </Box>
              </Typography>
            ))}
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <Typography variant="h6" gutterBottom>Contact</Typography>
            <Box sx={{ display: 'flex', alignItems: 'center', mb: 1, color: 'grey.400' }}>
              <Phone fontSize="small" sx={{ mr: 1 }} />
              <Typography variant="body2">+94 11 234 5678</Typography>
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center', mb: 1, color: 'grey.400' }}>
              <Email fontSize="small" sx={{ mr: 1 }} />
              <Typography variant="body2">info@smartdeals.lk</Typography>
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'flex-start', mb: 1, color: 'grey.400' }}>
              <LocationOn fontSize="small" sx={{ mr: 1 }} />
              <Typography variant="body2">Colombo, Sri Lanka</Typography>
            </Box>
          </Grid>
        </Grid>
        <Divider sx={{ borderColor: 'grey.800', my: 3 }} />
        <Box sx={{ display: 'flex', justifyContent: 'space-between', flexWrap: 'wrap' }}>
          <Typography variant="body2" color="grey.500">
            &copy; {new Date().getFullYear()} SmartDeals.lk - All rights reserved
          </Typography>
          <Typography variant="body2" color="grey.500">
            Trusted Verified Mobile Marketplace
          </Typography>
        </Box>
      </Container>
    </Box>
  );
}

export default Footer;
