import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import { formatAmount } from "../../../../utils/formatAmount";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import Table from "../../../../components/dashboard/table/Table";
import AppLayout from "../../../../Layouts/App";
import useTranslation from "../../../../hooks/useTranslation";
import { todayInputDate } from "../../../../utils/dateInput";
import { ActionIconLink } from "../../../../components/ActionIcon";

export default function Index({ filters, stats, withdraw }) {
    const { t } = useTranslation();
    const controlClass =
        "h-9 rounded-md border border-gray-300 bg-white px-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500";
    const [queryValue, setQueryValue] = useState(filters?.q ?? "");
    const [inlineSdate, setInlineSdate] = useState(filters?.sdate ?? "");
    const [inlineEdate, setInlineEdate] = useState(filters?.edate ?? "");
    const today = todayInputDate();

    const apply = (next = {}) => {
        router.get(
            route("system.withdraw.index"),
            {
                fst: next.fst ?? filters?.fst ?? "All",
                where: next.where ?? filters?.where ?? "",
                q: next.q ?? filters?.q ?? "",
                sdate: next.sdate ?? filters?.sdate ?? "",
                edate: next.edate ?? filters?.edate ?? "",
                page: next.page ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ["filters", "stats", "withdraw"],
            }
        );
    };

    useEffect(() => {
        setQueryValue(filters?.q ?? "");
        setInlineSdate(filters?.sdate ?? "");
        setInlineEdate(filters?.edate ?? "");
    }, [filters?.q, filters?.sdate, filters?.edate]);

    useEffect(() => {
        const timer = window.setTimeout(() => {
            if (queryValue !== (filters?.q ?? "")) {
                apply({
                    where: queryValue ? "query" : "",
                    q: queryValue,
                    page: undefined,
                });
            }
        }, 400);

        return () => window.clearTimeout(timer);
    }, [queryValue]);

    const print = () => {
        window.open(
            route("system.withdraw.print", {
                fst: filters?.fst ?? "All",
                where: queryValue ? "query" : (filters?.where ?? ""),
                q: queryValue ?? "",
                sdate: filters?.sdate ?? "",
                edate: filters?.edate ?? "",
            }),
            "_blank"
        );
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        apply({
            fst: nextUrl.searchParams.get("fst") ?? filters?.fst,
            where: nextUrl.searchParams.get("where") ?? filters?.where,
            q: nextUrl.searchParams.get("q") ?? filters?.q,
            sdate: nextUrl.searchParams.get("sdate") ?? filters?.sdate,
            edate: nextUrl.searchParams.get("edate") ?? filters?.edate,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = withdraw?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [withdraw?.links]);

    const resultSummary =
        withdraw?.total > 0
            ? `Showing ${withdraw?.from ?? 0}-${withdraw?.to ?? 0} of ${withdraw?.total ?? 0} withdraws`
            : "No withdraws found";
    const hasActiveFilters = Boolean(
        queryValue.trim() ||
            inlineSdate ||
            inlineEdate ||
            (filters?.fst ?? "All") !== "All" ||
            (filters?.where ?? "") !== ""
    );

    return (
        <AppLayout title={t("Withdraws")} header={<PageHeader>{t("Withdraws")}</PageHeader>}>
            <Head title={t("Withdraws")} />

            <Container>
                <OverviewSection>
                    <OverviewDiv title={t("Amount")} content={formatAmount(stats?.amount)} />
                    <OverviewDiv title={t("Payable")} content={formatAmount(stats?.payable)} />
                    <OverviewDiv title={t("Comission")} content={`${formatAmount(stats?.server_fee)} | ${formatAmount(stats?.maintenance_fee)}`} />
                    <OverviewDiv title={t("Paid")} content={formatAmount(stats?.paid)} />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title=""
                        content={
                            <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div className="flex flex-1 flex-col gap-3 md:flex-row md:items-center">
                                    <select
                                        value={filters?.fst ?? "All"}
                                        onChange={(e) => apply({ fst: e.target.value, page: undefined })}
                                        className={`${controlClass} w-full md:w-36`}
                                        id="filter_status"
                                    >
                                        <option value="All">{t("All")}{stats?.total ?? 0}</option>
                                        <option value="Pending">{t("Pending")}{stats?.pending ?? 0}</option>
                                        <option value="Accept">{t("Accepted")}{stats?.paid ?? 0}</option>
                                        <option value="Reject">{t("Rejected")}{stats?.reject ?? 0}</option>
                                    </select>
                                    <TextInput
                                        type="text"
                                        value={queryValue}
                                        onChange={(e) => setQueryValue(e.target.value)}
                                        placeholder={t("Search user...")}
                                        className={`${controlClass} w-full md:w-56`}
                                    />
                                </div>

                                <div className="flex flex-col gap-3 md:flex-row md:items-center">
                                    <TextInput
                                        type="date"
                                        value={inlineSdate || today}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setInlineSdate(value);
                                            apply({ sdate: value, edate: inlineEdate, page: undefined });
                                        }}
                                        className={`${controlClass} w-full md:w-44`}
                                        title={t("Start Date")}
                                    />
                                    <TextInput
                                        type="date"
                                        value={inlineEdate}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setInlineEdate(value);
                                            apply({ sdate: inlineSdate, edate: value, page: undefined });
                                        }}
                                        className={`${controlClass} w-full md:w-44`}
                                        title={t("End Date")}
                                    />
                                    <PrimaryButton type="button" onClick={print} className="h-9 min-w-10 justify-center px-3">
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    {hasActiveFilters ? (
                                        <button
                                            type="button"
                                            className="h-9 rounded-md border border-gray-300 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50"
                                            onClick={() => {
                                                setQueryValue("");
                                                setInlineSdate("");
                                                setInlineEdate("");
                                                apply({
                                                    fst: "All",
                                                    where: "",
                                                    q: "",
                                                    sdate: "",
                                                    edate: "",
                                                    page: undefined,
                                                });
                                            }}
                                        >
                                            {t("Reset")}
                                        </button>
                                    ) : null}
                                </div>
                            </div>
                        }
                    />
                    <br />


                    <Table data={withdraw?.data ?? []}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{t("ID")}</th>
                                <th>{t("User")}</th>
                                <th>{t("Amount")}</th>
                                <th>{t("Status")}</th>
                                <th>{t("Date")}</th>
                                <th>{t("A/C")}</th>
                            </tr>
                        </thead>

                        <tbody>
                            {(withdraw?.data ?? []).map((item, index) => (
                                <tr key={item.id} className={!item.seen_by_admin ? "bg-gray-200 font-bold" : ""}>
                                    <td>{(withdraw?.from ?? 1) + index}</td>
                                    <td>{item.id}</td>
                                    <td>
                                        <div>
                                            <div className="flex">
                                                {item.user?.name}
                                                {item.user?.subscription ? (
                                                    <span className="px-1 text-white bg-indigo-900 rounded ms-1">{t("vip")}</span>
                                                ) : null}
                                                <span className="px-1 text-white bg-gray-900 rounded-full ms-1">
                                                    U
                                                </span>
                                            </div>

                                            {item.user?.email}
                                        </div>
                                    </td>
                                    <td>{item.amount ?? "0"}{t("TK")}</td>
                                    <td>
                                        {!item.is_rejected ? (
                                            item.status ? "Accept" : "Pending"
                                        ) : (
                                            <div className="p-1">{t("Reject")}</div>
                                        )}
                                    </td>
                                    <td>{item.created_at_formatted}</td>
                                    <td>
                                        <div className="flex">
                                            <ActionIconLink href={route("system.withdraw.view", { id: item.id })} action="details" title={t("Details")} />
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr className="font-bold">
                                <td colSpan="3" className="font-bold text-right">{t("Total")}</td>
                                <td className="font-bold">{withdraw?.sum_amount ?? 0}</td>
                                <td colSpan="3"></td>
                            </tr>
                        </tfoot>
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
                                        >{t("Previous")}</button>
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
                                        >{t("Next")}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ) : null}

                </Section>
            </Container>
        </AppLayout>
    );
}
