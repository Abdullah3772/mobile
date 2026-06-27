import React, { useEffect, useState } from 'react';
import { Container, Grid, Typography, Box, CircularProgress } from '@mui/material';
import { FavoriteBorder } from '@mui/icons-material';
import { wishlistAPI } from '../../api/endpoints';
import ProductCard from '../../components/common/ProductCard';

function WishlistPage() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchWishlist = () => {
    setLoading(true);
    wishlistAPI.list().then(res => {
      setItems(res.data.data || []);
      setLoading(false);
    });
  };

  useEffect(() => { fetchWishlist(); }, []);

  if (loading) return <Box sx={{ display: 'flex', justifyContent: 'center', py: 10 }}><CircularProgress /></Box>;

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Typography variant="h4" fontWeight={700} gutterBottom>My Wishlist</Typography>
      {items.length === 0 ? (
        <Box sx={{ textAlign: 'center', py: 8 }}>
          <FavoriteBorder sx={{ fontSize: 64, color: 'grey.400', mb: 2 }} />
          <Typography variant="h6" color="text.secondary">Your wishlist is empty</Typography>
        </Box>
      ) : (
        <Grid container spacing={3}>
          {items.map((item) => (
            <Grid item xs={12} sm={6} md={3} key={item.id}>
              <ProductCard product={item.product} onWishlistToggle={fetchWishlist} />
            </Grid>
          ))}
        </Grid>
      )}
    </Container>
  );
}

export default WishlistPage;
