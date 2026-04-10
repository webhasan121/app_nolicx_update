import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import PrimaryButton from "../../../../../components/PrimaryButton";
import TextInput from "../../../../../components/TextInput";
import NavLinkBtn from "../../../../../components/NavLinkBtn";
import Container from "../../../../../components/dashboard/Container";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import Section from "../../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import Table from "../../../../../components/dashboard/table/Table";

export default function Index({ branches = {}, filters = {}, printUrl }) {
    const rows = branches.data ?? [];
    const [search, setSearch] = useState(filters.find ?? "");

    useEffect(() => {
        setSearch(filters.find ?? "");
    }, [filters.find]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            router.get(
                route("system.branches.index"),
                { find: trimmedSearch },
                {
                    preserveScroll: true,
                    preserveState: true,
                    replace: true,
                }
            );
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const destroy = (id) => {
        if (!window.confirm("Are you sure you want to delete this branch?")) {
            return;
        }

        router.delete(route("system.branches.destroy", { id }));
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        router.get(
            route("system.branches.index"),
            {
                find: nextUrl.searchParams.get("find") ?? search,
                page: nextUrl.searchParams.get("page") ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
            }
        );
    };

    const pagination = useMemo(() => {
        const links = branches?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [branches?.links]);

    const resultSummary =
        branches?.total > 0
            ? `Showing ${branches?.from ?? 0}-${branches?.to ?? 0} of ${branches?.total ?? 0} branches`
            : "No branches found";

    return (
        <AppLayout
            title="Settings"
            header={<PageHeader>Settings</PageHeader>}
        >
            <Head title="Settings" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div>Branch Management</div>
                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <TextInput
                                        type="search"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            router.get(
                                                route("system.branches.index"),
                                                { find: search.trim() },
                                                {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                    replace: true,
                                                }
                                            );
                                        }}
                                        className="py-1"
                                        placeholder="Search branches..."
                                    />
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    <NavLinkBtn href={route("system.branches.create")}>
                                        <i className="fas fa-plus pr-2"></i>
                                        <span>Branch</span>
                                    </NavLinkBtn>
                                </div>
                            </div>
                        }
                        content="Setup your necessary branches from here. add, edit and delete."
                    />

                    <SectionInner>
                        <div>
                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Created</th>
                                        <th width="60">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((branch) => (
                                        <tr key={branch.id}>
                                            <td>{branch.sl}</td>
                                            <td>{branch.name}</td>
                                            <td>{branch.email}</td>
                                            <td>{branch.type}</td>
                                            <td>{branch.created_at}</td>
                                            <td>
                                                <div className="flex items-center gap-2">
                                                    <NavLinkBtn href={route("system.branches.modify", branch.id)}>
                                                        <i className="fas fa-edit"></i>
                                                    </NavLinkBtn>
                                                    <DangerButton type="button" onClick={() => destroy(branch.id)}>
                                                        <i className="fas fa-trash"></i>
                                                    </DangerButton>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>

                            {pagination.pages.length ? (
                                <div className="w-full pt-4">
                                    <div className="flex w-full items-center justify-between gap-3">
                                        <div className="text-sm text-slate-700">
                                            {resultSummary}
                                        </div>
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
                        </div>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
