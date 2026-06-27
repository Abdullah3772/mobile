import React from 'react';
import { Link } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import {
  Card, CardMedia, CardContent, Typography, Box, Chip, IconButton, Tooltip,
} from '@mui/material';
import { FavoriteBorder, CompareArrows, Verified, Store } from '@mui/icons-material';
import { formatPrice, getDiscountPercentage, getConditionLabel, getConditionColor, getImageUrl } from '../../utils/helpers';
import { addToCompare, removeFromCompare } from '../../store/slices/cartSlice';
import { wishlistAPI } from '../../api/endpoints';

function ProductCard({ product, onWishlistToggle }) {
  const dispatch = useDispatch();
  const { compareList } = useSelector((state) => state.cart);
  const { user } = useSelector((state) => state.auth);
  const isInCompare = compareList.find(p => p.id === product.id);
  const discount = getDiscountPercentage(product.price, product.discount_price);
  const imageUrl = product.primary_image ? getImageUrl(product.primary_image.image_path) : '/placeholder.png';

  const handleWishlist = async (e) => {
    e.preventDefault();
    if (!user) return;
    try {
      await wishlistAPI.toggle(product.id);
      if (onWishlistToggle) onWishlistToggle();
    } catch (err) {
      console.error(err);
    }
  };

  const handleCompare = (e) => {
    e.preventDefault();
    if (isInCompare) {
      dispatch(removeFromCompare(product.id));
    } else {
      dispatch(addToCompare(product));
    }
  };

  return (
    <Card sx={{ height: '100%', display: 'flex', flexDirection: 'column', position: 'relative', transition: '0.3s', '&:hover': { transform: 'translateY(-4px)', boxShadow: 6 } }}>
      {discount > 0 && (
        <Chip label={`-${discount}%`} color="error" size="small" sx={{ position: 'absolute', top: 8, left: 8, zIndex: 1, fontWeight: 700 }} />
      )}
      <Box sx={{ position: 'absolute', top: 8, right: 8, zIndex: 1, display: 'flex', flexDirection: 'column', gap: 0.5 }}>
        {user && (
          <Tooltip title="Wishlist">
            <IconButton size="small" onClick={handleWishlist} sx={{ bgcolor: 'rgba(255,255,255,0.9)', '&:hover': { bgcolor: 'white' } }}>
              <FavoriteBorder fontSize="small" color="error" />
            </IconButton>
          </Tooltip>
        )}
        <Tooltip title={isInCompare ? 'Remove from compare' : 'Add to compare'}>
          <IconButton size="small" onClick={handleCompare} sx={{ bgcolor: isInCompare ? 'primary.main' : 'rgba(255,255,255,0.9)', color: isInCompare ? 'white' : 'inherit', '&:hover': { bgcolor: isInCompare ? 'primary.dark' : 'white' } }}>
            <CompareArrows fontSize="small" />
          </IconButton>
        </Tooltip>
      </Box>

      <Box component={Link} to={`/products/${product.slug}`} sx={{ textDecoration: 'none', color: 'inherit', flex: 1, display: 'flex', flexDirection: 'column' }}>
        <CardMedia
          component="img"
          height="200"
          image={imageUrl}
          alt={product.title}
          sx={{ objectFit: 'contain', p: 2, bgcolor: '#f8f9fa' }}
        />
        <CardContent sx={{ flex: 1, display: 'flex', flexDirection: 'column' }}>
          <Box sx={{ display: 'flex', gap: 0.5, mb: 1 }}>
            <Chip label={getConditionLabel(product.condition)} color={getConditionColor(product.condition)} size="small" />
            {product.trcsl_approved && <Chip icon={<Verified sx={{ fontSize: 14 }} />} label="TRCSL" size="small" color="success" variant="outlined" />}
          </Box>
          <Typography variant="subtitle2" fontWeight={600} sx={{ mb: 0.5, overflow: 'hidden', textOverflow: 'ellipsis', display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical' }}>
            {product.title}
          </Typography>
          {product.shop && (
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5, mb: 1 }}>
              <Store sx={{ fontSize: 14, color: 'grey.500' }} />
              <Typography variant="caption" color="text.secondary">{product.shop.name}</Typography>
              {product.shop.is_verified && <Verified sx={{ fontSize: 12, color: 'primary.main' }} />}
            </Box>
          )}
          <Box sx={{ mt: 'auto' }}>
            {product.discount_price ? (
              <Box>
                <Typography variant="body2" color="text.secondary" sx={{ textDecoration: 'line-through' }}>
                  {formatPrice(product.price)}
                </Typography>
                <Typography variant="h6" color="error.main" fontWeight={700}>
                  {formatPrice(product.discount_price)}
                </Typography>
              </Box>
            ) : (
              <Typography variant="h6" color="primary.main" fontWeight={700}>
                {formatPrice(product.price)}
              </Typography>
            )}
          </Box>
          {product.ram && product.storage && (
            <Typography variant="caption" color="text.secondary">
              {product.ram} RAM | {product.storage}
            </Typography>
          )}
        </CardContent>
      </Box>
    </Card>
  );
}

export default ProductCard;
