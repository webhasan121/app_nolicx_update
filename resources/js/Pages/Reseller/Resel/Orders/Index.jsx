import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Div from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import PageHeader from "../../../../components/dashboard/PageHeader";
import useTranslation from "../../../../hooks/useTranslation";
import { ActionIconLink } from "../../../../components/ActionIcon";
import { todayInputDate } from "../../../../utils/dateInput";
import { formatCurrency } from "../../../../utils/formatAmount";

function statusClass(status) {
    const classes = {
        Pending: "text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900",
        Accept: "text-xs p-1 border rounded-md bg-green-200 text-green-900",
        Picked: "text-xs p-1 border rounded-md bg-lime-200 text-lime-900",
        Delivery: "text-xs p-1 border rounded-md bg-sky-200 text-sky-900",
        Delivered: "text-xs p-1 border rounded-md bg-blue-200 text-blue-900",
        Confirm: "text-xs p-1 border rounded-md bg-indigo-200 text-indigo-900",
        Hold: "text-xs p-1 border rounded-md bg-gray-200 text-gray-900",
        Reject: "text-xs p-1 border rounded-md bg-red-200 text-red-900",
        Cancelled: "text-xs p-1 border rounded-md bg-red-200 text-red-900",
    };

    return classes[status] ?? "text-xs p-1 border rounded-md bg-gray-200 text-gray-900";
}

function buildQuery(filters, updates = {}) {
    return Object.fromEntries(
        Object.entries({ ...filters, ...updates }).filter(
            ([, value]) => value !== "" && value !== null && value !== undefined
        )
    );
}

