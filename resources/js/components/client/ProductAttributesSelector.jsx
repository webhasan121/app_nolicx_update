export function normalizeProductAttributeGroups(product) {
    const rawGroups = product?.attrs?.length
        ? product.attrs
        : product?.attr
          ? [product.attr]
          : [];

    return Object.values(
        rawGroups.reduce((groups, group) => {
            const name = String(group?.name ?? "").trim();
            if (!name) return groups;

            const key = name.toLowerCase();
            const values = String(group?.value ?? "")
                .split(",")
                .map((value) => value.trim())
                .filter(Boolean);

            if (!groups[key]) {
                groups[key] = { name, values: [] };
            }

            groups[key].values = [...new Set([...groups[key].values, ...values])];

            return groups;
        }, {})
    );
}

export default function ProductAttributesSelector({
    product,
    selectedAttrs = {},
    onChange,
}) {
    const attrGroups = normalizeProductAttributeGroups(product);

    if (!attrGroups.length) return null;

    return (
        <div className="py-2 my-3 space-y-3">
            {attrGroups.map((group, groupIndex) => {
                const isColorAttribute = String(group.name ?? "")
                    .toLowerCase()
                    .includes("color");

                if (!group.values.length) return null;

                return (
                    <div
                        key={`${group.name}-${groupIndex}`}
                        className="flex flex-wrap items-center gap-2"
                    >
                        <h4 className="min-w-[50px]">{group.name}</h4>
                        <div className="flex flex-wrap items-center justify-start gap-2">
                            {group.values.map((attr) => (
                                <button
                                    key={attr}
                                    type="button"
                                    onClick={() =>
                                        onChange({
                                            ...selectedAttrs,
                                            [group.name]: attr,
                                        })
                                    }
                                    className={`rounded border text-sm transition ${
                                        selectedAttrs[group.name] === attr
                                            ? "border-orange-500 ring-2 ring-orange-200"
                                            : "border-gray-200"
                                    } ${
                                        isColorAttribute
                                            ? "flex h-9 w-9 items-center justify-center bg-white p-1"
                                            : "bg-indigo-300 px-2 py-1 text-white"
                                    }`}
                                    title={attr}
                                >
                                    {isColorAttribute ? (
                                        <span
                                            className="block w-full h-full rounded"
                                            style={{ backgroundColor: attr }}
                                        />
                                    ) : (
                                        attr.toUpperCase()
                                    )}
                                </button>
                            ))}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
