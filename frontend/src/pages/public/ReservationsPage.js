import React, { useEffect, useState } from 'react';
import {
  Container, Typography, Box, Paper, Chip, Button, CircularProgress,
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
} from '@mui/material';
import { ShoppingBag } from '@mui/icons-material';
import { reservationAPI } from '../../api/endpoints';
import { getStatusColor } from '../../utils/helpers';

function ReservationsPage() {
  const [reservations, setReservations] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    reservationAPI.list().then(res => {
      setReservations(res.data.data || []);
      setLoading(false);
    });
  }, []);

  const handleCancel = async (id) => {
    if (!window.confirm('Cancel this reservation?')) return;
    await reservationAPI.cancel(id);
    setReservations(prev => prev.map(r => r.id === id ? { ...r, status: 'cancelled' } : r));
  };

  if (loading) return <Box sx={{ display: 'flex', justifyContent: 'center', py: 10 }}><CircularProgress /></Box>;

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Typography variant="h4" fontWeight={700} gutterBottom>My Reservations</Typography>
      {reservations.length === 0 ? (
        <Box sx={{ textAlign: 'center', py: 8 }}>
          <ShoppingBag sx={{ fontSize: 64, color: 'grey.400', mb: 2 }} />
          <Typography variant="h6" color="text.secondary">No reservations yet</Typography>
        </Box>
      ) : (
        <TableContainer component={Paper}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>Product</TableCell>
                <TableCell>Shop</TableCell>
                <TableCell>Pickup Date</TableCell>
                <TableCell>Status</TableCell>
                <TableCell>Actions</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {reservations.map((r) => (
                <TableRow key={r.id}>
                  <TableCell>{r.product?.title}</TableCell>
                  <TableCell>{r.shop?.name}</TableCell>
                  <TableCell>{r.pickup_date}</TableCell>
                  <TableCell><Chip label={r.status} color={getStatusColor(r.status)} size="small" /></TableCell>
                  <TableCell>
                    {['pending', 'confirmed'].includes(r.status) && (
                      <Button size="small" color="error" onClick={() => handleCancel(r.id)}>Cancel</Button>
                    )}
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      )}
    </Container>
  );
}

export default ReservationsPage;
