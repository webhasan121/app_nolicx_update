import { router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";

export default function History({ columns = [], histories = {}, filters = {}, printUrl }) {
    const historyRows = histories?.data ?? [];
    const [search, setSearch] = useState(filters.search ?? "");

    const requestHistories = ({ nextSearch = search, page = undefined } = {}) => {
        router.get(
            route("system.levels.history"),
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
            requestHistories({ nextSearch: trimmedSearch });
        }, 400);

        return () => window.clearTimeout(timer);
    }, [search]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        requestHistories({
            nextSearch: nextUrl.searchParams.get("search") ?? search,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = histories?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [histories?.links]);

    const resultSummary =
        histories?.total > 0
            ? `Showing ${histories?.from ?? 0}-${histories?.to ?? 0} of ${histories?.total ?? 0} histories`
            : "No histories found";

    return (
        <AppLayout
            title="Star System - History"
            header={<PageHeader>Star System - History</PageHeader>}
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
                                <h2>Level-Up History</h2>
                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <form
                                        onSubmit={(e) => {
                                            e.preventDefault();
                                            requestHistories();
                                        }}
                                    >
                                        <TextInput
                                            type="search"
                                            placeholder="Search histories..."
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
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <div className="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                            <table className="min-w-full divide-y divide-gray-200 text-sm">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {columns.map((column, index) => (
                                            <th
                                                key={`${column}-${index}`}
                                                className="px-4 py-3 text-left font-semibold text-gray-600"
                                            >
                                                <strong>{column}</strong>
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 bg-white">
                                    {historyRows.length ? (
                                        historyRows.map((history) => (
                                            <tr key={history.id} className="transition hover:bg-gray-50">
                                                <td className="px-4 py-3 font-medium text-gray-700">{history.sl}.</td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <strong>{history.user_name || "N/A"}</strong>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <span className="px-3 py-1 text-white bg-blue-500 hover:bg-blue-600 rounded-full">
                                                        {history.from_level_name || "N/A"}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <span className="px-3 py-1 text-white bg-purple-500 hover:bg-purple-600 rounded-full">
                                                        {history.to_level_name || "N/A"}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <span>{history.created_at_formatted || "N/A"}</span>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan={columns.length} className="px-4 py-6 text-center text-gray-500">
                                                <span>No histories found.</span>
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
        </AppLayout>
    );
}
