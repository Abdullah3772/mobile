import { configureStore } from '@reduxjs/toolkit';
import authReducer from './slices/authSlice';
import homeReducer from './slices/homeSlice';
import productReducer from './slices/productSlice';
import cartReducer from './slices/cartSlice';

const store = configureStore({
  reducer: {
    auth: authReducer,
    home: homeReducer,
    products: productReducer,
    cart: cartReducer,
  },
});

export default store;
