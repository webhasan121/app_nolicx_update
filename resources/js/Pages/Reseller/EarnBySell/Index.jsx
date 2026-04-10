import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import OverviewSection from "../../../components/dashboard/overview/Section";
import OverviewDiv from "../../../components/dashboard/overview/Div";
import Table from "../../../components/dashboard/table/Table";
import NavLink from "../../../components/NavLink";

function statusClass(status) {
    switch (status) {
        case "Pending":
            return "text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900";
        case "Accept":
            return "text-xs p-1 border rounded-md bg-green-200 text-green-900";
        case "Picked":
            return "text-xs p-1 border rounded-md bg-lime-200 text-lime-900";
        case "Delivery":
            return "text-xs p-1 border rounded-md bg-sky-200 text-sky-900";
        case "Delivered":
            return "text-xs p-1 border rounded-md bg-blue-200 text-blue-900";
        case "Confirm":
            return "text-xs p-1 border rounded-md bg-indigo-200 text-indigo-900";
        case "Hold":
            return "text-xs p-1 border rounded-md bg-gray-200 text-gray-900";
        case "Cancel":
        case "Cancelled":
            return "text-xs p-1 border rounded-md bg-red-200 text-red-900";
        default:
            return "text-xs p-1 border rounded-md bg-gray-200 text-gray-900";
    }
}

