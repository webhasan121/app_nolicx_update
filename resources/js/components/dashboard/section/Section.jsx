import React from "react";

export default function SectionSection({ children, className = "", ...props }) {
  return (
    <div
      className={`p-3 sm:p-6 mb-4  shadow-sm sm:rounded-lg bg-gray-50 ${className}`}
      {...props}
    >
      {children}
    </div>
  );
}
