import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  Container, Paper, Typography, TextField, Button, Box, Alert, Grid, MenuItem,
  CircularProgress,
} from '@mui/material';
import { Store } from '@mui/icons-material';
import { shopRegAPI } from '../../api/endpoints';
import { districts } from '../../utils/helpers';

function ShopRegisterPage() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [form, setForm] = useState({
    name: '', owner_name: '', nic: '', business_registration_number: '',
    address: '', district: '', phone: '', whatsapp: '', email: '', about: '',
    google_map_lat: '', google_map_lng: '',
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    try {
      const formData = new FormData();
      Object.entries(form).forEach(([k, v]) => { if (v) formData.append(k, v); });
      await shopRegAPI.register(formData);
      navigate('/shop/dashboard');
    } catch (err) {
      setError(err.response?.data?.message || 'Registration failed');
    }
    setLoading(false);
  };

  return (
    <Container maxWidth="md" sx={{ py: 6 }}>
      <Paper sx={{ p: 4 }}>
        <Box sx={{ textAlign: 'center', mb: 4 }}>
          <Store sx={{ fontSize: 48, color: 'primary.main', mb: 1 }} />
          <Typography variant="h4" fontWeight={700}>Register Your Shop</Typography>
          <Typography color="text.secondary">Join SmartDeals.lk as a verified phone shop</Typography>
        </Box>

        {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

        <Box component="form" onSubmit={handleSubmit}>
          <Grid container spacing={2}>
            <Grid size={12}><TextField fullWidth label="Shop Name" required value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}><TextField fullWidth label="Owner Name" required value={form.owner_name} onChange={(e) => setForm({ ...form, owner_name: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}><TextField fullWidth label="NIC Number" required value={form.nic} onChange={(e) => setForm({ ...form, nic: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}><TextField fullWidth label="Business Registration Number" value={form.business_registration_number} onChange={(e) => setForm({ ...form, business_registration_number: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField fullWidth label="District" required select value={form.district} onChange={(e) => setForm({ ...form, district: e.target.value })}>
                {districts.map(d => <MenuItem key={d} value={d}>{d}</MenuItem>)}
              </TextField>
            </Grid>
            <Grid size={12}><TextField fullWidth label="Address" required value={form.address} onChange={(e) => setForm({ ...form, address: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}><TextField fullWidth label="Phone" required value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}><TextField fullWidth label="WhatsApp" value={form.whatsapp} onChange={(e) => setForm({ ...form, whatsapp: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}><TextField fullWidth label="Email" type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}><TextField fullWidth label="Latitude" value={form.google_map_lat} onChange={(e) => setForm({ ...form, google_map_lat: e.target.value })} /></Grid>
            <Grid size={{ xs: 12, sm: 6 }}><TextField fullWidth label="Longitude" value={form.google_map_lng} onChange={(e) => setForm({ ...form, google_map_lng: e.target.value })} /></Grid>
            <Grid size={12}><TextField fullWidth label="About Your Shop" multiline rows={3} value={form.about} onChange={(e) => setForm({ ...form, about: e.target.value })} /></Grid>
          </Grid>

          <Alert severity="info" sx={{ mt: 3 }}>
            Your shop will be reviewed by our admin team. You will be notified once it's approved.
          </Alert>

          <Button fullWidth type="submit" variant="contained" size="large" sx={{ mt: 3 }} disabled={loading}>
            {loading ? <CircularProgress size={24} /> : 'Submit Registration'}
          </Button>
        </Box>
      </Paper>
    </Container>
  );
}

export default ShopRegisterPage;
