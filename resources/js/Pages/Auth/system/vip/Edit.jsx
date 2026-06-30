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
import useTranslation from "../../../../hooks/useTranslation";

export default function Edit() {
    const { t } = useTranslation();
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
        if (status === "reject" && !window.confirm("Are you sure you want to move this VIP user to trash?")) {
            return;
        }

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
        if (!window.confirm("Are you sure you want to delete this VIP user permanently?")) {
            return;
        }

        router.delete(route("system.vip.user.delete", { vip: vipData.id }));
    };

    const isActive = Boolean(vipData?.status) && !vipData?.deleted_at;
    const isPending = !vipData?.status && !vipData?.deleted_at;
    const isTrash = Boolean(vipData?.deleted_at);

    return (
        <AppLayout
            title={t("Edit VIP Users")}
            header={
                <PageHeader>
                    <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>{t("Edit VIP Users")}</div>
                        <NavLink href={route("system.vip.users")}>
                            {t("Index")}<i className="fa-solid fa-arrow-right ms-2"></i>
                        </NavLink>
                    </div>
                </PageHeader>
            }
        >
            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div className="min-w-0">
                                    <div className="flex flex-col gap-1 sm:flex-row sm:items-center">
                                        <NavLink
                                            href={route("system.users.edit", {
                                                id: vipData?.user_id,
                                            })}
                                            className="break-words"
                                        >
                                            {vipData?.name ?? "N/A"}
                                        </NavLink>
                                        <div className="hidden px-2 sm:block"></div>
                                        <div className="text-xs">
                                            {vipData?.created_at_formatted}
                                        </div>
                                    </div>
                                </div>
                                <div className="inline-flex w-fit rounded border bg-slate-900 px-2 py-1 text-sm text-white shadow">
                                    {vipData?.package_name ?? "N/A"}
                                </div>
                            </div>
                        }
                        content={
                            <div className="flex flex-wrap items-center gap-2 text-sm">
                                <button
                                    type="button"
                                    onClick={() => updateStatus("active")}
                                    className={`rounded px-3 py-1 cursor-pointer ${isActive ? "bg-indigo-800 text-white text-bold" : "bg-slate-100 text-slate-700"}`}
                                >{t("Active")}</button>
                                <button
                                    type="button"
                                    onClick={() => updateStatus("pending")}
                                    className={`rounded px-3 py-1 cursor-pointer ${isPending ? "bg-indigo-800 text-white text-bold" : "bg-slate-100 text-slate-700"}`}
                                >{t("Pending")}</button>
                                <button
                                    type="button"
                                    onClick={() => updateStatus("reject")}
                                    className={`rounded px-3 py-1 cursor-pointer ${isTrash ? "bg-indigo-800 text-white text-bold" : "bg-slate-100 text-slate-700"}`}
                                >{t("Trash")}</button>
                            </div>
                        }
                    />
                    <SectionInner>
                        {vipData?.expired ? (
                            <div className="inline-flex px-1 text-xs bg-yellow-200 rounded">{t("Expired")}</div>
                        ) : null}
                    </SectionInner>
                    <hr />
                    <SectionInner>
                        <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                            <SecondaryButton type="button" className="w-full justify-center sm:w-auto" onClick={reCalculateComission}>{t("Re-Calculate Comission")}</SecondaryButton>
                            <SecondaryButton type="button" className="w-full justify-center sm:w-auto" onClick={pushBackComission}>{t("Push Back Comission")}</SecondaryButton>
                        </div>
                    </SectionInner>
                </Section>

                <Section>
                    <SectionHeader
                        title={t("Users Payment and Package")}
                        content={t("view here vip users payment and packages informations.")}
                    />

                    <SectionInner>
                        <div className="grid grid-cols-1 gap-x-8 md:grid-cols-2">
                            <div className="min-w-0 border-b py-2">
                                <div className="text-sm">{t("Payment Method")}</div>
                                <div className="text-md">{vipData?.payment_by ?? "N/A"}</div>
                            </div>
                            <div className="min-w-0 border-b py-2">
                                <div className="text-sm">{t("TRX ID")}</div>
                                <div className="text-md">{vipData?.trx ?? "N/A"}</div>
                            </div>
                            <div className="min-w-0 border-b py-2">
                                <div className="text-sm">{t("NID")}</div>
                                <div className="text-md">{vipData?.nid ?? "N/A"}</div>
                            </div>
                            <div className="min-w-0 border-b py-2">
                                <div className="text-sm">{t("Phone")}</div>
                                <div className="text-md">{vipData?.phone ?? "N/A"}</div>
                            </div>
                            <div className="min-w-0 border-b py-2">
                                <div className="text-sm">{t("Date")}</div>
                                <div className="text-md">{vipData?.created_at_formatted}</div>
                            </div>
                            <div className="min-w-0 border-b py-2">
                                <div className="text-sm">{t("Comission")}</div>
                                <div className="text-md">{vipData?.comission ?? "N/A"}{t("TK")}</div>
                            </div>
                            <div className="min-w-0 border-b py-2">
                                <div className="text-sm">{t("Reffer By")}</div>
                                <div className="text-md break-words">
                                    {vipData?.refer_by_name ?? "N/A"} - {vipData?.refer_by_email ?? "N/A"}
                                </div>
                            </div>
                            <div className="min-w-0 border-b py-2">
                                <div className="text-sm">{t("Ref Code")}</div>
                                <div className="text-md">{vipData?.reference ?? "N/A"}</div>
                            </div>
                        </div>
                    </SectionInner>

                    <div className="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                        {vipData?.nid_front_url ? (
                            <img
                                className="h-auto w-full rounded border object-cover"
                                src={vipData.nid_front_url}
                                alt="NID Front"
                            />
                        ) : null}
                        {vipData?.nid_back_url ? (
                            <img
                                className="h-auto w-full rounded border object-cover"
                                src={vipData.nid_back_url}
                                alt="NID Back"
                            />
                        ) : null}
                    </div>
                </Section>

                <div className="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-start">
                    <Section className="xl:flex-1">
                        <SectionHeader
                            title={t("User VIP Package Update")}
                            content={
                                <>
                                    currently user belongs to <strong>{vipData?.package_name ?? "N/A"}</strong> package.
                                    Migrate to other package.
                                </>
                            }
                        />
                        <SectionInner>
                            {(vips ?? []).map((item) => (
                                <div key={item.id} className="mb-3 flex flex-col gap-2 rounded border p-3 sm:flex-row sm:items-center">
                                    <input
                                        id={`package_${item.id}`}
                                        type="radio"
                                        value={item.id}
                                        style={{ width: 20, height: 20 }}
                                        className="mr-3 rounded"
                                        checked={Number(selectedPackage) === Number(item.id)}
                                        onChange={() => setSelectedPackage(item.id)}
                                    />
                                    <div className="flex flex-wrap items-center">
                                        <InputLabel htmlFor={`package_${item.id}`}>{item.name}</InputLabel>
                                        <i className="px-2 fa-solid fa-arrow-right"></i>
                                        <div>{item.price}{t("TK")}</div>
                                    </div>
                                </div>
                            ))}
                            <br />
                            <div className="text-end">
                                <SecondaryButton type="button" className="w-full justify-center sm:w-auto">{t("Procced to Migrate")}</SecondaryButton>
                            </div>
                        </SectionInner>
                    </Section>

                    <Section className="xl:flex-1">
                        <SectionHeader
                            title={t("Update Task Type")}
                            content={
                                <>
                                    user currentry use <strong>{vipData?.task_type}.</strong>
                                </>
                            }
                        />
                        <SectionInner>
                            <form onSubmit={updateTask}>
                                <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
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
                                    <PrimaryButton type="submit" className="w-full justify-center sm:w-auto">{t("Update")}</PrimaryButton>
                                </div>
                            </form>
                        </SectionInner>
                    </Section>
                </div>

                <div>
                    <Section>
                        <SectionHeader
                            title={t("Update Validation")}
                            content={t("Update validation time for next 360 days, or your custom days. Give the valid day in input.")}
                        />

                        <SectionInner>
                            <form onSubmit={updateValidity}>
                                <div className="w-full p-3 my-2 text-red-900 bg-red-100 rounded">
                                    <div className="rounded p-2 break-words">{t("Package will expire on")}<strong>{vipData?.valid_till_formatted}</strong>
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
                                    <PrimaryButton type="submit" className="w-full justify-center sm:w-auto">{t("Update Validation")}</PrimaryButton>
                                </div>
                            </form>
                        </SectionInner>
                    </Section>

                    {vipData?.deleted_at ? (
                        <Section>
                            <SectionHeader
                                title={<div className="text-red-900">{t("VIP in Trash")}</div>}
                                content={t("trashed may be restored or deleted permanently.")}
                            />

                            <SectionInner>
                                <div className="flex flex-col gap-2 sm:flex-row">
                                    <DangerButton className="w-full justify-center sm:mr-1 sm:w-auto" type="button" onClick={restore}>{t("Restore")}</DangerButton>
                                    <DangerButton className="w-full justify-center sm:w-auto" type="button" onClick={destroy}>{t("Permanently Delete")}</DangerButton>
                                </div>
                            </SectionInner>
                        </Section>
                    ) : null}
                </div>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div>{t("User Tasks")}</div>
                            </div>
                        }
                        content={t("user tasks and earning against this packages.")}
                    />

                    <SectionInner>
                        <NavLinkBtn href="#" className="inline-flex w-full justify-center sm:w-auto">
                            View All
                        </NavLinkBtn>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
