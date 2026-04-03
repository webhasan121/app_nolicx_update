import { useForm, usePage } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import Hr from "../../../../components/Hr";
import InputFile from "../../../../components/InputFile";
import InputLabel from "../../../../components/InputLabel";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PermissionList from "../../../../components/PermissionList";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import UpdateProfileInformation from "../../../../livewire/system/users/partials/UpdateProfileInformation";
import UpdateProfileRole from "../../../../livewire/system/users/partials/UpdateProfileRole";

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

export default function Edit() {
    const {
        editUser,
        roles = [],
        permissions = [],
        defaultAdminRef,
    } = usePage().props;
    const [nav, setNav] = useState("profile");
    const [showViaRole, setShowViaRole] = useState(false);
    const [showRechargeModal, setShowRechargeModal] = useState(false);

    const profileForm = useForm({
        name: editUser?.name ?? "",
        email: editUser?.email ?? "",
        reference: editUser?.reference ?? "",
        cref: "",
        rechargeAmount: "",
    });

    const roleForm = useForm({
        user: [editUser?.id],
        role: editUser?.roles ?? [],
    });

    const permissionForm = useForm({
        permissions: editUser?.permissions ?? [],
    });

    const groupedPermissions = useMemo(() => {
        const startsWith = (prefix) =>
            permissions.filter((permission) =>
                permission.name.startsWith(prefix)
            );

        return [
            ["Role", startsWith("role_")],
            ["Permission", permissions.filter((permission) => permission.name.startsWith("permission"))],
            ["Access", startsWith("access")],
            ["Sync", startsWith("sync")],
            ["Admin", startsWith("admin")],
            ["Vendors", startsWith("vendors")],
            ["Resellers", startsWith("reseller")],
            ["Riders", startsWith("riders")],
            ["Users", startsWith("users")],
            ["Product", startsWith("product")],
            ["Category", startsWith("category")],
        ];
    }, [permissions]);

    const toggleArrayValue = (form, key, value) => {
        const current = form.data[key];
        form.setData(
            key,
            current.includes(value)
                ? current.filter((item) => item !== value)
                : [...current, value]
        );
    };

    const submitProfile = (e) => {
        e.preventDefault();
        profileForm.transform((data) => ({
            ...data,
            reference: data.cref || data.reference,
        }));
        profileForm.post(route("system.users.update", { id: editUser.id }));
    };

    const submitRoles = (e) => {
        e.preventDefault();
        roleForm.post(route("system.users.roles.update", { user: editUser.id }));
    };

    const submitPermissions = (e) => {
        e.preventDefault();
        permissionForm.post(
            route("system.users.permissions.update", { user: editUser.id })
        );
    };

    const openRechargeModal = (e) => {
        e.preventDefault();
        if (!profileForm.data.rechargeAmount) {
            return;
        }
        setShowRechargeModal(true);
    };

    const submitRecharge = () => {
        profileForm.post(route("system.users.recharge", { id: editUser.id }), {
            preserveScroll: true,
            onSuccess: () => {
                profileForm.setData("rechargeAmount", "");
                setShowRechargeModal(false);
            },
        });
    };

    const submitRefund = () => {
        profileForm.post(route("system.users.refund", { id: editUser.id }), {
            preserveScroll: true,
            onSuccess: () => {
                profileForm.setData("rechargeAmount", "");
                setShowRechargeModal(false);
            },
        });
    };

    return (
        <AppLayout
            title="User Update"
            header={
                <PageHeader>
                    User Update
                    <br />
                    <NavLink href={route("system.users.view")}>
                        <i className="fa-solid fa-up-right-from-square me-2"></i>
                        Users
                    </NavLink>
                </PageHeader>
            }
        >
            <div className="my-3">
                <div className="w-full px-2 mx-auto space-y-6 max-w-8xl sm:px-6 lg:px-8 ">
                    <SectionSection>
                        <SectionHeader
                            title={editUser?.name}
                            content={
                                <div>
                                    <NavLink
                                        href="#"
                                        active={nav === "profile"}
                                        className={nav === "profile" ? "active" : ""}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setNav("profile");
                                        }}
                                    >
                                        Profile
                                    </NavLink>
                                    <NavLink
                                        href="#"
                                        active={nav === "role"}
                                        className={nav === "role" ? "active" : ""}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setNav("role");
                                        }}
                                    >
                                        Permission
                                    </NavLink>
                                </div>
                            }
                        />
                    </SectionSection>

                    {nav === "profile" ? (
                        <SectionSection>
                            <SectionInner>
                                <UpdateProfileInformation
                                    editUser={editUser}
                                    defaultAdminRef={defaultAdminRef}
                                    profileForm={profileForm}
                                    onSubmit={submitProfile}
                                />

                                <Hr />
                                <InputFile label="User Coin" error="coin" name="coin">
                                    <div className="rounded-lg">
                                        <TextInput
                                            type="text"
                                            className=" border-0 w-32"
                                            disabled
                                            value={editUser?.coin ?? 0}
                                        />
                                        <div className="p-2 bg-ref-900 rounded border inline-block">
                                            <div className="text-xs">Recharge</div>
                                            <form onSubmit={openRechargeModal}>
                                                <TextInput
                                                    type="number"
                                                    className="py-1 w-32 mr-1"
                                                    value={profileForm.data.rechargeAmount}
                                                    onChange={(e) =>
                                                        profileForm.setData(
                                                            "rechargeAmount",
                                                            e.target.value
                                                        )
                                                    }
                                                />
                                                <PrimaryButton>Apply</PrimaryButton>
                                            </form>
                                        </div>
                                    </div>
                                </InputFile>
                                <Hr />
                            </SectionInner>
                        </SectionSection>
                    ) : null}

                    {nav === "role" ? (
                        <SectionSection>
                            <SectionInner>
                                <UpdateProfileRole
                                    roles={roles}
                                    roleForm={roleForm}
                                    onSubmit={submitRoles}
                                    onToggle={(name) =>
                                        toggleArrayValue(roleForm, "role", name)
                                    }
                                />
                                <Hr />
                                <div className="">
                                    <InputLabel
                                        style={{ width: 250 }}
                                        className="mb-4"
                                    >
                                        User Permission
                                    </InputLabel>
                                    <form onSubmit={submitPermissions}>
                                        <p>
                                            User has{" "}
                                            {editUser?.permissions_via_role?.length ?? 0}{" "}
                                            Permissions via Role. <br />
                                            <SecondaryButton
                                                type="button"
                                                className="py-1"
                                                onClick={() => setShowViaRole(true)}
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
                                                {groupedPermissions.map(
                                                    ([title, items]) => (
                                                        <PermissionGroup
                                                            key={title}
                                                            title={title}
                                                            permissions={items}
                                                            selected={
                                                                permissionForm.data.permissions
                                                            }
                                                            onToggle={(name) =>
                                                                toggleArrayValue(
                                                                    permissionForm,
                                                                    "permissions",
                                                                    name
                                                                )
                                                            }
                                                        />
                                                    )
                                                )}
                                            </div>
                                        </PermissionList>
                                        <Hr />
                                        <PrimaryButton>save</PrimaryButton>
                                    </form>
                                </div>
                            </SectionInner>
                        </SectionSection>
                    ) : null}
                </div>
            </div>

            <Modal
                show={showViaRole}
                onClose={() => setShowViaRole(false)}
                maxWidth="2xl"
            >
                <div className="p-3">
                        <p>Permissions</p>
                        <Hr />
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
                                    selected={editUser?.permissions_via_role ?? []}
                                    onToggle={() => {}}
                                    disabled
                                />
                            ))}
                        </div>
                        <div className="mt-4">
                            <DangerButton
                                type="button"
                                onClick={() => setShowViaRole(false)}
                            >
                                Close
                            </DangerButton>
                        </div>
                </div>
            </Modal>

            <Modal
                show={showRechargeModal}
                onClose={() => setShowRechargeModal(false)}
                maxWidth="xl"
            >
                <div className="p-4">
                        <div className="text-lg">Confirm Recharge</div>
                        <Hr />
                        <p className="py-5">
                            Are you sure to add {profileForm.data.rechargeAmount} TK
                            amount to {editUser?.name}, {editUser?.email}
                        </p>
                        <Hr />
                        <div className="flex">
                            <SecondaryButton
                                type="button"
                                onClick={() => setShowRechargeModal(false)}
                            >
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton
                                type="button"
                                onClick={submitRecharge}
                            >
                                Recharge
                            </PrimaryButton>
                            <DangerButton
                                type="button"
                                onClick={submitRefund}
                            >
                                Refund
                            </DangerButton>
                        </div>
                </div>
            </Modal>
        </AppLayout>
    );
}
