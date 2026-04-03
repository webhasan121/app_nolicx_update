export default function InputError({ messages = [], className = "" }) {
  if (!messages || messages.length === 0) return null;

  return (
    <ul className={`text-sm text-red-600 space-y-1 ${className}`}>
      {(Array.isArray(messages) ? messages : [messages]).map((msg, i) => (
        <li key={i}>{msg}</li>
      ))}
    </ul>
  );
}
