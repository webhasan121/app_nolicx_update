import InputLabel from "./InputLabel";

export default function InputFile({
    label,
    error,
    children,
    name,
    errors,
    className = "",
    labelWidth = "250px",
    inputClass = "w-full",
}) {

    return (
        <div>
            <div className={`my-3 form-group ${className}`}>
                <div className="justify-start md:flex">

                    <div style={{ width: labelWidth }}>

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

                    <div style={{ width: "100%" }} className={`flex-1 ${inputClass}`}>
                        {children}
                    </div>

                </div>
            </div>
        </div>
    );
}
