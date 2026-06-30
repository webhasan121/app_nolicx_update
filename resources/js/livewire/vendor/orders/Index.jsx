import { router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Div from "../../../components/dashboard/overview/Div";
import Section from "../../../components/dashboard/overview/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import SectionSection from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";
import Dropdown from "../../../components/Dropdown";
import SecondaryButton from "../../../components/SecondaryButton";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import NavLink from "../../../components/NavLink";
import { todayInputDate } from "../../../utils/dateInput";
import { ActionIconLink } from "../../../components/ActionIcon";
import useTranslation from "../../../hooks/useTranslation";
import { formatCurrency } from "../../../utils/formatAmount";

const navs = [
    "All",
    "Pending",
    "Accept",
    "Picked",
    "Delivery",
    "Delivered",
    "Confirm",
    "Hold",
    "Reject",
    "Cancelled",
];

function buildQuery(filters, updates = {}) {
    return Object.fromEntries(
        Object.entries({ ...filters, ...updates }).filter(([, value]) => value !== "" && value !== null && value !== undefined)
    );
}

function StatusBadge({ status }) {
    const { t } = useTranslation();
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

    return <span className={classes[status] ?? "text-xs p-1 border rounded-md bg-gray-200 text-gray-900"}>{t(status ?? "Unknown")}</span>;
}

export default function Index({ orderIndex, activeNav, embedded = false }) {
    const { t } = useTranslation();
    const filters = orderIndex?.filters ?? {};
    const summary = orderIndex?.summary ?? {};
    const list = orderIndex?.list ?? {};
    const rows = list?.data ?? [];
    const isReseller = activeNav === "reseller";
    const [search, setSearch] = useState(filters.find ?? "");
    const today = todayInputDate();

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
            updateFilters({ find: trimmedSearch });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const updateFilters = (updates) => {
        router.get(route("dashboard"), buildQuery(filters, { ...updates, page: 1 }), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const updateDateFilters = (updates) => {
        const nextStartDate = updates.start_date ?? filters.start_date ?? "";
        const nextEndDate = updates.end_date ?? filters.end_date ?? "";
        const create = nextStartDate && nextEndDate ? "between" : nextStartDate ? "day" : "all";

        updateFilters({
            ...updates,
            create,
            end_date: nextStartDate ? nextEndDate : "",
        });
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        router.get(route("dashboard"), buildQuery(filters, {
            page: nextUrl.searchParams.get("page") ?? 1,
            find: nextUrl.searchParams.get("find") ?? search.trim(),
        }), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const pagination = useMemo(() => {
        const links = list?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [list?.links]);

    const resultSummary =
        list?.total > 0
            ? t("Showing :from-:to of :total orders", {
                from: list?.from ?? 0,
                to: list?.to ?? 0,
                total: list?.total ?? 0,
            })
            : t("No orders found");
    const selectedNav = filters.nav ?? "Pending";
    const hasActiveFilters = Boolean(
        search.trim() ||
            selectedNav !== "Pending" ||
            (filters.delivery ?? "all") !== "all" ||
            (filters.create ?? "all") !== "all" ||
            (filters.area ?? "all") !== "all"
    );

    const Wrapper = ({ children }) =>
        embedded ? <div className="mb-3">{children}</div> : <Container>{children}</Container>;

    return (
        <div>
            <Wrapper>
                <PageHeader>
                    {t("Orders")}
                    <br />
                    {isReseller ? (
                        <div className="flex flex-wrap gap-2">
                            <NavLink
                                href={route("vendor.orders.index")}
                                active={route().current("vendor.orders.*")}
                            >
                                {t("User Orders")}
                            </NavLink>
                            <NavLink
                                href={route("reseller.resel-order.index")}
                                active={route().current("reseller.resel-order.*")}
                            >
                                {t("My Resel Order")}
                            </NavLink>
                        </div>
                    ) : null}
                </PageHeader>
            </Wrapper>

            <Wrapper>
                <Section>
                    <Div title={t("Orders")} content={summary.orders ?? 0} />
                    <Div title={t("Pending")} content={summary.pending ?? 0} />
                    <Div title={t("Cancel")} content={summary.cancel ?? 0} />
                    <Div title={t("Cancel by User")} content={summary.cancelled ?? 0} />
                    <Div title={t("Accepted")} content={summary.accept ?? 0} />
                </Section>

                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex flex-col gap-3">
                                <div className="flex flex-col gap-2 lg:flex-row lg:flex-wrap lg:items-center">
                                    <Dropdown
                                        align="left"
                                        trigger={
                                            <SecondaryButton className="inline-flex w-auto items-center justify-center self-start">
                                                {t("Delivery")} <i className="fas fa-angle-down ps-2"></i>
                                            </SecondaryButton>
                                        }
                                    >
                                        <div className="flex items-center w-full p-2 text-sm">
                                            <input type="radio" style={{ width: 20, height: 20 }} className="mr-2" checked={filters.delivery === "all"} onChange={() => updateFilters({ delivery: "all" })} /> {t("Not Defined")}
                                        </div>
                                        <hr />
                                        <div className="flex items-center w-full p-2 text-sm">
                                            <input type="radio" style={{ width: 20, height: 20 }} className="mr-2" checked={filters.delivery === "cash"} onChange={() => updateFilters({ delivery: "cash" })} /> {t("Home Delivery")}
                                        </div>
                                        <hr />
                                        <div className="flex items-center w-full p-2 text-sm">
                                            <input type="radio" style={{ width: 20, height: 20 }} className="mr-2" checked={filters.delivery === "courier"} onChange={() => updateFilters({ delivery: "courier" })} /> {t("Courier Delivery")}
                                        </div>
                                        <hr />
                                        <div className="flex items-center w-full p-2 text-sm">
                                            <input type="radio" style={{ width: 20, height: 20 }} className="mr-2" checked={filters.delivery === "hand"} onChange={() => updateFilters({ delivery: "hand" })} /> {t("Hand-to-Hand")}
                                        </div>
                                    </Dropdown>

                                    <Dropdown
                                        trigger={
                                            <SecondaryButton className="inline-flex w-auto items-center justify-center self-start">
                                                {t("Area")} <i className="fas fa-angle-down ps-2"></i>
                                            </SecondaryButton>
                                        }
                                    >
                                        <div className="flex items-center p-2 mb-2 text-sm border rounded-md">
                                            <input className="w-5 h-5 p-0 m-0 mr-3" type="radio" checked={filters.area === "all"} onChange={() => updateFilters({ area: "all" })} />
                                            <label className="p-0 m-0"> {t("Both")} </label>
                                        </div>
                                        <div className="flex items-center p-2 mb-2 text-sm border rounded-md">
                                            <input className="w-5 h-5 p-0 m-0 mr-3" type="radio" checked={filters.area === "Dhaka"} onChange={() => updateFilters({ area: "Dhaka" })} />
                                            <label className="p-0 m-0"> {t("Inside Dhaka")} </label>
                                        </div>
                                        <div className="flex items-center p-2 mb-2 text-sm border rounded-md">
                                            <input className="w-5 h-5 p-0 m-0 mr-3" type="radio" checked={filters.area === "Other"} onChange={() => updateFilters({ area: "Other" })} />
                                            <label className="p-0 m-0"> {t("Outside of Dhaka")} </label>
                                        </div>
                                    </Dropdown>

                                    <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                                        <label className="sr-only" htmlFor="order_start_date">{t("First Date")}</label>
                                        <div>
                                            <TextInput
                                                id="order_start_date"
                                                type="date"
                                                value={filters.start_date || today}
                                                onChange={(e) => updateDateFilters({ start_date: e.target.value })}
                                                className="h-10 w-full py-1 sm:w-auto"
                                                title={t("First Date")}
                                            />
                                        </div>
                                        <label className="sr-only" htmlFor="order_end_date">{t("Last Date")}</label>
                                        <div>
                                            <TextInput
                                                id="order_end_date"
                                                type="date"
                                                value={filters.end_date ?? ""}
                                                onChange={(e) => updateDateFilters({ end_date: e.target.value })}
                                                className="h-10 w-full py-1 sm:w-auto"
                                                title={t("Last Date")}
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center md:min-w-[260px]">
                                    <TextInput
                                        type="search"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            updateFilters({ find: search.trim() });
                                        }}
                                        className="h-10 w-full py-1 sm:w-auto"
                                        placeholder={t("Search orders...")}
                                    />
                                    <PrimaryButton
                                        type="button"
                                        className="inline-flex h-10 w-auto shrink-0 justify-center self-start px-4 py-1 text-sm sm:h-[34px]"
                                        onClick={() => window.open(orderIndex?.print_url, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                        content={
                            <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div className="flex flex-wrap gap-x-2 gap-y-1">
                                    {navs.map((nav) => (
                                        <NavLink
                                            key={nav}
                                            href={route("dashboard", buildQuery(filters, { nav, page: 1 }))}
                                            active={filters.nav === nav}
                                        >
                                            {nav === "Cancelled" ? t("Cancel by User") : t(nav)}
                                        </NavLink>
                                    ))}
                                </div>

                                <div>
                                    <NavLink
                                        href={route("dashboard", buildQuery(filters, { nav: "Trashed", page: 1 }))}
                                        active={filters.nav === "Trashed"}
                                    >
                                        {t("Trash")}
                                    </NavLink>
                                </div>
                            </div>
                        }
                    />

                    <SectionInner>
                        {hasActiveFilters ? (
                        <div className="overflow-x-auto">
                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th colSpan="3"> {rows.length} {t("Products")} </th>
                                        <th>{list.sum_total ?? 0} TK</th>
                                    </tr>
                                </thead>
                            </Table>
                        </div>
                        ) : null}

                        <div className="overflow-x-auto">
                        <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        <th>{t("ID")}</th>
                                        <th>{t("Pd")}</th>
                                        <th>{t("Total")}</th>
                                        <th>{t("Status")}</th>
                                        <th>{t("Date")}</th>
                                        <th>{t("Shipping")}</th>
                                        <th>{t("Contact")}</th>
                                        <th>{t("Com")}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{(list?.from ?? 1) + index}</td>
                                            <td>
                                                <div className="flex items-center gap-1">
                                                    <ActionIconLink href={route("vendor.orders.view", { order: item.id })} action="view" title={t("View")} />
                                                    <ActionIconLink href={route("vendor.orders.cprint", { order: item.id })} action="print" title={t("Print")} />
                                                </div>
                                            </td>
                                            <td>{item.id ?? "N/A"}</td>
                                            <td>{item.cart_orders_count ?? "N/A"} / {item.quantity ?? "N/A"}</td>
                                            <td>{item.total !== null && item.total !== undefined ? formatCurrency(item.total) : "N/A"} <br /> <span className="text-xs">+ {formatCurrency(item.shipping)}</span></td>
                                            <td><StatusBadge status={item.status} /></td>
                                            <td>
                                                <div className="text-xs text-nowarp">
                                                    <div>{item.created_at_human}</div>
                                                    <div className="text-xs">{item.created_at_formatted}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div className="flex space-x-1">
                                                    <p className={`text-xs px-1 rounded ${item.delevery === "cash" ? "bg-green-200" : "bg-blue-200"}`}>
                                                        {item.delevery}
                                                    </p>
                                                </div>
                                                <p className="text-xs">{item.location}</p>
                                            </td>
                                            <td>
                                                <span className="text-xs">
                                                    <div className="text-xs"> {item.user_name} </div>
                                                    {item.number ?? "N/A"}
                                                </span>
                                            </td>
                                            <th>{formatCurrency(item.comission)}</th>
                                        </tr>
                                    ))}
                                </tbody>
                        </Table>
                        </div>

                        {pagination.pages.length ? (
                                <div className="w-full pt-4">
                                    <div className="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div className="text-sm leading-6 text-slate-700">
                                            {resultSummary}
                                        </div>
                                        <div className="flex w-full justify-start sm:w-auto sm:justify-end">
                                            <div className="inline-flex flex-nowrap overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                                <button
                                                    type="button"
                                                    disabled={!pagination.prev?.url}
                                                    className="whitespace-nowrap border-r border-slate-200 px-3 py-2 text-xs text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 sm:px-4 sm:text-sm"
                                                    onClick={() => goToPage(pagination.prev?.url)}
                                                >
                                                    {t("Previous")}
                                                </button>
                                                {pagination.pages.map((link, pageIndex) => (
                                                    <button
                                                        key={`${link.label}-${pageIndex}`}
                                                        type="button"
                                                        disabled={!link.url}
                                                        className={`min-w-8 whitespace-nowrap border-r border-slate-200 px-3 py-2 text-xs font-semibold transition sm:min-w-10 sm:px-4 sm:text-sm ${
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
                                                    className="whitespace-nowrap px-3 py-2 text-xs text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 sm:px-4 sm:text-sm"
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
            </Wrapper>

        </div>
    );
}
