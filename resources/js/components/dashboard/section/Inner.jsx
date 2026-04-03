import React from "react";

export default function SectionInner({ children, className = "", ...props }) {
  return (
    <div className={`py-3 ${className}`} {...props}>
      {children}
    </div>
  );
}
