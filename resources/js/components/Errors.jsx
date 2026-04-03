import InputError from "./InputError";

export default function Errors({ errors = [] }) {
  const list = Array.isArray(errors)
    ? errors
    : Object.values(errors || {}).flat().filter(Boolean);

  if (!list.length) return null;

  return (
    <div>
      <ul>
        {list.map((item, idx) => (
          <li key={idx}>
            <InputError message={item} />
          </li>
        ))}
      </ul>
    </div>
  );
}

