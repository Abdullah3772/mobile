import React, { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import {
  Container, Grid, Typography, Box, TextField, MenuItem, Button,
  CircularProgress, Pagination, Paper,
  FormControlLabel, Checkbox, Drawer, IconButton, useMediaQuery, useTheme,
} from '@mui/material';
import { FilterList, Close } from '@mui/icons-material';
import { fetchProducts } from '../../store/slices/productSlice';
import ProductCard from '../../components/common/ProductCard';
import { categoryAPI, brandAPI } from '../../api/endpoints';
import { districts } from '../../utils/helpers';

function ProductsPage() {
  const dispatch = useDispatch();
  const { items, pagination, loading } = useSelector((state) => state.products);
  const [searchParams] = useSearchParams();
  const [categories, setCategories] = useState([]);
  const [brands, setBrands] = useState([]);
  const [filterDrawerOpen, setFilterDrawerOpen] = useState(false);
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down('md'));

  const [filters, setFilters] = useState({
    search: searchParams.get('search') || '',
    category_id: searchParams.get('category_id') || '',
    brand_id: searchParams.get('brand_id') || '',
    condition: searchParams.get('condition') || '',
    district: searchParams.get('district') || '',
    min_price: searchParams.get('min_price') || '',
    max_price: searchParams.get('max_price') || '',
    ram: searchParams.get('ram') || '',
    storage: searchParams.get('storage') || '',
    five_g_support: searchParams.get('five_g_support') || '',
    sort_by: searchParams.get('sort_by') || 'created_at',
    sort_dir: searchParams.get('sort_dir') || 'desc',
    page: searchParams.get('page') || 1,
  });

  useEffect(() => {
    categoryAPI.list().then(res => setCategories(res.data.categories || []));
    brandAPI.list().then(res => setBrands(res.data.brands || []));
  }, []);

  useEffect(() => {
    const params = {};
    Object.entries(filters).forEach(([k, v]) => { if (v) params[k] = v; });
    dispatch(fetchProducts(params));
  }, [dispatch, filters]);

  const handleFilterChange = (key, value) => {
    setFilters(prev => ({ ...prev, [key]: value, page: 1 }));
  };

  const clearFilters = () => {
    setFilters({
      search: '', category_id: '', brand_id: '', condition: '', district: '',
      min_price: '', max_price: '', ram: '', storage: '', five_g_support: '',
      sort_by: 'created_at', sort_dir: 'desc', page: 1,
    });
  };

  const FilterPanel = () => (
    <Box sx={{ p: 2 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 2 }}>
        <Typography variant="h6" fontWeight={600}>Filters</Typography>
        <Button size="small" onClick={clearFilters}>Clear All</Button>
      </Box>
      <TextField fullWidth label="Search" size="small" sx={{ mb: 2 }}
        value={filters.search} onChange={(e) => handleFilterChange('search', e.target.value)} />
      <TextField fullWidth label="Category" size="small" select sx={{ mb: 2 }}
        value={filters.category_id} onChange={(e) => handleFilterChange('category_id', e.target.value)}>
        <MenuItem value="">All Categories</MenuItem>
        {categories.map(c => (
          <MenuItem key={c.id} value={c.id}>{c.name}</MenuItem>
        ))}
      </TextField>
      <TextField fullWidth label="Brand" size="small" select sx={{ mb: 2 }}
        value={filters.brand_id} onChange={(e) => handleFilterChange('brand_id', e.target.value)}>
        <MenuItem value="">All Brands</MenuItem>
        {brands.map(b => <MenuItem key={b.id} value={b.id}>{b.name}</MenuItem>)}
      </TextField>
      <TextField fullWidth label="Condition" size="small" select sx={{ mb: 2 }}
        value={filters.condition} onChange={(e) => handleFilterChange('condition', e.target.value)}>
        <MenuItem value="">All</MenuItem>
        <MenuItem value="brand_new">Brand New</MenuItem>
        <MenuItem value="used">Used</MenuItem>
        <MenuItem value="refurbished">Refurbished</MenuItem>
      </TextField>
      <TextField fullWidth label="District" size="small" select sx={{ mb: 2 }}
        value={filters.district} onChange={(e) => handleFilterChange('district', e.target.value)}>
        <MenuItem value="">All Districts</MenuItem>
        {districts.map(d => <MenuItem key={d} value={d}>{d}</MenuItem>)}
      </TextField>
      <Grid container spacing={1} sx={{ mb: 2 }}>
        <Grid size={6}>
          <TextField fullWidth label="Min Price" size="small" type="number"
            value={filters.min_price} onChange={(e) => handleFilterChange('min_price', e.target.value)} />
        </Grid>
        <Grid size={6}>
          <TextField fullWidth label="Max Price" size="small" type="number"
            value={filters.max_price} onChange={(e) => handleFilterChange('max_price', e.target.value)} />
        </Grid>
      </Grid>
      <TextField fullWidth label="RAM" size="small" select sx={{ mb: 2 }}
        value={filters.ram} onChange={(e) => handleFilterChange('ram', e.target.value)}>
        <MenuItem value="">All</MenuItem>
        {['3GB', '4GB', '6GB', '8GB', '12GB', '16GB'].map(r => <MenuItem key={r} value={r}>{r}</MenuItem>)}
      </TextField>
      <TextField fullWidth label="Storage" size="small" select sx={{ mb: 2 }}
        value={filters.storage} onChange={(e) => handleFilterChange('storage', e.target.value)}>
        <MenuItem value="">All</MenuItem>
        {['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'].map(s => <MenuItem key={s} value={s}>{s}</MenuItem>)}
      </TextField>
      <FormControlLabel
        control={<Checkbox checked={filters.five_g_support === 'true'} onChange={(e) => handleFilterChange('five_g_support', e.target.checked ? 'true' : '')} />}
        label="5G Support"
      />
    </Box>
  );

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
        <Typography variant="h4" fontWeight={700}>
          {filters.search ? `Results for "${filters.search}"` : 'All Products'}
        </Typography>
        <Box sx={{ display: 'flex', gap: 2, alignItems: 'center' }}>
          {isMobile && (
            <Button startIcon={<FilterList />} variant="outlined" onClick={() => setFilterDrawerOpen(true)}>
              Filters
            </Button>
          )}
          <TextField size="small" select label="Sort By" sx={{ minWidth: 150 }}
            value={filters.sort_by} onChange={(e) => handleFilterChange('sort_by', e.target.value)}>
            <MenuItem value="created_at">Newest</MenuItem>
            <MenuItem value="price">Price</MenuItem>
            <MenuItem value="views_count">Most Viewed</MenuItem>
            <MenuItem value="favorites_count">Most Popular</MenuItem>
          </TextField>
        </Box>
      </Box>

      <Grid container spacing={3}>
        {!isMobile && (
          <Grid size={{ xs: 12, md: 3 }}>
            <Paper><FilterPanel /></Paper>
          </Grid>
        )}
        <Grid size={{ xs: 12, md: isMobile ? 12 : 9 }}>
          {loading ? (
            <Box sx={{ display: 'flex', justifyContent: 'center', py: 5 }}><CircularProgress /></Box>
          ) : items.length === 0 ? (
            <Box sx={{ textAlign: 'center', py: 5 }}>
              <Typography variant="h6" color="text.secondary">No products found</Typography>
              <Button onClick={clearFilters} sx={{ mt: 2 }}>Clear Filters</Button>
            </Box>
          ) : (
            <>
              <Typography variant="body2" color="text.secondary" sx={{ mb: 2 }}>
                {pagination?.total || 0} products found
              </Typography>
              <Grid container spacing={2}>
                {items.map((product) => (
                  <Grid size={{ xs: 12, sm: 6, md: 4 }} key={product.id}>
                    <ProductCard product={product} />
                  </Grid>
                ))}
              </Grid>
              {pagination && pagination.lastPage > 1 && (
                <Box sx={{ display: 'flex', justifyContent: 'center', mt: 4 }}>
                  <Pagination
                    count={pagination.lastPage}
                    page={Number(filters.page)}
                    onChange={(e, page) => handleFilterChange('page', page)}
                    color="primary"
                  />
                </Box>
              )}
            </>
          )}
        </Grid>
      </Grid>

      <Drawer anchor="left" open={filterDrawerOpen} onClose={() => setFilterDrawerOpen(false)}>
        <Box sx={{ width: 300 }}>
          <Box sx={{ display: 'flex', justifyContent: 'flex-end', p: 1 }}>
            <IconButton onClick={() => setFilterDrawerOpen(false)}><Close /></IconButton>
          </Box>
          <FilterPanel />
        </Box>
      </Drawer>
    </Container>
  );
}

export default ProductsPage;
