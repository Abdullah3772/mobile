import React, { useEffect, useState } from 'react';
import {
  Typography, Box, Paper, Table, TableBody, TableCell, TableContainer, TableHead,
  TableRow, Chip, Button, TextField, MenuItem, CircularProgress, Dialog, DialogTitle,
  DialogContent, DialogActions,
} from '@mui/material';
import { adminAPI } from '../../api/endpoints';
import { getStatusColor } from '../../utils/helpers';

function AdminShops() {
  const [shops, setShops] = useState([]);
  const [loading, setLoading] = useState(true);
  const [statusFilter, setStatusFilter] = useState('');
  const [rejectDialog, setRejectDialog] = useState({ open: false, shopId: null, reason: '' });

  const fetchShops = () => {
    setLoading(true);
    const params = {};
    if (statusFilter) params.status = statusFilter;
    adminAPI.shops(params).then(res => {
      setShops(res.data.data || []);
      setLoading(false);
    });
  };

  useEffect(() => { fetchShops(); // eslint-disable-next-line
  }, [statusFilter]);

  const handleApprove = async (id) => {
    await adminAPI.shopApprove(id);
    fetchShops();
  };

  const handleReject = async () => {
    await adminAPI.shopReject(rejectDialog.shopId, rejectDialog.reason);
    setRejectDialog({ open: false, shopId: null, reason: '' });
    fetchShops();
  };

  const handleSuspend = async (id) => {
    if (!window.confirm('Suspend this shop?')) return;
    await adminAPI.shopSuspend(id);
    fetchShops();
  };

  const handleVerify = async (id) => {
    await adminAPI.shopVerify(id);
    fetchShops();
  };

  return (
    <Box>
      <Typography variant="h4" fontWeight={700} gutterBottom>Manage Shops</Typography>
      <Box sx={{ display: 'flex', gap: 2, mb: 3 }}>
        <TextField size="small" select label="Status" sx={{ minWidth: 150 }}
          value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)}>
          <MenuItem value="">All</MenuItem>
          <MenuItem value="pending">Pending</MenuItem>
          <MenuItem value="approved">Approved</MenuItem>
          <MenuItem value="rejected">Rejected</MenuItem>
          <MenuItem value="suspended">Suspended</MenuItem>
        </TextField>
      </Box>

      {loading ? <CircularProgress /> : (
        <TableContainer component={Paper}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>Shop Name</TableCell>
                <TableCell>Owner</TableCell>
                <TableCell>District</TableCell>
                <TableCell>Status</TableCell>
                <TableCell>Verified</TableCell>
                <TableCell>Actions</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {shops.map((shop) => (
                <TableRow key={shop.id}>
                  <TableCell>{shop.name}</TableCell>
                  <TableCell>{shop.owner?.name}</TableCell>
                  <TableCell>{shop.district}</TableCell>
                  <TableCell><Chip label={shop.status} color={getStatusColor(shop.status)} size="small" /></TableCell>
                  <TableCell>{shop.is_verified ? <Chip label="Verified" color="success" size="small" /> : '-'}</TableCell>
                  <TableCell>
                    <Box sx={{ display: 'flex', gap: 1 }}>
                      {shop.status === 'pending' && (
                        <>
                          <Button size="small" color="success" variant="contained" onClick={() => handleApprove(shop.id)}>Approve</Button>
                          <Button size="small" color="error" variant="outlined"
                            onClick={() => setRejectDialog({ open: true, shopId: shop.id, reason: '' })}>Reject</Button>
                        </>
                      )}
                      {shop.status === 'approved' && !shop.is_verified && (
                        <Button size="small" color="primary" variant="outlined" onClick={() => handleVerify(shop.id)}>Verify</Button>
                      )}
                      {shop.status === 'approved' && (
                        <Button size="small" color="warning" variant="outlined" onClick={() => handleSuspend(shop.id)}>Suspend</Button>
                      )}
                    </Box>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      )}

      <Dialog open={rejectDialog.open} onClose={() => setRejectDialog({ ...rejectDialog, open: false })}>
        <DialogTitle>Reject Shop</DialogTitle>
        <DialogContent>
          <TextField fullWidth label="Rejection Reason" multiline rows={3} margin="normal"
            value={rejectDialog.reason} onChange={(e) => setRejectDialog({ ...rejectDialog, reason: e.target.value })} />
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setRejectDialog({ ...rejectDialog, open: false })}>Cancel</Button>
          <Button variant="contained" color="error" onClick={handleReject}>Reject</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}

export default AdminShops;
