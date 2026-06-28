import React, { useEffect, useState } from 'react';
import {
  Typography, Box, Grid, Button, TextField, MenuItem, Dialog,
  DialogTitle, DialogContent, DialogActions, CircularProgress, Card, CardContent,
  Chip, IconButton,
} from '@mui/material';
import { Add, Delete, LocalOffer } from '@mui/icons-material';
import { shopOfferAPI } from '../../api/endpoints';


function ShopOffers() {
  const [offers, setOffers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [dialog, setDialog] = useState(false);
  const [form, setForm] = useState({
    title: '', description: '', type: 'flash_sale', discount_percentage: '',
    starts_at: '', ends_at: '',
  });

  const fetchOffers = () => {
    setLoading(true);
    shopOfferAPI.list().then(res => {
      setOffers(res.data.data || res.data || []);
      setLoading(false);
    });
  };

  useEffect(() => { fetchOffers(); }, []);

  const handleCreate = async () => {
    try {
      await shopOfferAPI.create(form);
      setDialog(false);
      fetchOffers();
    } catch (err) {
      alert(err.response?.data?.message || 'Failed');
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this offer?')) return;
    await shopOfferAPI.delete(id);
    fetchOffers();
  };

  return (
    <Box>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
        <Typography variant="h4" fontWeight={700}>Offers & Deals</Typography>
        <Button variant="contained" startIcon={<Add />} onClick={() => setDialog(true)}>Create Offer</Button>
      </Box>

      {loading ? <CircularProgress /> : offers.length === 0 ? (
        <Box sx={{ textAlign: 'center', py: 8 }}>
          <LocalOffer sx={{ fontSize: 64, color: 'grey.400', mb: 2 }} />
          <Typography color="text.secondary">No offers yet. Create your first offer!</Typography>
        </Box>
      ) : (
        <Grid container spacing={3}>
          {offers.map((offer) => (
            <Grid size={{ xs: 12, sm: 6, md: 4 }} key={offer.id}>
              <Card>
                <CardContent>
                  <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
                    <Chip label={offer.type?.replace('_', ' ')} color="secondary" size="small" />
                    <IconButton size="small" color="error" onClick={() => handleDelete(offer.id)}><Delete fontSize="small" /></IconButton>
                  </Box>
                  <Typography variant="h6" fontWeight={600}>{offer.title}</Typography>
                  <Typography variant="body2" color="text.secondary" sx={{ mb: 1 }}>{offer.description}</Typography>
                  {offer.discount_percentage && (
                    <Chip label={`${offer.discount_percentage}% OFF`} color="error" />
                  )}
                  <Typography variant="caption" display="block" sx={{ mt: 1 }}>
                    {offer.starts_at} - {offer.ends_at}
                  </Typography>
                </CardContent>
              </Card>
            </Grid>
          ))}
        </Grid>
      )}

      <Dialog open={dialog} onClose={() => setDialog(false)} maxWidth="sm" fullWidth>
        <DialogTitle>Create Offer</DialogTitle>
        <DialogContent>
          <TextField fullWidth label="Title" margin="normal" required value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} />
          <TextField fullWidth label="Description" margin="normal" multiline rows={2} value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />
          <TextField fullWidth label="Type" margin="normal" select value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}>
            <MenuItem value="flash_sale">Flash Sale</MenuItem>
            <MenuItem value="weekend_offer">Weekend Offer</MenuItem>
            <MenuItem value="clearance_sale">Clearance Sale</MenuItem>
          </TextField>
          <TextField fullWidth label="Discount %" type="number" margin="normal" value={form.discount_percentage} onChange={(e) => setForm({ ...form, discount_percentage: e.target.value })} />
          <TextField fullWidth label="Start Date" type="date" margin="normal" InputLabelProps={{ shrink: true }} value={form.starts_at} onChange={(e) => setForm({ ...form, starts_at: e.target.value })} />
          <TextField fullWidth label="End Date" type="date" margin="normal" InputLabelProps={{ shrink: true }} value={form.ends_at} onChange={(e) => setForm({ ...form, ends_at: e.target.value })} />
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDialog(false)}>Cancel</Button>
          <Button variant="contained" onClick={handleCreate}>Create</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}

export default ShopOffers;
