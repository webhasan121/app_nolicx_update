export default function AuthSessionStatus({ status, className = "" }) {
  if (!status) return null;
  return (
    <div className={`font-medium text-sm text-green-600 ${className}`}>
      {status}
    </div>
  );
}

