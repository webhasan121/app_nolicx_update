import { useMemo, useState } from "react";

const DEFAULT_LIMIT = 100;

export function flattenCategories(categories = [], options = {}) {
    const {
        currentCategoryId = null,
        maxSelectableDepth = null,
        depth = 0,
        blocked = false,
    } = options;

    return categories.flatMap((category) => {
        const isCurrentCategory = String(category.id) === String(currentCategoryId);
        const isBlocked = blocked || isCurrentCategory;
        const isDepthBlocked =
            maxSelectableDepth !== null && depth > maxSelectableDepth;
        const prefix = depth === 0 ? "" : `${"-".repeat(depth * 2)} `;
        const label = `${prefix}${category.name ?? ""}`;
        const current = {
            id: category.id,
            label,
            searchText: `${category.name ?? ""} ${label}`.toLowerCase(),
            disabled: isBlocked || isDepthBlocked || !!category.disabled,
        };

        return [
            current,
            ...flattenCategories(category.children ?? [], {
                currentCategoryId,
                maxSelectableDepth,
                depth: depth + 1,
                blocked: isBlocked,
            }),
        ];
    });
}

export default function CategorySelect({
    categories = [],
    value = "",
    onChange,
    placeholder = "Select Category",
    noneLabel = "Select Category",
    noResultsLabel = "No category found.",
    limit = DEFAULT_LIMIT,
    currentCategoryId = null,
    maxSelectableDepth = null,
    className = "",
    inputClassName = "",
}) {
    const [isOpen, setIsOpen] = useState(false);
    const [search, setSearch] = useState("");
    const options = useMemo(
        () =>
            flattenCategories(categories, {
                currentCategoryId,
                maxSelectableDepth,
            }),
        [categories, currentCategoryId, maxSelectableDepth]
    );
    const selectedCategory = options.find(
        (category) => String(category.id) === String(value)
    );
    const normalizedSearch = search.trim().toLowerCase();
    const visibleValue = isOpen ? search : selectedCategory?.label ?? "";
    const filteredOptions = useMemo(() => {
        const matched = normalizedSearch
            ? options.filter((category) =>
                  category.searchText.includes(normalizedSearch)
              )
            : options;

        return matched.slice(0, limit);
    }, [normalizedSearch, options, limit]);

    const selectCategory = (category) => {
        if (category.disabled) {
            return;
        }

        onChange?.(category.id);
        setSearch("");
        setIsOpen(false);
    };

    return (
        <div className={`relative ${className}`}>
            <input
                type="text"
                value={visibleValue}
                onFocus={() => {
                    setSearch("");
                    setIsOpen(true);
                }}
                onChange={(event) => {
                    setSearch(event.target.value);
                    setIsOpen(true);
                }}
                onBlur={() => {
                    window.setTimeout(() => {
                        setSearch("");
                        setIsOpen(false);
                    }, 150);
                }}
                placeholder={placeholder}
                className={`w-full border-gray-300 rounded focus:border-blue-500 focus:ring-blue-500 ${inputClassName}`}
                autoComplete="off"
            />
            <i className="absolute text-gray-500 -translate-y-1/2 pointer-events-none fas fa-angle-down right-3 top-1/2"></i>

            {isOpen ? (
                <div className="absolute left-0 right-0 z-50 mt-1 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg max-h-64">
                    <button
                        type="button"
                        className="block w-full px-3 py-2 text-sm text-left hover:bg-gray-100 focus:bg-gray-100"
                        onMouseDown={(event) => event.preventDefault()}
                        onClick={() => {
                            onChange?.("");
                            setSearch("");
                            setIsOpen(false);
                        }}
                    >
                        {noneLabel}
                    </button>

                    {filteredOptions.length > 0 ? (
                        filteredOptions.map((category) => (
                            <button
                                key={category.id}
                                type="button"
                                disabled={category.disabled}
                                className={`block w-full px-3 py-2 text-sm text-left ${
                                    category.disabled
                                        ? "cursor-not-allowed text-gray-400"
                                        : "hover:bg-gray-100 focus:bg-gray-100"
                                }`}
                                onMouseDown={(event) => event.preventDefault()}
                                onClick={() => selectCategory(category)}
                            >
                                {category.label}
                            </button>
                        ))
                    ) : (
                        <div className="px-3 py-4 text-sm text-center text-gray-500">
                            {noResultsLabel}
                        </div>
                    )}
                </div>
            ) : null}
        </div>
    );
}
