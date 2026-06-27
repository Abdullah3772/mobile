import React, { useEffect, useState } from 'react';
import {
  Typography, Box, Paper, Table, TableBody, TableCell, TableContainer, TableHead,
  TableRow, Button, Chip, CircularProgress,
} from '@mui/material';
import { shopReservationAPI } from '../../api/endpoints';
import { getStatusColor } from '../../utils/helpers';

function ShopReservations() {
  const [reservations, setReservations] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchReservations = () => {
    setLoading(true);
    shopReservationAPI.list().then(res => {
      setReservations(res.data.data || []);
      setLoading(false);
    });
  };

  useEffect(() => { fetchReservations(); }, []);

  const handleAccept = async (id) => { await shopReservationAPI.accept(id); fetchReservations(); };
  const handleReject = async (id) => { await shopReservationAPI.reject(id, 'Not available'); fetchReservations(); };
  const handleComplete = async (id) => { await shopReservationAPI.complete(id); fetchReservations(); };

  return (
    <Box>
      <Typography variant="h4" fontWeight={700} gutterBottom>Reservations</Typography>
      {loading ? <CircularProgress /> : (
        <TableContainer component={Paper}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>Product</TableCell>
                <TableCell>Customer</TableCell>
                <TableCell>Phone</TableCell>
                <TableCell>Pickup Date</TableCell>
                <TableCell>Status</TableCell>
                <TableCell>Actions</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {reservations.map((r) => (
                <TableRow key={r.id}>
                  <TableCell>{r.product?.title}</TableCell>
                  <TableCell>{r.customer_name}</TableCell>
                  <TableCell>{r.customer_phone}</TableCell>
                  <TableCell>{r.pickup_date}</TableCell>
                  <TableCell><Chip label={r.status} color={getStatusColor(r.status)} size="small" /></TableCell>
                  <TableCell>
                    <Box sx={{ display: 'flex', gap: 1 }}>
                      {r.status === 'pending' && (
                        <>
                          <Button size="small" color="success" variant="contained" onClick={() => handleAccept(r.id)}>Accept</Button>
                          <Button size="small" color="error" variant="outlined" onClick={() => handleReject(r.id)}>Reject</Button>
                        </>
                      )}
                      {r.status === 'confirmed' && (
                        <Button size="small" color="primary" variant="contained" onClick={() => handleComplete(r.id)}>Complete</Button>
                      )}
                    </Box>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      )}
    </Box>
  );
}

export default ShopReservations;
