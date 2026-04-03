export default function ApplicationName() {
  const appName = import.meta.env.VITE_APP_NAME || "NOLICX";

  return (
    <div>
      {appName.toUpperCase()}
    </div>
  );
}
