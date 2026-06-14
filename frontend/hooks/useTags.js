import { useState, useEffect } from 'react';
import api from '../services/api';

export function useTags() {
  const [tags, setTags] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchTags = async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await api.get('/tags');
      setTags(response.data);
    } catch (err) {
      setError('Erreur lors du chargement des tags.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTags();
  }, []);

  return { tags, loading, error };
}