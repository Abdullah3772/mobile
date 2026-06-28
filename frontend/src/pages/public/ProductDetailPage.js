import React, { useEffect, useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import {
  Container, Grid, Typography, Box, Chip, Button, Paper, Dialog,
  DialogTitle, DialogContent, DialogActions, TextField, CircularProgress,
  Alert,
} from '@mui/material';
import {
  Verified, Store, FavoriteBorder, Favorite, CompareArrows, Chat, ShoppingBag,
  CheckCircle, Cancel, BatteryChargingFull, Memory, Storage, ScreenRotation,
  SignalCellular4Bar, Camera,
} from '@mui/icons-material';
import { formatPrice, getConditionLabel, getConditionColor, getImageUrl, getDiscountPercentage } from '../../utils/helpers';
import { addToCompare } from '../../store/slices/cartSlice';
import { productAPI, wishlistAPI, reservationAPI, chatAPI } from '../../api/endpoints';
import ProductCard from '../../components/common/ProductCard';

function ProductDetailPage() {
  const { slug } = useParams();
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const { user } = useSelector((state) => state.auth);
  const [product, setProduct] = useState(null);
  const [related, setRelated] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedImage, setSelectedImage] = useState(0);
  const [reserveOpen, setReserveOpen] = useState(false);
  const [reserveForm, setReserveForm] = useState({ customer_name: '', customer_phone: '', pickup_date: '', notes: '' });
  const [wishlisted, setWishlisted] = useState(false);


  useEffect(() => {
    setLoading(true);
    productAPI.show(slug).then(res => {
      setProduct(res.data.product);
      setRelated(res.data.related_products || []);
      setLoading(false);
    }).catch(() => setLoading(false));
  }, [slug]);

  useEffect(() => {
    if (user && product) {
      wishlistAPI.check(product.id).then(res => setWishlisted(res.data.wishlisted));
    }
  }, [user, product]);

  const handleWishlist = async () => {
    if (!user) return navigate('/login');
    const res = await wishlistAPI.toggle(product.id);
    setWishlisted(res.data.wishlisted);
  };

  const handleReserve = async () => {
    if (!user) return navigate('/login');
    try {
      await reservationAPI.create({ product_id: product.id, ...reserveForm });
      setReserveOpen(false);
      alert('Reservation placed successfully!');
    } catch (err) {
      alert(err.response?.data?.error || 'Failed to reserve');
    }
  };

  const handleChat = async () => {
    if (!user) return navigate('/login');
    try {
      await chatAPI.start({ shop_id: product.shop_id, product_id: product.id, message: `Hi, I'm interested in ${product.title}` });
      navigate('/chat');
    } catch (err) {
      alert('Failed to start chat');
    }
  };

  if (loading) return <Box sx={{ display: 'flex', justifyContent: 'center', py: 10 }}><CircularProgress /></Box>;
  if (!product) return <Container sx={{ py: 5 }}><Alert severity="error">Product not found</Alert></Container>;

  const discount = getDiscountPercentage(product.price, product.discount_price);
  const images = product.images || [];
  const currentImage = images[selectedImage]?.image_path;

  const specs = [
    { icon: <Memory />, label: 'RAM', value: product.ram },
    { icon: <Storage />, label: 'Storage', value: product.storage },
    { icon: <BatteryChargingFull />, label: 'Battery', value: product.battery },
    { icon: <Camera />, label: 'Camera', value: product.camera },
    { icon: <ScreenRotation />, label: 'Screen', value: product.screen_size },
    { icon: <SignalCellular4Bar />, label: 'Network', value: product.network_type },
  ].filter(s => s.value);

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Grid container spacing={4}>
        {/* Image Gallery */}
        <Grid size={{ xs: 12, md: 5 }}>
          <Paper sx={{ p: 2 }}>
            <Box sx={{ position: 'relative' }}>
              {discount > 0 && (
                <Chip label={`-${discount}% OFF`} color="error" sx={{ position: 'absolute', top: 8, left: 8, zIndex: 1, fontWeight: 700 }} />
              )}
              <Box sx={{ height: 400, display: 'flex', alignItems: 'center', justifyContent: 'center', bgcolor: '#f8f9fa', borderRadius: 2 }}>
                <img src={currentImage ? getImageUrl(currentImage) : '/placeholder.png'} alt={product.title}
                  style={{ maxWidth: '100%', maxHeight: '100%', objectFit: 'contain' }} />
              </Box>
            </Box>
            {images.length > 1 && (
              <Box sx={{ display: 'flex', gap: 1, mt: 2, overflowX: 'auto' }}>
                {images.map((img, i) => (
                  <Box key={i} onClick={() => setSelectedImage(i)}
                    sx={{ width: 64, height: 64, border: i === selectedImage ? '2px solid' : '1px solid #ddd',
                      borderColor: i === selectedImage ? 'primary.main' : '#ddd', borderRadius: 1, cursor: 'pointer',
                      overflow: 'hidden', flexShrink: 0 }}>
                    <img src={getImageUrl(img.image_path)} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                  </Box>
                ))}
              </Box>
            )}
          </Paper>
        </Grid>

        {/* Product Info */}
        <Grid size={{ xs: 12, md: 7 }}>
          <Box sx={{ display: 'flex', gap: 1, mb: 2 }}>
            <Chip label={getConditionLabel(product.condition)} color={getConditionColor(product.condition)} />
            {product.trcsl_approved && <Chip icon={<Verified />} label="TRCSL Approved" color="success" variant="outlined" />}
            {product.warranty && <Chip icon={<CheckCircle />} label={product.warranty} color="info" variant="outlined" />}
          </Box>

          <Typography variant="h4" fontWeight={700} gutterBottom>{product.title}</Typography>

          {product.brand && <Typography variant="body1" color="text.secondary" gutterBottom>Brand: {product.brand.name} | Model: {product.model}</Typography>}

          {/* Shop Info */}
          {product.shop && (
            <Box component={Link} to={`/shops/${product.shop.slug}`} sx={{ display: 'flex', alignItems: 'center', gap: 1, textDecoration: 'none', color: 'inherit', mb: 2 }}>
              <Store color="primary" />
              <Typography variant="body1" fontWeight={600}>{product.shop.name}</Typography>
              {product.shop.is_verified && <Verified sx={{ fontSize: 16, color: 'primary.main' }} />}
              <Chip label={product.shop.district} size="small" variant="outlined" />
            </Box>
          )}

          {/* Price */}
          <Paper sx={{ p: 2, mb: 3, bgcolor: '#f0f7ff' }}>
            {product.discount_price ? (
              <Box>
                <Typography variant="body1" color="text.secondary" sx={{ textDecoration: 'line-through' }}>
                  {formatPrice(product.price)}
                </Typography>
                <Typography variant="h4" color="error.main" fontWeight={800}>{formatPrice(product.discount_price)}</Typography>
                <Chip label={`Save ${formatPrice(product.price - product.discount_price)}`} color="error" size="small" />
              </Box>
            ) : (
              <Typography variant="h4" color="primary.main" fontWeight={800}>{formatPrice(product.price)}</Typography>
            )}
            {product.cash_price && <Typography variant="body2" sx={{ mt: 1 }}>Cash Price: {formatPrice(product.cash_price)}</Typography>}
            {product.card_price && <Typography variant="body2">Card Price: {formatPrice(product.card_price)}</Typography>}
            {product.emi_available && <Chip label="EMI Available" color="success" size="small" sx={{ mt: 1 }} />}
          </Paper>

          {/* Action Buttons */}
          <Box sx={{ display: 'flex', gap: 2, mb: 3, flexWrap: 'wrap' }}>
            <Button variant="contained" size="large" startIcon={<ShoppingBag />} onClick={() => user ? setReserveOpen(true) : navigate('/login')}>
              Reserve Now
            </Button>
            <Button variant="outlined" startIcon={wishlisted ? <Favorite color="error" /> : <FavoriteBorder />} onClick={handleWishlist}>
              {wishlisted ? 'Wishlisted' : 'Wishlist'}
            </Button>
            <Button variant="outlined" startIcon={<CompareArrows />} onClick={() => dispatch(addToCompare(product))}>
              Compare
            </Button>
            <Button variant="outlined" color="success" startIcon={<Chat />} onClick={handleChat}>
              Chat with Shop
            </Button>
          </Box>

          {/* Specs */}
          {specs.length > 0 && (
            <Paper sx={{ p: 2, mb: 3 }}>
              <Typography variant="h6" fontWeight={600} gutterBottom>Specifications</Typography>
              <Grid container spacing={2}>
                {specs.map((spec, i) => (
                  <Grid size={{ xs: 6, sm: 4 }} key={i}>
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                      <Box sx={{ color: 'primary.main' }}>{spec.icon}</Box>
                      <Box>
                        <Typography variant="caption" color="text.secondary">{spec.label}</Typography>
                        <Typography variant="body2" fontWeight={600}>{spec.value}</Typography>
                      </Box>
                    </Box>
                  </Grid>
                ))}
              </Grid>
            </Paper>
          )}

          {/* Used Phone Details */}
          {product.condition === 'used' && (
            <Paper sx={{ p: 2, mb: 3 }}>
              <Typography variant="h6" fontWeight={600} gutterBottom>Condition Details</Typography>
              <Grid container spacing={2}>
                {product.battery_health && <Grid size={6}><Typography variant="body2">Battery Health: <strong>{product.battery_health}</strong></Typography></Grid>}
                {product.scratches && <Grid size={6}><Typography variant="body2">Scratches: <strong>{product.scratches}</strong></Typography></Grid>}
                <Grid size={6}><Typography variant="body2">Face ID: {product.face_id_working ? <CheckCircle color="success" sx={{ fontSize: 16 }} /> : <Cancel color="error" sx={{ fontSize: 16 }} />}</Typography></Grid>
                <Grid size={6}><Typography variant="body2">Original Display: {product.original_display ? <CheckCircle color="success" sx={{ fontSize: 16 }} /> : <Cancel color="error" sx={{ fontSize: 16 }} />}</Typography></Grid>
                {product.repair_history && <Grid size={12}><Typography variant="body2">Repair History: {product.repair_history}</Typography></Grid>}
              </Grid>
            </Paper>
          )}

          {/* Trust Badges */}
          <Paper sx={{ p: 2, bgcolor: '#f0fff4' }}>
            <Typography variant="subtitle2" fontWeight={600} gutterBottom>Trust & Verification</Typography>
            <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
              {product.shop?.is_verified && <Chip icon={<Verified />} label="Verified Shop" color="primary" />}
              {product.trcsl_approved && <Chip icon={<CheckCircle />} label="TRCSL Approved" color="success" />}
              {product.warranty && <Chip icon={<CheckCircle />} label="Warranty Verified" color="info" />}
              {product.imei && <Chip label="Original IMEI" color="default" />}
            </Box>
          </Paper>
        </Grid>
      </Grid>

      {/* Description */}
      {product.description && (
        <Paper sx={{ p: 3, mt: 4 }}>
          <Typography variant="h6" fontWeight={600} gutterBottom>Description</Typography>
          <Typography variant="body1" sx={{ whiteSpace: 'pre-line' }}>{product.description}</Typography>
        </Paper>
      )}

      {/* Related Products */}
      {related.length > 0 && (
        <Box sx={{ mt: 5 }}>
          <Typography variant="h5" fontWeight={700} gutterBottom>Related Products</Typography>
          <Grid container spacing={3}>
            {related.map((p) => (
              <Grid size={{ xs: 12, sm: 6, md: 3 }} key={p.id}><ProductCard product={p} /></Grid>
            ))}
          </Grid>
        </Box>
      )}

      {/* Reserve Dialog */}
      <Dialog open={reserveOpen} onClose={() => setReserveOpen(false)} maxWidth="sm" fullWidth>
        <DialogTitle>Reserve Product</DialogTitle>
        <DialogContent>
          <Typography variant="body2" color="text.secondary" sx={{ mb: 2 }}>
            Reserve "{product.title}" for pickup
          </Typography>
          <TextField fullWidth label="Your Name" margin="normal" required
            value={reserveForm.customer_name} onChange={(e) => setReserveForm({ ...reserveForm, customer_name: e.target.value })} />
          <TextField fullWidth label="Phone Number" margin="normal" required
            value={reserveForm.customer_phone} onChange={(e) => setReserveForm({ ...reserveForm, customer_phone: e.target.value })} />
          <TextField fullWidth label="Pickup Date" type="date" margin="normal" required InputLabelProps={{ shrink: true }}
            value={reserveForm.pickup_date} onChange={(e) => setReserveForm({ ...reserveForm, pickup_date: e.target.value })} />
          <TextField fullWidth label="Notes" margin="normal" multiline rows={2}
            value={reserveForm.notes} onChange={(e) => setReserveForm({ ...reserveForm, notes: e.target.value })} />
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setReserveOpen(false)}>Cancel</Button>
          <Button variant="contained" onClick={handleReserve}>Confirm Reservation</Button>
        </DialogActions>
      </Dialog>
    </Container>
  );
}

export default ProductDetailPage;
