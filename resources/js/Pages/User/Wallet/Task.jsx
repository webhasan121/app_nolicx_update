import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import UserDash from "../../../components/user/dash/UserDash";
import useTranslation from "../../../hooks/useTranslation";
import { formatCurrency } from "../../../utils/formatAmount";

export default function Task() {
    const { t } = useTranslation();
    const { tasks = [], pagination = {}, filters = {}, printUrl } = usePage().props;
    const [search, setSearch] = useState(filters.find ?? "");

    useEffect(() => {
        setSearch(filters.find ?? "");
    }, [filters.find]);

    const updateFilters = (updates = {}) => {
        router.get(
            route("user.wallet.tasks"),
            {
                find: filters.find ?? "",
                ...updates,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ["tasks", "pagination", "filters", "printUrl"],
            }
        );
    };

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            updateFilters({ find: trimmedSearch, page: undefined });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        updateFilters({
            find: nextUrl.searchParams.get("find") ?? search.trim(),
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pageLinks = useMemo(() => pagination?.links ?? [], [pagination?.links]);
    const pager = useMemo(() => ({
        prev: pageLinks[0] ?? null,
        next: pageLinks[pageLinks.length - 1] ?? null,
        pages: pageLinks.slice(1, -1),
    }), [pageLinks]);

    const resultSummary =
        pagination?.total > 0
            ? `${t("Showing")} ${pagination?.from ?? 0}-${pagination?.to ?? 0} ${t("of")} ${pagination?.total ?? 0} ${t("tasks")}`
            : t("No tasks found");

    const printCurrent = () => {
        if (printUrl) {
            window.open(printUrl, "_blank");
        }
    };

    const rowNumber = (index) => (pagination?.from ?? 1) + index;

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={t("Your Tasks")}
                        content={t("Your task and earnings")}
                    />

                    <SectionInner>
                        <div className="flex flex-wrap items-center justify-end gap-2 mb-2">
                            <TextInput
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                onKeyDown={(e) => {
                                    if (e.key !== "Enter") {
                                        return;
                                    }

                                    e.preventDefault();
                                    updateFilters({ find: search.trim(), page: undefined });
                                }}
                                className="py-1"
                                placeholder={t("Search tasks...")}
                            />
                            <PrimaryButton type="button" onClick={printCurrent}>
                                <i className="fas fa-print"></i>
                            </PrimaryButton>
                        </div>

                        <Table data={tasks}>
                            <thead>
                                <tr>
                                    <th>{t("#")}</th>
                                    <th>{t("Date")}</th>
                                    <th>{t("Earning")}</th>
                                    <th>{t("Time")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {tasks.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{rowNumber(index)}</td>
                                        <td>{item.date}</td>
                                        <td>{formatCurrency(item.earning)}</td>
                                        <td>{item.time}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>

                        {pager.pages.length ? (
                            <div className="w-full pt-4">
                                <div className="flex items-center justify-between w-full gap-3">
                                    <div className="text-sm text-slate-700">
                                        {resultSummary}
                                    </div>
                                    <div className="flex items-center md:justify-end">
                                        <div className="overflow-hidden bg-white border shadow-sm rounded-xl border-slate-200">
                                            <button
                                                type="button"
                                                disabled={!pager.prev?.url}
                                                className="px-4 py-2 text-sm transition border-r border-slate-200 text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pager.prev?.url)}
                                            >
                                                {t("Previous")}
                                            </button>
                                            {pager.pages.map((link, index) => (
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
                                                disabled={!pager.next?.url}
                                                className="px-4 py-2 text-sm transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pager.next?.url)}
                                            >
                                                {t("Next")}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : null}
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
