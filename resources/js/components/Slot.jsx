export default function Slot({ title, content }) {
  if (!title && !content) return null;

  return (
    <div>
      {title && (
        <div>
          {title}
        </div>
      )}

      {content && (
        <div>
          {content}
        </div>
      )}
    </div>
  );
}
