import Hr from "../../../../components/Hr";
import InputLabel from "../../../../components/InputLabel";
import PermissionList from "../../../../components/PermissionList";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";

function PermissionGroup({
    title,
    permissions,
    selected,
    onToggle,
    disabled = false,
}) {
    if (!permissions.length) {
        return null;
    }

    return (
        <div>
            <InputLabel>{title}</InputLabel>
            {permissions.map((permission) => (
                <div key={permission.id}>
                    <TextInput
                        className="m-0"
                        type="checkbox"
                        id={`perm_${permission.id}`}
                        checked={selected.includes(permission.name)}
                        disabled={disabled}
                        onChange={() => onToggle(permission.name)}
                    />
                    <label
                        className="pl-3 text-sm"
                        htmlFor={`perm_${permission.id}`}
                    >
                        {permission.name}
                    </label>
                </div>
            ))}
        </div>
    );
}

export default function UpdateProfilePermission({
    editUser,
    groupedPermissions,
    permissionForm,
    onSubmit,
    onToggle,
    onOpenViaRole,
}) {
    return (
        <div className="">
            <InputLabel
                style={{ width: "min(100%, 250px)" }}
                className="mb-4"
            >
                User Permission
            </InputLabel>
            <form onSubmit={onSubmit}>
                <p className="space-y-2">
                    User has{" "}
                    {editUser?.permissions_via_role?.length ?? 0}{" "}
                    Permissions via Role. <br />
                    <SecondaryButton
                        type="button"
                        className="mt-2 py-1"
                        onClick={onOpenViaRole}
                    >
                        check
                    </SecondaryButton>
                </p>
                <Hr />
                <PermissionList>
                    <div
                        style={{
                            display: "grid",
                            gridTemplateColumns:
                                "repeat(auto-fit, minmax(230px, 1fr))",
                            gap: 10,
                        }}
                        className="w-full"
                    >
                        {groupedPermissions.map(([title, items]) => (
                            <PermissionGroup
                                key={title}
                                title={title}
                                permissions={items}
                                selected={permissionForm.data.permissions}
                                onToggle={onToggle}
                            />
                        ))}
                    </div>
                </PermissionList>
                <Hr />
                <PrimaryButton>save</PrimaryButton>
            </form>
        </div>
    );
}

export { PermissionGroup };
