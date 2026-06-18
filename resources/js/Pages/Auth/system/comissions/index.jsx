import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import useTranslation from "../../../../hooks/useTranslation";
import { todayInputDate } from "../../../../utils/dateInput";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import { formatCurrency } from "../../../../utils/formatAmount";
import OverviewSection from "../../../../components/dashboard/overview/Section";

function SummaryBadge({ value, className = "" }) {
    return (
        <span
            className={`inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ${className}`}
        >
            {formatCurrency(value)}
        </span>
    );
}

export default function Index({ filters, comissions }) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters?.wid ?? "");
    const today = todayInputDate();

    const apply = (next = {}) => {
        router.get(
            route("system.comissions.index"),
            {
                confirm: next.confirm ?? filters?.confirm ?? "",
                where: next.where ?? filters?.where ?? "",
                from: next.from ?? filters?.from ?? "",
                to: next.to ?? filters?.to ?? "",
                wid: next.wid ?? filters?.wid ?? "",
                page: next.page ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ["filters", "comissions"],
            }
        );
    };

    useEffect(() => {
        setSearch(filters?.wid ?? "");
    }, [filters?.wid]);

    useEffect(() => {
        if ((filters?.where ?? "") !== "") {
            return;
        }

        const trimmedSearch = search.trim();
        const currentSearch = (filters?.wid ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            apply({ wid: trimmedSearch, where: "", page: undefined });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search, filters?.where]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        apply({
            confirm: nextUrl.searchParams.get("confirm") ?? filters?.confirm ?? "All",
            where: nextUrl.searchParams.get("where") ?? filters?.where ?? "",
            from: nextUrl.searchParams.get("from") ?? filters?.from ?? "",
            to: nextUrl.searchParams.get("to") ?? filters?.to ?? "",
            wid: nextUrl.searchParams.get("wid") ?? filters?.wid ?? "",
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = comissions?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [comissions?.links]);

    const resultSummary =
        comissions?.total > 0
            ? `Showing ${comissions?.from ?? 0}-${comissions?.to ?? 0} of ${comissions?.total ?? 0} comissions`
            : "No comissions found";
    const urlParams = new URLSearchParams(typeof window !== "undefined" ? window.location.search : "");
    const hasDateFilters = Boolean(urlParams.get("from") || urlParams.get("to"));

    const openPrintable = () => {
        window.open(
            route("system.comissions.takes", {
                confirm: filters?.confirm ?? "",
                where: filters?.where ?? "",
                from: filters?.from ?? "",
                to: filters?.to ?? "",
                wid: filters?.wid ?? "",
            }),
            "_blank"
        );
    };

    const confirmTakeComission = (id) => {
        router.post(route("system.comissions.take.confirm", { id }), {}, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <AppLayout
            title={t("Comissions")}
            header={
                <PageHeader>
                    <div className="flex justify-between">
                        <div>{t("Comissions")}</div>
                    </div>
                </PageHeader>
            }
        >
            <Head title={t("Comissions")} />

            <Container>
                <div className="mb-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <OverviewSection>
                    <OverviewDiv
                        title={t("Seller Total Profit")}
                        content={formatCurrency(comissions?.summary?.profit)}
                    />
                    <OverviewDiv
                        title={t("Cut comission")}
                        content={formatCurrency(comissions?.summary?.take_comission)}
                    />
                    <OverviewDiv
                        title={t("Distribute")}
                        content={formatCurrency(comissions?.summary?.distribute_comission)}
                    />
                    <OverviewDiv
                        title={t("Store")}
                        content={formatCurrency(comissions?.summary?.store)}
                    />
                    <OverviewDiv
                        title={t("Return")}
                        content={formatCurrency(comissions?.summary?.return)}
                    />
                    </OverviewSection>
                </div>

                <div className="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div className="flex flex-wrap items-end justify-end gap-2">
                        <TextInput
                            className="w-full py-1 sm:w-40"
                            type="date"
                            value={filters?.from || today}
                            onChange={(e) => apply({ from: e.target.value })}
                        />

                        <TextInput
                            className="w-full py-1 sm:w-40"
                            type="date"
                            value={filters?.to ?? ""}
                            onChange={(e) => apply({ to: e.target.value })}
                        />

                        <TextInput
                            className="w-full py-1 sm:w-56"
                            type="search"
                            placeholder={t("Search comissions...")}
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => {
                                if (e.key !== "Enter") {
                                    return;
                                }

                                e.preventDefault();
                                apply({ wid: search.trim(), where: "", page: undefined });
                            }}
                        />

                        <PrimaryButton type="button" onClick={openPrintable} className="btn">
                            <i className="fas fa-print"></i>
                        </PrimaryButton>

                        {hasDateFilters ? (
                            <button
                                type="button"
                                className="rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50"
                                onClick={() => {
                                    setSearch("");
                                    apply({
                                        confirm: "",
                                        where: "",
                                        from: "",
                                        to: "",
                                        wid: "",
                                        page: undefined,
                                    });
                                }}
                            >
                                {t("Reset")}
                            </button>
                        ) : null}
                    </div>
                </div>

                <Section id="pdf-content">
                    <Hr />
                    <Table data={comissions?.data ?? []}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{t("DT")}</th>
                                <th>{t("ID")}</th>
                                <th>{t("Order")}</th>
                                <th>{t("Product")}</th>
                                <th>{t("Buy")}</th>
                                <th>{t("Sell")}</th>
                                <th>{t("Profit")}</th>
                                <th>{t("Rate")}</th>
                                <th>{t("Take")}</th>
                                <th>{t("Give")}</th>
                                <th>{t("Store")}</th>
                                <th>{t("Return")}</th>
                                <th>{t("Confirmed")}</th>
                                <th>{t("A/C")}</th>
                            </tr>
                        </thead>

                        <tbody>
                            {(comissions?.data ?? []).map((item, index) => (
                                <tr key={item.id}>
                                    <td>{(comissions?.from ?? 1) + index}</td>
                                    <td>{item.created_at_formatted}</td>
                                    <td>{item.id ?? "N/A"}</td>
                                    <td>{item.order_id ?? 0}</td>
                                    <td>{item.product_id ?? 0}</td>
                                    <td>{formatCurrency(item.buying_price)}</td>
                                    <td>{formatCurrency(item.selling_price)}</td>
                                    <td>{formatCurrency(item.profit)}</td>
                                    <td>{item.comission_range ?? 0} %</td>
                                    <td>{formatCurrency(item.take_comission)}</td>
                                    <td>{formatCurrency(item.distribute_comission)}</td>
                                    <td>{formatCurrency(item.store)}</td>
                                    <td>{formatCurrency(item.return)}</td>
                                    <td>
                                        {item.confirmed ? (
                                            <>
                                                <span className="p-1 px-2 rounded-xl bg-green-900 text-white">{t("Confirmed")}</span>
                                                <NavLink href={route("system.comissions.take.refund", { id: item.id })}>
                                                    {" "}{t("Refund")}</NavLink>
                                            </>
                                        ) : (
                                            <>
                                                <span className="p-1 px-2 rounded-xl bg-gray-900 text-white">{t("Pending")}</span>
                                                <button
                                                    type="button"
                                                    onClick={() => confirmTakeComission(item.id)}
                                                >{t("Confirm")}</button>
                                            </>
                                        )}
                                    </td>
                                    <td>
                                        <div className="flex space-x-2">
                                            <NavLink href={route("system.comissions.distributes", { id: item.id })}>{t("Details")}</NavLink>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>
                                    <SummaryBadge
                                        value={comissions?.summary?.buying_price}
                                        className="bg-slate-200 text-slate-700"
                                    />
                                </th>
                                <th>
                                    <SummaryBadge
                                        value={comissions?.summary?.selling_price}
                                        className="bg-blue-100 text-blue-700"
                                    />
                                </th>
                                <th>
                                    <SummaryBadge
                                        value={comissions?.summary?.profit}
                                        className="bg-emerald-100 text-emerald-700"
                                    />
                                </th>
                                <td></td>
                                <th>
                                    <SummaryBadge
                                        value={comissions?.summary?.take_comission}
                                        className="bg-rose-100 text-rose-700"
                                    />
                                </th>
                                <th>
                                    <SummaryBadge
                                        value={comissions?.summary?.distribute_comission}
                                        className="bg-amber-100 text-amber-700"
                                    />
                                </th>
                                <th>
                                    <SummaryBadge
                                        value={comissions?.summary?.store}
                                        className="bg-violet-100 text-violet-700"
                                    />
                                </th>
                                <th>
                                    <SummaryBadge
                                        value={comissions?.summary?.return}
                                        className="bg-cyan-100 text-cyan-700"
                                    />
                                </th>
                                <th></th>
                                <th></th>
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
