import Hr from "../Hr";
import InputLabel from "../InputLabel";
import PrimaryButton from "../PrimaryButton";
import TextInput from "../TextInput";

export default function RoleToUser({
    type,
    id,
    user,
    users = [],
    roles = [],
    selectedUsers = [],
    selectedRoles = [],
    onSubmit,
    onChange,
}) {
    return (
        <div>
            <form
                onSubmit={onSubmit}
                action={route("multiple_role_to_single_user")}
                method="post"
            >
                <div className="flex justify-between items-start w-full">
                    <div
                        className={`p-2 rounded border ${
                            type === "user" ? "hidden" : ""
                        }`}
                    >
                        {users.map((item) => {
                            const checked =
                                type === "user" && id === item.id
                                    ? true
                                    : selectedUsers.includes(item.id);

                            return (
                                <div
                                    key={item.id}
                                    className="flex items-center space-y-2"
                                >
                                    <TextInput
                                        checked={checked}
                                        type="checkbox"
                                        name="user[]"
                                        value={item.id}
                                        onChange={onChange}
                                    />
                                    <InputLabel
                                        className="pl-3 text-md"
                                        value={item.name}
                                    />
                                </div>
                            );
                        })}
                    </div>

                    <div
                        className={`p-2 rounded border ${
                            type === "role" ? "hidden" : ""
                        }`}
                    >
                        {roles.map((item) => {
                            const checked =
                                type === "role" && id === item.id
                                    ? true
                                    : selectedRoles.includes(item.name) ||
                                      user?.roles?.includes?.(item.name);

                            return (
                                <div
                                    key={item.id ?? item.name}
                                    className="flex items-center space-y-2"
                                >
                                    <TextInput
                                        checked={checked}
                                        type="checkbox"
                                        name="role[]"
                                        value={item.name}
                                        onChange={onChange}
                                    />
                                    <InputLabel
                                        className="pl-3 text-md"
                                        value={item.name}
                                    />
                                </div>
                            );
                        })}
                    </div>
                </div>
                <Hr />
                <PrimaryButton>Save</PrimaryButton>
            </form>
        </div>
    );
}
