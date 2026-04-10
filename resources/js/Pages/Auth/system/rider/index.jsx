import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Foreach from "../../../../components/dashboard/Foreach";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import TextInput from "../../../../components/TextInput";

const FILTERS = ["all", "Active", "Pending", "Disabled", "Suspended"];

export default function Index() {
    const { filters = {}, widgets = [], riders = {}, printUrl } = usePage().props;
    const [search, setSearch] = useState(filters.find ?? "");
    const [sd, setSd] = useState(filters.sd ?? "");
    const [ed, setEd] = useState(filters.ed ?? "");

    const requestRiders = ({
        nextSearch = search,
        nextCondition = filters.condition ?? "Active",
        nextSd = sd,
        nextEd = ed,
        page = undefined,
    } = {}) => {
        router.get(
            route("system.rider.index"),
            {
                condition: nextCondition,
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
            requestRiders({
                nextSearch: trimmedSearch,
                nextCondition: filters.condition ?? "Active",
                nextSd: sd,
                nextEd: ed,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const pagination = useMemo(() => {
        const links = riders?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [riders?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        requestRiders({
            nextSearch: nextUrl.searchParams.get("find") ?? search,
            nextCondition: nextUrl.searchParams.get("condition") ?? filters.condition ?? "Active",
            nextSd: nextUrl.searchParams.get("sd") ?? sd,
            nextEd: nextUrl.searchParams.get("ed") ?? ed,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultSummary =
        riders?.total > 0
            ? `Showing ${riders?.from ?? 0}-${riders?.to ?? 0} of ${riders?.total ?? 0} riders`
            : "No riders found";

    return (
        <AppLayout
            title="Rider - Delevary Man"
            header={<PageHeader>Rider - Delevary Man</PageHeader>}
        >
            <div>
                <Container>
                    <OverviewSection>
                        {widgets.map((item) => (
                            <OverviewDiv
                                key={item.title}
                                title={item.title}
                                content={item.content}
                            />
                        ))}
                    </OverviewSection>
                </Container>

                <Container>
                    <SectionSection>
                        <SectionHeader
                            title="Riders"
                            content={
                                <>
                                    <div className="flex justify-between items-start gap-4">
                                        <div>
                                            <select
                                                value={filters.condition ?? "Active"}
                                                onChange={(e) =>
                                                    requestRiders({
                                                        nextSearch: search,
                                                        nextCondition: e.target.value,
                                                        nextSd: sd,
                                                        nextEd: ed,
                                                    })
                                                }
                                                className="rounded-md border-gray-300 shadow-sm"
                                            >
                                                {FILTERS.map((item) => (
                                                    <option key={item} value={item}>
                                                        {item === "all" ? "All" : item}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>

                                        <div className="flex flex-wrap items-center justify-end gap-2">
                                            <TextInput
                                                type="date"
                                                className="py-1"
                                                value={sd}
                                                onChange={(e) => {
                                                    const value = e.target.value;

                                                    setSd(value);
                                                    requestRiders({
                                                        nextSearch: search,
                                                        nextCondition: filters.condition ?? "Active",
                                                        nextSd: value,
                                                        nextEd: ed,
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
                                                    requestRiders({
                                                        nextSearch: search,
                                                        nextCondition: filters.condition ?? "Active",
                                                        nextSd: sd,
                                                        nextEd: value,
                                                    });
                                                }}
                                            />
                                            <TextInput
                                                type="search"
                                                placeholder="Search riders..."
                                                className="py-1"
                                                value={search}
                                                onChange={(e) => setSearch(e.target.value)}
                                                onKeyDown={(e) => {
                                                    if (e.key !== "Enter") {
                                                        return;
                                                    }

                                                    e.preventDefault();
                                                    requestRiders();
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
                                </>
                            }
                        />

                        <SectionInner>
                            <Foreach data={riders?.data ?? []}>
                                <Table data={riders?.data ?? []}>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Join Data</th>
                                            <th>A/C</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {(riders?.data ?? []).map((item) => (
                                            <tr key={item.id}>
                                                <td>{item.sl}</td>
                                                <td>{item.user_name}</td>
                                                <td>{item.status}</td>
                                                <td>
                                                    {item.created_at_formatted}
                                                    <br />
                                                    <span className="text-xs">
                                                        {item.created_at_human}
                                                    </span>
                                                </td>
                                                <td>
                                                    <NavLink href={route("system.rider.edit", { id: item.id })}>
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
                            </Foreach>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </div>
        </AppLayout>
    );
}
