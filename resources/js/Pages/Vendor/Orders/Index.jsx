import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Foreach from "../../../components/dashboard/Foreach";
import Div from "../../../components/dashboard/overview/Div";
import OverviewSection from "../../../components/dashboard/overview/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import SectionSection from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";
import Dropdown from "../../../components/Dropdown";
import NavLink from "../../../components/NavLink";
import PrimaryButton from "../../../components/PrimaryButton";
import SecondaryButton from "../../../components/SecondaryButton";
import TextInput from "../../../components/TextInput";

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
        Object.entries({ ...filters, ...updates }).filter(
            ([, value]) => value !== "" && value !== null && value !== undefined
        )
    );
}

function statusClass(status) {
    const classes = {
        Pending: "text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900",
        Accept: "text-xs p-1 border rounded-md bg-green-200 text-green-900",
        Cancel: "text-xs p-1 border rounded-md bg-orange-200 text-orange-900",
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

export default function Index({ filters = {}, summary = {}, list = {}, activeNav, printUrl }) {
    const rows = list?.data ?? [];
    const isReseller = activeNav === "reseller";
    const [search, setSearch] = useState(filters.find ?? "");

    const updateFilters = (updates, resetPage = true) => {
        router.get(route("vendor.orders.index"), buildQuery(filters, { ...updates, ...(resetPage ? { page: 1 } : {}) }), {
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

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        router.get(route("vendor.orders.index"), buildQuery(filters, {
            nav: nextUrl.searchParams.get("nav") ?? filters.nav,
            delivery: nextUrl.searchParams.get("delivery") ?? filters.delivery,
            create: nextUrl.searchParams.get("create") ?? filters.create,
            start_date: nextUrl.searchParams.get("start_date") ?? filters.start_date,
            end_date: nextUrl.searchParams.get("end_date") ?? filters.end_date,
            area: nextUrl.searchParams.get("area") ?? filters.area,
            find: nextUrl.searchParams.get("find") ?? search.trim(),
            page: nextUrl.searchParams.get("page") ?? undefined,
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
            ? `Showing ${list?.from ?? 0}-${list?.to ?? 0} of ${list?.total ?? 0} orders`
            : "No orders found";

    return (
        <AppLayout
            title="Orders"
            header={
                <PageHeader>
                    Orders
                    <br />
                    {isReseller ? (
                        <div>
                            <NavLink href={route("vendor.orders.index")} active={route().current("vendor.orders.*")}>
                                User Orders
                            </NavLink>
                            <NavLink href={route("reseller.resel-order.index")} active={route().current("reseller.resel-order.*")}>
                                My Resel Order
                            </NavLink>
                        </div>
                    ) : null}
                </PageHeader>
            }
        >
            <Head title="Orders" />
            <Container>
                    <OverviewSection>
                        <Div title="Orders" content={summary.orders ?? 0} />
                        <Div title="Pending" content={summary.pending ?? 0} />
                        <Div title="Cancel" content={summary.cancel ?? 0} />
                        <Div title="Cancel by User" content={summary.cancelled ?? 0} />
                        <Div title="Accepted" content={summary.accept ?? 0} />
                    </OverviewSection>

                    <SectionSection>
                        <SectionHeader
                            title={
                                <div className="flex items-center justify-between gap-3">
                                    <div className="flex flex-wrap justify-start items-center gap-2">
                                        <Dropdown
                                            align="left"
                                            trigger={
                                                <SecondaryButton className="inline-flex items-center ">
                                                    Delivery <i className="fas fa-angle-down ps-2"></i>
                                                </SecondaryButton>
                                            }
                                        >
                                            <div className="flex items-center w-full p-2 text-sm">
                                                <input type="radio" style={{ width: 20, height: 20 }} className="mr-2" checked={filters.delivery === "all"} onChange={() => updateFilters({ delivery: "all" })} /> Not Defined
                                            </div>
                                            <hr />
                                            <div className="flex items-center w-full p-2 text-sm">
                                                <input type="radio" style={{ width: 20, height: 20 }} className="mr-2" checked={filters.delivery === "cash"} onChange={() => updateFilters({ delivery: "cash" })} /> Home Delivery
                                            </div>
                                            <hr />
                                            <div className="flex items-center w-full p-2 text-sm">
                                                <input type="radio" style={{ width: 20, height: 20 }} className="mr-2" checked={filters.delivery === "courier"} onChange={() => updateFilters({ delivery: "courier" })} /> Courier Delivery
                                            </div>
                                            <hr />
                                            <div className="flex items-center w-full p-2 text-sm">
                                                <input type="radio" style={{ width: 20, height: 20 }} className="mr-2" checked={filters.delivery === "hand"} onChange={() => updateFilters({ delivery: "hand" })} /> Hand-to-Hand
                                            </div>
                                        </Dropdown>

                                        <Dropdown
                                            trigger={
                                                <SecondaryButton>
                                                    Area <i className="fas fa-angle-down ps-2"></i>
                                                </SecondaryButton>
                                            }
                                        >
                                            <div className="flex items-center mb-2 rounded-md border p-2 text-sm">
                                                <input className="w-5 h-5 p-0 m-0 mr-3" type="radio" checked={filters.area === "all"} onChange={() => updateFilters({ area: "all" })} />
                                                <label className="p-0 m-0"> Both </label>
                                            </div>
                                            <div className="flex items-center mb-2 rounded-md border p-2 text-sm">
                                                <input className="w-5 h-5 p-0 m-0 mr-3" type="radio" checked={filters.area === "Dhaka"} onChange={() => updateFilters({ area: "Dhaka" })} />
                                                <label className="p-0 m-0"> Inside Dhaka </label>
                                            </div>
                                            <div className="flex items-center mb-2 rounded-md border p-2 text-sm">
                                                <input className="w-5 h-5 p-0 m-0 mr-3" type="radio" checked={filters.area === "Other"} onChange={() => updateFilters({ area: "Other" })} />
                                                <label className="p-0 m-0"> Outside of Dhaka </label>
                                            </div>
                                        </Dropdown>

                                        <label className="sr-only" htmlFor="order_start_date">First Date</label>
                                        <TextInput
                                            id="order_start_date"
                                            type="date"
                                            value={filters.start_date ?? ""}
                                            onChange={(e) => updateDateFilters({ start_date: e.target.value })}
                                            className="min-w-[168px] py-1"
                                            title="First Date"
                                        />
                                        <label className="sr-only" htmlFor="order_end_date">Last Date</label>
                                        <TextInput
                                            id="order_end_date"
                                            type="date"
                                            value={filters.end_date ?? ""}
                                            onChange={(e) => updateDateFilters({ end_date: e.target.value })}
                                            className="min-w-[168px] py-1"
                                            title="Last Date"
                                        />
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
                                                updateFilters({ find: search.trim() });
                                            }}
                                            className="py-1"
                                            placeholder="Search orders..."
                                        />
                                        <PrimaryButton type="button" onClick={() => window.open(printUrl, "_blank")}>
                                            <i className="fas fa-print"></i>
                                        </PrimaryButton>
                                    </div>
                                </div>
                            }
                            content={
                                <div className="flex justify-between">
                                    <div>
                                        {navs.map((nav) => (
                                            <NavLink
                                                key={nav}
                                                href={route("vendor.orders.index", buildQuery(filters, { nav, page: 1 }))}
                                                active={filters.nav === nav}
                                            >
                                                {nav === "Cancelled" ? "Cancel by User" : nav}
                                            </NavLink>
                                        ))}
                                    </div>

                                    <NavLink
                                        href={route("vendor.orders.index", buildQuery(filters, { nav: "Trash", page: 1 }))}
                                        active={filters.nav === "Trash"}
                                    >
                                        Trash
                                    </NavLink>
                                </div>
                            }
                        />

                        <SectionInner>
                            <Foreach data={rows}>
                                <Table data={rows}>
                                    <thead>
                                        <tr>
                                            <th colSpan="3"> {rows.length} Products </th>
                                            <th>{list.sum_total ?? 0} TK</th>
                                        </tr>
                                    </thead>
                                </Table>

                                <Table data={rows}>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th></th>
                                            <th>ID</th>
                                            <th>Pd</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Shipping</th>
                                            <th>Contact</th>
                                            <th>Com</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {rows.map((item, index) => (
                                            <tr key={item.id}>
                                                <td>{(list?.from ?? 1) + index}</td>
                                                <td>
                                                    <NavLink href={route("vendor.orders.view", { order: item.id })}>view</NavLink>
                                                    <NavLink href={route("vendor.orders.cprint", { order: item.id })}>Print</NavLink>
                                                </td>
                                                <td>{item.id ?? "N/A"}</td>
                                                <td>{item.cart_orders_count ?? "N/A"} / {item.quantity ?? "N/A"}</td>
                                                <td>{item.total ?? "N/A"} <br /> <span className="text-xs">+ {item.shipping}</span></td>
                                                <td><span className={statusClass(item.status)}>{item.status ?? "Unknown"}</span></td>
                                                <td>
                                                    <div className="text-nowarp text-xs">
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
                                                <th>{item.comission}</th>
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
                                                    {pagination.pages.map((link, idx) => (
                                                        <button
                                                            key={`${link.label}-${idx}`}
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
                            </Foreach>
                        </SectionInner>
                    </SectionSection>
                </Container>

        </AppLayout>
    );
}
