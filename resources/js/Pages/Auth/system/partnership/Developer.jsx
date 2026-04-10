import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";

export default function Developer({ applications = { data: [] }, filters = {}, printUrl }) {
    const items = applications?.data ?? [];
    const [search, setSearch] = useState(filters.find ?? "");

    const visit = ({ nextSearch = search, page = undefined } = {}) => {
        router.get(
            route("system.partnership.developer"),
            {
                find: nextSearch.trim(),
                page,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    };

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
            visit({ nextSearch: trimmedSearch });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const accept = (id) => {
        router.post(route("system.partnership.developer.accept", { id }));
    };

    const reject = (id) => {
        router.post(route("system.partnership.developer.reject", { id }));
    };

    const destroy = (id) => {
        if (!window.confirm("Are you sure you want to delete this application?")) {
            return;
        }

        router.delete(route("system.partnership.developer.destroy", { id }));
    };

    const pagination = useMemo(() => {
        const links = applications?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [applications?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        visit({
            nextSearch: nextUrl.searchParams.get("find") ?? search,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultSummary =
        applications?.total > 0
            ? `Showing ${applications?.from ?? 0}-${applications?.to ?? 0} of ${applications?.total ?? 0} applications`
            : "No applications found";

    return (
        <AppLayout
            title="Partnership - Developer Access"
            header={<PageHeader>Partnership - Developer Access</PageHeader>}
        >
            <Head title="Partnership - Developer Access" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-start gap-4">
                                <div>Developer Access</div>
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
                                            visit();
                                        }}
                                        placeholder="Search applications..."
                                        className="my-1 py-1"
                                    />
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

                    <div className="py-3">
                        <div className="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                            <table className="min-w-full divide-y divide-gray-200 text-sm">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">SL No.</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">Name of User</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">User Email</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">Responded By</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600" width="100">A/C</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 bg-white">
                                    {items.map((app) => (
                                        <tr key={app.id} className="hover:bg-gray-50 transition">
                                            <td className="px-4 py-3 font-medium text-gray-700">{app.sl}</td>
                                            <td className="px-4 py-3 text-gray-700">{app.user_name}</td>
                                            <td className="px-4 py-3 text-gray-700">{app.user_email}</td>
                                            <td className="px-4 py-3 text-gray-700">{app.status_text}</td>
                                            <td className="px-4 py-3 text-gray-700">{app.responder_name}</td>
                                            <td className="px-4 py-3 text-center space-x-2">
                                                {app.status !== null ? (
                                                    <>
                                                        {app.status === 1 ? (
                                                            <button className="inline-flex justify-center items-center p-2 rounded-lg bg-green-500 text-white text-xs font-medium hover:bg-green-600 w-7 h-7 transition" disabled>
                                                                <i className="fas fa-check-circle"></i>
                                                            </button>
                                                        ) : (
                                                            <button className="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition" disabled>
                                                                <i className="fas fa-circle-xmark"></i>
                                                            </button>
                                                        )}
                                                        <button
                                                            type="button"
                                                            onClick={() => destroy(app.id)}
                                                            className="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition"
                                                        >
                                                            <i className="fas fa-trash-alt"></i>
                                                        </button>
                                                    </>
                                                ) : (
                                                    <>
                                                        <button
                                                            type="button"
                                                            onClick={() => accept(app.id)}
                                                            className="inline-flex justify-center items-center p-2 rounded-lg bg-green-500 text-white text-xs font-medium hover:bg-green-600 w-7 h-7 transition"
                                                        >
                                                            <i className="fas fa-check"></i>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() => reject(app.id)}
                                                            className="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition"
                                                        >
                                                            <i className="fas fa-times"></i>
                                                        </button>
                                                    </>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
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
                    </div>
                </Section>
            </Container>
        </AppLayout>
    );
}
