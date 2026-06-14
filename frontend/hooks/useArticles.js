import { useState, useEffect } from 'react';
import api from '../services/api';

export function useArticles() {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [page, setPage] = useState(1);
  const [pagination, setPagination] = useState(null);

  const fetchArticles = async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await api.get('/articles', { params: { page } });
      setArticles(response.data.data);
      setPagination({
        currentPage: response.data.current_page,
        lastPage: response.data.last_page,
        total: response.data.total,
      });
    } catch (err) {
      setError('Erreur lors du chargement des articles.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchArticles();
  }, [page]);

  const handleCreate = async (formData) => {
    try {
      await api.post('/articles', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      fetchArticles();
    } catch (err) {
      setError('Erreur lors de la création de l\'article.');
    }
  };

  const handleDelete = async (id) => {
    try {
      await api.delete(`/articles/${id}`);
      fetchArticles();
    } catch (err) {
      setError('Erreur lors de la suppression de l\'article.');
    }
  };

  return {
    articles,
    loading,
    error,
    page,
    setPage,
    pagination,
    handleCreate,
    handleDelete,
  };
}