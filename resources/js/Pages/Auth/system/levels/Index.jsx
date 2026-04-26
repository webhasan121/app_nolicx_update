import { router, useForm } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../components/PrimaryButton";
import InputLabel from "../../../../components/InputLabel";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";

const emptyForm = {
    name: "",
    req_users: "",
    vip_users: "",
    bonus: "",
    rewards: "",
};

export default function Index({ columns = [], levels = {}, filters = {}, printUrl }) {
    const levelRows = levels?.data ?? [];
    const [search, setSearch] = useState(filters.search ?? "");
    const [showModal, setShowModal] = useState(false);
    const [editingLevelId, setEditingLevelId] = useState(null);
    const form = useForm(emptyForm);

    const requestLevels = ({ nextSearch = search, page = undefined } = {}) => {
        router.get(
            route("system.levels.index"),
            {
                search: nextSearch.trim(),
                page,
            },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    };

    useEffect(() => {
        setSearch(filters.search ?? "");
    }, [filters.search]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.search ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timer = window.setTimeout(() => {
            requestLevels({ nextSearch: trimmedSearch });
        }, 400);

        return () => window.clearTimeout(timer);
    }, [search]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        requestLevels({
            nextSearch: nextUrl.searchParams.get("search") ?? search,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = levels?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [levels?.links]);

    const resultSummary =
        levels?.total > 0
            ? `Showing ${levels?.from ?? 0}-${levels?.to ?? 0} of ${levels?.total ?? 0} levels`
            : "No levels found";

    const openCreateModal = () => {
        setEditingLevelId(null);
        form.clearErrors();
        form.setData(emptyForm);
        setShowModal(true);
    };

    const openEditModal = (level) => {
        setEditingLevelId(level.id);
        form.clearErrors();
        form.setData({
            name: level.name ?? "",
            req_users: level.req_users ?? "",
            vip_users: level.vip_users ?? "",
            bonus: level.bonus ?? "",
            rewards: level.rewards ?? "",
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingLevelId(null);
        form.clearErrors();
        form.setData(emptyForm);
    };

    const submit = (e) => {
        e.preventDefault();

        const url = editingLevelId
            ? route("system.levels.update", { level: editingLevelId })
            : route("system.levels.store");

        form.post(url, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    };

    const destroy = (levelId) => {
        if (!window.confirm("Delete this level?")) {
            return;
        }

        router.delete(route("system.levels.destroy", { level: levelId }), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            title="Star System - Levels"
            header={<PageHeader>Star System - Levels</PageHeader>}
        >
            <Container>
                <div className="flex items-center gap-2">
                    <NavLinkBtn href={route("system.levels.index")}>Levels</NavLinkBtn>
                    <NavLinkBtn href={route("system.levels.history")}>History</NavLinkBtn>
                </div>
            </Container>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <h2>Levels</h2>
                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <form
                                        onSubmit={(e) => {
                                            e.preventDefault();
                                            requestLevels();
                                        }}
                                    >
                                        <TextInput
                                            type="search"
                                            placeholder="Search levels..."
                                            className="py-1"
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                        />
                                    </form>
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    <PrimaryButton type="button" onClick={openCreateModal}>
                                        <i className="mr-2 fas fa-plus"></i>
                                        <span>Add New</span>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <div className="overflow-x-auto border border-gray-200 shadow-sm rounded-xl">
                            <table className="min-w-full text-sm divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {columns.map((column, index) => (
                                            <th
                                                key={`${column}-${index}`}
                                                className="px-4 py-3 font-medium text-left text-gray-600"
                                                width={index === columns.length - 1 ? "100" : undefined}
                                            >
                                                <strong>{column}</strong>
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-100">
                                    {levelRows.length ? (
                                        levelRows.map((level) => (
                                            <tr key={level.id} className="transition hover:bg-gray-50">
                                                <td className="px-4 py-3 font-medium text-gray-700">{level.sl}.</td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <strong className="px-3 py-1 text-white bg-blue-500 rounded-full hover:bg-blue-600">
                                                        {level.name}
                                                    </strong>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <p className="flex items-center gap-2">
                                                        <strong>N. Users :</strong>
                                                        <span>{Number(level.req_users ?? 0).toLocaleString()}</span>
                                                    </p>
                                                    <p className="flex items-center gap-2">
                                                        <strong>VIP Users :</strong>
                                                        <span>{Number(level.vip_users ?? 0).toLocaleString()}</span>
                                                    </p>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    {Number(level.bonus ?? 0).toFixed(2)}
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    {level.rewards || "Not Available"}
                                                </td>
                                                <td className="px-4 py-3 space-x-2 text-center">
                                                    <button
                                                        type="button"
                                                        onClick={() => openEditModal(level)}
                                                        className="inline-flex items-center justify-center p-2 text-xs font-medium text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700 w-7 h-7"
                                                    >
                                                        <i className="fas fa-edit"></i>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        onClick={() => destroy(level.id)}
                                                        className="inline-flex items-center justify-center p-2 text-xs font-medium text-white transition bg-red-600 rounded-lg hover:bg-red-700 w-7 h-7"
                                                    >
                                                        <i className="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan={columns.length} className="px-4 py-6 text-center text-gray-500">
                                                <span>No levels found.</span>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {pagination.pages.length ? (
                            <div className="w-full pt-4">
                                <div className="flex w-full items-center justify-between gap-3">
                                    <div className="text-sm text-slate-700">{resultSummary}</div>
                                    <div className="flex items-center md:justify-end">
                                        <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="border-r border-slate-200 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.prev?.url)}
                                            >
                                                Previous
                                            </button>
                                            {pagination.pages.map((link, index) => (
                                                <button
                                                    key={`${link.label}-${index}`}
                                                    type="button"
                                                    disabled={!link.url}
                                                    className={`min-w-10 border-r border-slate-200 px-4 py-2 text-sm font-semibold transition ${
                                                        link.active
                                                            ? "bg-slate-100 text-blue-600"
                                                            : "bg-white text-slate-700 hover:bg-slate-50"
                                                    } disabled:cursor-not-allowed disabled:opacity-50`}
                                                    onClick={() => goToPage(link.url)}
                                                >
                                                    {link.label}
                                                </button>
                                            ))}
                                            <button
                                                type="button"
                                                disabled={!pagination.next?.url}
                                                className="px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.next?.url)}
                                            >
                                                Next
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : null}
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={showModal} onClose={closeModal} maxWidth="md">
                <div className="p-3">{`${editingLevelId ? "Update" : "Add New"} Level`}</div>
                <hr className="my-2" />
                <div className="p-4">
                    <form onSubmit={submit} className="space-y-6">
                        <div className="relative">
                            <InputLabel>Name of Level</InputLabel>
                            <TextInput
                                type="text"
                                className="w-full"
                                value={form.data.name}
                                onChange={(e) => form.setData("name", e.target.value)}
                                placeholder="Enter Level Name"
                            />
                            {form.errors.name ? (
                                <div className="mt-1 text-sm text-red-500">{form.errors.name}</div>
                            ) : null}
                        </div>

                        <div className="grid grid-cols-3 gap-6">
                            <div className="relative">
                                <InputLabel>Normal Users</InputLabel>
                                <TextInput
                                    type="number"
                                    className="w-full"
                                    value={form.data.req_users}
                                    onChange={(e) => form.setData("req_users", e.target.value)}
                                    placeholder="Required normal users"
                                />
                                {form.errors.req_users ? (
                                    <div className="mt-1 text-sm text-red-500">{form.errors.req_users}</div>
                                ) : null}
                            </div>

                            <div className="relative">
                                <InputLabel>VIP Users</InputLabel>
                                <TextInput
                                    type="number"
                                    className="w-full"
                                    value={form.data.vip_users}
                                    onChange={(e) => form.setData("vip_users", e.target.value)}
                                    placeholder="Required vip users"
                                />
                                {form.errors.vip_users ? (
                                    <div className="mt-1 text-sm text-red-500">{form.errors.vip_users}</div>
                                ) : null}
                            </div>

                            <div className="relative">
                                <InputLabel>Commission</InputLabel>
                                <TextInput
                                    type="number"
                                    step="0.01"
                                    className="w-full"
                                    value={form.data.bonus}
                                    onChange={(e) => form.setData("bonus", e.target.value)}
                                    placeholder="Enter commission"
                                />
                                {form.errors.bonus ? (
                                    <div className="mt-1 text-sm text-red-500">{form.errors.bonus}</div>
                                ) : null}
                            </div>
                        </div>

                        <div className="relative">
                            <InputLabel>Reward of Level</InputLabel>
                            <TextInput
                                type="text"
                                className="w-full"
                                value={form.data.rewards}
                                onChange={(e) => form.setData("rewards", e.target.value)}
                                placeholder="Enter level rewards"
                            />
                            {form.errors.rewards ? (
                                <div className="mt-1 text-sm text-red-500">{form.errors.rewards}</div>
                            ) : null}
                        </div>

                        <div className="flex justify-end">
                            <PrimaryButton type="submit" disabled={form.processing}>
                                Save Level
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AppLayout>
    );
}
