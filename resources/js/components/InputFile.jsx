import InputLabel from "@/Components/InputLabel";

export default function InputFile({ label, error, children, name, errors }) {

    return (
        <div>
            <div className="my-3 form-group">
                <div className="justify-start md:flex ">

                    <div style={{ width: "250px" }}>

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

                    <div style={{ width: "100%" }} className="flex-1">
                        {children}
                    </div>

                </div>
            </div>
        </div>
    );
}
