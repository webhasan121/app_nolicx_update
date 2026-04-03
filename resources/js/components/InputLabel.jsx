export default function InputLabel({
  htmlFor,
  children,
  className = "",
  ...props
}) {
  return (
    <label
      htmlFor={htmlFor}
      className={`block font-medium text-sm text-gray-700 ${className}`}
      {...props}
    >
      {children}
    </label>
  );
}
