import { useMemo, useState } from "react";

export default function SearchableSelect({
    value = "",
    options = [],
    onChange,
    placeholder = "Select",
    noneLabel = "Select",
    noResultsLabel = "No option found.",
    valueKey = "id",
    labelKey = "name",
    className = "",
    inputClassName = "",
    disabled = false,
}) {
    const [isOpen, setIsOpen] = useState(false);
    const [search, setSearch] = useState("");

    const normalizedOptions = useMemo(
        () =>
            options.map((option) => ({
                ...option,
                searchableText: String(option?.[labelKey] ?? "").toLowerCase(),
            })),
        [options, labelKey],
    );

    const selectedOption = normalizedOptions.find(
        (option) => String(option?.[valueKey]) === String(value),
    );
    const normalizedSearch = search.trim().toLowerCase();
    const filteredOptions = normalizedSearch
        ? normalizedOptions.filter((option) =>
              option.searchableText.includes(normalizedSearch),
          )
        : normalizedOptions;
    const visibleValue = isOpen ? search : selectedOption?.[labelKey] ?? "";

    const closeDropdown = () => {
        window.setTimeout(() => {
            setSearch("");
            setIsOpen(false);
        }, 150);
    };

    const selectOption = (option) => {
        onChange?.(option?.[valueKey] ?? "");
        setSearch("");
        setIsOpen(false);
    };

    return (
        <div className={`relative ${className}`}>
            <input
                type="text"
                value={visibleValue}
                onFocus={() => {
                    if (!disabled) {
                        setSearch("");
                        setIsOpen(true);
                    }
                }}
                onChange={(event) => {
                    setSearch(event.target.value);
                    setIsOpen(true);
                }}
                onBlur={closeDropdown}
                placeholder={placeholder}
                className={`w-full truncate border-gray-300 pr-10 rounded-md focus:border-blue-500 focus:ring-blue-500 ${inputClassName}`}
                autoComplete="off"
                disabled={disabled}
            />
            <i className="absolute text-gray-500 -translate-y-1/2 pointer-events-none fas fa-angle-down right-3 top-1/2"></i>

            {isOpen && !disabled ? (
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
                        filteredOptions.map((option) => (
                            <button
                                key={option?.[valueKey]}
                                type="button"
                                className="block w-full px-3 py-2 text-sm text-left hover:bg-gray-100 focus:bg-gray-100"
                                onMouseDown={(event) => event.preventDefault()}
                                onClick={() => selectOption(option)}
                            >
                                {option?.[labelKey]}
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
