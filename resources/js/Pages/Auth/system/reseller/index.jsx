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
    const { widgets = [], resellers = {}, filters = {}, printUrl } = usePage().props;
    const [search, setSearch] = useState(filters.find ?? "");
    const [sd, setSd] = useState(filters.sd ?? "");
    const [ed, setEd] = useState(filters.ed ?? "");
    const rows = resellers.data ?? [];

    const requestResellers = ({
        nextSearch = search,
        nextFilter = filters.filter ?? "Active",
        nextSd = sd,
        nextEd = ed,
        page = undefined,
    } = {}) => {
        router.get(
            route("system.reseller.index"),
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
            requestResellers({
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

        requestResellers({
            nextSearch: nextUrl.searchParams.get("find") ?? search,
            nextFilter: nextUrl.searchParams.get("filter") ?? filters.filter ?? "Active",
            nextSd: nextUrl.searchParams.get("sd") ?? sd,
            nextEd: nextUrl.searchParams.get("ed") ?? ed,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = resellers?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [resellers?.links]);

    const resultSummary =
        resellers?.total > 0
            ? `Showing ${resellers?.from ?? 0}-${resellers?.to ?? 0} of ${resellers?.total ?? 0} resellers`
            : "No resellers found";

    return (
        <AppLayout title="Resellers" header={<PageHeader>Resellers</PageHeader>}>
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
                        <SectionHeader
                            title={
                                <div className="flex justify-between items-start gap-4">
                                    <div>
                                        <select
                                            value={filters.filter ?? "Active"}
                                            onChange={(e) =>
                                                requestResellers({
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

                                    <div className="flex flex-wrap items-center justify-end gap-2">
                                        <TextInput
                                            type="date"
                                            className="my-1 py-1"
                                            value={sd}
                                            onChange={(e) => {
                                                const value = e.target.value;

                                                setSd(value);
                                                requestResellers({
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
                                                requestResellers({
                                                    nextSearch: search,
                                                    nextFilter: filters.filter ?? "Active",
                                                    nextSd: sd,
                                                    nextEd: value,
                                                });
                                            }}
                                        />
                                        <TextInput
                                            type="search"
                                            placeholder="Search resellers..."
                                            className="my-1 py-1"
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                            onKeyDown={(e) => {
                                                if (e.key !== "Enter") {
                                                    return;
                                                }

                                                e.preventDefault();
                                                requestResellers();
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
                            <Foreach data={rows}>
                                <div>
                                    <Table data={rows}>
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Commission</th>
                                                <th>Category</th>
                                                <th>Product</th>
                                                <th>Join</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {rows.map((item, index) => (
                                                <tr key={item.id}>
                                                    <td>{(resellers?.from ?? 1) + index}</td>
                                                    <td>
                                                        {item.user_name}
                                                        <br />
                                                        <span className="text-xs">
                                                            {item.shop_name_bn}
                                                        </span>
                                                    </td>
                                                    <td>{item.status}</td>
                                                    <td>{item.system_get_comission}</td>
                                                    <td>{item.categories_count}</td>
                                                    <td>{item.products_count}</td>
                                                    <td>
                                                        {item.created_at_human}
                                                        <br />
                                                        <span className="text-xs">
                                                            {item.created_at_formatted}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <NavLink
                                                            href={route("system.reseller.edit", {
                                                                id: item.id,
                                                                filter: filters.filter ?? "Active",
                                                            })}
                                                        >
                                                            edit
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
