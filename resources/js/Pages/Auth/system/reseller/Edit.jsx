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
import useTranslation from "../../../../hooks/useTranslation";

export default function Edit() {
    const { t } = useTranslation();
    const {
        reseller,
        nav = "documents",
        editUser,
        roles = [],
        permissions = [],
        defaultAdminRef,
    } = usePage().props;
    const [userNav, setUserNav] = useState("profile");
    const [showViaRole, setShowViaRole] = useState(false);
    const [showRechargeModal, setShowRechargeModal] = useState(false);

    const statusForm = useForm({
        status: reseller?.status ?? "Disabled",
    });
    const comissionForm = useForm({
        comission: reseller?.system_get_comission ?? 0,
        allow_max_product_upload: reseller?.allow_max_product_upload ?? "0",
        allow_max_resell_product: reseller?.allow_max_resell_product ?? "0",
        max_product_upload: reseller?.max_product_upload ?? 0,
        max_resell_product: reseller?.max_resell_product ?? 0,
        fixed_amount: reseller?.fixed_amount ?? 0,
    });
    const deadlineForm = useForm({
        deatline: reseller?.documents?.deatline ?? "",
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
        statusForm.post(route("system.reseller.status.update", { id: reseller.id }));
    };
    const submitComission = (e) => {
        e.preventDefault();
        comissionForm.post(route("system.reseller.comission.update", { id: reseller.id }));
    };
    const submitDeadline = (e) => {
        e.preventDefault();
        deadlineForm.post(route("system.reseller.documents.deatline", { id: reseller.id }));
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
            title={t("Resellers")}
            header={
                <PageHeader>{t("Resellers")}<br />
                    <NavLink href={route("system.users.edit", { id: reseller?.user?.id ?? "" })}>
                        {reseller?.user?.name ?? "N/A"}
                    </NavLink>
                    {" - "}
                    <span className="text-sm"> {reseller?.shop_name_bn ?? "N/A"} </span>
                    <br />
                    <span className="text-xs">{reseller?.status ?? "Pending"}</span>
                    <br />
                    <div className="mt-2 flex flex-wrap items-center gap-4">
                        <NavLink active={nav === "user"} href={route("system.reseller.edit", { id: reseller?.id, nav: "user" })}>{t("user")}</NavLink>
                        <NavLink active={nav === "documents"} href={route("system.reseller.edit", { id: reseller?.id, nav: "documents" })}>{t("Documents")}</NavLink>
                        <NavLink active={nav === "products"} href={route("system.reseller.edit", { id: reseller?.id, nav: "products" })}>{t("Products")}</NavLink>
                        <NavLink active={nav === "categories"} href={route("system.reseller.edit", { id: reseller?.id, nav: "categories" })}>{t("Categories")}</NavLink>
                        <NavLink active={nav === "orders"} href={route("system.reseller.edit", { id: reseller?.id, nav: "orders" })}>{t("Orders")}</NavLink>
                    </div>
                </PageHeader>
            }
        >
            <div>
                <Container>
                    <SectionSection>
                        <SectionHeader
                            title={t("Reseller and Shops")}
                            content={
                                <div className="flex w-full flex-1 flex-col gap-4 md:flex-row md:gap-10">
                                    <div className="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                                        <hr />
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">{t("Reseller ID:")}</div><div>{reseller?.id ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">{t("Reseller Name:")}</div><div>{reseller?.user?.name ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">{t("Reseller Email:")}</div><div>{reseller?.user?.email ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">{t("Reseller Phone:")}</div><div>{reseller?.user?.phone ?? "N/A"}</div></div>
                                        <div className="text-md  w-full p-3"><div className="font-bold">{t("Shop Name:")}</div><div>{reseller?.shop_name_en ?? "N/A"}</div></div>
                                    </div>
                                    <div className="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                                        <hr />
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">{t("Shop Email:")}</div><div>{reseller?.email ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">{t("Shop Phone:")}</div><div>{reseller?.phone ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">{t("Shop Address:")}</div><div>{reseller?.address ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">{t("Shop Location:")}</div><div>{reseller?.upazila ?? "N/A"}, {reseller?.district ?? "N/A"}, {reseller?.country ?? "N/A"}</div></div>
                                    </div>
                                </div>
                            }
                        />
                        <SectionInner>
                            <Hr />
                            <form onSubmit={submitStatus}>
                                <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div className="w-full md:w-auto">
                                        <p className="text-sm">{t("Current Status is :")}<strong>{reseller?.status}</strong>{t(". Change status to -")}</p>
                                        <select id="resStatus" className="w-full rounded-lg py-1 md:w-auto" value={statusForm.data.status} onChange={(e) => statusForm.setData("status", e.target.value)}>
                                            <option value="Select Status">{t("-- Select --")}</option>
                                            <option value="Pending">{t("Pending")}</option>
                                            <option value="Disabled">{t("Disabled")}</option>
                                            <option value="Suspended">{t("Suspended")}</option>
                                            <option value="Active">{t("Active")}</option>
                                        </select>
                                    </div>
                                    <div className="text-left md:text-end">
                                        <p className="text-sm">{t("update :")}{reseller?.updated_at_human ?? ""}</p>
                                        <PrimaryButton className="w-full justify-center md:ml-2 md:w-auto">{t("set")}</PrimaryButton>
                                    </div>
                                </div>
                            </form>
                            <Hr />
                            <form onSubmit={submitComission}>
                                <div className="flex flex-col gap-2 md:flex-row md:items-start md:justify-between"><div className="w-full md:w-auto"><input type="text" className="w-full rounded shadow md:w-auto" value={comissionForm.data.comission} onChange={(e) => comissionForm.setData("comission", e.target.value)} /><div className="text-xs">{t("You take")}{reseller?.system_get_comission ?? "0"}{t("% profit from this vendor revinew.")}</div></div></div>
                                <div className="my-2 rounded bg-gray-50 border-gray-200 p-3">
                                    <div className="flex w-full flex-col gap-3 p-3 md:flex-row md:items-center md:justify-between"><div className="font-bold">{t("Prevent adding unlimited product :")}</div><div className="flex flex-wrap gap-4 md:gap-10"><div className="flex items-center"><input type="radio" name="allow_max_product_upload" value="1" style={{ width: "20px", height: "20px" }} checked={comissionForm.data.allow_max_product_upload === "1"} onChange={(e) => comissionForm.setData("allow_max_product_upload", e.target.value)} /><div className="px-2">{t("Yes")}</div></div><div className="flex items-center"><input type="radio" name="allow_max_product_upload" value="0" style={{ width: "20px", height: "20px" }} checked={comissionForm.data.allow_max_product_upload === "0"} onChange={(e) => comissionForm.setData("allow_max_product_upload", e.target.value)} /><div className="px-2">{t("No")}</div></div></div></div>
                                    <div className="flex w-full flex-col gap-2 px-3 md:flex-row md:items-center md:justify-between"><div className="font-bold">{t("Maximum Product :")}</div><div><TextInput type="number" placeholder="100" className="w-full md:w-20" value={comissionForm.data.max_product_upload} onChange={(e) => comissionForm.setData("max_product_upload", e.target.value)} /></div></div>
                                    <div className="text-xs text-gray-500 px-3">{t("If you set the maximum product, then the vendor will not be able to upload more than this number of products.")}</div>
                                </div>
                                <div className="my-2 bg-gray-50 p-3">
                                    <div className="flex w-full flex-col gap-3 px-3 md:flex-row md:items-center md:justify-between"><div className="font-bold">{t("Allow to resell products :")}</div><div className="flex flex-wrap gap-4 md:gap-10"><div className="flex items-center"><input type="radio" name="allow_max_resell_product" value="1" style={{ width: "20px", height: "20px" }} checked={comissionForm.data.allow_max_resell_product === "1"} onChange={(e) => comissionForm.setData("allow_max_resell_product", e.target.value)} /><div className="px-2">{t("Yes")}</div></div><div className="flex items-center"><input type="radio" name="allow_max_resell_product" value="0" style={{ width: "20px", height: "20px" }} checked={comissionForm.data.allow_max_resell_product === "0"} onChange={(e) => comissionForm.setData("allow_max_resell_product", e.target.value)} /><div className="px-2">{t("No")}</div></div></div></div>
                                    <div className="flex w-full flex-col gap-2 px-3 md:flex-row md:items-center md:justify-between"><div className="font-bold">{t("Maximum Resel Product :")}</div><div><TextInput type="number" placeholder="100" className="w-full md:w-20" value={comissionForm.data.max_resell_product} onChange={(e) => comissionForm.setData("max_resell_product", e.target.value)} /></div></div>
                                    <div className="text-xs text-gray-500 px-3">{t("If you allow the vendor to resell products, then the vendor will be able to resell products from other resellers.")}</div>
                                </div>
                                <div className="my-2 bg-gray-50 p-3"><div className="flex w-full flex-col gap-2 px-3 md:flex-row md:items-center md:justify-between"><div className="font-bold">{t("Define Fixed Amount :")}</div><div><TextInput type="number" placeholder="100" className="w-full md:w-20" value={comissionForm.data.fixed_amount} onChange={(e) => comissionForm.setData("fixed_amount", e.target.value)} /></div></div></div>
                                <div><PrimaryButton>{t("Update")}</PrimaryButton></div>
                            </form>
                        </SectionInner>
                    </SectionSection>

                    {nav === "documents" ? (
                        <>
                            <SectionSection>
                                <SectionHeader title={t("Documents")} content={t("See the listed document submitted by the user")} />
                                <SectionInner>
                                    <InputFile label={t("Document Submited Last Date")} error="deatline">
                                        <div className="border px-2 rounded shadow-sm">
                                            {reseller?.documents?.deatline_formatted}
                                            {reseller?.documents?.deatline_human ? ` - ${reseller.documents.deatline_human}` : ""}
                                        </div>
                                    </InputFile>
                                    <Hr />
                                    <form onSubmit={submitDeadline}>
                                        <InputFile label={t("set New Date")} error="deatline">
                                            <div className="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                <TextInput type="date" className="w-full py-1 sm:w-auto" value={deadlineForm.data.deatline} onChange={(e) => deadlineForm.setData("deatline", e.target.value)} />
                                                <PrimaryButton className="py-1 sm:ms-2">{t("set")}</PrimaryButton>
                                            </div>
                                        </InputFile>
                                    </form>
                                </SectionInner>
                            </SectionSection>
                            <SectionSection>
                                <InputFile label={t("Nid")} error="nid">
                                    <TextInput type="number" className="form-control py-1" value={reseller?.documents?.nid ?? ""} label={t("NID No")} name="nid" error="nid" onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label={t("NID Image (front side)")} error="nid_front">
                                    {reseller?.documents?.nid_front_url ? <img className="w-full max-w-xs" width="300px" height="200px" src={reseller.documents.nid_front_url} alt="" /> : <div>{t("N/A")}</div>}
                                </InputFile>
                                <Hr />
                                <InputFile label={t("NID Image (back side)")} error="nid_back">
                                    {reseller?.documents?.nid_back_url ? <img className="w-full max-w-xs" width="300px" height="200px" src={reseller.documents.nid_back_url} alt="" /> : <div>{t("N/A")}</div>}
                                </InputFile>
                                <Hr />
                            </SectionSection>
                            <SectionSection>
                                <InputFile label={t("TIN No")} error="tin">
                                    <TextInput type="text" name="" value={reseller?.documents?.shop_tin ?? ""} id="" onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label={t("TIN Image")} error="shop_tin">
                                    {reseller?.documents?.shop_tin_image_url ? <img className="w-full max-w-xs" width="300px" height="200px" src={reseller.documents.shop_tin_image_url} alt="" /> : <div>{t("N/A")}</div>}
                                </InputFile>
                            </SectionSection>
                            <SectionSection>
                                <InputFile label={t("Shop Trade")} error="shop_trade">
                                    <TextInput type="text" name="" value={reseller?.documents?.shop_trade ?? ""} id="" onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label={t("Trade License Image")} error="shop_trade_image">
                                    {reseller?.documents?.shop_trade_image_url ? <img className="w-full max-w-xs" width="300px" height="200px" src={reseller.documents.shop_trade_image_url} alt="" /> : <div>{t("N/A")}</div>}
                                </InputFile>
                            </SectionSection>
                        </>
                    ) : null}

                    {nav === "products" ? <SectionSection><SectionInner></SectionInner></SectionSection> : null}
                    {nav === "orders" ? <SectionSection><SectionInner></SectionInner></SectionSection> : null}
                </Container>

                {nav === "user" && editUser ? (
                    <div className="my-3">
                        <div className="w-full px-2 mx-auto space-y-6 max-w-8xl sm:px-6 lg:px-8 ">
                            <SectionSection>
                                <SectionHeader
                                    title={editUser?.name}
                                    content={
                                        <div className="flex flex-wrap items-center gap-4">
                                            <NavLink href="#" active={userNav === "profile"} className={userNav === "profile" ? "active" : ""} onClick={(e) => { e.preventDefault(); setUserNav("profile"); }}>{t("Profile")}</NavLink>
                                            <NavLink href="#" active={userNav === "role"} className={userNav === "role" ? "active" : ""} onClick={(e) => { e.preventDefault(); setUserNav("role"); }}>{t("Permission")}</NavLink>
                                        </div>
                                    }
                                />
                            </SectionSection>

                            {userNav === "profile" ? (
                                <SectionSection>
                                    <SectionInner>
                                        <UpdateProfileInformation editUser={editUser} defaultAdminRef={defaultAdminRef} profileForm={profileForm} onSubmit={submitProfile} />
                                        <Hr />
                                        <InputFile label={t("User Coin")} error="coin" name="coin">
                                            <div className="space-y-3 rounded-lg">
                                                <TextInput type="text" className="w-full border-0 sm:w-32" disabled value={editUser?.coin ?? 0} />
                                                <div className="inline-block w-full rounded border bg-ref-900 p-2 sm:w-auto">
                                                    <div className="text-xs">{t("Recharge")}</div>
                                                    <form onSubmit={openRechargeModal} className="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
                                                        <TextInput type="number" className="w-full py-1 sm:w-32" value={profileForm.data.rechargeAmount} onChange={(e) => profileForm.setData("rechargeAmount", e.target.value)} />
                                                        <PrimaryButton className="w-fit self-start whitespace-nowrap">{t("Apply")}</PrimaryButton>
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
            </div>

            <Modal show={showViaRole} onClose={() => setShowViaRole(false)} maxWidth="2xl">
                <div className="p-3">
                    <p>{t("Permissions")}</p>
                    <Hr />
                    <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(230px, 1fr))", gap: 10 }}>
                        {groupedPermissions.map(([title, items]) => (
                            <PermissionGroup key={title} title={title} permissions={items} selected={editUser?.permissions_via_role ?? []} onToggle={() => {}} disabled />
                        ))}
                    </div>
                    <div className="mt-4 flex justify-end">
                        <DangerButton type="button" onClick={() => setShowViaRole(false)}>{t("Close")}</DangerButton>
                    </div>
                </div>
            </Modal>

            <Modal show={showRechargeModal} onClose={() => setShowRechargeModal(false)} maxWidth="xl">
                <div className="p-4">
                    <div className="text-lg">{t("Confirm Recharge")}</div>
                    <Hr />
                    <p className="py-5">{t("Are you sure to add")}{profileForm.data.rechargeAmount}{t("TK amount to")}{editUser?.name}, {editUser?.email}</p>
                    <Hr />
                    <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                        <SecondaryButton type="button" onClick={() => setShowRechargeModal(false)}>{t("Cancel")}</SecondaryButton>
                        <PrimaryButton type="button" onClick={submitRecharge}>{t("Recharge")}</PrimaryButton>
                        <DangerButton type="button" onClick={submitRefund}>{t("Refund")}</DangerButton>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}
