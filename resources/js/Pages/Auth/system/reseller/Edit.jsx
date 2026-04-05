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
            title="Resellers"
            header={
                <PageHeader>
                    Resellers
                    <br />
                    <NavLink href={route("system.users.edit", { id: reseller?.user?.id ?? "" })}>
                        {reseller?.user?.name ?? "N/A"}
                    </NavLink>
                    {" - "}
                    <span className="text-sm"> {reseller?.shop_name_bn ?? "N/A"} </span>
                    <br />
                    <span className="text-xs">{reseller?.status ?? "Pending"}</span>
                    <br />
                    <div>
                        <NavLink active={nav === "user"} href={route("system.reseller.edit", { id: reseller?.id, nav: "user" })}>user</NavLink>
                        <NavLink active={nav === "documents"} href={route("system.reseller.edit", { id: reseller?.id, nav: "documents" })}>Documents</NavLink>
                        <NavLink active={nav === "products"} href={route("system.reseller.edit", { id: reseller?.id, nav: "products" })}>Products</NavLink>
                        <NavLink active={nav === "categories"} href={route("system.reseller.edit", { id: reseller?.id, nav: "categories" })}>Categories</NavLink>
                        <NavLink active={nav === "orders"} href={route("system.reseller.edit", { id: reseller?.id, nav: "orders" })}>Orders</NavLink>
                    </div>
                </PageHeader>
            }
        >
            <div>
                <Container>
                    <SectionSection>
                        <SectionHeader
                            title="Reseller and Shops"
                            content={
                                <div className="md:flex w-full flex-1 gap-10">
                                    <div className="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                                        <hr />
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">Reseller ID: </div><div>{reseller?.id ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">Reseller Name: </div><div>{reseller?.user?.name ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">Reseller Email: </div><div>{reseller?.user?.email ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">Reseller Phone: </div><div>{reseller?.user?.phone ?? "N/A"}</div></div>
                                        <div className="text-md  w-full p-3"><div className="font-bold">Shop Name: </div><div>{reseller?.shop_name_en ?? "N/A"}</div></div>
                                    </div>
                                    <div className="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                                        <hr />
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">Shop Email: </div><div>{reseller?.email ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">Shop Phone: </div><div>{reseller?.phone ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">Shop Address: </div><div>{reseller?.address ?? "N/A"}</div></div>
                                        <div className="text-md border-b w-full p-3"><div className="font-bold">Shop Location: </div><div>{reseller?.upazila ?? "N/A"}, {reseller?.district ?? "N/A"}, {reseller?.country ?? "N/A"}</div></div>
                                    </div>
                                </div>
                            }
                        />
                        <SectionInner>
                            <Hr />
                            <form onSubmit={submitStatus}>
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm">Current Status is : <strong>{reseller?.status}</strong>. Change status to - </p>
                                        <select id="resStatus" className="rounded-lg py-1" value={statusForm.data.status} onChange={(e) => statusForm.setData("status", e.target.value)}>
                                            <option value="Select Status">-- Select -- </option>
                                            <option value="Pending">Pending</option>
                                            <option value="Disabled">Disabled</option>
                                            <option value="Suspended">Suspended</option>
                                            <option value="Active">Active</option>
                                        </select>
                                    </div>
                                    <div className="text-end">
                                        <p className="text-sm">update : {reseller?.updated_at_human ?? ""}</p>
                                        <PrimaryButton className="ml-2">set</PrimaryButton>
                                    </div>
                                </div>
                            </form>
                            <Hr />
                            <form onSubmit={submitComission}>
                                <div className="flex justify-between items-start"><div><input type="text" className="rounded shadow" value={comissionForm.data.comission} onChange={(e) => comissionForm.setData("comission", e.target.value)} /><div className="text-xs">You take {reseller?.system_get_comission ?? "0"}% profit from this vendor revinew.</div></div></div>
                                <div className="my-2 rounded bg-gray-50 border-gray-200 p-3">
                                    <div className="p-3 w-full flex justify-between items-center"><div className="font-bold">Prevent adding unlimited product  : </div><div className="flex gap-10"><div className="flex items-center"><input type="radio" name="allow_max_product_upload" value="1" style={{ width: "20px", height: "20px" }} checked={comissionForm.data.allow_max_product_upload === "1"} onChange={(e) => comissionForm.setData("allow_max_product_upload", e.target.value)} /><div className="px-2">Yes</div></div><div className="flex items-center"><input type="radio" name="allow_max_product_upload" value="0" style={{ width: "20px", height: "20px" }} checked={comissionForm.data.allow_max_product_upload === "0"} onChange={(e) => comissionForm.setData("allow_max_product_upload", e.target.value)} /><div className="px-2">No</div></div></div></div>
                                    <div className="px-3 w-full flex justify-between items-center"><div className="font-bold">Maximum Product : </div><div><TextInput type="number" placeholder="100" className="w-20" value={comissionForm.data.max_product_upload} onChange={(e) => comissionForm.setData("max_product_upload", e.target.value)} /></div></div>
                                    <div className="text-xs text-gray-500 px-3">If you set the maximum product, then the vendor will not be able to upload more than this number of products.</div>
                                </div>
                                <div className="my-2 bg-gray-50 p-3">
                                    <div className="px-3 w-full flex justify-between items-center"><div className="font-bold">Allow to resell products : </div><div className="flex gap-10"><div className="flex items-center"><input type="radio" name="allow_max_resell_product" value="1" style={{ width: "20px", height: "20px" }} checked={comissionForm.data.allow_max_resell_product === "1"} onChange={(e) => comissionForm.setData("allow_max_resell_product", e.target.value)} /><div className="px-2">Yes</div></div><div className="flex items-center"><input type="radio" name="allow_max_resell_product" value="0" style={{ width: "20px", height: "20px" }} checked={comissionForm.data.allow_max_resell_product === "0"} onChange={(e) => comissionForm.setData("allow_max_resell_product", e.target.value)} /><div className="px-2">No</div></div></div></div>
                                    <div className="px-3 w-full flex justify-between items-center"><div className="font-bold">Maximum Resel Product : </div><div><TextInput type="number" placeholder="100" className="w-20" value={comissionForm.data.max_resell_product} onChange={(e) => comissionForm.setData("max_resell_product", e.target.value)} /></div></div>
                                    <div className="text-xs text-gray-500 px-3">If you allow the vendor to resell products, then the vendor will be able to resell products from other resellers.</div>
                                </div>
                                <div className="my-2 bg-gray-50 p-3"><div className="px-3 w-full flex justify-between items-center"><div className="font-bold">Define Fixed Amount : </div><div><TextInput type="number" placeholder="100" className="w-20" value={comissionForm.data.fixed_amount} onChange={(e) => comissionForm.setData("fixed_amount", e.target.value)} /></div></div></div>
                                <div><PrimaryButton>Update</PrimaryButton></div>
                            </form>
                        </SectionInner>
                    </SectionSection>

                    {nav === "documents" ? (
                        <>
                            <SectionSection>
                                <SectionHeader title="Documents" content="See the listed document submitted by the user" />
                                <SectionInner>
                                    <InputFile label="Document Submited Last Date" error="deatline">
                                        <div className="border px-2 rounded shadow-sm">
                                            {reseller?.documents?.deatline_formatted}
                                            {reseller?.documents?.deatline_human ? ` - ${reseller.documents.deatline_human}` : ""}
                                        </div>
                                    </InputFile>
                                    <Hr />
                                    <form onSubmit={submitDeadline}>
                                        <InputFile label="set New Date" error="deatline">
                                            <div className="flex">
                                                <TextInput type="date" className="py-1" value={deadlineForm.data.deatline} onChange={(e) => deadlineForm.setData("deatline", e.target.value)} />
                                                <PrimaryButton className="ms-2 py-1">set</PrimaryButton>
                                            </div>
                                        </InputFile>
                                    </form>
                                </SectionInner>
                            </SectionSection>
                            <SectionSection>
                                <InputFile label="Nid" error="nid">
                                    <TextInput type="number" className="form-control py-1" value={reseller?.documents?.nid ?? ""} label="NID No" name="nid" error="nid" onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label="NID Image (front side)" error="nid_front">
                                    {reseller?.documents?.nid_front_url ? <img width="300px" height="200px" src={reseller.documents.nid_front_url} alt="" /> : <div>N/A</div>}
                                </InputFile>
                                <Hr />
                                <InputFile label="NID Image (back side)" error="nid_back">
                                    {reseller?.documents?.nid_back_url ? <img width="300px" height="200px" src={reseller.documents.nid_back_url} alt="" /> : <div>N/A</div>}
                                </InputFile>
                                <Hr />
                            </SectionSection>
                            <SectionSection>
                                <InputFile label="TIN No" error="tin">
                                    <TextInput type="text" name="" value={reseller?.documents?.shop_tin ?? ""} id="" onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label="TIN Image" error="shop_tin">
                                    {reseller?.documents?.shop_tin_image_url ? <img width="300px" height="200px" src={reseller.documents.shop_tin_image_url} alt="" /> : <div>N/A</div>}
                                </InputFile>
                            </SectionSection>
                            <SectionSection>
                                <InputFile label="Shop Trade" error="shop_trade">
                                    <TextInput type="text" name="" value={reseller?.documents?.shop_trade ?? ""} id="" onChange={() => {}} />
                                </InputFile>
                                <Hr />
                                <InputFile label="Trade License Image" error="shop_trade_image">
                                    {reseller?.documents?.shop_trade_image_url ? <img width="300px" height="200px" src={reseller.documents.shop_trade_image_url} alt="" /> : <div>N/A</div>}
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
            </div>

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
