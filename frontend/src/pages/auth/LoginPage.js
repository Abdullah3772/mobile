import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import {
  Container, Paper, Typography, TextField, Button, Box, Alert, CircularProgress,
} from '@mui/material';
import { loginUser, clearError } from '../../store/slices/authSlice';

function LoginPage() {
  const [form, setForm] = useState({ email: '', password: '' });
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { loading, error } = useSelector((state) => state.auth);

  const handleSubmit = async (e) => {
    e.preventDefault();
    dispatch(clearError());
    const result = await dispatch(loginUser(form));
    if (loginUser.fulfilled.match(result)) {
      const user = result.payload.user;
      if (user.role === 'super_admin') navigate('/admin');
      else if (user.role === 'shop_owner') navigate('/shop/dashboard');
      else navigate('/');
    }
  };

  return (
    <Container maxWidth="sm" sx={{ py: 8 }}>
      <Paper sx={{ p: 4 }}>
        <Typography variant="h4" textAlign="center" fontWeight={700} gutterBottom>Welcome Back</Typography>
        <Typography variant="body1" textAlign="center" color="text.secondary" sx={{ mb: 3 }}>
          Sign in to SmartDeals.lk
        </Typography>
        {error && <Alert severity="error" sx={{ mb: 2 }}>{typeof error === 'string' ? error : 'Login failed'}</Alert>}
        <Box component="form" onSubmit={handleSubmit}>
          <TextField fullWidth label="Email" type="email" margin="normal" required
            value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
          <TextField fullWidth label="Password" type="password" margin="normal" required
            value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} />
          <Button fullWidth type="submit" variant="contained" size="large" sx={{ mt: 3 }} disabled={loading}>
            {loading ? <CircularProgress size={24} /> : 'Sign In'}
          </Button>
        </Box>
        <Typography variant="body2" textAlign="center" sx={{ mt: 3 }}>
          Don't have an account? <Box component={Link} to="/register" sx={{ color: 'primary.main' }}>Register</Box>
        </Typography>
      </Paper>
    </Container>
  );
}

export default LoginPage;
