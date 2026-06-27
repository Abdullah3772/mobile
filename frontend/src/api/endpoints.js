import API from './axios';

// Auth
export const authAPI = {
  register: (data) => API.post('/auth/register', data),
  login: (data) => API.post('/auth/login', data),
  me: () => API.get('/auth/me'),
  logout: () => API.post('/auth/logout'),
  updateProfile: (data) => API.post('/auth/profile', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
  changePassword: (data) => API.put('/auth/password', data),
};

// Home
export const homeAPI = {
  getHome: () => API.get('/home'),
};

// Products
export const productAPI = {
  list: (params) => API.get('/products', { params }),
  featured: () => API.get('/products/featured'),
  latest: () => API.get('/products/latest'),
  show: (slug) => API.get(`/products/${slug}`),
  compare: (productIds) => API.post('/products/compare', { product_ids: productIds }),
};

// Shops
export const shopAPI = {
  list: (params) => API.get('/shops', { params }),
  show: (slug) => API.get(`/shops/${slug}`),
  topRated: () => API.get('/shops/top-rated'),
  verified: () => API.get('/shops/verified'),
};

// Categories & Brands
export const categoryAPI = {
  list: () => API.get('/categories'),
};
export const brandAPI = {
  list: () => API.get('/brands'),
};

// Wishlist
export const wishlistAPI = {
  list: () => API.get('/wishlist'),
  toggle: (productId) => API.post('/wishlist/toggle', { product_id: productId }),
  check: (productId) => API.get(`/wishlist/check/${productId}`),
};

// Reservations
export const reservationAPI = {
  list: () => API.get('/reservations'),
  create: (data) => API.post('/reservations', data),
  cancel: (id) => API.put(`/reservations/${id}/cancel`),
};

// Reviews
export const reviewAPI = {
  create: (data) => API.post('/reviews', data),
  shopReviews: (shopId) => API.get(`/reviews/shop/${shopId}`),
};

// Chat
export const chatAPI = {
  conversations: () => API.get('/chat/conversations'),
  start: (data) => API.post('/chat/start', data),
  messages: (id) => API.get(`/chat/${id}/messages`),
  sendMessage: (id, data) => API.post(`/chat/${id}/messages`, data),
  unreadCount: () => API.get('/chat/unread-count'),
};

// Notifications
export const notificationAPI = {
  list: () => API.get('/notifications'),
  markRead: (id) => API.put(`/notifications/${id}/read`),
  markAllRead: () => API.put('/notifications/read-all'),
  unreadCount: () => API.get('/notifications/unread-count'),
  delete: (id) => API.delete(`/notifications/${id}`),
};

// Complaints
export const complaintAPI = {
  list: () => API.get('/complaints'),
  create: (data) => API.post('/complaints', data),
};

// Shop Follow
export const followAPI = {
  toggle: (shopId) => API.post('/shops/follow', { shop_id: shopId }),
  following: () => API.get('/shops/following'),
  check: (shopId) => API.get(`/shops/${shopId}/following`),
};

// Shop Registration
export const shopRegAPI = {
  register: (data) => API.post('/shop/register', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
  myShop: () => API.get('/shop/my-shop'),
  update: (data) => API.put('/shop/update', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
};

// Shop Products
export const shopProductAPI = {
  list: (params) => API.get('/shop/products', { params }),
  create: (data) => API.post('/shop/products', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
  show: (id) => API.get(`/shop/products/${id}`),
  update: (id, data) => API.put(`/shop/products/${id}`, data, { headers: { 'Content-Type': 'multipart/form-data' } }),
  delete: (id) => API.delete(`/shop/products/${id}`),
  markSold: (id) => API.put(`/shop/products/${id}/sold`),
  updateStock: (id, qty) => API.put(`/shop/products/${id}/stock`, { stock_quantity: qty }),
};

// Shop Reservations
export const shopReservationAPI = {
  list: (params) => API.get('/shop/reservations', { params }),
  accept: (id) => API.put(`/shop/reservations/${id}/accept`),
  reject: (id, reason) => API.put(`/shop/reservations/${id}/reject`, { rejection_reason: reason }),
  complete: (id) => API.put(`/shop/reservations/${id}/complete`),
};

// Shop Offers
export const shopOfferAPI = {
  list: () => API.get('/shop/offers'),
  create: (data) => API.post('/shop/offers', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
  update: (id, data) => API.put(`/shop/offers/${id}`, data),
  delete: (id) => API.delete(`/shop/offers/${id}`),
};

// Shop Analytics
export const shopAnalyticsAPI = {
  dashboard: () => API.get('/shop/analytics'),
};

// Shop Ads
export const shopAdAPI = {
  packages: () => API.get('/shop/ad-packages'),
  myAds: () => API.get('/shop/my-ads'),
  purchase: (data) => API.post('/shop/ads/purchase', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
};

// Admin APIs
export const adminAPI = {
  dashboard: () => API.get('/admin/dashboard'),
  // Shops
  shops: (params) => API.get('/admin/shops', { params }),
  shopStats: () => API.get('/admin/shops/stats'),
  shopShow: (id) => API.get(`/admin/shops/${id}`),
  shopApprove: (id) => API.put(`/admin/shops/${id}/approve`),
  shopReject: (id, reason) => API.put(`/admin/shops/${id}/reject`, { reason }),
  shopSuspend: (id) => API.put(`/admin/shops/${id}/suspend`),
  shopVerify: (id) => API.put(`/admin/shops/${id}/verify`),
  // Categories
  categories: () => API.get('/admin/categories'),
  createCategory: (data) => API.post('/admin/categories', data),
  updateCategory: (id, data) => API.put(`/admin/categories/${id}`, data),
  deleteCategory: (id) => API.delete(`/admin/categories/${id}`),
  // Brands
  brands: () => API.get('/admin/brands'),
  createBrand: (data) => API.post('/admin/brands', data),
  updateBrand: (id, data) => API.put(`/admin/brands/${id}`, data),
  deleteBrand: (id) => API.delete(`/admin/brands/${id}`),
  // Products
  products: (params) => API.get('/admin/products', { params }),
  featureProduct: (id) => API.put(`/admin/products/${id}/feature`),
  deleteProduct: (id) => API.delete(`/admin/products/${id}`),
  // Reviews
  reviews: (params) => API.get('/admin/reviews', { params }),
  approveReview: (id) => API.put(`/admin/reviews/${id}/approve`),
  rejectReview: (id) => API.put(`/admin/reviews/${id}/reject`),
  // Complaints
  complaints: (params) => API.get('/admin/complaints', { params }),
  respondComplaint: (id, data) => API.put(`/admin/complaints/${id}/respond`, data),
  // Announcements
  announcements: () => API.get('/admin/announcements'),
  createAnnouncement: (data) => API.post('/admin/announcements', data),
  updateAnnouncement: (id, data) => API.put(`/admin/announcements/${id}`, data),
  deleteAnnouncement: (id) => API.delete(`/admin/announcements/${id}`),
  // Banners
  banners: () => API.get('/admin/banners'),
  createBanner: (data) => API.post('/admin/banners', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
  updateBanner: (id, data) => API.put(`/admin/banners/${id}`, data),
  deleteBanner: (id) => API.delete(`/admin/banners/${id}`),
  // Delivery Partners
  deliveryPartners: () => API.get('/admin/delivery-partners'),
  createDeliveryPartner: (data) => API.post('/admin/delivery-partners', data),
  updateDeliveryPartner: (id, data) => API.put(`/admin/delivery-partners/${id}`, data),
  deleteDeliveryPartner: (id) => API.delete(`/admin/delivery-partners/${id}`),
  // Ad Packages
  adPackages: () => API.get('/admin/ad-packages'),
  createAdPackage: (data) => API.post('/admin/ad-packages', data),
  updateAdPackage: (id, data) => API.put(`/admin/ad-packages/${id}`, data),
  deleteAdPackage: (id) => API.delete(`/admin/ad-packages/${id}`),
  // Advertisements
  advertisements: (params) => API.get('/admin/advertisements', { params }),
  approveAd: (id) => API.put(`/admin/advertisements/${id}/approve`),
  rejectAd: (id) => API.put(`/admin/advertisements/${id}/reject`),
  // Analytics
  topShops: () => API.get('/admin/analytics/top-shops'),
  mostViewed: () => API.get('/admin/analytics/most-viewed'),
  recentReservations: () => API.get('/admin/analytics/recent-reservations'),
  userStats: () => API.get('/admin/analytics/users'),
};

// Announcements
export const announcementAPI = {
  active: () => API.get('/announcements'),
};
