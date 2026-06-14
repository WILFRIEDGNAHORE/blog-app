import { useState, useEffect } from 'react';
import api from '../services/api';

function CommentList({ articleId }) {
  const [comments, setComments] = useState([]);
  const [content, setContent] = useState('');

  const fetchComments = async () => {
    try {
      const response = await api.get(`/articles/${articleId}/comments`);
      setComments(response.data);
    } catch (err) {
      setComments([]);
    }
  };

  useEffect(() => {
    fetchComments();
  }, [articleId]);

  const handleAddComment = async () => {
    if (!content) return;
    try {
      await api.post(`/articles/${articleId}/comments`, { content });
      setContent('');
      fetchComments();
    } catch (err) {
      // erreur silencieuse pour l'instant
    }
  };

  return (
    <div style={{ marginLeft: '20px' }}>
      <h4>Commentaires ({comments.length})</h4>

      {comments.map((comment) => (
        <div key={comment.id}>
          <strong>{comment.user?.name}</strong> : {comment.content}
        </div>
      ))}

      <input
        type="text"
        value={content}
        onChange={(e) => setContent(e.target.value)}
        placeholder="Ajouter un commentaire..."
      />
      <button onClick={handleAddComment}>Commenter</button>
    </div>
  );
}

export default CommentList;