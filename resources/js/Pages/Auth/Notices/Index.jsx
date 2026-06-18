import { Head, Link, router, useForm } from "@inertiajs/react";
import axios from "axios";
import { useEffect, useState } from "react";
import AppLayout from "../../../Layouts/App";
import UserDash from "../../../components/user/dash/UserDash";
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
    noticeRole = null,
    personal = false,
    targetRoles = [],
    filters = { search: "", start_date: "", end_date: "" },
    notices = { data: [], links: [], total: 0 },
}) {
    const { t } = useTranslation();
    const [editingId, setEditingId] = useState(null);
    const [noticeItems, setNoticeItems] = useState(notices.data ?? []);
    const [noticeMeta, setNoticeMeta] = useState(notices);
    const [loadingMore, setLoadingMore] = useState(false);
    const [refreshingNotices, setRefreshingNotices] = useState(false);
    const [selectedNoticeIds, setSelectedNoticeIds] = useState([]);
    const [bulkDeleting, setBulkDeleting] = useState(false);
    const [search, setSearch] = useState(filters.search ?? "");
    const [startDate, setStartDate] = useState(filters.start_date ?? "");
    const [endDate, setEndDate] = useState(filters.end_date ?? "");
    const form = useForm(emptyNotice);

    useEffect(() => {
        setNoticeItems(notices.data ?? []);
        setNoticeMeta(notices);
        setSelectedNoticeIds([]);
    }, [notices]);

    useEffect(() => {
        setSearch(filters.search ?? "");
        setStartDate(filters.start_date ?? "");
        setEndDate(filters.end_date ?? "");
    }, [filters.end_date, filters.search, filters.start_date]);

    const filterParams = (page = null, overrides = {}) => {
        const params = {
            ...(noticeRole ? { role: noticeRole } : {}),
            ...(personal ? { personal: 1 } : {}),
            search: overrides.search ?? search,
            start_date: overrides.start_date ?? startDate,
            end_date: overrides.end_date ?? endDate,
        };

        if (page) {
            params.page = page;
        }

        return params;
    };

    const applyFilters = (overrides = {}) => {
        router.get(route("dashboard.notices.index"), filterParams(null, overrides), {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            only: ["filters", "notices"],
        });
    };

    useEffect(() => {
        const nextSearch = search.trim();

        if (nextSearch === (filters.search ?? "")) {
            return undefined;
        }

        const timeout = window.setTimeout(() => {
            applyFilters({ search: nextSearch });
        }, 400);

        return () => window.clearTimeout(timeout);
    }, [search]);

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

    const toggleNoticeSelection = (noticeId) => {
        setSelectedNoticeIds((ids) => {
            if (ids.includes(noticeId)) {
                return ids.filter((id) => id !== noticeId);
            }

            if (ids.length >= 50) {
                return ids;
            }

            return [...ids, noticeId];
        });
    };

    const selectLoadedNotices = () => {
        setSelectedNoticeIds(noticeItems.slice(0, 50).map((notice) => notice.id));
    };

    const clearSelectedNotices = () => {
        setSelectedNoticeIds([]);
    };

    const bulkDeleteNotices = async () => {
        if (!selectedNoticeIds.length || bulkDeleting) {
            return;
        }

        if (!window.confirm(`Delete ${selectedNoticeIds.length} selected notices?`)) {
            return;
        }

        setBulkDeleting(true);

        try {
            const response = await axios.delete("/dashboard/notices/bulk-delete", {
                data: {
                    ids: selectedNoticeIds,
                },
            });
            const deletedIds = response.data.ids ?? selectedNoticeIds;

            setNoticeItems((items) => items.filter((notice) => !deletedIds.includes(notice.id)));
            setNoticeMeta((meta) => ({
                ...meta,
                total: Math.max(Number(meta.total ?? 0) - deletedIds.length, 0),
            }));
            setSelectedNoticeIds([]);
        } finally {
            setBulkDeleting(false);
        }
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

    const mergeFreshNotices = (freshNotices = []) => {
        if (!freshNotices.length) {
            return;
        }

        setNoticeItems((items) => {
            const freshIds = freshNotices.map((notice) => notice.id);
            const remainingItems = items.filter((notice) => !freshIds.includes(notice.id));

            return [...freshNotices, ...remainingItems];
        });
    };

    const refreshNotices = async () => {
        if (refreshingNotices || bulkDeleting) {
            return;
        }

        setRefreshingNotices(true);

        try {
            const response = await axios.get(route("dashboard.notices.index"), {
                params: filterParams(1),
                headers: {
                    Accept: "application/json",
                },
            });
            const freshNotices = response.data.notices ?? { data: [] };

            mergeFreshNotices(freshNotices.data ?? []);
            setNoticeMeta((meta) => ({
                ...freshNotices,
                current_page: Math.max(Number(meta.current_page ?? 1), Number(freshNotices.current_page ?? 1)),
                has_more: Boolean(meta.has_more || freshNotices.has_more),
            }));
        } finally {
            setRefreshingNotices(false);
        }
    };

    useEffect(() => {
        const interval = window.setInterval(() => {
            refreshNotices().catch(() => {});
        }, 5000);

        const handleFocus = () => {
            refreshNotices().catch(() => {});
        };

        window.addEventListener("focus", handleFocus);

        return () => {
            window.clearInterval(interval);
            window.removeEventListener("focus", handleFocus);
        };
    }, [refreshingNotices, bulkDeleting, noticeMeta.current_page, noticeMeta.has_more, noticeRole, search, startDate, endDate]);

    const loadMoreNotices = async () => {
        if (loadingMore || !noticeMeta.has_more) {
            return;
        }

        setLoadingMore(true);

        try {
            const response = await axios.get(route("dashboard.notices.index"), {
                params: filterParams(Number(noticeMeta.current_page ?? 1) + 1),
                headers: {
                    Accept: "application/json",
                },
            });

            const nextNotices = response.data.notices ?? { data: [] };
            setNoticeItems((items) => [...items, ...(nextNotices.data ?? [])]);
            setNoticeMeta(nextNotices);
        } finally {
            setLoadingMore(false);
        }
    };

    const content = (
        <>
            <Head title={t("Notice")} />

            <Container>
                {canManage && (
                    <Section>
                        <SectionHeader
                            title={editingId ? t("Edit Notice") : t("Create Notice")}
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
                                        type="date"
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
                                        type="date"
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
                                    {editingId ? t("Update Notice") : t("Create Notice")}
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
                        title={t("Notice (:count)", { count: noticeMeta.total ?? noticeItems.length })}
                        content={canManage ? t("All notices are listed below.") : t("Notices for your account are listed below.")}
                    />

                    <div className="mb-4 flex flex-wrap items-center justify-end gap-2">
                        <TextInput
                            type="date"
                            className="h-10 w-40 py-2"
                            value={startDate}
                            onChange={(e) => {
                                const value = e.target.value;
                                setStartDate(value);
                                applyFilters({ start_date: value });
                            }}
                        />
                        <TextInput
                            type="date"
                            className="h-10 w-40 py-2"
                            value={endDate}
                            onChange={(e) => {
                                const value = e.target.value;
                                setEndDate(value);
                                applyFilters({ end_date: value });
                            }}
                        />
                        <TextInput
                            type="search"
                            className="h-10 w-full py-2 sm:w-64"
                            value={search}
                            placeholder={t("Search notices...")}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => {
                                if (e.key !== "Enter") {
                                    return;
                                }

                                e.preventDefault();
                                applyFilters({ search: search.trim() });
                            }}
                        />
                        <PrimaryButton
                            type="button"
                            onClick={() => applyFilters({ search: search.trim() })}
                        >
                            <i className="fas fa-search"></i>
                        </PrimaryButton>
                        {(search || startDate || endDate) && (
                            <button
                                type="button"
                                className="h-10 rounded-md border border-gray-300 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50"
                                onClick={() => {
                                    setSearch("");
                                    setStartDate("");
                                    setEndDate("");
                                    applyFilters({
                                        search: "",
                                        start_date: "",
                                        end_date: "",
                                    });
                                }}
                            >
                                {t("Reset")}
                            </button>
                        )}
                    </div>

                    {canManage && noticeItems.length > 0 && (
                        <div className="mb-4 flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                onClick={selectLoadedNotices}
                                className="rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase text-gray-700"
                            >
                                {t("Select 50")}
                            </button>
                            <button
                                type="button"
                                onClick={clearSelectedNotices}
                                className="rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase text-gray-700"
                            >
                                Clear
                            </button>
                            <button
                                type="button"
                                onClick={bulkDeleteNotices}
                                disabled={!selectedNoticeIds.length || bulkDeleting}
                                className={`rounded border px-3 py-2 text-xs font-semibold uppercase ${
                                    selectedNoticeIds.length && !bulkDeleting
                                        ? "border-red-200 bg-white text-red-700"
                                        : "pointer-events-none border-gray-200 bg-gray-100 text-gray-400"
                                }`}
                            >
                                <i className="fas fa-trash pr-1"></i>
                                {bulkDeleting ? t("Deleting...") : t("Delete Selected (:count)", { count: selectedNoticeIds.length })}
                            </button>
                            <span className="text-xs text-gray-500">
                                {t("Maximum 50 notices at once.")}
                            </span>
                        </div>
                    )}

                    <div className="space-y-3">
                        {noticeItems.length === 0 && (
                            <div className="rounded border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">{t("No notice found.")}</div>
                        )}

                        {noticeItems.map((notice) => {
                            const cardClassName = `relative block w-full rounded-md border border-gray-200 bg-white p-4 text-left shadow-sm transition hover:border-orange-200 hover:bg-orange-50/30 ${
                                notice.link_url ? "cursor-pointer" : "cursor-default"
                            } ${!notice.is_read ? "border-l-4 border-l-orange-500" : ""}`;
                            const cardContent = (
                                <>
                                    {canManage && (
                                        <label className="mb-3 inline-flex items-center gap-2 text-xs font-semibold uppercase text-gray-700">
                                            <input
                                                type="checkbox"
                                                checked={selectedNoticeIds.includes(notice.id)}
                                                onChange={() => toggleNoticeSelection(notice.id)}
                                            />
                                            Select
                                        </label>
                                    )}

                                    <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                        <div>
                                            <h2 className="text-lg font-semibold text-gray-900">
                                                {notice.title}
                                            </h2>
                                            <div className="mt-1 text-xs text-gray-500">{t("Published:")} {notice.published_at_formatted} {t("| Expires:")} {notice.expires_at_formatted}
                                            </div>
                                        </div>

                                        {canManage && (
                                            <div className="flex gap-2">
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

                                    <p className="mt-3 whitespace-pre-wrap text-sm leading-6 text-gray-700">
                                        {notice.body}
                                    </p>

                                    {canManage && (
                                        <div className="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                            <span className={`rounded px-2 py-1 ${notice.is_active ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"}`}>
                                                {notice.is_active ? "Active" : "Inactive"}
                                            </span>
                                            <span className="rounded bg-gray-100 px-2 py-1 text-gray-700">{t("To:")}{notice.target_roles.length ? notice.target_roles.join(", ") : "All"}
                                            </span>
                                            <span className="text-gray-500">{t("By")}{notice.creator_name}{t("on")}{notice.created_at_formatted}
                                            </span>
                                        </div>
                                    )}
                                </>
                            );

                            if (!canManage && notice.link_url) {
                                return (
                                    <Link
                                        key={notice.id}
                                        href={notice.link_url}
                                        onClick={() => markNoticeRead(notice)}
                                        className={cardClassName}
                                    >
                                        {cardContent}
                                    </Link>
                                );
                            }

                            return (
                                <article key={notice.id} className={cardClassName}>
                                    {cardContent}
                                </article>
                            );
                        })}
                    </div>

                    {noticeMeta.has_more && (
                        <div className="mt-4 flex justify-center">
                            <PrimaryButton
                                type="button"
                                onClick={loadMoreNotices}
                                disabled={loadingMore}
                            >
                                {loadingMore ? t("Loading...") : t("Load More")}
                            </PrimaryButton>
                        </div>
                    )}
                </Section>
            </Container>
        </>
    );

    if (!canManage && noticeRole === "user") {
        return <UserDash>{content}</UserDash>;
    }

    return <AppLayout title={t("Notice")}>{content}</AppLayout>;
}
