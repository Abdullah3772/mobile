import React, { useEffect, useState } from 'react';
import {
  Typography, Box, Paper, List, ListItem, ListItemText, ListItemSecondaryAction,
  IconButton, Button, Dialog, DialogTitle, DialogContent, DialogActions, TextField,
  MenuItem, CircularProgress, Collapse,
} from '@mui/material';
import { Add, Edit, Delete, ExpandMore, ExpandLess } from '@mui/icons-material';
import { adminAPI } from '../../api/endpoints';

function AdminCategories() {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [dialog, setDialog] = useState({ open: false, mode: 'create', data: { name: '', description: '', parent_id: '', sort_order: 0 } });
  const [expanded, setExpanded] = useState({});

  const fetchCategories = () => {
    setLoading(true);
    adminAPI.categories().then(res => {
      setCategories(res.data.categories || []);
      setLoading(false);
    });
  };

  useEffect(() => { fetchCategories(); }, []);

  const handleSave = async () => {
    if (dialog.mode === 'create') {
      await adminAPI.createCategory(dialog.data);
    } else {
      await adminAPI.updateCategory(dialog.data.id, dialog.data);
    }
    setDialog({ ...dialog, open: false });
    fetchCategories();
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this category?')) return;
    await adminAPI.deleteCategory(id);
    fetchCategories();
  };

  if (loading) return <CircularProgress />;

  return (
    <Box>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
        <Typography variant="h4" fontWeight={700}>Categories</Typography>
        <Button variant="contained" startIcon={<Add />}
          onClick={() => setDialog({ open: true, mode: 'create', data: { name: '', description: '', parent_id: '', sort_order: 0 } })}>
          Add Category
        </Button>
      </Box>

      <Paper>
        <List>
          {categories.map((cat) => (
            <React.Fragment key={cat.id}>
              <ListItem>
                <ListItemText primary={cat.name} secondary={`${cat.children?.length || 0} subcategories`} />
                <ListItemSecondaryAction>
                  {cat.children?.length > 0 && (
                    <IconButton onClick={() => setExpanded({ ...expanded, [cat.id]: !expanded[cat.id] })}>
                      {expanded[cat.id] ? <ExpandLess /> : <ExpandMore />}
                    </IconButton>
                  )}
                  <IconButton onClick={() => setDialog({ open: true, mode: 'edit', data: cat })}><Edit /></IconButton>
                  <IconButton color="error" onClick={() => handleDelete(cat.id)}><Delete /></IconButton>
                </ListItemSecondaryAction>
              </ListItem>
              {cat.children?.length > 0 && (
                <Collapse in={expanded[cat.id]}>
                  <List sx={{ pl: 4 }}>
                    {cat.children.map((child) => (
                      <ListItem key={child.id}>
                        <ListItemText primary={child.name} />
                        <ListItemSecondaryAction>
                          <IconButton onClick={() => setDialog({ open: true, mode: 'edit', data: child })}><Edit fontSize="small" /></IconButton>
                          <IconButton color="error" onClick={() => handleDelete(child.id)}><Delete fontSize="small" /></IconButton>
                        </ListItemSecondaryAction>
                      </ListItem>
                    ))}
                  </List>
                </Collapse>
              )}
            </React.Fragment>
          ))}
        </List>
      </Paper>

      <Dialog open={dialog.open} onClose={() => setDialog({ ...dialog, open: false })} maxWidth="sm" fullWidth>
        <DialogTitle>{dialog.mode === 'create' ? 'Add' : 'Edit'} Category</DialogTitle>
        <DialogContent>
          <TextField fullWidth label="Name" margin="normal" required
            value={dialog.data.name} onChange={(e) => setDialog({ ...dialog, data: { ...dialog.data, name: e.target.value } })} />
          <TextField fullWidth label="Description" margin="normal" multiline rows={2}
            value={dialog.data.description || ''} onChange={(e) => setDialog({ ...dialog, data: { ...dialog.data, description: e.target.value } })} />
          <TextField fullWidth label="Parent Category" margin="normal" select
            value={dialog.data.parent_id || ''} onChange={(e) => setDialog({ ...dialog, data: { ...dialog.data, parent_id: e.target.value } })}>
            <MenuItem value="">None (Main Category)</MenuItem>
            {categories.map(c => <MenuItem key={c.id} value={c.id}>{c.name}</MenuItem>)}
          </TextField>
          <TextField fullWidth label="Sort Order" type="number" margin="normal"
            value={dialog.data.sort_order || 0} onChange={(e) => setDialog({ ...dialog, data: { ...dialog.data, sort_order: Number(e.target.value) } })} />
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDialog({ ...dialog, open: false })}>Cancel</Button>
          <Button variant="contained" onClick={handleSave}>Save</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}

export default AdminCategories;
