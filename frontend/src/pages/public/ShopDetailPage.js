import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useSelector } from 'react-redux';
import {
  Container, Grid, Typography, Box, Paper, Chip, Button, Rating,
  CircularProgress, Alert, Tab, Tabs,
} from '@mui/material';
import { Verified, Store, LocationOn, Phone, WhatsApp, Email, Star, AccessTime } from '@mui/icons-material';
import { shopAPI, followAPI } from '../../api/endpoints';
import ProductCard from '../../components/common/ProductCard';

function ShopDetailPage() {
  const { slug } = useParams();
  const navigate = useNavigate();
  const { user } = useSelector((state) => state.auth);
  const [shop, setShop] = useState(null);
  const [products, setProducts] = useState([]);
  const [reviews, setReviews] = useState([]);
  const [loading, setLoading] = useState(true);
  const [following, setFollowing] = useState(false);
  const [tab, setTab] = useState(0);

  useEffect(() => {
    shopAPI.show(slug).then(res => {
      setShop(res.data.shop);
      setProducts(res.data.products?.data || []);
      setReviews(res.data.reviews || []);
      setLoading(false);
      if (user) followAPI.check(res.data.shop.id).then(r => setFollowing(r.data.following)).catch(() => {});
    }).catch(() => setLoading(false));
  }, [slug, user]);

  const handleFollow = async () => {
    if (!user) return navigate('/login');
    const res = await followAPI.toggle(shop.id);
    setFollowing(res.data.following);
  };

  if (loading) return <Box sx={{ display: 'flex', justifyContent: 'center', py: 10 }}><CircularProgress /></Box>;
  if (!shop) return <Container sx={{ py: 5 }}><Alert severity="error">Shop not found</Alert></Container>;

  return (
    <Box>
      {/* Cover */}
      <Box sx={{ bgcolor: 'primary.main', color: 'white', py: 5 }}>
        <Container maxWidth="lg">
          <Grid container spacing={3} alignItems="center">
            <Grid item>
              <Box sx={{ width: 100, height: 100, borderRadius: '50%', bgcolor: 'white', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <Store sx={{ fontSize: 48, color: 'primary.main' }} />
              </Box>
            </Grid>
            <Grid item xs>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1 }}>
                <Typography variant="h4" fontWeight={700}>{shop.name}</Typography>
                {shop.is_verified && <Verified sx={{ fontSize: 28 }} />}
              </Box>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, flexWrap: 'wrap' }}>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                  <LocationOn fontSize="small" /> <Typography variant="body2">{shop.district}</Typography>
                </Box>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                  <Star sx={{ color: '#FFD54F' }} />
                  <Typography variant="body1" fontWeight={600}>{shop.rating}</Typography>
                  <Typography variant="body2">({shop.total_reviews} reviews)</Typography>
                </Box>
                <Chip label={`${shop.total_products} Products`} size="small" sx={{ bgcolor: 'rgba(255,255,255,0.2)', color: 'white' }} />
              </Box>
            </Grid>
            <Grid item>
              <Button variant={following ? 'outlined' : 'contained'} color={following ? 'inherit' : 'secondary'}
                onClick={handleFollow} sx={{ color: following ? 'white' : undefined, borderColor: following ? 'white' : undefined }}>
                {following ? 'Following' : 'Follow Shop'}
              </Button>
            </Grid>
          </Grid>
        </Container>
      </Box>

      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Grid container spacing={4}>
          <Grid item xs={12} md={4}>
            <Paper sx={{ p: 3, mb: 3 }}>
              <Typography variant="h6" fontWeight={600} gutterBottom>Shop Info</Typography>
              <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1.5 }}>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                  <LocationOn color="primary" /> <Typography variant="body2">{shop.address}</Typography>
                </Box>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                  <Phone color="primary" /> <Typography variant="body2">{shop.phone}</Typography>
                </Box>
                {shop.whatsapp && (
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                    <WhatsApp color="success" /> <Typography variant="body2">{shop.whatsapp}</Typography>
                  </Box>
                )}
                {shop.email && (
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                    <Email color="primary" /> <Typography variant="body2">{shop.email}</Typography>
                  </Box>
                )}
              </Box>
              {shop.about && (
                <Box sx={{ mt: 2 }}>
                  <Typography variant="subtitle2" fontWeight={600}>About</Typography>
                  <Typography variant="body2" color="text.secondary">{shop.about}</Typography>
                </Box>
              )}
              {shop.opening_hours && (
                <Box sx={{ mt: 2 }}>
                  <Typography variant="subtitle2" fontWeight={600} gutterBottom>
                    <AccessTime sx={{ fontSize: 16, mr: 0.5 }} />Opening Hours
                  </Typography>
                  {Object.entries(shop.opening_hours).map(([day, hours]) => (
                    <Box key={day} sx={{ display: 'flex', justifyContent: 'space-between' }}>
                      <Typography variant="caption" sx={{ textTransform: 'capitalize' }}>{day}</Typography>
                      <Typography variant="caption" fontWeight={600}>{hours}</Typography>
                    </Box>
                  ))}
                </Box>
              )}
            </Paper>
          </Grid>

          <Grid item xs={12} md={8}>
            <Tabs value={tab} onChange={(e, v) => setTab(v)} sx={{ mb: 3 }}>
              <Tab label={`Products (${products.length})`} />
              <Tab label={`Reviews (${reviews.length})`} />
            </Tabs>

            {tab === 0 && (
              <Grid container spacing={2}>
                {products.map((p) => (
                  <Grid item xs={12} sm={6} md={4} key={p.id}><ProductCard product={p} /></Grid>
                ))}
              </Grid>
            )}

            {tab === 1 && (
              <Box>
                {reviews.length === 0 ? (
                  <Typography color="text.secondary">No reviews yet</Typography>
                ) : reviews.map((review) => (
                  <Paper key={review.id} sx={{ p: 2, mb: 2 }}>
                    <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
                      <Typography variant="subtitle2" fontWeight={600}>{review.user?.name}</Typography>
                      <Rating value={review.rating} size="small" readOnly />
                    </Box>
                    <Typography variant="body2">{review.comment}</Typography>
                  </Paper>
                ))}
              </Box>
            )}
          </Grid>
        </Grid>
      </Container>
    </Box>
  );
}

export default ShopDetailPage;
