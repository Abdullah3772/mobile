import React, { useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useSelector, useDispatch } from 'react-redux';
import {
  Container, Grid, Typography, Box, Button, Card, CardContent,
  Chip, InputBase, Paper, CircularProgress, Alert,
} from '@mui/material';
import { Search, Verified, LocalOffer, TrendingUp, NewReleases, Store, Star } from '@mui/icons-material';
import { fetchHome } from '../../store/slices/homeSlice';
import ProductCard from '../../components/common/ProductCard';


function HomePage() {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { data, loading } = useSelector((state) => state.home);
  const [searchQuery, setSearchQuery] = React.useState('');

  useEffect(() => { dispatch(fetchHome()); }, [dispatch]);

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchQuery.trim()) navigate(`/products?search=${encodeURIComponent(searchQuery)}`);
  };

  if (loading) return <Box sx={{ display: 'flex', justifyContent: 'center', py: 10 }}><CircularProgress /></Box>;

  return (
    <Box>
      {/* Hero Section */}
      <Box sx={{
        background: 'linear-gradient(135deg, #1565C0 0%, #0D47A1 50%, #1a237e 100%)',
        color: 'white', py: { xs: 6, md: 10 }, position: 'relative', overflow: 'hidden',
      }}>
        <Box sx={{ position: 'absolute', top: -100, right: -100, width: 300, height: 300, borderRadius: '50%', bgcolor: 'rgba(255,255,255,0.05)' }} />
        <Box sx={{ position: 'absolute', bottom: -50, left: -50, width: 200, height: 200, borderRadius: '50%', bgcolor: 'rgba(255,255,255,0.03)' }} />
        <Container maxWidth="lg" sx={{ position: 'relative', zIndex: 1 }}>
          <Grid container spacing={4} alignItems="center">
            <Grid item xs={12} md={7}>
              <Chip label="Trusted Marketplace" icon={<Verified />} sx={{ bgcolor: 'rgba(255,255,255,0.15)', color: 'white', mb: 2 }} />
              <Typography variant="h3" fontWeight={800} sx={{ mb: 2, fontSize: { xs: '2rem', md: '3rem' } }}>
                Sri Lanka's Trusted<br />
                <Box component="span" sx={{ color: '#FFD54F' }}>Verified Mobile</Box> Marketplace
              </Typography>
              <Typography variant="h6" sx={{ mb: 4, opacity: 0.9, fontWeight: 400 }}>
                Buy from verified phone shops with confidence. No scams, only trusted sellers.
              </Typography>
              <Paper component="form" onSubmit={handleSearch} sx={{ display: 'flex', alignItems: 'center', p: 1, borderRadius: 3, maxWidth: 520 }}>
                <Search sx={{ mx: 1, color: 'grey.500' }} />
                <InputBase
                  placeholder="Search phones, brands, models..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  sx={{ flex: 1 }}
                />
                <Button type="submit" variant="contained" color="primary" sx={{ borderRadius: 2 }}>Search</Button>
              </Paper>
            </Grid>
          </Grid>
        </Container>
      </Box>

      {/* Announcements */}
      {data?.announcements?.length > 0 && (
        <Container maxWidth="lg" sx={{ mt: 2 }}>
          {data.announcements.map((ann) => (
            <Alert key={ann.id} severity={ann.type || 'info'} sx={{ mb: 1 }}>{ann.title}: {ann.message}</Alert>
          ))}
        </Container>
      )}

      <Container maxWidth="lg">
        {/* Popular Brands */}
        {data?.brands?.length > 0 && (
          <Box sx={{ my: 5 }}>
            <Typography variant="h5" fontWeight={700} gutterBottom>Popular Brands</Typography>
            <Box sx={{ display: 'flex', gap: 2, overflowX: 'auto', pb: 1, '&::-webkit-scrollbar': { height: 4 } }}>
              {data.brands.map((brand) => (
                <Card key={brand.id} component={Link} to={`/products?brand_id=${brand.id}`}
                  sx={{ minWidth: 120, textAlign: 'center', textDecoration: 'none', cursor: 'pointer', transition: '0.2s', '&:hover': { transform: 'scale(1.05)' } }}>
                  <CardContent sx={{ py: 2 }}>
                    <Typography variant="subtitle2" fontWeight={600}>{brand.name}</Typography>
                  </CardContent>
                </Card>
              ))}
            </Box>
          </Box>
        )}

        {/* Categories */}
        {data?.categories?.length > 0 && (
          <Box sx={{ my: 5 }}>
            <Typography variant="h5" fontWeight={700} gutterBottom>Browse Categories</Typography>
            <Grid container spacing={2}>
              {data.categories.map((cat) => (
                <Grid item xs={6} sm={4} md={2} key={cat.id}>
                  <Card component={Link} to={`/products?category_id=${cat.id}`}
                    sx={{ textAlign: 'center', textDecoration: 'none', cursor: 'pointer', transition: '0.2s', '&:hover': { transform: 'translateY(-4px)', boxShadow: 4 } }}>
                    <CardContent>
                      <Typography variant="subtitle2" fontWeight={600}>{cat.name}</Typography>
                      {cat.children?.length > 0 && (
                        <Typography variant="caption" color="text.secondary">{cat.children.length} subcategories</Typography>
                      )}
                    </CardContent>
                  </Card>
                </Grid>
              ))}
            </Grid>
          </Box>
        )}

        {/* Featured Products */}
        {data?.featured_products?.length > 0 && (
          <Box sx={{ my: 5 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <TrendingUp color="primary" />
                <Typography variant="h5" fontWeight={700}>Featured Products</Typography>
              </Box>
              <Button component={Link} to="/products?is_featured=1" color="primary">View All</Button>
            </Box>
            <Grid container spacing={3}>
              {data.featured_products.map((product) => (
                <Grid item xs={12} sm={6} md={3} key={product.id}>
                  <ProductCard product={product} />
                </Grid>
              ))}
            </Grid>
          </Box>
        )}

        {/* Hot Deals */}
        {data?.hot_deals?.length > 0 && (
          <Box sx={{ my: 5 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <LocalOffer color="error" />
                <Typography variant="h5" fontWeight={700}>Hot Deals</Typography>
              </Box>
              <Button component={Link} to="/products?sort_by=discount" color="error">View All</Button>
            </Box>
            <Grid container spacing={3}>
              {data.hot_deals.map((product) => (
                <Grid item xs={12} sm={6} md={3} key={product.id}>
                  <ProductCard product={product} />
                </Grid>
              ))}
            </Grid>
          </Box>
        )}

        {/* Latest Arrivals */}
        {data?.latest_products?.length > 0 && (
          <Box sx={{ my: 5 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <NewReleases color="secondary" />
                <Typography variant="h5" fontWeight={700}>Latest Arrivals</Typography>
              </Box>
              <Button component={Link} to="/products?sort_by=created_at" color="secondary">View All</Button>
            </Box>
            <Grid container spacing={3}>
              {data.latest_products.map((product) => (
                <Grid item xs={12} sm={6} md={3} key={product.id}>
                  <ProductCard product={product} />
                </Grid>
              ))}
            </Grid>
          </Box>
        )}

        {/* Verified Shops */}
        {data?.verified_shops?.length > 0 && (
          <Box sx={{ my: 5 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <Verified color="primary" />
                <Typography variant="h5" fontWeight={700}>Verified Shops</Typography>
              </Box>
              <Button component={Link} to="/shops?verified_only=true" color="primary">View All</Button>
            </Box>
            <Grid container spacing={3}>
              {data.verified_shops.map((shop) => (
                <Grid item xs={12} sm={6} md={3} key={shop.id}>
                  <Card component={Link} to={`/shops/${shop.slug}`} sx={{ textDecoration: 'none', transition: '0.3s', '&:hover': { transform: 'translateY(-4px)', boxShadow: 6 } }}>
                    <CardContent sx={{ textAlign: 'center' }}>
                      <Box sx={{ width: 64, height: 64, borderRadius: '50%', bgcolor: 'primary.light', display: 'flex', alignItems: 'center', justifyContent: 'center', mx: 'auto', mb: 1 }}>
                        <Store sx={{ fontSize: 32, color: 'white' }} />
                      </Box>
                      <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 0.5 }}>
                        <Typography variant="subtitle1" fontWeight={600}>{shop.name}</Typography>
                        {shop.is_verified && <Verified sx={{ fontSize: 16, color: 'primary.main' }} />}
                      </Box>
                      <Typography variant="body2" color="text.secondary">{shop.district}</Typography>
                      <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 0.5, mt: 1 }}>
                        <Star sx={{ fontSize: 16, color: '#FFB300' }} />
                        <Typography variant="body2" fontWeight={600}>{shop.rating}</Typography>
                        <Typography variant="caption" color="text.secondary">({shop.total_reviews} reviews)</Typography>
                      </Box>
                    </CardContent>
                  </Card>
                </Grid>
              ))}
            </Grid>
          </Box>
        )}

        {/* Trust Section */}
        <Box sx={{ my: 6, py: 5, textAlign: 'center', bgcolor: '#f0f4ff', borderRadius: 4 }}>
          <Typography variant="h4" fontWeight={700} gutterBottom>Why SmartDeals.lk?</Typography>
          <Grid container spacing={4} sx={{ mt: 2 }}>
            {[
              { icon: <Verified sx={{ fontSize: 48 }} />, title: 'Verified Shops Only', desc: 'Every shop is vetted and approved by our admin team' },
              { icon: <Store sx={{ fontSize: 48 }} />, title: 'TRCSL Approved', desc: 'Products with TRCSL verification for authenticity' },
              { icon: <Star sx={{ fontSize: 48 }} />, title: 'Trusted Reviews', desc: 'Real reviews from real customers' },
              { icon: <LocalOffer sx={{ fontSize: 48 }} />, title: 'Best Deals', desc: 'Compare prices across verified shops' },
            ].map((item, i) => (
              <Grid item xs={12} sm={6} md={3} key={i}>
                <Box sx={{ color: 'primary.main', mb: 1 }}>{item.icon}</Box>
                <Typography variant="h6" fontWeight={600}>{item.title}</Typography>
                <Typography variant="body2" color="text.secondary">{item.desc}</Typography>
              </Grid>
            ))}
          </Grid>
        </Box>
      </Container>
    </Box>
  );
}

export default HomePage;
