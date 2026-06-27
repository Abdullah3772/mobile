import React, { useEffect, useState } from 'react';
import {
  Typography, Box, Paper, Table, TableBody, TableCell, TableContainer, TableHead,
  TableRow, Button, Chip, IconButton, CircularProgress, Dialog, DialogTitle,
  DialogContent, DialogActions, TextField, MenuItem, Grid,
} from '@mui/material';
import { Add, Delete } from '@mui/icons-material';
import { shopProductAPI } from '../../api/endpoints';
import { formatPrice, getConditionLabel, getConditionColor } from '../../utils/helpers';

function ShopProducts() {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [addDialog, setAddDialog] = useState(false);
  const [form, setForm] = useState({
    title: '', model: '', condition: 'brand_new', category_id: '', brand_id: '',
    storage: '', ram: '', color: '', price: '', discount_price: '', warranty: '',
    network_type: '', battery_health: '', stock_quantity: 1, description: '',
  });

  const fetchProducts = () => {
    setLoading(true);
    shopProductAPI.list().then(res => {
      setProducts(res.data.data || []);
      setLoading(false);
    });
  };

  useEffect(() => { fetchProducts(); }, []);

  const handleCreate = async () => {
    try {
      const formData = new FormData();
      Object.entries(form).forEach(([k, v]) => { if (v) formData.append(k, v); });
      await shopProductAPI.create(formData);
      setAddDialog(false);
      fetchProducts();
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to create product');
    }
  };

  const handleMarkSold = async (id) => {
    await shopProductAPI.markSold(id);
    fetchProducts();
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this product?')) return;
    await shopProductAPI.delete(id);
    fetchProducts();
  };

  return (
    <Box>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
        <Typography variant="h4" fontWeight={700}>My Products</Typography>
        <Button variant="contained" startIcon={<Add />} onClick={() => setAddDialog(true)}>Add Product</Button>
      </Box>

      {loading ? <CircularProgress /> : (
        <TableContainer component={Paper}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>Title</TableCell>
                <TableCell>Condition</TableCell>
                <TableCell>Price</TableCell>
                <TableCell>Stock</TableCell>
                <TableCell>Status</TableCell>
                <TableCell>Actions</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {products.map((p) => (
                <TableRow key={p.id}>
                  <TableCell>{p.title}</TableCell>
                  <TableCell><Chip label={getConditionLabel(p.condition)} color={getConditionColor(p.condition)} size="small" /></TableCell>
                  <TableCell>{formatPrice(p.discount_price || p.price)}</TableCell>
                  <TableCell>{p.stock_quantity}</TableCell>
                  <TableCell><Chip label={p.status} size="small" color={p.status === 'active' ? 'success' : 'default'} /></TableCell>
                  <TableCell>
                    <Button size="small" onClick={() => handleMarkSold(p.id)}>Mark Sold</Button>
                    <IconButton color="error" size="small" onClick={() => handleDelete(p.id)}><Delete fontSize="small" /></IconButton>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      )}

      <Dialog open={addDialog} onClose={() => setAddDialog(false)} maxWidth="md" fullWidth>
        <DialogTitle>Add New Product</DialogTitle>
        <DialogContent>
          <Grid container spacing={2} sx={{ mt: 1 }}>
            <Grid item xs={12}><TextField fullWidth label="Title" required value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} /></Grid>
            <Grid item xs={6}><TextField fullWidth label="Model" value={form.model} onChange={(e) => setForm({ ...form, model: e.target.value })} /></Grid>
            <Grid item xs={6}>
              <TextField fullWidth label="Condition" select value={form.condition} onChange={(e) => setForm({ ...form, condition: e.target.value })}>
                <MenuItem value="brand_new">Brand New</MenuItem>
                <MenuItem value="used">Used</MenuItem>
                <MenuItem value="refurbished">Refurbished</MenuItem>
              </TextField>
            </Grid>
            <Grid item xs={6}><TextField fullWidth label="Storage" value={form.storage} onChange={(e) => setForm({ ...form, storage: e.target.value })} /></Grid>
            <Grid item xs={6}><TextField fullWidth label="RAM" value={form.ram} onChange={(e) => setForm({ ...form, ram: e.target.value })} /></Grid>
            <Grid item xs={6}><TextField fullWidth label="Color" value={form.color} onChange={(e) => setForm({ ...form, color: e.target.value })} /></Grid>
            <Grid item xs={6}><TextField fullWidth label="Price" type="number" required value={form.price} onChange={(e) => setForm({ ...form, price: e.target.value })} /></Grid>
            <Grid item xs={6}><TextField fullWidth label="Discount Price" type="number" value={form.discount_price} onChange={(e) => setForm({ ...form, discount_price: e.target.value })} /></Grid>
            <Grid item xs={6}><TextField fullWidth label="Stock Quantity" type="number" value={form.stock_quantity} onChange={(e) => setForm({ ...form, stock_quantity: e.target.value })} /></Grid>
            <Grid item xs={6}><TextField fullWidth label="Warranty" value={form.warranty} onChange={(e) => setForm({ ...form, warranty: e.target.value })} /></Grid>
            <Grid item xs={6}><TextField fullWidth label="Network Type" value={form.network_type} onChange={(e) => setForm({ ...form, network_type: e.target.value })} /></Grid>
            <Grid item xs={12}><TextField fullWidth label="Description" multiline rows={3} value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} /></Grid>
          </Grid>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setAddDialog(false)}>Cancel</Button>
          <Button variant="contained" onClick={handleCreate}>Create Product</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}

export default ShopProducts;
