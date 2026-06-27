import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import {
  Container, Paper, Typography, TextField, Button, Box, Alert, CircularProgress,
  MenuItem,
} from '@mui/material';
import { registerUser, clearError } from '../../store/slices/authSlice';
import { districts } from '../../utils/helpers';

function RegisterPage() {
  const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '', phone: '', district: '' });
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { loading, error } = useSelector((state) => state.auth);

  const handleSubmit = async (e) => {
    e.preventDefault();
    dispatch(clearError());
    const result = await dispatch(registerUser(form));
    if (registerUser.fulfilled.match(result)) navigate('/');
  };

  return (
    <Container maxWidth="sm" sx={{ py: 8 }}>
      <Paper sx={{ p: 4 }}>
        <Typography variant="h4" textAlign="center" fontWeight={700} gutterBottom>Create Account</Typography>
        <Typography variant="body1" textAlign="center" color="text.secondary" sx={{ mb: 3 }}>
          Join SmartDeals.lk today
        </Typography>
        {error && <Alert severity="error" sx={{ mb: 2 }}>{typeof error === 'object' ? JSON.stringify(error) : error}</Alert>}
        <Box component="form" onSubmit={handleSubmit}>
          <TextField fullWidth label="Full Name" margin="normal" required
            value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
          <TextField fullWidth label="Email" type="email" margin="normal" required
            value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
          <TextField fullWidth label="Phone" margin="normal"
            value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} />
          <TextField fullWidth label="District" margin="normal" select
            value={form.district} onChange={(e) => setForm({ ...form, district: e.target.value })}>
            {districts.map((d) => <MenuItem key={d} value={d}>{d}</MenuItem>)}
          </TextField>
          <TextField fullWidth label="Password" type="password" margin="normal" required
            value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} />
          <TextField fullWidth label="Confirm Password" type="password" margin="normal" required
            value={form.password_confirmation} onChange={(e) => setForm({ ...form, password_confirmation: e.target.value })} />
          <Button fullWidth type="submit" variant="contained" size="large" sx={{ mt: 3 }} disabled={loading}>
            {loading ? <CircularProgress size={24} /> : 'Register'}
          </Button>
        </Box>
        <Typography variant="body2" textAlign="center" sx={{ mt: 3 }}>
          Already have an account? <Box component={Link} to="/login" sx={{ color: 'primary.main' }}>Sign In</Box>
        </Typography>
      </Paper>
    </Container>
  );
}

export default RegisterPage;
