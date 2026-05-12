import React from "react";

export default function SectionSection({ children, className = "", ...props }) {
    return (
        <div
            className={`p-3 space-y-3 sm:p-6 mb-4 shadow-sm sm:rounded-lg bg-white ${className}`}
            {...props}
        >
            {children}
        </div>
    );
}
