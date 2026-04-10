import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import NavLink from "../../../../../components/NavLink";
import NavLinkBtn from "../../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../../components/PrimaryButton";
import TextInput from "../../../../../components/TextInput";
import Container from "../../../../../components/dashboard/Container";
import Section from "../../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import Table from "../../../../../components/dashboard/table/Table";

export default function Index({ pages = {}, filters = {}, printUrl }) {
    const rows = pages.data ?? [];
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
                route("system.pages.index"),
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
        if (!window.confirm("Are you sure you want to delete this page?")) {
            return;
        }

        router.delete(route("system.pages.destroy", { id }));
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        router.get(
            route("system.pages.index"),
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
        const links = pages?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [pages?.links]);

    const resultSummary =
        pages?.total > 0
            ? `Showing ${pages?.from ?? 0}-${pages?.to ?? 0} of ${pages?.total ?? 0} pages`
            : "No pages found";

    return (
        <AppLayout title="Page Setup">
            <Head title="Page Setup" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div>
                                    Page Setup
                                </div>

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
                                                route("system.pages.index"),
                                                { find: search.trim() },
                                                {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                    replace: true,
                                                }
                                            );
                                        }}
                                        className="py-1"
                                        placeholder="Search pages..."
                                    />
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    <NavLinkBtn href={route("system.pages.create")}>
                                        <i className="pr-2 fas fa-plus"></i> Page
                                    </NavLinkBtn>
                                </div>
                            </div>
                        }
                        content="Setup your necessary pages from here. add, edit and delete."
                    />

                    <SectionInner>
                        <div>
                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Name </th>
                                        <th>Content</th>
                                        <th> Status </th>
                                        <th> A/C </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {rows.map((item) => (
                                        <tr key={item.id} className="border-b hvoer:bg-gray-50">
                                            <td>{item.id}</td>
                                            <td>
                                                <NavLink href={route("system.pages.create", { page: item.slug })}>
                                                    {item.name}
                                                </NavLink>
                                                <br />
                                                <p className="text-xs">
                                                    {item.title}
                                                </p>
                                            </td>
                                            <td dangerouslySetInnerHTML={{ __html: item.content }} />
                                            <td>{item.status}</td>
                                            <td>
                                                <div className="flex">
                                                    <DangerButton type="button" onClick={() => destroy(item.id)}>
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
