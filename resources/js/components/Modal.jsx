import { useEffect, useRef } from "react";

export default function Modal({
  show = false,
  onClose,
  maxWidth = "2xl",
  children,
}) {
  const modalRef = useRef(null);

  const maxWidthClass = {
    sm: "sm:max-w-sm",
    md: "sm:max-w-md",
    lg: "sm:max-w-lg",
    xl: "sm:max-w-xl",
    "2xl": "sm:max-w-2xl",
  }[maxWidth];

  // ESC key close
  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === "Escape") onClose();
    };

    if (show) {
      document.body.classList.add("overflow-y-hidden");
      window.addEventListener("keydown", handleEscape);
    }

    return () => {
      document.body.classList.remove("overflow-y-hidden");
      window.removeEventListener("keydown", handleEscape);
    };
  }, [show]);

  if (!show) return null;

  return (
    <div className="fixed inset-0 z-50 px-4 py-6 overflow-y-auto sm:px-0">

      {/* BACKDROP */}
      <div
        className="fixed inset-0 transition-opacity bg-gray-500 opacity-75"
        onClick={onClose}
      ></div>

      {/* MODAL CONTENT */}
      <div
        ref={modalRef}
        className={`mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full ${maxWidthClass} sm:mx-auto`}
      >
        {children}
      </div>
    </div>
  );
}
