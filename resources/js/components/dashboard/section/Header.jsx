import React from "react";

export default function SectionHeader({ title, content }) {
  return (
    <div>
      <header>
        <h2 className="text-lg font-medium text-gray-900">
          {title}
        </h2>

        <div className="mt-1 text-sm text-gray-600">
          {content}
        </div>
      </header>
    </div>
  );
}
