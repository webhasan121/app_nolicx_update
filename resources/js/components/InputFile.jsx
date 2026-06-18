import InputLabel from "./InputLabel";

export default function InputFile({
    label,
    error,
    children,
    name,
    errors,
    className = "md:flex",
    labelWidth = "250px",
    inputClass = "w-full",
}) {

    return (
        <div>
            <div className="my-3 form-group">
                <div className={`justify-start ${className}`}>

                    <div style={{ width: labelWidth }} className="shrink-0">

                        <InputLabel
                            htmlFor={name ?? label}
                            className="block text-sm font-medium text-gray-700"
                        >
                            {label}
                        </InputLabel>

                        {errors?.[error] && (
                            <div className="text-sm text-red-600">
                                {errors[error]}
                            </div>
                        )}

                    </div>

                    <div className={`flex-1 min-w-0 ${inputClass}`}>
                        {children}
                    </div>

                </div>
            </div>
        </div>
    );
}
