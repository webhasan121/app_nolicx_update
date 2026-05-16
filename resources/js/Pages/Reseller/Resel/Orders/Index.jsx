import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Foreach from "../../../../components/dashboard/Foreach";
import Div from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import PageHeader from "../../../../components/dashboard/PageHeader";

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
    const [filterOpen, setFilterOpen] = useState(false);
    const [find, setFind] = useState(filters.find ?? "");
    const rows = list?.data ?? [];

    const updateFilters = (updates = {}) => {
        router.get(route("reseller.resel-order.index"), buildQuery(filters, updates), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
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

        router.get(url, {}, { preserveScroll: true, preserveState: true });
    };

    const resultSummary =
        list?.total > 0
            ? `Showing ${list?.from ?? 0}-${list?.to ?? 0} of ${list?.total ?? 0} resel orders`
            : "No resel orders found";

    return (
        <AppLayout
            title="Resel Orders"
            header={
                <PageHeader>
                    Resel Orders
                    <br />
                    {activeNav === "reseller" ? (
                        <div>
                            <NavLink href={route("vendor.orders.index")} active={route().current("vendor.orders.*")}>
                                To Me
                            </NavLink>
                            <NavLink href={route("reseller.resel-order.index")} active={route().current("reseller.resel-order.*")}>
                                Resel Order
                            </NavLink>
                        </div>
                    ) : null}
                </PageHeader>
            }
        >
            <Head title="Resel Orders" />

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
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <div className="flex flex-wrap items-center gap-2">
                                    <SecondaryButton
                                        type="button"
                                        onClick={() => setFilterOpen(true)}
                                        className="inline-flex h-9 items-center gap-2 px-4 text-xs"
                                    >
                                        <i className="fas fa-filter text-sm"></i>
                                        <span>Filter</span>
                                    </SecondaryButton>

                                    <div className="relative">
                                        <select
                                            id="status"
                                            value={filters.nav ?? "Pending"}
                                            onChange={(e) => updateFilters({ nav: e.target.value, page: undefined })}
                                            className="h-9 min-w-28 appearance-none rounded-md border border-slate-300 bg-white py-1 pl-3 pr-9 text-sm text-slate-900 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                        >
                                            <option value="All">Any</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Accept">Accept</option>
                                            <option value="Picked">Picked</option>
                                            <option value="Delivery">Delivery</option>
                                            <option value="Delivered">Delivered</option>
                                            <option value="Confirm">Confirm</option>
                                            <option value="Reject">Reject</option>
                                            <option value="Hold">Hold</option>
                                        </select>
                                        <i className="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500"></i>
                                    </div>

                                    <div className="relative">
                                        <select
                                            id="type"
                                            value={filters.type ?? "All"}
                                            onChange={(e) => updateFilters({ type: e.target.value, page: undefined })}
                                            className="h-9 min-w-24 appearance-none rounded-md border border-slate-300 bg-white py-1 pl-3 pr-9 text-sm text-slate-900 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                        >
                                            <option value="All">All</option>
                                            <option value="Resel">Resel</option>
                                            <option value="Purchase">Purchase</option>
                                        </select>
                                        <i className="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500"></i>
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
                                        placeholder="Search orders..."
                                        className="h-9 w-64 py-1 text-sm"
                                    />
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                        className="inline-flex h-9 w-12 items-center justify-center px-0"
                                    >
                                        <i className="fas fa-print text-sm"></i>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                        content={
                            <p>
                                View your resel product, income and comission here. You might find the order that have
                                already passed to the vendor for your resel product.
                            </p>
                        }
                    />

                    <SectionInner>
                        <Foreach data={rows}>
                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th> </th>
                                        <th> ID </th>
                                        <th> Shop </th>
                                        <th> Sync</th>
                                        <th> Total </th>
                                        <th> Profit </th>
                                        <th> Shipping </th>
                                        <th> Date </th>
                                        <th> Status </th>
                                        <th> A/C </th>
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
                                                        className="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm text-gray-700 hover:bg-gray-50 focus:outline-none"
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
                                                        <div className="px-2 bg-gray-200 rounded shadow flex">
                                                            {item.sync.user_order_id}/{item.sync.user_cart_order_id}
                                                        </div>
                                                        <NavLink href={item.sync.view_url}>view</NavLink>
                                                    </div>
                                                ) : (
                                                    <div className="px-2 inline-flex rounded bg-indigo-900 text-white">Purchase</div>
                                                )}
                                            </td>
                                            <td>
                                                {item.total} + {item.shipping}
                                            </td>
                                            <td className="font-bold">{item.profit}</td>
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
                                                <NavLink href={item.view_url}>view</NavLink>
                                                <NavLink href={item.print_url}>Print</NavLink>
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
                                                        {cleanLabel(link.label)}
                                                    </button>
                                                ))}
                                                <button
                                                    type="button"
                                                    disabled={!pagination.next?.url}
                                                    className="px-4 py-2 text-sm transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
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

            <Modal show={filterOpen} onClose={() => setFilterOpen(false)} maxWidth="xl">
                <div className="p-2">
                    <div>Filter</div>
                    <Hr />
                    <div className="md:flex justify-between">
                        <div>
                            <div>
                                <div>Delevery Type</div>
                                <div className="px-2">
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={(filters.delivery ?? "all") === "all"}
                                            onChange={() => updateFilters({ delivery: "all" })}
                                        />{" "}
                                        Not Defined
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.delivery === "cash"}
                                            onChange={() => updateFilters({ delivery: "cash" })}
                                        />{" "}
                                        Home Delivery
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.delivery === "courier"}
                                            onChange={() => updateFilters({ delivery: "courier" })}
                                        />{" "}
                                        Courier Delivery
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.delivery === "hand"}
                                            onChange={() => updateFilters({ delivery: "hand" })}
                                        />
                                        Hand-to-Hand
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="mt-2 w-1/2">
                            <div className=" border rounded-md">
                                <div className=" p-2 ">
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={(filters.create ?? "all") === "all"}
                                            onChange={() => updateFilters({ create: "all" })}
                                        />
                                        All Time
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.create === "day"}
                                            onChange={() => updateFilters({ create: "day" })}
                                        />
                                        From First Date
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.create === "between"}
                                            onChange={() => updateFilters({ create: "between" })}
                                        />
                                        Between in Range
                                    </div>
                                </div>

                                <div className="space-y-2 p-2 ">
                                    <div>
                                        First Date
                                        <input
                                            className="rounded-md"
                                            type="date"
                                            value={filters.start_date ?? ""}
                                            onChange={(e) => updateFilters({ start_date: e.target.value })}
                                        />
                                    </div>
                                    <div>
                                        Last Date
                                        <input
                                            className="rounded-md"
                                            type="date"
                                            value={filters.end_date ?? ""}
                                            onChange={(e) => updateFilters({ end_date: e.target.value })}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}

