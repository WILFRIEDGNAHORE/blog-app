function TagSelector({ tags, selectedTags, onChange }) {
  const toggleTag = (tagId) => {
    if (selectedTags.includes(tagId)) {
      onChange(selectedTags.filter((id) => id !== tagId));
    } else {
      onChange([...selectedTags, tagId]);
    }
  };

  return (
    <div>
      <p>Tags :</p>
      {tags.map((tag) => (
        <label key={tag.id} style={{ marginRight: '10px' }}>
          <input
            type="checkbox"
            checked={selectedTags.includes(tag.id)}
            onChange={() => toggleTag(tag.id)}
          />
          {tag.name}
        </label>
      ))}
    </div>
  );
}

export default TagSelector;