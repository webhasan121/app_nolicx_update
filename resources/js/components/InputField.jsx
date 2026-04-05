import InputLabel from "./InputLabel";
import TextInput from "./TextInput";

export default function InputField({
    label,
    name,
    error,
    value,
    type = "text",
    required = false,
    labelWidth = "350px",
    inputClass = "w-full",
    className = "",
    onChange,
}) {
    return (
        <div className={`my-3 ${className}`}>
            <div style={{ width: labelWidth }}>
                <InputLabel
                    htmlFor={name}
                    className="block text-sm font-medium text-gray-700"
                >
                    {label} {required ? "*" : ""}
                </InputLabel>
            </div>

            <TextInput
                type={type}
                id={name}
                name={name}
                className={inputClass}
                value={value || ""}
                onChange={onChange}
                placeholder={label}
                required={required}
            />
                {error && <div className="text-sm text-red-600">{error}</div>}

        </div>
    );
}
