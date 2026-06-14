import { useArticles } from '../hooks/useArticles';
import { useTags } from '../hooks/useTags';
import ArticleItem from '../components/ArticleItem';
import ArticleForm from '../components/ArticleForm';

function App() {
  const { tags } = useTags();
  const {
    articles,
    loading,
    error,
    page,
    setPage,
    pagination,
    handleCreate,
    handleDelete,
  } = useArticles();

  return (
    <div style={{ maxWidth: '800px', margin: '0 auto', padding: '20px' }}>
      <h1>Blog</h1>

      {error && <p style={{ color: 'red' }}>{error}</p>}

      <ArticleForm tags={tags} onSubmit={handleCreate} />

      <hr />

      {loading && <p>Chargement...</p>}

      {articles.map((article) => (
        <ArticleItem
          key={article.id}
          article={article}
          onDelete={handleDelete}
        />
      ))}

      {pagination && pagination.lastPage > 1 && (
        <div>
          <button
            onClick={() => setPage(page - 1)}
            disabled={page === 1}
          >
            Précédent
          </button>
          <span>
            {' '}
            Page {pagination.currentPage} sur {pagination.lastPage}{' '}
          </span>
          <button
            onClick={() => setPage(page + 1)}
            disabled={page === pagination.lastPage}
          >
            Suivant
          </button>
        </div>
      )}
    </div>
  );
}

export default App;