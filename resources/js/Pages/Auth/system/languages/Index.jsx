import { useEffect, useState } from "react";
import { Link, router, usePage } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";

export default function Index() {
    const { pageTitle = "Translations", languages = [], summary = {}, filters = {} } = usePage().props;
    const [search, setSearch] = useState(filters.search ?? "");

    useEffect(() => {
        setSearch(filters.search ?? "");
    }, [filters.search]);

    useEffect(() => {
        const timeout = window.setTimeout(() => {
            router.get(
                route("system.languages.index"),
                { search: search.trim() },
                { preserveScroll: true, preserveState: true, replace: true }
            );
        }, 350);

        return () => window.clearTimeout(timeout);
    }, [search]);

    const setDefault = (language) => {
        router.post(route("system.languages.default", language.id), {}, { preserveScroll: true });
    };

    return (
        <AppLayout title={pageTitle} header={<PageHeader>{pageTitle}</PageHeader>}>
            <Container>
                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="flex flex-col gap-4 px-6 py-6 md:flex-row md:items-center md:justify-between">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <i className="fas fa-language text-xl"></i>
                            </div>
                            <div>
                                <h1 className="text-lg font-bold text-slate-950">Translations</h1>
                                <p className="text-sm text-slate-600">
                                    {summary.count ?? languages.length} / {summary.count ?? languages.length} Languages
                                </p>
                            </div>
                        </div>
                        <div className="relative w-full md:w-80">
                            <i className="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-indigo-500"></i>
                            <input
                                type="search"
                                value={search}
                                onChange={(event) => setSearch(event.target.value)}
                                placeholder="Search modules..."
                                className="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-4 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100"
                            />
                        </div>
                    </div>

                    <div className="overflow-x-auto px-6 pb-6">
                        <table className="min-w-full text-left text-sm">
                            <thead>
                                <tr className="border-y border-slate-100 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <th className="px-4 py-4">Icon</th>
                                    <th className="px-4 py-4">Language</th>
                                    <th className="px-4 py-4">Progress</th>
                                    <th className="px-4 py-4">Done</th>
                                    <th className="px-4 py-4">Total</th>
                                    <th className="px-4 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {languages.map((language) => (
                                    <tr key={language.id} className="text-slate-800">
                                        <td className="px-4 py-5">
                                            <span className="inline-flex h-10 min-w-10 items-center justify-center rounded-md border border-slate-100 bg-white px-2 font-semibold text-slate-900 shadow-sm">
                                                {language.icon}
                                            </span>
                                        </td>
                                        <td className="px-4 py-5">
                                            <Link
                                                href={language.edit_url}
                                                className="font-semibold text-blue-600 hover:text-blue-700"
                                            >
                                                {language.name}
                                            </Link>
                                            <div className="mt-1 text-xs font-semibold uppercase text-slate-400">
                                                {language.code}
                                            </div>
                                        </td>
                                        <td className="min-w-80 px-4 py-5">
                                            <div className="h-7 overflow-hidden rounded-full bg-slate-100">
                                                <div
                                                    className="flex h-full items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white"
                                                    style={{ width: `${language.progress}%` }}
                                                >
                                                    {language.progress}%
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-4 py-5 font-medium">{language.done}</td>
                                        <td className="px-4 py-5 font-medium">{language.total}</td>
                                        <td className="px-4 py-5">
                                            <div className="flex items-center justify-end gap-2">
                                                <button
                                                    type="button"
                                                    onClick={() => setDefault(language)}
                                                    className={`flex h-9 w-9 items-center justify-center rounded-lg border transition ${
                                                        language.is_default
                                                            ? "border-indigo-500 bg-indigo-600 text-white"
                                                            : "border-slate-200 bg-white text-slate-400 hover:text-indigo-600"
                                                    }`}
                                                    title="Set default"
                                                >
                                                    <i className="fas fa-check text-xs"></i>
                                                </button>
                                                <Link
                                                    href={language.edit_url}
                                                    className="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-white transition hover:bg-slate-700"
                                                    title="Edit translations"
                                                >
                                                    <i className="fas fa-pen"></i>
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </Container>
        </AppLayout>
    );
}
