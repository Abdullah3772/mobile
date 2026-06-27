import { createSlice } from '@reduxjs/toolkit';

const cartSlice = createSlice({
  name: 'cart',
  initialState: {
    compareList: JSON.parse(localStorage.getItem('compareList') || '[]'),
  },
  reducers: {
    addToCompare: (state, action) => {
      if (state.compareList.length < 4 && !state.compareList.find(p => p.id === action.payload.id)) {
        state.compareList.push(action.payload);
        localStorage.setItem('compareList', JSON.stringify(state.compareList));
      }
    },
    removeFromCompare: (state, action) => {
      state.compareList = state.compareList.filter(p => p.id !== action.payload);
      localStorage.setItem('compareList', JSON.stringify(state.compareList));
    },
    clearCompare: (state) => {
      state.compareList = [];
      localStorage.setItem('compareList', '[]');
    },
  },
});

export const { addToCompare, removeFromCompare, clearCompare } = cartSlice.actions;
export default cartSlice.reducer;
