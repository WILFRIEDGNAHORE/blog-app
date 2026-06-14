import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { articleSchema } from '../schemas/articleSchema';
import TagSelector from './TagSelector';

function ArticleForm({ tags, onSubmit }) {
  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(articleSchema),
    defaultValues: { title: '', content: '', status: 'draft' },
  });

  const [selectedTags, setSelectedTags] = useState([]);
  const [image, setImage] = useState(null);
  const [preview, setPreview] = useState(null);

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setImage(file);
      setPreview(URL.createObjectURL(file));
    }
  };

  const submit = (data) => {
    const formData = new FormData();
    formData.append('title', data.title);
    formData.append('content', data.content);
    formData.append('status', data.status);

    if (image) {
      formData.append('image', image);
    }

    selectedTags.forEach((tagId) => {
      formData.append('tags[]', tagId);
    });

    onSubmit(formData);

    reset();
    setSelectedTags([]);
    setImage(null);
    setPreview(null);
  };

  return (
    <div>
      <h2>Nouvel article</h2>

      <div>
        <input type="text" placeholder="Titre" {...register('title')} />
        {errors.title && <p style={{ color: 'red' }}>{errors.title.message}</p>}
      </div>

      <div>
        <textarea placeholder="Contenu" {...register('content')} />
        {errors.content && (
          <p style={{ color: 'red' }}>{errors.content.message}</p>
        )}
      </div>

      <div>
        <select {...register('status')}>
          <option value="draft">Brouillon</option>
          <option value="published">Publié</option>
        </select>
      </div>

      <div>
        <input type="file" accept="image/*" onChange={handleImageChange} />
        {preview && (
          <img src={preview} alt="Aperçu" style={{ width: '150px' }} />
        )}
      </div>

      <TagSelector
        tags={tags}
        selectedTags={selectedTags}
        onChange={setSelectedTags}
      />

      <button onClick={handleSubmit(submit)}>Créer l'article</button>
    </div>
  );
}

export default ArticleForm;