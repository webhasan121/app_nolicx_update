import SecondaryButton from "./SecondaryButton";

export default function ProductAttributesInput({ attributes = [], onChange }) {
    const rows = attributes.length ? attributes : [{ name: "", value: "" }];

    const updateRow = (index, key, value) => {
        onChange(
            rows.map((row, rowIndex) =>
                rowIndex === index ? { ...row, [key]: value } : row
            )
        );
    };

    const addRow = () => {
        onChange([...rows, { name: "", value: "" }]);
    };

    const removeRow = (index) => {
        if (rows.length === 1) {
            onChange([{ name: "", value: "" }]);
            return;
        }

        onChange(rows.filter((_, rowIndex) => rowIndex !== index));
    };

    return (
        <div className="space-y-2">
            {rows.map((row, index) => (
                <div className="flex items-center gap-2" key={index}>
                    <input
                        type="text"
                        value={row.name ?? ""}
                        onChange={(e) => updateRow(index, "name", e.target.value)}
                        placeholder="Name"
                    />
                    <input
                        type="text"
                        value={row.value ?? ""}
                        onChange={(e) => updateRow(index, "value", e.target.value)}
                        placeholder="Value"
                    />
                    <button
                        type="button"
                        onClick={() => removeRow(index)}
                        className="flex items-center justify-center w-8 h-8 text-red-600 border rounded hover:bg-red-50"
                        title="Remove attribute"
                    >
                        <i className="fas fa-minus"></i>
                    </button>
                </div>
            ))}

            <SecondaryButton type="button" onClick={addRow} className="mt-2">
                <i className="mr-2 fas fa-plus"></i> Add Attribute
            </SecondaryButton>
        </div>
    );
}
