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
                style={{ width: 250 }}
                className="mb-4"
            >
                User Permission
            </InputLabel>
            <form onSubmit={onSubmit}>
                <p>
                    User has{" "}
                    {editUser?.permissions_via_role?.length ?? 0}{" "}
                    Permissions via Role. <br />
                    <SecondaryButton
                        type="button"
                        className="py-1"
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
