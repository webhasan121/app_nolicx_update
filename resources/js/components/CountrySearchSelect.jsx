import { useEffect, useMemo, useState } from "react";

export default function CountrySearchSelect({
    value,
    options = [],
    onChange,
    placeholder = "Country",
}) {
    const selected = useMemo(
        () =>
            options.find(
                (country) =>
                    String(country.name ?? "").trim().toLowerCase() ===
                    String(value ?? "").trim().toLowerCase(),
            ),
        [options, value],
    );
    const [query, setQuery] = useState(selected?.name ?? "");
    const [open, setOpen] = useState(false);

    useEffect(() => {
        setQuery(selected?.name ?? "");
    }, [selected?.name]);

    const filteredCountries = query.trim()
        ? options.filter((country) =>
              String(country.name ?? "")
                  .toLowerCase()
                  .includes(query.trim().toLowerCase()),
          )
        : options;

    const updateQuery = (nextQuery) => {
        setQuery(nextQuery);
        setOpen(true);

        if (!nextQuery.trim()) {
            onChange("");
        }
    };

    const selectCountry = (country) => {
        setQuery(country.name ?? "");
        onChange(country.name ?? "");
        setOpen(false);
    };

    return (
        <div className="relative w-36">
            <input
                type="search"
                value={query}
                onChange={(e) => updateQuery(e.target.value)}
                onFocus={() => setOpen(true)}
                onBlur={() => {
                    window.setTimeout(() => {
                        setOpen(false);
                        setQuery(selected?.name ?? "");
                    }, 150);
                }}
                placeholder={placeholder}
                autoComplete="off"
                aria-label={placeholder}
                className="h-10 w-full rounded-md border border-gray-200 px-3 pr-9 text-sm shadow-0 focus:border-gray-300 focus:ring-1 focus:ring-gray-200"
                style={{ marginBottom: 0 }}
            />
            <button
                type="button"
                onMouseDown={(e) => {
                    e.preventDefault();
                    setOpen((current) => !current);
                }}
                className="absolute inset-y-0 right-0 flex items-center justify-center w-9 text-gray-500"
                aria-label={placeholder}
            >
                <i className="fas fa-chevron-down text-xs"></i>
            </button>

            {open ? (
                <div className="absolute left-0 z-50 w-64 mt-1 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg top-full max-h-80">
                    {filteredCountries.length ? (
                        filteredCountries.map((country) => (
                            <button
                                key={country.id}
                                type="button"
                                onMouseDown={(e) => {
                                    e.preventDefault();
                                    selectCountry(country);
                                }}
                                className={`block w-full px-3 py-2 text-sm text-left hover:bg-gray-100 ${
                                    selected?.id === country.id ? "bg-gray-100" : ""
                                }`}
                            >
                                {country.name}
                            </button>
                        ))
                    ) : (
                        <div className="px-3 py-2 text-sm text-gray-500">
                            No country found
                        </div>
                    )}
                </div>
            ) : null}
        </div>
    );
}
