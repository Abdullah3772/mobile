import React, { useEffect, useState } from 'react';
import {
  Container, Typography, Box, Paper, List, ListItem, ListItemText, IconButton,
  CircularProgress, Button, Divider,
} from '@mui/material';
import { Delete, NotificationsNone, MarkEmailRead } from '@mui/icons-material';
import { notificationAPI } from '../../api/endpoints';

function NotificationsPage() {
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    notificationAPI.list().then(res => {
      setNotifications(res.data.data || []);
      setLoading(false);
    });
  }, []);

  const markAsRead = async (id) => {
    await notificationAPI.markRead(id);
    setNotifications(prev => prev.map(n => n.id === id ? { ...n, is_read: true } : n));
  };

  const markAllRead = async () => {
    await notificationAPI.markAllRead();
    setNotifications(prev => prev.map(n => ({ ...n, is_read: true })));
  };

  const handleDelete = async (id) => {
    await notificationAPI.delete(id);
    setNotifications(prev => prev.filter(n => n.id !== id));
  };

  if (loading) return <Box sx={{ display: 'flex', justifyContent: 'center', py: 10 }}><CircularProgress /></Box>;

  return (
    <Container maxWidth="md" sx={{ py: 4 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
        <Typography variant="h4" fontWeight={700}>Notifications</Typography>
        <Button startIcon={<MarkEmailRead />} onClick={markAllRead}>Mark All Read</Button>
      </Box>
      {notifications.length === 0 ? (
        <Box sx={{ textAlign: 'center', py: 8 }}>
          <NotificationsNone sx={{ fontSize: 64, color: 'grey.400', mb: 2 }} />
          <Typography variant="h6" color="text.secondary">No notifications</Typography>
        </Box>
      ) : (
        <Paper>
          <List>
            {notifications.map((n, i) => (
              <React.Fragment key={n.id}>
                <ListItem
                  sx={{ bgcolor: n.is_read ? 'inherit' : 'action.hover', cursor: 'pointer' }}
                  onClick={() => !n.is_read && markAsRead(n.id)}
                  secondaryAction={
                    <IconButton onClick={() => handleDelete(n.id)}><Delete fontSize="small" /></IconButton>
                  }
                >
                  <ListItemText
                    primary={<Typography variant="subtitle2" fontWeight={n.is_read ? 400 : 700}>{n.title}</Typography>}
                    secondary={
                      <>
                        <Typography variant="body2">{n.message}</Typography>
                        <Typography variant="caption" color="text.secondary">
                          {new Date(n.created_at).toLocaleString()}
                        </Typography>
                      </>
                    }
                  />
                </ListItem>
                {i < notifications.length - 1 && <Divider />}
              </React.Fragment>
            ))}
          </List>
        </Paper>
      )}
    </Container>
  );
}

export default NotificationsPage;
