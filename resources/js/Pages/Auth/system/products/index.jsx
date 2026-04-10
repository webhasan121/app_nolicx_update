import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

export default function Index() {
    const {
        filters = {
            filter: "Active",
            from: "all",
            find: "",
            sd: "",
            ed: "",
            isIncludeResel: true,
        },
        products = {},
        printUrl,
    } = usePage().props;

    const [from, setFrom] = useState(filters.from ?? "all");
    const [find, setFind] = useState(filters.find ?? "");
    const [sd, setSd] = useState(filters.sd ?? "");
    const [ed, setEd] = useState(filters.ed ?? "");
    const rows = products.data ?? [];

    const requestProducts = ({
        nextFilter = filters.filter ?? "Active",
        nextFrom = from,
        nextFind = find,
        nextSd = sd,
        nextEd = ed,
        nextIsIncludeResel = filters.isIncludeResel ?? true,
        page = undefined,
    } = {}) => {
        router.get(
            route("system.products.index"),
            {
                filter: nextFilter,
                from: nextFrom,
                find: nextFind.trim(),
                sd: nextSd,
                ed: nextEd,
                isIncludeResel: nextIsIncludeResel,
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
        setFrom(filters.from ?? "all");
        setFind(filters.find ?? "");
        setSd(filters.sd ?? "");
        setEd(filters.ed ?? "");
    }, [filters.ed, filters.find, filters.from, filters.sd]);

    useEffect(() => {
        const trimmedFind = find.trim();
        const currentFind = (filters.find ?? "").trim();

        if (trimmedFind === currentFind) {
            return;
        }

        const timeout = setTimeout(() => {
            requestProducts({
                nextFilter: filters.filter ?? "Active",
                nextFrom: from,
                nextFind: trimmedFind,
                nextSd: sd,
                nextEd: ed,
                nextIsIncludeResel: filters.isIncludeResel ?? true,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [find]);

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

        const nextUrl = new URL(url);

        requestProducts({
            nextFilter: nextUrl.searchParams.get("filter") ?? filters.filter ?? "Active",
            nextFrom: nextUrl.searchParams.get("from") ?? from,
            nextFind: nextUrl.searchParams.get("find") ?? find,
            nextSd: nextUrl.searchParams.get("sd") ?? sd,
            nextEd: nextUrl.searchParams.get("ed") ?? ed,
            nextIsIncludeResel: (nextUrl.searchParams.get("isIncludeResel") ?? String(filters.isIncludeResel ?? true)) === "true",
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultSummary =
        products?.total > 0
            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} products`
            : "No products found";

    return (
        <AppLayout
            title="Products"
            header={
                <PageHeader>
                    <div className="flex justify-between items-center">
                        <div>
                            Products
                            <br />
                            <NavLink href={route("reseller.resel-product.index")}>
                                Browse
                            </NavLink>
                        </div>
                    </div>
                </PageHeader>
            }
        >
            <div>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-start gap-4">
                                <div className="flex flex-wrap items-center gap-2">
                                    <select
                                        value={filters.filter ?? "Active"}
                                        onChange={(e) =>
                                            requestProducts({
                                                nextFilter: e.target.value,
                                                nextFrom: from,
                                                nextFind: find,
                                                nextSd: sd,
                                                nextEd: ed,
                                                nextIsIncludeResel: filters.isIncludeResel ?? true,
                                            })
                                        }
                                        className="rounded-md border-gray-300 shadow-sm"
                                    >
                                        <option value="Active">Active</option>
                                        <option value="Disable">Disable</option>
                                        <option value="both">Both</option>
                                    </select>
                                    <select
                                        value={from}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setFrom(value);
                                            requestProducts({
                                                nextFilter: filters.filter ?? "Active",
                                                nextFrom: value,
                                                nextFind: find,
                                                nextSd: sd,
                                                nextEd: ed,
                                                nextIsIncludeResel: filters.isIncludeResel ?? true,
                                            });
                                        }}
                                        className="rounded-md border-gray-300 shadow-sm"
                                    >
                                        <option value="all">All</option>
                                        <option value="vendor">Vendor</option>
                                        <option value="reseller">Reseller</option>
                                    </select>
                                    {from === "reseller" ? (
                                        <label className="flex items-center gap-2 text-sm text-slate-700">
                                            <input
                                                type="checkbox"
                                                checked={filters.isIncludeResel ?? true}
                                                onChange={(e) =>
                                                    requestProducts({
                                                        nextFilter: filters.filter ?? "Active",
                                                        nextFrom: from,
                                                        nextFind: find,
                                                        nextSd: sd,
                                                        nextEd: ed,
                                                        nextIsIncludeResel: e.target.checked,
                                                    })
                                                }
                                            />
                                            Include Resel
                                        </label>
                                    ) : null}
                                </div>

                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <TextInput
                                        type="date"
                                        className="py-1"
                                        value={sd}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setSd(value);
                                            requestProducts({
                                                nextFilter: filters.filter ?? "Active",
                                                nextFrom: from,
                                                nextFind: find,
                                                nextSd: value,
                                                nextEd: ed,
                                                nextIsIncludeResel: filters.isIncludeResel ?? true,
                                            });
                                        }}
                                    />
                                    <TextInput
                                        type="date"
                                        className="py-1"
                                        value={ed}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setEd(value);
                                            requestProducts({
                                                nextFilter: filters.filter ?? "Active",
                                                nextFrom: from,
                                                nextFind: find,
                                                nextSd: sd,
                                                nextEd: value,
                                                nextIsIncludeResel: filters.isIncludeResel ?? true,
                                            });
                                        }}
                                    />
                                    <TextInput
                                        type="search"
                                        placeholder="Search products..."
                                        className="py-1"
                                        value={find}
                                        onChange={(e) => setFind(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            requestProducts();
                                        }}
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

                    <SectionInner>
                        <Table data={rows}>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Owner</th>
                                            <th>Price</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {rows.map((item, index) => (
                                            <tr key={item.id}>
                                                <td>{(products?.from ?? 1) + index}</td>
                                                <td>{item.id}</td>

                                                <td>
                                                    <NavLink
                                                        className="text-xs"
                                                        href={route(
                                                            "products.details",
                                                            {
                                                                id:
                                                                    item.id ??
                                                                    "",
                                                                slug:
                                                                    item.slug ??
                                                                    "",
                                                            }
                                                        )}
                                                    >
                                                        {item.thumbnail ? (
                                                            <img
                                                                width="30px"
                                                                height="30px"
                                                                src={
                                                                    item.thumbnail
                                                                }
                                                                alt=""
                                                                className="mr-2 rounded-full"
                                                            />
                                                        ) : null}
                                                        {item.name ?? "N/A"}
                                                    </NavLink>
                                                    <br />
                                                    <div className="text-xs border rounded inline-block">
                                                        {item.status ?? "N/A"}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div>
                                                        <div className="text-gray-700 ">
                                                            {item.owner_name}
                                                        </div>
                                                        {item.is_resel_count ? (
                                                            <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                                <i className="fas fa-caret-left"></i>
                                                                R
                                                            </span>
                                                        ) : null}
                                                        {item.resel_count ? (
                                                            <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                                {
                                                                    item.resel_count
                                                                }
                                                                <i className="fas fa-caret-right"></i>
                                                            </span>
                                                        ) : null}
                                                        <div className="text-xs">
                                                            {
                                                                item.belongs_to_type
                                                            }
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    {item.price ?? 0} TK
                                                    {item.discount_meta ? (
                                                        <div className="flex items-center text-center p-1 rounded bg-gray-100 text-xs">
                                                            D:{" "}
                                                            {
                                                                item
                                                                    .discount_meta
                                                                    .discount
                                                            }{" "}
                                                            |{" "}
                                                            {
                                                                item
                                                                    .discount_meta
                                                                    .off_percent
                                                            }
                                                            % off
                                                        </div>
                                                    ) : null}
                                                </td>
                                                <td>
                                                    {
                                                        item.created_at_formatted
                                                    }
                                                </td>
                                                <td>
                                                    <div className="flex">
                                                        <NavLink
                                                            href={route(
                                                                "system.products.edit",
                                                                {
                                                                    product:
                                                                        item.id,
                                                                }
                                                            )}
                                                        >
                                                            View
                                                        </NavLink>
                                                    </div>
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
                    </SectionInner>
                </SectionSection>
            </div>
        </AppLayout>
    );
}
