import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../../Layouts/App";
import NavLink from "../../../../../components/NavLink";
import NavLinkBtn from "../../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../../components/PrimaryButton";
import TextInput from "../../../../../components/TextInput";
import Container from "../../../../../components/dashboard/Container";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../../components/dashboard/section/Section";
import Table from "../../../../../components/dashboard/table/Table";
import useTranslation from "../../../../../hooks/useTranslation";
import { ActionIconButton, ActionIconLink } from "../../../../../components/ActionIcon";

export default function Index() {
    const { t } = useTranslation();
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
                only: ["nav", "filters", "packages", "printUrl"],
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
        if (!window.confirm("Are you sure you want to move this package to trash?")) {
            return;
        }

        router.post(route("system.vip.trash", { id }));
    };

    const handleRestore = (id) => {
        router.post(route("system.vip.restore", { id }));
    };

    const handleStatusToggle = (item) => {
        const nextStatus = Number(item.status ?? 0) === 1 ? 0 : 1;
        const action = nextStatus === 1 ? "activate" : "inactivate";

        if (!window.confirm(`Are you sure you want to ${action} this package?`)) {
            return;
        }

        router.post(`/dashboard/system/packages/${item.id}/status`, {
            status: nextStatus,
        });
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
            title={t("VIP")}
            header={
                <PageHeader>{t("VIP")}<br />
                    <div>
                        <NavLink
                            href={route("system.vip.index")}
                            active={route().current("system.vip.index")}
                        >
                            <i className="fa-solid fa-up-right-from-square me-2"></i>{t("Package")}</NavLink>
                        <NavLink
                            href={route("system.vip.users")}
                            active={route().current("system.vip.users")}
                        >
                            <i className="fa-solid fa-up-right-from-square me-2"></i>{t("User")}</NavLink>
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
                                        placeholder={t("Search packages...")}
                                        className="py-1 my-1"
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
                                >{t("Active")}</NavLink>
                                <NavLink
                                    href={route("system.vip.index", {
                                        nav: "Trash",
                                        find: filters.find ?? "",
                                    })}
                                    active={nav === "Trash"}
                                >{t("Trash")}</NavLink>
                            </>
                        }
                    />

                    <SectionInner>
                        <div>
                            <Table data={rows}>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{t("Name")}</th>
                                            <th>{t("Price")}</th>
                                            <th>{t("Timer")}</th>
                                            <th>{t("Coin")}</th>
                                            <th>{t("Sell")}</th>
                                            <th>{t("Earn")}</th>
                                            <th>{t("Status")}</th>
                                            <th>{t("Created")}</th>
                                            <th>{t("A/C")}</th>
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
                                                <td>{item.price}{t("TK")}</td>
                                                <td>{item.countdown}{t("Minute")}</td>
                                                <td>
                                                    <div>{t("D -")}{item.coin}</div>
                                                    <div>{t("M -")}{item.m_coin}</div>
                                                    <hr className="my-1" />
                                                    <div>{t("Ref -")}{item.ref_owner_get_coin}</div>
                                                </td>
                                                <td>{item.users_count ?? "0"}</td>
                                                <td>{item.earn}</td>
                                                <td>
                                                    {nav === "Trash" ? (
                                                        <span className="inline-flex px-3 py-1 text-xs text-gray-600 bg-gray-100 rounded">
                                                            -
                                                        </span>
                                                    ) : (
                                                        <button
                                                            type="button"
                                                            onClick={() => handleStatusToggle(item)}
                                                            className={`inline-flex rounded px-3 py-1 text-xs font-bold text-white ${
                                                                Number(item.status ?? 0) === 1
                                                                    ? "bg-green-600"
                                                                    : "bg-gray-500"
                                                            }`}
                                                        >
                                                            {Number(item.status ?? 0) === 1 ? "Active" : "Inactive"}
                                                        </button>
                                                    )}
                                                </td>
                                                <td>
                                                    <div>{item.created_at_human}</div>
                                                    <div className="text-xs">
                                                        {item.created_at_formatted}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div className="flex items-center gap-1">
                                                        <ActionIconLink
                                                            href={route(
                                                                "system.package.edit",
                                                                { packages: item.id }
                                                            )}
                                                            action="view"
                                                            title={t("View")}
                                                        />

                                                        {nav === "Trash" ? (
                                                            <ActionIconButton
                                                                action="restore"
                                                                title={t("Restore")}
                                                                onClick={(e) => {
                                                                    e.preventDefault();
                                                                    handleRestore(item.id);
                                                                }}
                                                            />
                                                        ) : (
                                                            <ActionIconButton
                                                                action="trash"
                                                                title={t("Trash")}
                                                                onClick={() => handleTrash(item.id)}
                                                            />
                                                        )}
                                                    </div>
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
                                                    >{t("Previous")}</button>
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
                                                        className="px-4 py-2 text-sm transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                        onClick={() => goToPage(pagination.next?.url)}
                                                    >{t("Next")}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            ) : null}
                        </div>
                    </SectionInner>
                </SectionSection>
            </Container>
        </AppLayout>
    );
}