export default function Index({ activeNav, filters = {}, summary = {}, list = {}, printUrl }) {
    const { t } = useTranslation();
    const [find, setFind] = useState(filters.find ?? "");
    const rows = list?.data ?? [];
    const today = todayInputDate();
    const primaryFilterValue =
        (filters.type ?? "All") !== "All"
            ? `type:${filters.type}`
            : `nav:${filters.nav ?? "Pending"}`;

    const updateFilters = (updates = {}) => {
        router.get(route("reseller.resel-order.index"), buildQuery(filters, updates), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["filters", "summary", "list", "printUrl"],
        });
    };

    useEffect(() => {
        setFind(filters.find ?? "");
    }, [filters.find]);

    useEffect(() => {
        const nextFind = find.trim();
        const currentFind = (filters.find ?? "").trim();

        if (nextFind === currentFind) {
            return;
        }

        const timeout = setTimeout(() => {
            updateFilters({ find: nextFind, page: undefined });
        }, 400);

        return () => clearTimeout(timeout);
    }, [find, filters.find]);

    const cleanLabel = (label) =>
        String(label)
            .replace(/&laquo;/g, "")
            .replace(/&raquo;/g, "")
            .trim();

    const pagination = useMemo(() => {
        const links = list?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [list?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        router.get(url, {}, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            only: ["filters", "summary", "list", "printUrl"],
        });
    };

    const resultSummary =
        list?.total > 0
            ? `${t("Showing")} ${list?.from ?? 0}-${list?.to ?? 0} ${t("of")} ${list?.total ?? 0} ${t("resel orders")}`
            : t("No resel orders found");
    const hasActiveFilters = Boolean(
        find.trim() ||
            (filters.nav ?? "Pending") !== "Pending" ||
            (filters.type ?? "All") !== "All" ||
            (filters.delivery ?? "all") !== "all" ||
            (filters.create ?? "all") !== "all" ||
            filters.start_date ||
            filters.end_date
    );

    return (
        <AppLayout
            title={t("Resel Orders")}
            header={
                <PageHeader>{t("Resel Orders")}<br />
                    {activeNav === "reseller" ? (
                        <div>
                            <NavLink href={route("vendor.orders.index")} active={route().current("vendor.orders.*")}>{t("To Me")}</NavLink>
                            <NavLink href={route("reseller.resel-order.index")} active={route().current("reseller.resel-order.*")}>{t("Resel Order")}</NavLink>
                        </div>
                    ) : null}
                </PageHeader>
            }
        >
            <Head title={t("Resel Orders")} />

            <Container>
                <OverviewSection>
                    <Div title={t("Orders")} content={summary.orders ?? 0} />
                    <Div title={t("Pending")} content={summary.pending ?? 0} />
                    <Div title={t("Cancel")} content={summary.cancel ?? 0} />
                    <Div title={t("Cancel by User")} content={summary.cancelled ?? 0} />
                    <Div title={t("Accepted")} content={summary.accept ?? 0} />
                </OverviewSection>

                <SectionSection>
                    <SectionHeader
                        title={
                                <div className="flex flex-wrap items-center justify-between gap-2">
                                    <div className="flex flex-wrap items-center gap-2">
                                    <div className="relative">
                                        <select
                                            id="status"
                                            value={primaryFilterValue}
                                            onChange={(e) => {
                                                const [filterKey, filterValue] = e.target.value.split(":");

                                                if (filterKey === "type") {
                                                    updateFilters({
                                                        nav: "All",
                                                        type: filterValue,
                                                        page: undefined,
                                                    });
                                                    return;
                                                }

                                                updateFilters({
                                                    nav: filterValue,
                                                    type: "All",
                                                    page: undefined,
                                                });
                                            }}
                                            className="py-1 pl-3 text-sm bg-white border rounded-md shadow-sm appearance-none h-9 min-w-28 border-slate-300 pr-9 text-slate-900 focus:border-orange-500 focus:ring-orange-500"
                                        >
                                            <option value="nav:All">{t("Any")}</option>
                                            <option value="nav:Pending">{t("Pending")}</option>
                                            <option value="nav:Accept">{t("Accept")}</option>
                                            <option value="nav:Picked">{t("Picked")}</option>
                                            <option value="nav:Delivery">{t("Delivery")}</option>
                                            <option value="nav:Delivered">{t("Delivered")}</option>
                                            <option value="nav:Confirm">{t("Confirm")}</option>
                                            <option value="nav:Reject">{t("Reject")}</option>
                                            <option value="nav:Hold">{t("Hold")}</option>
                                            <option value="type:Resel">{t("Resel")}</option>
                                            <option value="type:Purchase">{t("Purchase")}</option>
                                            </select>
                                        <i className="absolute text-xs -translate-y-1/2 pointer-events-none fas fa-chevron-down right-3 top-1/2 text-slate-500"></i>
                                    </div>
                                </div>

                                <div className="flex min-w-[320px] flex-1 flex-wrap items-center gap-2">
                                    <div className="relative">
                                        <select
                                            value={filters.delivery ?? "all"}
                                            onChange={(e) =>
                                                updateFilters({
                                                    delivery: e.target.value,
                                                    page: undefined,
                                                })
                                            }
                                            className="py-1 pl-3 text-sm bg-white border rounded-md shadow-sm appearance-none h-9 min-w-40 border-slate-300 pr-9 text-slate-900 focus:border-orange-500 focus:ring-orange-500"
                                        >
                                            <option value="all">{t("Not Defined")}</option>
                                            <option value="cash">{t("Home Delivery")}</option>
                                            <option value="courier">{t("Courier Delivery")}</option>
                                            <option value="hand">{t("Hand-to-Hand")}</option>
                                        </select>
                                        <i className="absolute text-xs -translate-y-1/2 pointer-events-none fas fa-chevron-down right-3 top-1/2 text-slate-500"></i>
                                    </div>

                                    <div className="flex flex-wrap items-center gap-2">
                                        <div className="relative">
                                            <input
                                                type="date"
                                                value={filters.start_date || today}
                                                onChange={(e) =>
                                                    updateFilters({
                                                        start_date: e.target.value,
                                                        page: undefined,
                                                    })
                                                }
                                                className="py-1 pl-3 text-sm bg-white border rounded-md shadow-sm h-9 min-w-36 border-slate-300 text-slate-900 focus:border-orange-500 focus:ring-orange-500"
                                            />
                                        </div>
                                        <div className="relative">
                                            <input
                                                type="date"
                                                value={filters.end_date ?? ""}
                                                onChange={(e) =>
                                                    updateFilters({
                                                        end_date: e.target.value,
                                                        page: undefined,
                                                    })
                                                }
                                                className="py-1 pl-3 text-sm bg-white border rounded-md shadow-sm h-9 min-w-36 border-slate-300 text-slate-900 focus:border-orange-500 focus:ring-orange-500"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="flex items-center gap-2">
                                    <TextInput
                                        type="search"
                                        value={find}
                                        onChange={(e) => setFind(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            updateFilters({ find: find.trim(), page: undefined });
                                        }}
                                        placeholder={t("Search orders...")}
                                        className="w-64 py-1 text-sm h-9"
                                    />
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                        className="inline-flex items-center justify-center w-12 px-0 h-9"
                                    >
                                        <i className="text-sm fas fa-print"></i>
                                    </PrimaryButton>
                                    {hasActiveFilters ? (
                                        <button
                                            type="button"
                                            className="h-9 rounded-md border border-gray-300 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50"
                                            onClick={() => {
                                                setFind("");
                                                updateFilters({
                                                    nav: "Pending",
                                                    type: "All",
                                                    delivery: "all",
                                                    create: "all",
                                                    start_date: "",
                                                    end_date: "",
                                                    find: "",
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
                        content={
                            <p>{t("View your resel product, income and comission here. You might find the order that have already passed to the vendor for your resel product.")}</p>
                        }
                    />

                    <SectionInner>
                        <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th> </th>
                                        <th>{t("ID")}</th>
                                        <th>{t("Shop")}</th>
                                        <th>{t("Sync")}</th>
                                        <th>{t("Total")}</th>
                                        <th>{t("Profit")}</th>
                                        <th>{t("Shipping")}</th>
                                        <th>{t("Date")}</th>
                                        <th>{t("Status")}</th>
                                        <th>{t("A/C")}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {rows.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{item.sl ?? index + 1}</td>
                                            <td>{item.id}</td>
                                            <td>
                                                {item.shop_id ? (
                                                    <a
                                                        className="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none"
                                                        href={route("shops", {
                                                            get: item.shop_id,
                                                            slug: item.shop_name_en || "not_found",
                                                        })}
                                                    >
                                                        {item.shop_name_en}
                                                    </a>
                                                ) : null}
                                                <div>{item.seller_phone}</div>
                                            </td>
                                            <td>
                                                {item.sync ? (
                                                    <div>
                                                        <div className="flex px-2 bg-gray-200 rounded shadow">
                                                            {item.sync.user_order_id}/{item.sync.user_cart_order_id}
                                                        </div>
                                                        <ActionIconLink href={item.sync.view_url} action="view" title={t("view")} />
                                                    </div>
                                                ) : (
                                                    <div className="inline-flex px-2 text-white bg-indigo-900 rounded">{t("Purchase")}</div>
                                                )}
                                            </td>
                                            <td>
                                                {formatCurrency(item.total)} + {formatCurrency(item.shipping)}
                                            </td>
                                            <td className="font-bold">{formatCurrency(item.profit)}</td>
                                            <td>
                                                <p className={`inline-flex text-xs px-1 rounded ${item.delevery === "cash" ? "bg-green-200" : "bg-blue-200"}`}>
                                                    {item.delevery}
                                                </p>
                                                <p>{item.location}</p>
                                            </td>
                                            <td>{item.created_at_formatted ?? 0}</td>
                                            <td>
                                                <span className={statusClass(item.status)}>
                                                    {item.status === "Reject" ? "Rejected" : item.status ?? "Unknown"}
                                                </span>
                                            </td>
                                            <td>
                                                <div className="flex items-center gap-1">
                                                    <ActionIconLink href={item.view_url} action="view" title={t("view")} />
                                                    <ActionIconLink href={item.print_url} action="print" title={t("Print")} />
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                        </Table>

                        {pagination.pages.length ? (
                                <div className="w-full pt-4">
                                    <div className="flex items-center justify-between w-full gap-3">
                                        <div className="text-sm text-slate-700">
                                            {resultSummary}
                                        </div>
                                        <div className="flex items-center md:justify-end">
                                            <div className="overflow-hidden bg-white border shadow-sm rounded-xl border-slate-200">
                                                <button
                                                    type="button"
                                                    disabled={!pagination.prev?.url}
                                                    className="px-4 py-2 text-sm transition border-r border-slate-200 text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                    onClick={() => goToPage(pagination.prev?.url)}
                                                >
                                                    {t("Previous")}
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
                                                        {cleanLabel(link.label)}
                                                    </button>
                                                ))}
                                                <button
                                                    type="button"
                                                    disabled={!pagination.next?.url}
                                                    className="px-4 py-2 text-sm transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                    onClick={() => goToPage(pagination.next?.url)}
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
        </AppLayout>
    );
}
