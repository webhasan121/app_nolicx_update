import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import NavLink from "../../../../../components/NavLink";
import NavLinkBtn from "../../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../../components/PrimaryButton";
import TextInput from "../../../../../components/TextInput";
import Container from "../../../../../components/dashboard/Container";
import Foreach from "../../../../../components/dashboard/Foreach";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../../components/dashboard/section/Section";
import Table from "../../../../../components/dashboard/table/Table";

export default function Index() {
    const {
        nav = "Active",
        filters = {},
        packages = {},
        printUrl,
    } = usePage().props;
    const rows = packages.data ?? [];
    const [search, setSearch] = useState(filters.find ?? "");

    const requestPackages = ({
        nextNav = nav,
        nextSearch = search,
        page = undefined,
    } = {}) => {
        router.get(
            route("system.vip.index"),
            {
                nav: nextNav,
                find: nextSearch.trim(),
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
    }, [filters.find]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            requestPackages({
                nextNav: nav,
                nextSearch: trimmedSearch,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const handleTrash = (id) => {
        router.post(route("system.vip.trash", { id }));
    };

    const handleRestore = (id) => {
        router.post(route("system.vip.restore", { id }));
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        requestPackages({
            nextNav: nextUrl.searchParams.get("nav") ?? nav,
            nextSearch: nextUrl.searchParams.get("find") ?? search,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = packages?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [packages?.links]);

    const resultSummary =
        packages?.total > 0
            ? `Showing ${packages?.from ?? 0}-${packages?.to ?? 0} of ${packages?.total ?? 0} packages`
            : "No packages found";

    return (
        <AppLayout
            title="VIP"
            header={
                <PageHeader>
                    VIP
                    <br />
                    <div>
                        <NavLink
                            href={route("system.vip.index")}
                            active={route().current("system.vip.index")}
                        >
                            <i className="fa-solid fa-up-right-from-square me-2"></i> Package
                        </NavLink>
                        <NavLink
                            href={route("system.vip.users")}
                            active={route().current("system.vip.users")}
                        >
                            <i className="fa-solid fa-up-right-from-square me-2"></i> User
                        </NavLink>
                    </div>
                </PageHeader>
            }
        >
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex flex-wrap items-start justify-between gap-4">
                                <NavLinkBtn href={route("system.vip.crate")}>
                                    New
                                </NavLinkBtn>

                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <TextInput
                                        type="search"
                                        placeholder="Search packages..."
                                        className="my-1 py-1"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            requestPackages();
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
                        content={
                            <>
                                <NavLink
                                    href={route("system.vip.index", {
                                        nav: "Active",
                                        find: filters.find ?? "",
                                    })}
                                    active={nav === "Active"}
                                >
                                    Active
                                </NavLink>
                                <NavLink
                                    href={route("system.vip.index", {
                                        nav: "Trash",
                                        find: filters.find ?? "",
                                    })}
                                    active={nav === "Trash"}
                                >
                                    Trash
                                </NavLink>
                            </>
                        }
                    />

                    <SectionInner>
                        <Foreach data={rows}>
                            <div>
                                <Table data={rows}>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Timer</th>
                                            <th>Coin</th>
                                            <th>Sell</th>
                                            <th>Earn</th>
                                            <th>Created</th>
                                            <th>A/C</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {rows.map((item, index) => (
                                            <tr key={item.id}>
                                                <td>{(packages?.from ?? 1) + index}</td>
                                                <td>
                                                    <div className="position-relative">
                                                        {item.name}
                                                    </div>
                                                </td>
                                                <td>{item.price} TK</td>
                                                <td>{item.countdown} Minute</td>
                                                <td>
                                                    <div>D - {item.coin}</div>
                                                    <div>M - {item.m_coin}</div>
                                                    <hr className="my-1" />
                                                    <div>Ref - {item.ref_owner_get_coin}</div>
                                                </td>
                                                <td>{item.users_count ?? "0"}</td>
                                                <td>{item.earn}</td>
                                                <td>
                                                    <div>{item.created_at_human}</div>
                                                    <div className="text-xs">
                                                        {item.created_at_formatted}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div className="flex">
                                                        <NavLinkBtn
                                                            href={route(
                                                                "system.package.edit",
                                                                { packages: item.id }
                                                            )}
                                                            className="me-2"
                                                        >
                                                            View
                                                        </NavLinkBtn>

                                                        {nav === "Trash" ? (
                                                            <NavLinkBtn
                                                                href="#"
                                                                onClick={(e) => {
                                                                    e.preventDefault();
                                                                    handleRestore(item.id);
                                                                }}
                                                            >
                                                                Restore
                                                            </NavLinkBtn>
                                                        ) : (
                                                            <DangerButton
                                                                type="button"
                                                                onClick={() => handleTrash(item.id)}
                                                            >
                                                                Trash
                                                            </DangerButton>
                                                        )}
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
        </AppLayout>
    );
}
