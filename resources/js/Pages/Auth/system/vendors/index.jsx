import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Foreach from "../../../../components/dashboard/Foreach";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Div from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

const FILTERS = ["*", "Active", "Pending", "Disabled", "Suspended"];

export default function Index() {
    const { widgets = [], vendors, filters = {}, printUrl } = usePage().props;
    const [search, setSearch] = useState(filters.find ?? "");
    const [sd, setSd] = useState(filters.sd ?? "");
    const [ed, setEd] = useState(filters.ed ?? "");

    const requestVendors = ({
        nextSearch = search,
        nextFilter = filters.filter ?? "Active",
        nextSd = sd,
        nextEd = ed,
        page = undefined,
    } = {}) => {
        router.get(
            route("system.vendor.index"),
            {
                filter: nextFilter,
                find: nextSearch.trim(),
                sd: nextSd,
                ed: nextEd,
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
        setSearch(filters.find ?? "");
        setSd(filters.sd ?? "");
        setEd(filters.ed ?? "");
    }, [filters.ed, filters.find, filters.sd]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            requestVendors({
                nextSearch: trimmedSearch,
                nextFilter: filters.filter ?? "Active",
                nextSd: sd,
                nextEd: ed,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        requestVendors({
            nextSearch: nextUrl.searchParams.get("find") ?? search,
            nextFilter: nextUrl.searchParams.get("filter") ?? filters.filter ?? "Active",
            nextSd: nextUrl.searchParams.get("sd") ?? sd,
            nextEd: nextUrl.searchParams.get("ed") ?? ed,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = vendors?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [vendors?.links]);

    const resultSummary =
        vendors?.total > 0
            ? `Showing ${vendors?.from ?? 0}-${vendors?.to ?? 0} of ${vendors?.total ?? 0} vendors`
            : "No vendors found";

    return (
        <AppLayout title="Vendors" header={<PageHeader>Vendors</PageHeader>}>
            <div>
                <Container>
                    <SectionSection>
                        <OverviewSection>
                            {widgets.map((widget) => (
                                <Div
                                    key={widget.title}
                                    title={widget.title}
                                    content={widget.content}
                                />
                            ))}
                        </OverviewSection>
                    </SectionSection>

                    <SectionSection>
                        <div className="flex justify-between items-start gap-4">
                            <div>
                                <select
                                    value={filters.filter ?? "Active"}
                                    onChange={(e) =>
                                        requestVendors({
                                            nextSearch: search,
                                            nextFilter: e.target.value,
                                            nextSd: sd,
                                            nextEd: ed,
                                        })
                                    }
                                    className="rounded-md border-gray-300 shadow-sm"
                                >
                                    {FILTERS.map((item) => (
                                        <option key={item} value={item}>
                                            {item === "*" ? "All" : item}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="flex flex-wrap items-center gap-2">
                                <TextInput
                                    type="date"
                                    className="my-1 py-1"
                                    value={sd}
                                    onChange={(e) => {
                                        const value = e.target.value;

                                        setSd(value);
                                        requestVendors({
                                            nextSearch: search,
                                            nextFilter: filters.filter ?? "Active",
                                            nextSd: value,
                                            nextEd: ed,
                                        });
                                    }}
                                />
                                <TextInput
                                    type="date"
                                    className="my-1 py-1"
                                    value={ed}
                                    onChange={(e) => {
                                        const value = e.target.value;

                                        setEd(value);
                                        requestVendors({
                                            nextSearch: search,
                                            nextFilter: filters.filter ?? "Active",
                                            nextSd: sd,
                                            nextEd: value,
                                        });
                                    }}
                                />
                                <TextInput
                                    type="search"
                                    placeholder="Search vendors..."
                                    className="my-1 py-1"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) => {
                                        if (e.key !== "Enter") {
                                            return;
                                        }

                                        e.preventDefault();
                                        requestVendors();
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

                        <SectionInner>
                            <Foreach data={vendors?.data ?? []}>
                                <div>
                                    <Table data={vendors?.data ?? []}>
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Contact</th>
                                                <th>Status</th>
                                                <th>Commission</th>
                                                <th>Product</th>
                                                <th>Join</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        {(vendors?.data ?? []).map((vendor, index) => (
                                            <tr key={vendor.id}>
                                                <td>{(vendors?.from ?? 1) + index}</td>
                                                <td>{vendor.id}</td>
                                                <td>
                                                    <div className="text-nowrap">
                                                        {vendor.user_name}
                                                    </div>
                                                    <div className="badge badge-info text-nowrap">
                                                        {vendor.shop_name_en}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div className="text-nowrap">
                                                        {vendor.email}
                                                    </div>
                                                    <div className="text-nowrap">
                                                        {vendor.phone}
                                                    </div>
                                                    <div className="text-nowrap">
                                                        {vendor.location}
                                                    </div>
                                                </td>
                                                <td>{vendor.status}</td>
                                                <td>
                                                    <span className="badge badge-success">
                                                        {" "}
                                                        {vendor.system_get_comission}{" "}
                                                    </span>{" "}
                                                    %
                                                </td>
                                                <td>
                                                    <span className="badge badge-info">
                                                        {vendor.products_count}
                                                    </span>
                                                    <NavLink
                                                        href={route("system.products.index", {
                                                            find: vendor.id,
                                                            from: "vendor",
                                                        })}
                                                    >
                                                        View
                                                    </NavLink>
                                                </td>
                                                <td>{vendor.created_at_formatted}</td>
                                                <td>
                                                    <NavLink
                                                        href={route(
                                                            "system.vendor.settings",
                                                            {
                                                                id: vendor.id,
                                                            }
                                                        )}
                                                    >
                                                        Edit
                                                    </NavLink>
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
                                </div>
                            </Foreach>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </div>
        </AppLayout>
    );
}
