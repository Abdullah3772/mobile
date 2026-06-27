import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import { homeAPI } from '../../api/endpoints';

export const fetchHome = createAsyncThunk('home/fetch', async () => {
  const res = await homeAPI.getHome();
  return res.data;
});

const homeSlice = createSlice({
  name: 'home',
  initialState: {
    data: null,
    loading: false,
    error: null,
  },
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchHome.pending, (state) => { state.loading = true; })
      .addCase(fetchHome.fulfilled, (state, action) => {
        state.loading = false;
        state.data = action.payload;
      })
      .addCase(fetchHome.rejected, (state, action) => {
        state.loading = false;
        state.error = action.error.message;
      });
  },
});

export default homeSlice.reducer;
