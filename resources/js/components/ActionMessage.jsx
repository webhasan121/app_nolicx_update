import { useEffect, useRef, useState } from "react";

export default function ActionMessage({
  on = null,
  show = false,
  className = "text-sm text-gray-600",
  children,
}) {
  const [shown, setShown] = useState(Boolean(show));
  const timerRef = useRef(null);

  useEffect(() => {
    if (!show) return;
    setShown(true);
    clearTimeout(timerRef.current);
    timerRef.current = setTimeout(() => setShown(false), 2000);
    return () => clearTimeout(timerRef.current);
  }, [show]);

  useEffect(() => {
    if (!on) return;
    const handler = () => {
      setShown(true);
      clearTimeout(timerRef.current);
      timerRef.current = setTimeout(() => setShown(false), 2000);
    };
    window.addEventListener(on, handler);
    return () => window.removeEventListener(on, handler);
  }, [on]);

  if (!shown) return null;
  return <div className={className}>{children || "Saved."}</div>;
}

