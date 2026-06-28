import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import {
  Container, Grid, Typography, Box, Card, CardContent, TextField, MenuItem,
  CircularProgress, Pagination, Chip, Rating, FormControlLabel, Checkbox,
} from '@mui/material';
import { Verified, Store, LocationOn } from '@mui/icons-material';
import { shopAPI } from '../../api/endpoints';
import { districts } from '../../utils/helpers';

function ShopsPage() {
  const [shops, setShops] = useState([]);
  const [loading, setLoading] = useState(true);
  const [pagination, setPagination] = useState(null);
  const [filters, setFilters] = useState({ search: '', district: '', verified_only: false, sort_by: 'rating', page: 1 });

  useEffect(() => {
    setLoading(true);
    const params = {};
    Object.entries(filters).forEach(([k, v]) => { if (v) params[k] = v; });
    shopAPI.list(params).then(res => {
      setShops(res.data.data || []);
      setPagination({ currentPage: res.data.current_page, lastPage: res.data.last_page, total: res.data.total });
      setLoading(false);
    });
  }, [filters]);

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Typography variant="h4" fontWeight={700} gutterBottom>Verified Phone Shops</Typography>
      <Box sx={{ display: 'flex', gap: 2, mb: 3, flexWrap: 'wrap' }}>
        <TextField size="small" label="Search shops" sx={{ minWidth: 200 }}
          value={filters.search} onChange={(e) => setFilters({ ...filters, search: e.target.value, page: 1 })} />
        <TextField size="small" label="District" select sx={{ minWidth: 150 }}
          value={filters.district} onChange={(e) => setFilters({ ...filters, district: e.target.value, page: 1 })}>
          <MenuItem value="">All</MenuItem>
          {districts.map(d => <MenuItem key={d} value={d}>{d}</MenuItem>)}
        </TextField>
        <TextField size="small" label="Sort" select sx={{ minWidth: 150 }}
          value={filters.sort_by} onChange={(e) => setFilters({ ...filters, sort_by: e.target.value })}>
          <MenuItem value="rating">Top Rated</MenuItem>
          <MenuItem value="newest">Newest</MenuItem>
        </TextField>
        <FormControlLabel
          control={<Checkbox checked={filters.verified_only} onChange={(e) => setFilters({ ...filters, verified_only: e.target.checked, page: 1 })} />}
          label="Verified Only"
        />
      </Box>

      {loading ? (
        <Box sx={{ display: 'flex', justifyContent: 'center', py: 5 }}><CircularProgress /></Box>
      ) : shops.length === 0 ? (
        <Typography variant="h6" color="text.secondary" textAlign="center" sx={{ py: 5 }}>No shops found</Typography>
      ) : (
        <>
          <Grid container spacing={3}>
            {shops.map((shop) => (
              <Grid size={{ xs: 12, sm: 6, md: 4 }} key={shop.id}>
                <Card component={Link} to={`/shops/${shop.slug}`}
                  sx={{ textDecoration: 'none', height: '100%', transition: '0.3s', '&:hover': { transform: 'translateY(-4px)', boxShadow: 6 } }}>
                  <CardContent>
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 2 }}>
                      <Box sx={{ width: 56, height: 56, borderRadius: '50%', bgcolor: 'primary.light', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                        <Store sx={{ fontSize: 28, color: 'white' }} />
                      </Box>
                      <Box sx={{ flex: 1 }}>
                        <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                          <Typography variant="h6" fontWeight={600}>{shop.name}</Typography>
                          {shop.is_verified && <Verified sx={{ fontSize: 18, color: 'primary.main' }} />}
                        </Box>
                        <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                          <LocationOn sx={{ fontSize: 14, color: 'grey.500' }} />
                          <Typography variant="body2" color="text.secondary">{shop.district}</Typography>
                        </Box>
                      </Box>
                    </Box>
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1 }}>
                      <Rating value={Number(shop.rating)} precision={0.5} size="small" readOnly />
                      <Typography variant="body2" fontWeight={600}>{shop.rating}</Typography>
                      <Typography variant="caption" color="text.secondary">({shop.total_reviews} reviews)</Typography>
                    </Box>
                    <Box sx={{ display: 'flex', gap: 1 }}>
                      <Chip label={`${shop.total_products} products`} size="small" variant="outlined" />
                      {shop.is_verified && <Chip label="Verified" size="small" color="primary" />}
                    </Box>
                  </CardContent>
                </Card>
              </Grid>
            ))}
          </Grid>
          {pagination && pagination.lastPage > 1 && (
            <Box sx={{ display: 'flex', justifyContent: 'center', mt: 4 }}>
              <Pagination count={pagination.lastPage} page={filters.page} onChange={(e, p) => setFilters({ ...filters, page: p })} color="primary" />
            </Box>
          )}
        </>
      )}
    </Container>
  );
}

export default ShopsPage;
