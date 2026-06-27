export const formatPrice = (price) => {
  if (!price) return 'N/A';
  return `Rs. ${Number(price).toLocaleString('en-LK')}`;
};

export const getDiscountPercentage = (price, discountPrice) => {
  if (!discountPrice || !price) return 0;
  return Math.round(((price - discountPrice) / price) * 100);
};

export const getConditionLabel = (condition) => {
  const labels = {
    brand_new: 'Brand New',
    used: 'Used',
    refurbished: 'Refurbished',
  };
  return labels[condition] || condition;
};

export const getConditionColor = (condition) => {
  const colors = {
    brand_new: 'success',
    used: 'warning',
    refurbished: 'info',
  };
  return colors[condition] || 'default';
};

export const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    approved: 'success',
    rejected: 'error',
    suspended: 'error',
    active: 'success',
    inactive: 'default',
    sold: 'info',
    confirmed: 'success',
    completed: 'success',
    cancelled: 'error',
  };
  return colors[status] || 'default';
};

export const getImageUrl = (path) => {
  if (!path) return '/placeholder.png';
  if (path.startsWith('http')) return path;
  return `${process.env.REACT_APP_STORAGE_URL || 'http://localhost:8000/storage'}/${path}`;
};

export const districts = [
  'Colombo', 'Gampaha', 'Kalutara', 'Kandy', 'Matale', 'Nuwara Eliya',
  'Galle', 'Matara', 'Hambantota', 'Jaffna', 'Kilinochchi', 'Mannar',
  'Mullaitivu', 'Vavuniya', 'Trincomalee', 'Batticaloa', 'Ampara',
  'Kurunegala', 'Puttalam', 'Anuradhapura', 'Polonnaruwa', 'Badulla',
  'Monaragala', 'Ratnapura', 'Kegalle',
];
