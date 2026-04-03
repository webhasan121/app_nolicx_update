export default function TextInput({
  type = "text",
  disabled = false,
  className = "",
  ...props
}) {
  return (
    <input
      type={type}
      disabled={disabled}
      className={`border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm ${className}`}
      {...props}
    />
  );
}
