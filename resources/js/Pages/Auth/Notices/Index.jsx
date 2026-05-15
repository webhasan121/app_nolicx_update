import { Head, Link, router, useForm } from "@inertiajs/react";
import axios from "axios";
import { useState } from "react";
import AppLayout from "../../../Layouts/App";
import InputError from "../../../components/InputError";
import InputLabel from "../../../components/InputLabel";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import useTranslation from "../../../hooks/useTranslation";

const emptyNotice = {
    title: "",
    body: "",
    target_roles: ["user"],
    is_active: true,
    published_at: "",
    expires_at: "",
};

export default function NoticeIndex({
    canManage = false,
    targetRoles = [],
    notices = { data: [], links: [], total: 0 },
}) {
    const { t } = useTranslation();
    const [editingId, setEditingId] = useState(null);
    const form = useForm(emptyNotice);

    const editNotice = (notice) => {
        setEditingId(notice.id);
        form.setData({
            title: notice.title ?? "",
            body: notice.body ?? "",
            target_roles: notice.target_roles?.length ? notice.target_roles : [],
            is_active: Boolean(notice.is_active),
            published_at: notice.published_at ?? "",
            expires_at: notice.expires_at ?? "",
        });
    };

    const resetForm = () => {
        setEditingId(null);
        form.reset();
        form.clearErrors();
    };

    const submit = (e) => {
        e.preventDefault();

        const options = {
            preserveScroll: true,
            onSuccess: resetForm,
        };

        if (editingId) {
            form.put(route("dashboard.notices.update", { notice: editingId }), options);
            return;
        }

        form.post(route("dashboard.notices.store"), options);
    };

    const destroyNotice = (notice) => {
        if (!window.confirm("Delete this notice?")) {
            return;
        }

        router.delete(route("dashboard.notices.destroy", { notice: notice.id }), {
            preserveScroll: true,
        });
    };

    const toggleRole = (role) => {
        const roles = form.data.target_roles ?? [];

        form.setData(
            "target_roles",
            roles.includes(role)
                ? roles.filter((item) => item !== role)
                : [...roles, role]
        );
    };

    const markNoticeRead = (notice) => {
        if (!notice.is_read) {
            axios.post(route("dashboard.notices.read", { notice: notice.id }));
        }
    };

    return (
        <AppLayout title={t("Notice")}>
            <Head title={t("Notice")} />

            <Container>
                {canManage && (
                    <Section>
                        <SectionHeader
                            title={editingId ? "Edit Notice" : "Create Notice"}
                            content={t("Publish notices for users, vendors, resellers, riders, and the system panel.")}
                        />

                        <form onSubmit={submit} className="space-y-4">
                            <div>
                                <InputLabel htmlFor="title">Title</InputLabel>
                                <TextInput
                                    id="title"
                                    className="mt-1 block w-full"
                                    value={form.data.title}
                                    onChange={(e) => form.setData("title", e.target.value)}
                                />
                                <InputError messages={form.errors.title} className="mt-1" />
                            </div>

                            <div>
                                <InputLabel htmlFor="body">Message</InputLabel>
                                <textarea
                                    id="body"
                                    rows="5"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value={form.data.body}
                                    onChange={(e) => form.setData("body", e.target.value)}
                                />
                                <InputError messages={form.errors.body} className="mt-1" />
                            </div>

                            <div>
                                <InputLabel>Show To</InputLabel>
                                <div className="mt-2 flex flex-wrap gap-3">
                                    {targetRoles.map((role) => (
                                        <label
                                            key={role.value}
                                            className="inline-flex items-center gap-2 rounded border border-gray-200 bg-white px-3 py-2 text-sm"
                                        >
                                            <input
                                                type="checkbox"
                                                checked={(form.data.target_roles ?? []).includes(role.value)}
                                                onChange={() => toggleRole(role.value)}
                                            />
                                            {role.label}
                                        </label>
                                    ))}
                                </div>
                                <div className="mt-1 text-xs text-gray-500">{t("Leave all unchecked to show the notice to every dashboard user.")}</div>
                                <InputError messages={form.errors.target_roles} className="mt-1" />
                            </div>

                            <div className="grid gap-4 md:grid-cols-3">
                                <div>
                                    <InputLabel htmlFor="published_at">Publish At</InputLabel>
                                    <TextInput
                                        id="published_at"
                                        type="datetime-local"
                                        className="mt-1 block w-full"
                                        value={form.data.published_at}
                                        onChange={(e) => form.setData("published_at", e.target.value)}
                                    />
                                    <InputError messages={form.errors.published_at} className="mt-1" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="expires_at">Expire At</InputLabel>
                                    <TextInput
                                        id="expires_at"
                                        type="datetime-local"
                                        className="mt-1 block w-full"
                                        value={form.data.expires_at}
                                        onChange={(e) => form.setData("expires_at", e.target.value)}
                                    />
                                    <InputError messages={form.errors.expires_at} className="mt-1" />
                                </div>

                                <label className="mt-6 inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="checkbox"
                                        checked={form.data.is_active}
                                        onChange={(e) => form.setData("is_active", e.target.checked)}
                                    />{t("Active")}</label>
                            </div>

                            <div className="flex gap-2">
                                <PrimaryButton disabled={form.processing}>
                                    <i className={`fas ${editingId ? "fa-save" : "fa-plus"} pr-2`}></i>
                                    {editingId ? "Update Notice" : "Create Notice"}
                                </PrimaryButton>
                                {editingId && (
                                    <button
                                        type="button"
                                        onClick={resetForm}
                                        className="rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase text-gray-700"
                                    >{t("Cancel")}</button>
                                )}
                            </div>
                        </form>
                    </Section>
                )}

                <Section>
                    <SectionHeader
                        title={`Notice (${notices.total ?? notices.data.length})`}
                        content={canManage ? "All notices are listed below." : "Notices for your account are listed below."}
                    />

                    <div className="space-y-3">
                        {notices.data.length === 0 && (
                            <div className="rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">{t("No notice found.")}</div>
                        )}

                        {notices.data.map((notice) => {
                            return (
                            <article
                                key={notice.id}
                                className={`relative block w-full rounded-md border border-gray-200 bg-white p-4 text-left shadow-sm transition hover:border-orange-200 hover:bg-orange-50/30 ${
                                    notice.link_url ? "cursor-pointer" : "cursor-default"
                                } ${!notice.is_read ? "border-l-4 border-l-orange-500" : ""}`}
                            >
                                {notice.link_url && (
                                    <Link
                                        href={notice.link_url}
                                        onClick={() => markNoticeRead(notice)}
                                        className="absolute inset-0 z-0 rounded-md"
                                        aria-label={notice.title}
                                    />
                                )}

                                <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div className="relative z-10 pointer-events-none">
                                        <h2 className="text-lg font-semibold text-gray-900">
                                            {notice.title}
                                        </h2>
                                        <div className="mt-1 text-xs text-gray-500">{t("Published:")}{notice.published_at_formatted}{t("| Expires:")}{notice.expires_at_formatted}
                                        </div>
                                    </div>

                                    {canManage && (
                                        <div className="relative z-10 flex gap-2">
                                            <button
                                                type="button"
                                                onClick={() => editNotice(notice)}
                                                className="rounded border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700"
                                            >
                                                <i className="fas fa-pen pr-1"></i>{t("Edit")}</button>
                                            <button
                                                type="button"
                                                onClick={() => destroyNotice(notice)}
                                                className="rounded border border-red-200 px-3 py-1 text-xs font-semibold text-red-700"
                                            >
                                                <i className="fas fa-trash pr-1"></i>{t("Delete")}</button>
                                        </div>
                                    )}
                                </div>

                                <p className="relative z-10 mt-3 whitespace-pre-wrap text-sm leading-6 text-gray-700 pointer-events-none">
                                    {notice.body}
                                </p>

                                {canManage && (
                                    <div className="relative z-10 mt-3 flex flex-wrap items-center gap-2 text-xs pointer-events-none">
                                        <span className={`rounded px-2 py-1 ${notice.is_active ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"}`}>
                                            {notice.is_active ? "Active" : "Inactive"}
                                        </span>
                                        <span className="rounded bg-gray-100 px-2 py-1 text-gray-700">{t("To:")}{notice.target_roles.length ? notice.target_roles.join(", ") : "All"}
                                        </span>
                                        <span className="text-gray-500">{t("By")}{notice.creator_name}{t("on")}{notice.created_at_formatted}
                                        </span>
                                    </div>
                                )}
                            </article>
                        );
                        })}
                    </div>

                    {notices.links?.length > 0 && (
                        <div className="mt-4 flex flex-wrap gap-2">
                            {notices.links.map((link, index) => (
                                <Link
                                    key={`${link.label}-${index}`}
                                    href={link.url ?? "#"}
                                    className={`rounded border px-3 py-1 text-sm ${
                                        link.active
                                            ? "border-orange-500 bg-orange-500 text-white"
                                            : "border-gray-300 bg-white text-gray-700"
                                    } ${!link.url ? "pointer-events-none opacity-50" : ""}`}
                                    preserveScroll
                                >
                                    {link.label}
                                </Link>
                            ))}
                        </div>
                    )}
                </Section>
            </Container>
        </AppLayout>
    );
}
