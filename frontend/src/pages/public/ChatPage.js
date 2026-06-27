import React, { useEffect, useState, useRef } from 'react';
import { useSelector } from 'react-redux';
import {
  Container, Grid, Typography, Box, Paper, TextField, IconButton, List,
  ListItem, ListItemAvatar, ListItemText, Avatar, CircularProgress, Divider,
} from '@mui/material';
import { Send, Store, Person, Chat as ChatIcon } from '@mui/icons-material';
import { chatAPI } from '../../api/endpoints';

function ChatPage() {
  const { user } = useSelector((state) => state.auth);
  const [conversations, setConversations] = useState([]);
  const [selected, setSelected] = useState(null);
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState('');
  const [loading, setLoading] = useState(true);
  const messagesEndRef = useRef(null);

  useEffect(() => {
    chatAPI.conversations().then(res => {
      setConversations(res.data.data || []);
      setLoading(false);
    });
  }, []);

  useEffect(() => {
    if (selected) {
      chatAPI.messages(selected.id).then(res => {
        setMessages(res.data.data || []);
        setTimeout(() => messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' }), 100);
      });
    }
  }, [selected]);

  const handleSend = async () => {
    if (!newMessage.trim() || !selected) return;
    try {
      const res = await chatAPI.sendMessage(selected.id, { message: newMessage, type: 'text' });
      setMessages(prev => [...prev, res.data.message]);
      setNewMessage('');
      messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    } catch (err) {
      console.error(err);
    }
  };

  if (loading) return <Box sx={{ display: 'flex', justifyContent: 'center', py: 10 }}><CircularProgress /></Box>;

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Typography variant="h4" fontWeight={700} gutterBottom>Messages</Typography>
      {conversations.length === 0 ? (
        <Box sx={{ textAlign: 'center', py: 8 }}>
          <ChatIcon sx={{ fontSize: 64, color: 'grey.400', mb: 2 }} />
          <Typography variant="h6" color="text.secondary">No conversations yet</Typography>
        </Box>
      ) : (
        <Grid container spacing={2} sx={{ height: 600 }}>
          <Grid item xs={12} md={4}>
            <Paper sx={{ height: '100%', overflow: 'auto' }}>
              <List>
                {conversations.map((conv) => (
                  <React.Fragment key={conv.id}>
                    <ListItem button selected={selected?.id === conv.id} onClick={() => setSelected(conv)}
                      sx={{ cursor: 'pointer', bgcolor: selected?.id === conv.id ? 'action.selected' : 'inherit' }}>
                      <ListItemAvatar>
                        <Avatar sx={{ bgcolor: 'primary.main' }}>
                          {user?.role === 'shop_owner' ? <Person /> : <Store />}
                        </Avatar>
                      </ListItemAvatar>
                      <ListItemText
                        primary={user?.role === 'shop_owner' ? conv.user?.name : conv.shop?.name}
                        secondary={conv.latest_message?.message || 'No messages'}
                        secondaryTypographyProps={{ noWrap: true }}
                      />
                    </ListItem>
                    <Divider />
                  </React.Fragment>
                ))}
              </List>
            </Paper>
          </Grid>
          <Grid item xs={12} md={8}>
            <Paper sx={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
              {selected ? (
                <>
                  <Box sx={{ p: 2, borderBottom: '1px solid #eee' }}>
                    <Typography variant="h6" fontWeight={600}>
                      {user?.role === 'shop_owner' ? selected.user?.name : selected.shop?.name}
                    </Typography>
                  </Box>
                  <Box sx={{ flex: 1, overflow: 'auto', p: 2 }}>
                    {messages.map((msg) => (
                      <Box key={msg.id} sx={{
                        display: 'flex', justifyContent: msg.sender_id === user?.id ? 'flex-end' : 'flex-start', mb: 1,
                      }}>
                        <Paper sx={{
                          p: 1.5, maxWidth: '70%', bgcolor: msg.sender_id === user?.id ? 'primary.main' : '#f0f0f0',
                          color: msg.sender_id === user?.id ? 'white' : 'inherit', borderRadius: 2,
                        }}>
                          <Typography variant="body2">{msg.message}</Typography>
                          <Typography variant="caption" sx={{ opacity: 0.7 }}>
                            {new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                          </Typography>
                        </Paper>
                      </Box>
                    ))}
                    <div ref={messagesEndRef} />
                  </Box>
                  <Box sx={{ p: 2, borderTop: '1px solid #eee', display: 'flex', gap: 1 }}>
                    <TextField fullWidth size="small" placeholder="Type a message..."
                      value={newMessage} onChange={(e) => setNewMessage(e.target.value)}
                      onKeyPress={(e) => e.key === 'Enter' && handleSend()} />
                    <IconButton color="primary" onClick={handleSend}><Send /></IconButton>
                  </Box>
                </>
              ) : (
                <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
                  <Typography color="text.secondary">Select a conversation</Typography>
                </Box>
              )}
            </Paper>
          </Grid>
        </Grid>
      )}
    </Container>
  );
}

export default ChatPage;
