import { useForm, usePage } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import Hr from "../../../../components/Hr";
import InputFile from "../../../../components/InputFile";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import UpdateProfileInformation from "../../../../livewire/system/users/partials/UpdateProfileInformation";
import UpdateProfilePermission, {
    PermissionGroup,
} from "../../../../livewire/system/users/partials/UpdateProfilePermission";
import UpdateProfileRole from "../../../../livewire/system/users/partials/UpdateProfileRole";

export default function Edit() {
    const {
        rider,
        nav = "user",
        editUser,
        roles = [],
        permissions = [],
        defaultAdminRef,
    } = usePage().props;
    const [userNav, setUserNav] = useState("profile");
    const [showViaRole, setShowViaRole] = useState(false);
    const [showRechargeModal, setShowRechargeModal] = useState(false);

    const statusForm = useForm({
        status: rider?.status ?? "Pending",
        comission: rider?.comission ?? "",
    });
    const profileForm = useForm({
        name: editUser?.name ?? "",
        email: editUser?.email ?? "",
        reference: editUser?.reference ?? "",
        cref: "",
        rechargeAmount: "",
    });
    const roleForm = useForm({
        user: editUser?.id ? [editUser.id] : [],
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

    const submitStatus = (e) => {
        e.preventDefault();
        statusForm.post(route("system.rider.status.update", { id: rider.id }));
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
        permissionForm.post(route("system.users.permissions.update", { user: editUser.id }));
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
            title="Riders"
            header={
                <PageHeader>
                    Riders - {rider?.user?.name ?? "N/A"}
                    <br />
                    <div className="text-sm my-2">
                        Area - {rider?.area_condition}, {rider?.targeted_area ?? ""}
                    </div>
                    <div className="text-xs">{rider?.status}</div>
                    <div className="text-red">{rider?.rejected_for}</div>
                    <NavLink active={nav === "user"} href={route("system.rider.edit", { id: rider?.id, nav: "user" })}>User</NavLink>
                    <NavLink active={nav === "document"} href={route("system.rider.edit", { id: rider?.id, nav: "document" })}>Documents</NavLink>
                    <NavLink active={nav === "delevary"} href={route("system.rider.edit", { id: rider?.id, nav: "delevary" })}>Delevary</NavLink>
                </PageHeader>
            }
        >
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Rider Upate - Delevary Man"
                        content={
                            <>
                                <Hr />
                                <form onSubmit={submitStatus}>
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <div>
                                                <p className="text-sm">update : {rider?.updated_at_human}</p>
                                            </div>
                                            <p className="text-sm">Current Status is : <strong> {rider?.status} </strong>. Change status to - </p>
                                            <select className="rounded-lg py-1" value={statusForm.data.status} onChange={(e) => statusForm.setData("status", e.target.value)}>
                                                <option value="Select Status">-- Select -- </option>
                                                <option value="Active">Active</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Disabled">Disabled</option>
                                                <option value="Suspended">Suspended</option>
                                            </select>

                                            <div className="mt-1">
                                                <textarea className="rounded-lg" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <Hr />
                                    <div className="flex justify-between items-center">
                                        <div className="text-md">Comission (%)</div>
                                        <TextInput value={statusForm.data.comission} onChange={(e) => statusForm.setData("comission", e.target.value)} placeholder="" />
                                    </div>
                                    <Hr />
                                    <PrimaryButton className="ml-2"><i className="fas fa-sync pr-2"></i> Update </PrimaryButton>
                                </form>
                            </>
                        }
                    />
                </SectionSection>

                {nav === "document" ? (
                    <>
                        <SectionSection>
                            <SectionHeader title="Submitted Documents" content="" />
                            <SectionInner>
                                <Hr />
                                <InputFile label="Rider Phone" name="phone" error="phone">
                                    <TextInput type="text" name="phone" value={rider?.phone ?? ""} onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label="Rider Email" name="email" error="email">
                                    <TextInput type="text" name="email" value={rider?.email ?? ""} onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label="Rider NID" name="nid" error="nid">
                                    <TextInput type="text" name="nid" value={rider?.nid ?? ""} onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label="Rider Photo Front" name="nid_photo_front" error="nid_photo_front">
                                    <div className="flex">
                                        {rider?.nid_photo_front_url ? <img src={rider.nid_photo_front_url} alt="nid_photo_front" /> : null}
                                        {rider?.nid_photo_back_url ? <img src={rider.nid_photo_back_url} alt="nid_photo_back" /> : null}
                                    </div>
                                </InputFile>
                            </SectionInner>
                        </SectionSection>

                        <SectionSection>
                            <SectionHeader title="Rider Address and Area" content="See the rider areas about the rider address" />
                            <SectionInner>
                                <InputFile label="Rider Present Address" name="nid" error="nid">
                                    <div>{rider?.current_address ?? ""}</div>
                                </InputFile>
                                <Hr />
                                <InputFile label="Rider Permanent Address" name="nid" error="nid">
                                    <div>{rider?.fixed_address ?? ""}</div>
                                </InputFile>
                                <Hr />
                                <InputFile label="Rider Targetted Area" name="nid" error="nid">
                                    <div>{rider?.area_condition}, {rider?.targeted_area ?? ""}</div>
                                </InputFile>
                                <Hr />
                            </SectionInner>
                        </SectionSection>
                    </>
                ) : null}
            </Container>

            {nav === "user" && editUser ? (
                <div className="my-3">
                    <div className="w-full px-2 mx-auto space-y-6 max-w-8xl sm:px-6 lg:px-8 ">
                        <SectionSection>
                            <SectionHeader
                                title={editUser?.name}
                                content={
                                    <div>
                                        <NavLink href="#" active={userNav === "profile"} className={userNav === "profile" ? "active" : ""} onClick={(e) => { e.preventDefault(); setUserNav("profile"); }}>Profile</NavLink>
                                        <NavLink href="#" active={userNav === "role"} className={userNav === "role" ? "active" : ""} onClick={(e) => { e.preventDefault(); setUserNav("role"); }}>Permission</NavLink>
                                    </div>
                                }
                            />
                        </SectionSection>

                        {userNav === "profile" ? (
                            <SectionSection>
                                <SectionInner>
                                    <UpdateProfileInformation editUser={editUser} defaultAdminRef={defaultAdminRef} profileForm={profileForm} onSubmit={submitProfile} />
                                    <Hr />
                                    <InputFile label="User Coin" error="coin" name="coin">
                                        <div className="rounded-lg">
                                            <TextInput type="text" className=" border-0 w-32" disabled value={editUser?.coin ?? 0} />
                                            <div className="p-2 bg-ref-900 rounded border inline-block">
                                                <div className="text-xs">Recharge</div>
                                                <form onSubmit={openRechargeModal}>
                                                    <TextInput type="number" className="py-1 w-32 mr-1" value={profileForm.data.rechargeAmount} onChange={(e) => profileForm.setData("rechargeAmount", e.target.value)} />
                                                    <PrimaryButton>Apply</PrimaryButton>
                                                </form>
                                            </div>
                                        </div>
                                    </InputFile>
                                    <Hr />
                                </SectionInner>
                            </SectionSection>
                        ) : null}

                        {userNav === "role" ? (
                            <SectionSection>
                                <SectionInner>
                                    <UpdateProfileRole roles={roles} roleForm={roleForm} onSubmit={submitRoles} onToggle={(name) => toggleArrayValue(roleForm, "role", name)} />
                                    <Hr />
                                    <UpdateProfilePermission editUser={editUser} groupedPermissions={groupedPermissions} permissionForm={permissionForm} onSubmit={submitPermissions} onToggle={(name) => toggleArrayValue(permissionForm, "permissions", name)} onOpenViaRole={() => setShowViaRole(true)} />
                                </SectionInner>
                            </SectionSection>
                        ) : null}
                    </div>
                </div>
            ) : null}

            <Modal show={showViaRole} onClose={() => setShowViaRole(false)} maxWidth="2xl">
                <div className="p-3">
                    <p>Permissions</p>
                    <Hr />
                    <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(230px, 1fr))", gap: 10 }}>
                        {groupedPermissions.map(([title, items]) => (
                            <PermissionGroup key={title} title={title} permissions={items} selected={editUser?.permissions_via_role ?? []} onToggle={() => {}} disabled />
                        ))}
                    </div>
                    <div className="mt-4">
                        <DangerButton type="button" onClick={() => setShowViaRole(false)}>Close</DangerButton>
                    </div>
                </div>
            </Modal>

            <Modal show={showRechargeModal} onClose={() => setShowRechargeModal(false)} maxWidth="xl">
                <div className="p-4">
                    <div className="text-lg">Confirm Recharge</div>
                    <Hr />
                    <p className="py-5">Are you sure to add {profileForm.data.rechargeAmount} TK amount to {editUser?.name}, {editUser?.email}</p>
                    <Hr />
                    <div className="flex">
                        <SecondaryButton type="button" onClick={() => setShowRechargeModal(false)}>Cancel</SecondaryButton>
                        <PrimaryButton type="button" onClick={submitRecharge}>Recharge</PrimaryButton>
                        <DangerButton type="button" onClick={submitRefund}>Refund</DangerButton>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}
