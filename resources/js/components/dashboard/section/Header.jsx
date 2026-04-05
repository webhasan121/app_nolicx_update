import React from "react";

export default function SectionHeader({ title, content }) {
    return (
        <div>
            <header>
                <div className="text-lg font-medium text-gray-900">
                    {title}
                </div>

                <div className="mt-1 text-sm text-gray-600">
                    {content}
                </div>
            </header>
        </div>
    );
}
