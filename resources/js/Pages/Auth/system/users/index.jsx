import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import Container from "../../../../components/dashboard/Container";
import Foreach from "../../../../components/dashboard/Foreach";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Div from "../../../../components/dashboard/overview/Div";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";

export default function Index() {
    const { widgets = [], users, filters = {}, printUrl } = usePage().props;
    const [search, setSearch] = useState(filters.search ?? "");
    const [sd, setSd] = useState(filters.sd ?? "");
    const [ed, setEd] = useState(filters.ed ?? "");

    const requestUsers = ({
        nextSearch = search,
        nextSd = sd,
        nextEd = ed,
        page = undefined,
    } = {}) => {
        router.get(
            route("system.users.view"),
            {
                search: nextSearch.trim(),
                sd: nextSd,
                ed: nextEd,
                page,
            },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    };

    useEffect(() => {
        setSearch(filters.search ?? "");
        setSd(filters.sd ?? "");
        setEd(filters.ed ?? "");
    }, [filters.ed, filters.sd, filters.search]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.search ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timer = window.setTimeout(() => {
            requestUsers({
                nextSearch: trimmedSearch,
                nextSd: sd,
                nextEd: ed,
            });
        }, 400);

        return () => window.clearTimeout(timer);
    }, [search]);

    const applyFilters = () => {
        requestUsers();
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        requestUsers({
            nextSearch: nextUrl.searchParams.get("search") ?? search,
            nextSd: nextUrl.searchParams.get("sd") ?? sd,
            nextEd: nextUrl.searchParams.get("ed") ?? ed,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = users?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [users?.links]);

    const resultSummary =
        users?.total > 0
            ? `Showing ${users?.from ?? 0}-${users?.to ?? 0} of ${users?.total ?? 0} users`
            : "No users found";

    return (
        <AppLayout
            title="Users"
            header={<PageHeader>Users</PageHeader>}
        >
            <div>
                <Container>
                    <SectionSection>
                        <div className="grid grid-cols-6 gap-6">
                            {widgets.map((widget) => (
                                <Div
                                    key={widget.head}
                                    title={widget.head}
                                    content={widget.data}
                                />
                            ))}
                        </div>
                    </SectionSection>

                    <SectionSection>
                        <SectionHeader
                            title=""
                            content={
                                <form
                                    className="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-end"
                                    onSubmit={(e) => {
                                        e.preventDefault();
                                        applyFilters();
                                    }}
                                >
                                    <div className="flex flex-wrap items-center justify-end gap-2">
                                        <TextInput
                                            type="date"
                                            className="py-1"
                                            value={sd}
                                            onChange={(e) => {
                                                const value = e.target.value;
                                                const nextEd = ed;

                                                setSd(value);
                                                requestUsers({
                                                    nextSearch: search,
                                                    nextSd: value,
                                                    nextEd,
                                                });
                                            }}
                                        />
                                        <TextInput
                                            type="date"
                                            className="py-1"
                                            value={ed}
                                            onChange={(e) => {
                                                const value = e.target.value;
                                                const nextSd = sd;

                                                setEd(value);
                                                requestUsers({
                                                    nextSearch: search,
                                                    nextSd,
                                                    nextEd: value,
                                                });
                                            }}
                                        />
                                        <TextInput
                                            type="search"
                                            placeholder="Search users..."
                                            className="py-1"
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                            onKeyDown={(e) => {
                                                if (e.key !== "Enter") {
                                                    return;
                                                }

                                                e.preventDefault();
                                                applyFilters();
                                            }}
                                        />
                                        <PrimaryButton
                                            type="button"
                                            onClick={() => window.open(printUrl, "_blank")}
                                        >
                                            <i className="fas fa-print"></i>
                                        </PrimaryButton>
                                    </div>
                                </form>
                            }
                        />

                        <SectionInner>
                            <Foreach data={users?.data ?? []}>
                                <div>
                                    <Table data={users?.data ?? []}>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Ref & Reference</th>
                                                <th>Role</th>
                                                <th>Permissions</th>
                                                <th>VIP</th>
                                                <th>Order</th>
                                                <th>Wallet</th>
                                                <th>Created</th>
                                                <th>A/C</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {(users?.data ?? []).map((user, index) => (
                                                <tr key={user.id}>
                                                    <td>{(users?.from ?? 1) + index}</td>
                                                    <td>{user.id ?? "N/A"}</td>
                                                    <td>
                                                        {user.name ?? "N/A"}
                                                        <br />
                                                        <b className="text-xs">
                                                            {user.email ?? "N/A"}
                                                        </b>
                                                    </td>
                                                    <td>
                                                        {user.ref ?? "N/A"}
                                                        <br />
                                                        <span className="px-2 text-xs rounded border">
                                                            {user.reference ?? "Not Found"} &gt;{" "}
                                                            {user.reference_owner_name}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div className="flex">
                                                            {user.roles.map((role) => (
                                                                <div
                                                                    key={`${user.id}-${role}`}
                                                                    className="px-1 rounded border m-1 text-sm"
                                                                >
                                                                    {role}
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </td>
                                                    <td>{user.permissions_count}</td>
                                                    <td>
                                                        <div className={user.vip_status.className}>
                                                            {user.vip_status.label}
                                                        </div>
                                                    </td>
                                                    <td>{user.orders_count}</td>
                                                    <td>{user.coin}</td>
                                                    <td>{user.created_at_formatted}</td>
                                                    <td>
                                                        <div className="flex">
                                                            <NavLink
                                                                href={route(
                                                                    "system.users.edit",
                                                                    {
                                                                        id: user.id,
                                                                    }
                                                                )}
                                                            >
                                                                <i className="fa-solid fa-pen mr-2"></i> Edit
                                                            </NavLink>
                                                            <NavLink href="#">
                                                                <i className="fa-solid fa-eye mr-2"></i> view
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
                                </div>
                            </Foreach>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </div>
        </AppLayout>
    );
}
