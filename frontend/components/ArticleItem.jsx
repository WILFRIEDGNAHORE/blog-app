import CommentList from './CommentList';

function ArticleItem({ article, onDelete }) {
  const imageUrl = article.image
    ? `http://localhost:8000/storage/${article.image}`
    : null;

  return (
    <div
      style={{
        border: '1px solid #ccc',
        padding: '15px',
        marginBottom: '15px',
      }}
    >
      <h2>{article.title}</h2>

      <p>
        <small>
          Par {article.user?.name} — {article.status}
        </small>
      </p>

      {imageUrl && (
        <img src={imageUrl} alt={article.title} style={{ width: '300px' }} />
      )}

      <p>{article.content}</p>

      <div>
        {article.tags?.map((tag) => (
          <span
            key={tag.id}
            style={{
              background: '#eee',
              padding: '3px 8px',
              marginRight: '5px',
              borderRadius: '4px',
            }}
          >
            {tag.name}
          </span>
        ))}
      </div>

      <button onClick={() => onDelete(article.id)}>Supprimer</button>

      <CommentList articleId={article.id} />
    </div>
  );
}

export default ArticleItem;