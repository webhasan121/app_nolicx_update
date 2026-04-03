import { useForm, usePage } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import Hr from "../../../../components/Hr";
import InputFile from "../../../../components/InputFile";
import Modal from "../../../../components/Modal";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import UpdateProfileInformation from "../../../../livewire/system/users/partials/UpdateProfileInformation";
import UpdateProfilePermission, {
    PermissionGroup,
} from "../../../../livewire/system/users/partials/UpdateProfilePermission";
import UpdateProfileRole from "../../../../livewire/system/users/partials/UpdateProfileRole";
import VendorNavigation from "./_VendorNavigation";

export default function Edit() {
    const {
        vendor,
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
            title="Vendor Edit"
            header={
                <VendorNavigation
                    vendor={vendor}
                    activeRoute="system.vendor.edit"
                />
            }
        >
            <div>
                <Container>
                    <SectionSection>
                        <SectionInner>
                            <div>
                                <a
                                    href="#"
                                    className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${nav === "profile" ? "border-orange-400 text-gray-900 active" : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"}`}
                                    onClick={(e) => {
                                        e.preventDefault();
                                        setNav("profile");
                                    }}
                                >
                                    Profile
                                </a>
                                <a
                                    href="#"
                                    className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${nav === "role" ? "border-orange-400 text-gray-900 active" : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"}`}
                                    onClick={(e) => {
                                        e.preventDefault();
                                        setNav("role");
                                    }}
                                >
                                    Permission
                                </a>
                            </div>
                        </SectionInner>
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
                                <UpdateProfilePermission
                                    editUser={editUser}
                                    groupedPermissions={groupedPermissions}
                                    permissionForm={permissionForm}
                                    onSubmit={submitPermissions}
                                    onToggle={(name) =>
                                        toggleArrayValue(
                                            permissionForm,
                                            "permissions",
                                            name
                                        )
                                    }
                                    onOpenViaRole={() => setShowViaRole(true)}
                                />
                            </SectionInner>
                        </SectionSection>
                    ) : null}
                </Container>
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