export default function Index({
    filters = {},
    overview = {},
    products = { data: [], links: [] },
    counts = {},
    printUrl,
}) {
    const [nav, setNav] = useState(filters.nav ?? "sold");
    const [fd, setFd] = useState(filters.fd ?? "");
    const [lastDate, setLastDate] = useState(filters.lastDate ?? "");
    const [search, setSearch] = useState(filters.search ?? "");

    const cleanLabel = (label) =>
        String(label)
            .replace(/&laquo;/g, "")
            .replace(/&raquo;/g, "")
            .trim();

    const requestProducts = (overrides = {}, options = {}) => {
        const nextNav = overrides.nav ?? nav;
        const nextFd = overrides.fd ?? fd;
        const nextLastDate = overrides.lastDate ?? lastDate;
        const nextSearch = overrides.search ?? search.trim();

        router.get(
            route("reseller.sel.index"),
            {
                nav: nextNav,
                fd: nextFd || undefined,
                lastDate: nextLastDate || undefined,
                search: nextSearch || undefined,
                page: overrides.page ?? undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                ...options,
            },
        );
    };

    const changeNav = (value) => {
        setNav(value);
        requestProducts({ nav: value });
    };

    const resetFilters = () => {
        setFd("");
        setLastDate("");
        requestProducts({ fd: "", lastDate: "" });
    };

    useEffect(() => {
        setSearch(filters.search ?? "");
    }, [filters.search]);

    useEffect(() => {
        setNav(filters.nav ?? "sold");
        setFd(filters.fd ?? "");
        setLastDate(filters.lastDate ?? "");
    }, [filters.nav, filters.fd, filters.lastDate]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.search ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            requestProducts({ search: trimmedSearch });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search, nav, fd, lastDate, filters.search]);

    const formattedFd = useMemo(() => {
        if (!fd) return "";
        return new Date(fd).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });
    }, [fd]);

    const formattedLastDate = useMemo(() => {
        if (!lastDate) return "";
        return new Date(lastDate).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });
    }, [lastDate]);

    const pagination = useMemo(() => {
        const links = products?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [products?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url, window.location.origin);

        requestProducts({
            nav: nextUrl.searchParams.get("nav") ?? nav,
            fd: nextUrl.searchParams.get("fd") ?? fd,
            lastDate: nextUrl.searchParams.get("lastDate") ?? lastDate,
            search: nextUrl.searchParams.get("search") ?? search.trim(),
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultSummary =
        products?.total > 0
            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} items`
            : "No items found";

    const updateStartDate = (value) => {
        setFd(value);
        requestProducts({ fd: value, lastDate, page: undefined });
    };

    const updateLastDate = (value) => {
        setLastDate(value);
        requestProducts({ fd, lastDate: value, page: undefined });
    };

    return (
        <AppLayout title="Sell and Profit">
            <Head title="Sell and Profit" />

            <Container>
                <p className="text-xl">Sell and Profit</p>

                <OverviewSection>
                    <OverviewDiv
                        title="Total Sell"
                        content={`${overview.totalSell ?? 0} TK`}
                    />
                    <OverviewDiv
                        title="Profit"
                        content={`${overview.tp ?? 0} TK`}
                    />
                    <OverviewDiv
                        title="Neet"
                        content={`${overview.tn ?? 0} TK`}
                    />
                    <OverviewDiv
                        title="Shop"
                        content={`${overview.shop ?? 0}`}
                    />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between gap-3">
                                <div className="flex space-x-2">
                                    <select
                                        value={nav}
                                        onChange={(e) =>
                                            changeNav(e.target.value)
                                        }
                                        className="rounded py-1"
                                    >
                                        <option value="all">Both</option>
                                        <option value="sold">Sold</option>
                                        <option value="selling">
                                            On-Selling
                                        </option>
                                    </select>
                                </div>
                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <div>
                                        <input
                                            type="date"
                                            value={fd}
                                            onChange={(e) =>
                                                updateStartDate(e.target.value)
                                            }
                                            className="rounded py-1 text-sm"
                                            title={formattedFd}
                                        />
                                    </div>
                                    <div>
                                        <input
                                            type="date"
                                            value={lastDate}
                                            onChange={(e) =>
                                                updateLastDate(e.target.value)
                                            }
                                            className="rounded py-1 text-sm"
                                            title={formattedLastDate}
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        className="rounded border px-3 py-1 text-sm text-slate-700"
                                        onClick={resetFilters}
                                    >
                                        Reset
                                    </button>
                                    <TextInput
                                        type="search"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            requestProducts({
                                                search: search.trim(),
                                            });
                                        }}
                                        className="py-1"
                                        placeholder="Search products..."
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
                    />
                    <hr />
                    <SectionInner>
                        <Table data={products.data ?? []} className="p-2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Flow</th>
                                    <th>Owner</th>
                                    <th>Price</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(products.data ?? []).map((item, idx) => (
                                    <tr key={item.id}>
                                        <td>{(products?.from ?? 1) + idx}</td>
                                        <td>{item.id}</td>
                                        <td>
                                            <NavLink
                                                className="text-xs"
                                                href={route(
                                                    "products.details",
                                                    {
                                                        id:
                                                            item.product_id ??
                                                            "",
                                                        slug:
                                                            item.product_slug ??
                                                            "",
                                                    },
                                                )}
                                            >
                                                {item.product_thumbnail ? (
                                                    <img
                                                        width="30px"
                                                        height="30px"
                                                        src={
                                                            item.product_thumbnail
                                                        }
                                                        alt=""
                                                        className="mr-2 rounded-full"
                                                    />
                                                ) : null}
                                                {item.product_name ?? "N/A"}
                                            </NavLink>
                                            <br />
                                            <div className="text-xs border rounded inline-block">
                                                {item.product_status ?? "N/A"}
                                            </div>
                                        </td>
                                        <td>
                                            <div className="flex items-center">
                                                {item.user_type}{" "}
                                                <i className="fas fa-angle-right mx-2"></i>
                                                {item.belongs_to_type}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div className="text-gray-700">
                                                    {item.owner_name ?? "N/A"}
                                                </div>
                                                {item.is_resel_count ? (
                                                    <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                        <i className="fas fa-caret-left"></i>
                                                        R
                                                    </span>
                                                ) : null}
                                                {item.resel_count ? (
                                                    <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                        {item.resel_count}
                                                        <i className="fas fa-caret-right"></i>
                                                    </span>
                                                ) : null}
                                            </div>
                                        </td>
                                        <td>
                                            {item.product_price ?? 0} TK
                                            {item.offer_type ? (
                                                <div className="flex items-center text-center p-1 rounded bg-gray-100 text-xs">
                                                    D: {item.discount} |{" "}
                                                    {item.discount_percent}% off
                                                </div>
                                            ) : null}
                                        </td>
                                        <td>
                                            {item.product_created_at ?? "N/A"}
                                        </td>
                                        <td>
                                            <span
                                                className={statusClass(
                                                    item.status,
                                                )}
                                            >
                                                {item.status ?? "Unknown"}
                                            </span>
                                        </td>
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
                                                    {cleanLabel(link.label)}
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
