import InputFile from "../../../../components/InputFile";
import InputLabel from "../../../../components/InputLabel";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Hr from "../../../../components/Hr";

export default function UpdateProfileRole({
    roles,
    roleForm,
    onSubmit,
    onToggle,
}) {
    return (
        <form onSubmit={onSubmit}>
            <div>
                <InputFile label="User Role" error="role" name="role">
                    <div className="flex">
                        {roles.map((item) => (
                            <div
                                key={item.id}
                                className="flex items-center p-3 border shadow-sm"
                            >
                                <TextInput
                                    className="m-0"
                                    type="checkbox"
                                    checked={roleForm.data.role.includes(
                                        item.name,
                                    )}
                                    onChange={() => onToggle(item.name)}
                                />

                                <InputLabel className="m-0 p-0 pl-3 text-md">
                                    {item.name}
                                </InputLabel>
                            </div>
                        ))}
                    </div>
                    <Hr />
                    <PrimaryButton>save</PrimaryButton>
                </InputFile>
            </div>
        </form>
    );
}
