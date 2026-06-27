import React from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { Link } from 'react-router-dom';
import {
  Container, Typography, Box, Paper, Table, TableBody, TableCell, TableContainer,
  TableHead, TableRow, Button, IconButton,
} from '@mui/material';
import { Delete, CompareArrows } from '@mui/icons-material';
import { removeFromCompare, clearCompare } from '../../store/slices/cartSlice';
import { formatPrice, getConditionLabel, getImageUrl } from '../../utils/helpers';

function ComparePage() {
  const dispatch = useDispatch();
  const { compareList } = useSelector((state) => state.cart);

  if (compareList.length === 0) {
    return (
      <Container maxWidth="md" sx={{ py: 8, textAlign: 'center' }}>
        <CompareArrows sx={{ fontSize: 64, color: 'grey.400', mb: 2 }} />
        <Typography variant="h5" fontWeight={600} gutterBottom>No products to compare</Typography>
        <Typography color="text.secondary" sx={{ mb: 3 }}>Add products to compare from product listings</Typography>
        <Button variant="contained" component={Link} to="/products">Browse Products</Button>
      </Container>
    );
  }

  const specs = [
    { label: 'Price', key: 'price', format: formatPrice },
    { label: 'Discount Price', key: 'discount_price', format: formatPrice },
    { label: 'Condition', key: 'condition', format: getConditionLabel },
    { label: 'Brand', key: 'brand', format: (v) => v?.name || '-' },
    { label: 'RAM', key: 'ram' },
    { label: 'Storage', key: 'storage' },
    { label: 'Camera', key: 'camera' },
    { label: 'Battery', key: 'battery' },
    { label: 'Processor', key: 'processor' },
    { label: 'Screen Size', key: 'screen_size' },
    { label: 'Network', key: 'network_type' },
    { label: '5G Support', key: 'five_g_support', format: (v) => v ? 'Yes' : 'No' },
    { label: 'Warranty', key: 'warranty' },
    { label: 'TRCSL Approved', key: 'trcsl_approved', format: (v) => v ? 'Yes' : 'No' },
    { label: 'Battery Health', key: 'battery_health' },
    { label: 'EMI Available', key: 'emi_available', format: (v) => v ? 'Yes' : 'No' },
  ];

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
        <Typography variant="h4" fontWeight={700}>Compare Products ({compareList.length})</Typography>
        <Button color="error" onClick={() => dispatch(clearCompare())}>Clear All</Button>
      </Box>

      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell sx={{ fontWeight: 700, minWidth: 140 }}>Feature</TableCell>
              {compareList.map((p) => (
                <TableCell key={p.id} align="center" sx={{ minWidth: 200 }}>
                  <Box sx={{ position: 'relative' }}>
                    <IconButton size="small" onClick={() => dispatch(removeFromCompare(p.id))}
                      sx={{ position: 'absolute', top: -8, right: -8 }}>
                      <Delete fontSize="small" />
                    </IconButton>
                    <Box component={Link} to={`/products/${p.slug}`} sx={{ textDecoration: 'none', color: 'inherit' }}>
                      <img src={p.primary_image ? getImageUrl(p.primary_image.image_path) : '/placeholder.png'}
                        alt={p.title} style={{ width: 80, height: 80, objectFit: 'contain' }} />
                      <Typography variant="subtitle2" fontWeight={600}>{p.title}</Typography>
                    </Box>
                  </Box>
                </TableCell>
              ))}
            </TableRow>
          </TableHead>
          <TableBody>
            {specs.map((spec) => (
              <TableRow key={spec.key} sx={{ '&:nth-of-type(odd)': { bgcolor: '#f8f9fa' } }}>
                <TableCell sx={{ fontWeight: 600 }}>{spec.label}</TableCell>
                {compareList.map((p) => {
                  const val = p[spec.key];
                  const display = spec.format ? spec.format(val) : (val || '-');
                  return <TableCell key={p.id} align="center">{display}</TableCell>;
                })}
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Container>
  );
}

export default ComparePage;
