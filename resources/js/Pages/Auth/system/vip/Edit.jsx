import { router, useForm, usePage } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import NavLink from "../../../../components/NavLink";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import InputLabel from "../../../../components/InputLabel";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Section from "../../../../components/dashboard/section/Section";

export default function Edit() {
    const { vipData, vips = [] } = usePage().props;
    const [selectedPackage, setSelectedPackage] = useState(vipData?.package_id ?? null);

    const taskForm = useForm({
        task: vipData?.task_type ?? "daily",
    });
    const validityForm = useForm({
        valid_days: 360,
    });
    const statusForm = useForm({
        status: "active",
        valid_days: 360,
    });

    const updateStatus = (status) => {
        statusForm.setData((data) => ({ ...data, status }));
        router.post(
            route("system.vip.status", { vip: vipData.id }),
            {
                status,
                valid_days: validityForm.data.valid_days,
            }
        );
    };

    const updateTask = (e) => {
        e.preventDefault();
        taskForm.post(route("system.vip.task", { vip: vipData.id }));
    };

    const updateValidity = (e) => {
        e.preventDefault();
        validityForm.post(route("system.vip.validity", { vip: vipData.id }));
    };

    const reCalculateComission = () => {
        router.post(route("system.vip.recalculate-comission", { vip: vipData.id }));
    };

    const pushBackComission = () => {
        router.post(route("system.vip.pushback-comission", { vip: vipData.id }));
    };

    const restore = () => {
        router.post(route("system.vip.user.restore", { vip: vipData.id }));
    };

    const destroy = () => {
        router.delete(route("system.vip.user.delete", { vip: vipData.id }));
    };

    const isActive = Boolean(vipData?.status) && !vipData?.deleted_at;
    const isPending = !vipData?.status && !vipData?.deleted_at;
    const isTrash = Boolean(vipData?.deleted_at);

    return (
        <AppLayout
            title="Edit VIP Users"
            header={
                <PageHeader>
                    Edit VIP Users
                    <br />
                    <NavLink href={route("system.vip.users")}>
                        Index <i className="fa-solid fa-arrow-right ms-2"></i>
                    </NavLink>
                </PageHeader>
            }
        >
            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between text-wrap">
                                <div className="flex items-center">
                                    <NavLink
                                        href={route("system.users.edit", {
                                            id: vipData?.user_id,
                                        })}
                                    >
                                        {vipData?.name ?? "N/A"}
                                    </NavLink>
                                    <div className="px-2"></div>
                                    <div className="text-xs">
                                        {vipData?.created_at_formatted}
                                    </div>
                                </div>
                                <div className="px-2 py-1 text-sm text-white border rounded shadow bg-slate-900">
                                    {vipData?.package_name ?? "N/A"}
                                </div>
                            </div>
                        }
                        content={
                            <div className="flex text-sm">
                                <button
                                    type="button"
                                    onClick={() => updateStatus("active")}
                                    className={`px-2 rounded cursor-pointer ${isActive ? "bg-indigo-800 text-white text-bold" : ""}`}
                                >
                                    Active
                                </button>
                                <button
                                    type="button"
                                    onClick={() => updateStatus("pending")}
                                    className={`px-2 rounded cursor-pointer space-x-2 ${isPending ? "bg-indigo-800 text-white text-bold" : ""}`}
                                >
                                    Pending
                                </button>
                                <button
                                    type="button"
                                    onClick={() => updateStatus("reject")}
                                    className={`px-2 rounded cursor-pointer ${isTrash ? "bg-indigo-800 text-white text-bold" : ""}`}
                                >
                                    Trash
                                </button>
                            </div>
                        }
                    />
                    <SectionInner>
                        {vipData?.expired ? (
                            <div className="inline-flex px-1 text-xs bg-yellow-200 rounded">
                                Expired
                            </div>
                        ) : null}
                    </SectionInner>
                    <hr />
                    <SectionInner>
                        <div className="flex items-center justify-end space-x-2">
                            <SecondaryButton type="button" onClick={reCalculateComission}>
                                Re-Calculate Comission
                            </SecondaryButton>
                            <SecondaryButton type="button" onClick={pushBackComission}>
                                Push Back Comission
                            </SecondaryButton>
                        </div>
                    </SectionInner>
                </Section>

                <Section>
                    <SectionHeader
                        title="Users Payment and Package"
                        content="view here vip users payment and packages informations."
                    />

                    <SectionInner>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                            <div className="py-2 border-b w-md">
                                <div className="text-sm">Payment Method</div>
                                <div className="text-md">{vipData?.payment_by ?? "N/A"}</div>
                            </div>
                            <div className="py-2 border-b w-md">
                                <div className="text-sm">TRX ID</div>
                                <div className="text-md">{vipData?.trx ?? "N/A"}</div>
                            </div>
                            <div className="py-2 border-b w-md">
                                <div className="text-sm">NID</div>
                                <div className="text-md">{vipData?.nid ?? "N/A"}</div>
                            </div>
                            <div className="py-2 border-b w-md">
                                <div className="text-sm">Phone</div>
                                <div className="text-md">{vipData?.phone ?? "N/A"}</div>
                            </div>
                            <div className="py-2 border-b w-md">
                                <div className="text-sm">Date</div>
                                <div className="text-md">{vipData?.created_at_formatted}</div>
                            </div>
                            <div className="py-2 border-b w-md">
                                <div className="text-sm">Comission</div>
                                <div className="text-md">{vipData?.comission ?? "N/A"} TK</div>
                            </div>
                            <div className="py-2 border-b w-md">
                                <div className="text-sm">Reffer By</div>
                                <div className="text-md">
                                    {vipData?.refer_by_name ?? "N/A"} - {vipData?.refer_by_email ?? "N/A"}
                                </div>
                            </div>
                            <div className="py-2 border-b w-md">
                                <div className="text-sm">Ref Code</div>
                                <div className="text-md">{vipData?.reference ?? "N/A"}</div>
                            </div>
                        </div>
                    </SectionInner>

                    <div className="flex mt-2 space-x-3">
                        {vipData?.nid_front_url ? (
                            <img
                                className="border rounded"
                                src={vipData.nid_front_url}
                                width="300px"
                                height="80px"
                                alt="NID Front"
                            />
                        ) : null}
                        {vipData?.nid_back_url ? (
                            <img
                                className="border rounded"
                                src={vipData.nid_back_url}
                                width="300px"
                                height="80px"
                                alt="NID Back"
                            />
                        ) : null}
                    </div>
                </Section>

                <div className="items-start justify-start md:flex">
                    <Section>
                        <SectionHeader
                            title="User VIP Package Update"
                            content={
                                <>
                                    currently user belongs to <strong>{vipData?.package_name ?? "N/A"}</strong> package.
                                    Migrate to other package.
                                </>
                            }
                        />
                        <SectionInner>
                            {(vips ?? []).map((item) => (
                                <div key={item.id} className="flex items-center p-2 mb-3 border rounded">
                                    <input
                                        id={`package_${item.id}`}
                                        type="radio"
                                        value={item.id}
                                        style={{ width: 20, height: 20 }}
                                        className="mr-3 rounded"
                                        checked={Number(selectedPackage) === Number(item.id)}
                                        onChange={() => setSelectedPackage(item.id)}
                                    />
                                    <div className="flex items-center">
                                        <InputLabel htmlFor={`package_${item.id}`}>{item.name}</InputLabel>
                                        <i className="px-2 fa-solid fa-arrow-right"></i>
                                        <div>{item.price} TK</div>
                                    </div>
                                </div>
                            ))}
                            <br />
                            <div className="text-end">
                                <SecondaryButton type="button">
                                    Procced to Migrate
                                </SecondaryButton>
                            </div>
                        </SectionInner>
                    </Section>

                    <Section>
                        <SectionHeader
                            title="Update Task Type"
                            content={
                                <>
                                    user currentry use <strong>{vipData?.task_type}.</strong>
                                </>
                            }
                        />
                        <SectionInner>
                            <form onSubmit={updateTask}>
                                <div className="flex flex-wrap">
                                    <div className="flex items-center p-2 m-1 border rounded">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            value="daily"
                                            checked={taskForm.data.task === "daily"}
                                            onChange={(e) => taskForm.setData("task", e.target.value)}
                                            className="mr-3 rounded"
                                            id="vip-task-daily"
                                        />
                                        <InputLabel htmlFor="vip-task-daily">Daily</InputLabel>
                                    </div>
                                    <div className="flex items-center p-2 m-1 border rounded">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            value="monthly"
                                            checked={taskForm.data.task === "monthly"}
                                            onChange={(e) => taskForm.setData("task", e.target.value)}
                                            className="mr-3 rounded"
                                            id="vip-task-monthly"
                                        />
                                        <InputLabel htmlFor="vip-task-monthly">Monthly</InputLabel>
                                    </div>
                                    <div className="flex items-center p-2 m-1 border rounded">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            value="disabled"
                                            checked={taskForm.data.task === "disabled"}
                                            onChange={(e) => taskForm.setData("task", e.target.value)}
                                            className="mr-3 rounded"
                                            id="vip-task-disabled"
                                        />
                                        <InputLabel htmlFor="vip-task-disabled">Disabled Task</InputLabel>
                                    </div>
                                </div>
                                <br />
                                <div className="text-end">
                                    <PrimaryButton type="submit">Update</PrimaryButton>
                                </div>
                            </form>
                        </SectionInner>
                    </Section>
                </div>

                <div>
                    <Section>
                        <SectionHeader
                            title="Update Validation"
                            content="Update validation time for next 360 days, or your custom days. Give the valid day in input."
                        />

                        <SectionInner>
                            <form onSubmit={updateValidity}>
                                <div className="w-full p-3 my-2 text-red-900 bg-red-100 rounded">
                                    <div className="p-2 rounded">
                                        Package will expire on <strong>{vipData?.valid_till_formatted}</strong>
                                        {vipData?.valid_till_human ? ` (${vipData.valid_till_human})` : ""}
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="vip-valid-days">New Valid Days</InputLabel>
                                        <TextInput
                                            id="vip-valid-days"
                                            type="number"
                                            value={validityForm.data.valid_days}
                                            onChange={(e) => validityForm.setData("valid_days", e.target.value)}
                                        />
                                    </div>
                                </div>
                                <div className="text-end">
                                    <PrimaryButton type="submit">Update Validation</PrimaryButton>
                                </div>
                            </form>
                        </SectionInner>
                    </Section>

                    {vipData?.deleted_at ? (
                        <Section>
                            <SectionHeader
                                title={<div className="text-red-900">VIP in Trash</div>}
                                content="trashed may be restored or deleted permanently."
                            />

                            <SectionInner>
                                <DangerButton className="mr-1" type="button" onClick={restore}>
                                    Restore
                                </DangerButton>
                                <DangerButton type="button" onClick={destroy}>
                                    Permanently Delete
                                </DangerButton>
                            </SectionInner>
                        </Section>
                    ) : null}
                </div>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div>User Tasks</div>
                            </div>
                        }
                        content="user tasks and earning against this packages."
                    />

                    <SectionInner>
                        <NavLinkBtn href="#">
                            View All
                        </NavLinkBtn>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
