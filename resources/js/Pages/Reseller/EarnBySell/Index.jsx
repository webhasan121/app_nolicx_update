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
import { formatCurrency, formatTk } from "../../../utils/formatAmount";
import Table from "../../../components/dashboard/table/Table";
import NavLink from "../../../components/NavLink";
import ProductName from "../../../components/ProductName";
import useTranslation from "../../../hooks/useTranslation";
import { todayInputDate } from "../../../utils/dateInput";

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
    const { t } = useTranslation();
    const [nav, setNav] = useState(filters.nav ?? "sold");
    const [fd, setFd] = useState(filters.fd ?? "");
    const [lastDate, setLastDate] = useState(filters.lastDate ?? "");
    const [search, setSearch] = useState(filters.search ?? "");
    const today = todayInputDate();

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
                only: ["filters", "overview", "products", "counts", "printUrl"],
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
    const hasActiveFilters = Boolean(search.trim() || fd || lastDate || nav !== "sold");

    const updateStartDate = (value) => {
        setFd(value);
        requestProducts({ fd: value, lastDate, page: undefined });
    };

    const updateLastDate = (value) => {
        setLastDate(value);
        requestProducts({ fd, lastDate: value, page: undefined });
    };

    return (
        <AppLayout title={t("Sell and Profit")}>
            <Head title={t("Sell and Profit")} />

            <Container>
                <p className="text-xl">{t("Sell and Profit")}</p>

                <OverviewSection>
                    <OverviewDiv
                        title={t("Total Sell")}
                        content={formatTk(overview.totalSell)}
                    />
                    <OverviewDiv
                        title={t("Profit")}
                        content={formatTk(overview.tp)}
                    />
                    <OverviewDiv
                        title={t("Neet")}
                        content={formatTk(overview.tn)}
                    />
                    <OverviewDiv
                        title={t("Shop")}
                        content={`${overview.shop ?? 0}`}
                    />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center md:shrink-0">
                                    <select
                                        value={nav}
                                        onChange={(e) =>
                                            changeNav(e.target.value)
                                        }
                                        className="w-full rounded py-1 sm:w-auto md:min-w-[160px]"
                                    >
                                        <option value="all">{t("Both")}</option>
                                        <option value="sold">{t("Sold")}</option>
                                        <option value="selling">{t("On-Selling")}</option>
                                    </select>
                                </div>

                                <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center md:justify-end">
                                    <div>
                                        <input
                                            type="date"
                                            value={fd || today}
                                            onChange={(e) =>
                                                updateStartDate(e.target.value)
                                            }
                                            className="w-full rounded py-1 text-sm sm:w-auto md:min-w-[150px]"
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
                                            className="w-full rounded py-1 text-sm sm:w-auto md:min-w-[150px]"
                                            title={formattedLastDate}
                                        />
                                    </div>
                                    {hasActiveFilters ? (
                                        <button
                                            type="button"
                                            className="w-full rounded border px-3 py-2 text-sm text-slate-700 sm:w-auto"
                                            onClick={() => {
                                                setSearch("");
                                                setNav("sold");
                                                setFd("");
                                                setLastDate("");
                                                requestProducts({
                                                    nav: "sold",
                                                    fd: "",
                                                    lastDate: "",
                                                    search: "",
                                                    page: undefined,
                                                });
                                            }}
                                        >{t("Reset")}</button>
                                    ) : null}
                                    <div className="flex w-full items-center gap-2 sm:w-auto md:min-w-[260px]">
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
                                            className="w-full py-1 sm:w-auto"
                                            placeholder={t("Search products...")}
                                        />
                                        <PrimaryButton
                                            type="button"
                                            className="h-[34px] shrink-0 justify-center px-4 py-1 text-sm"
                                            onClick={() => window.open(printUrl, "_blank")}
                                        >
                                            <i className="fas fa-print"></i>
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </div>
                        }
                    />
                    <hr />
                    <SectionInner>
                        <Table
                            data={products.data ?? []}
                            className="p-2 [&_table]:min-w-[980px]"
                        >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{t("ID")}</th>
                                    <th>{t("Product")}</th>
                                    <th>{t("Flow")}</th>
                                    <th>{t("Owner")}</th>
                                    <th>{t("Price")}</th>
                                    <th>{t("Created")}</th>
                                    <th>{t("Action")}</th>
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
                                                <ProductName value={item.product_name} />
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
                                            {formatCurrency(item.product_price)}{item.offer_type ? (
                                                <div className="flex items-center text-center p-1 rounded bg-gray-100 text-xs">{t("D:")}{item.discount} |{" "}
                                                    {item.discount_percent}{t("% off")}</div>
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
                                <div className="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div className="text-sm text-slate-700">
                                        {resultSummary}
                                    </div>
                                    <div className="flex w-full justify-center sm:w-auto sm:justify-end">
                                        <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="border-r border-slate-200 px-3 py-2 text-xs text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 sm:px-4 sm:text-sm"
                                                onClick={() => goToPage(pagination.prev?.url)}
                                            >{t("Previous")}</button>
                                            {pagination.pages.map((link, idx) => (
                                                <button
                                                    key={`${link.label}-${idx}`}
                                                    type="button"
                                                    disabled={!link.url}
                                                    className={`min-w-8 border-r border-slate-200 px-3 py-2 text-xs font-semibold transition sm:min-w-10 sm:px-4 sm:text-sm ${
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
                                                className="px-3 py-2 text-xs text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 sm:px-4 sm:text-sm"
                                                onClick={() => goToPage(pagination.next?.url)}
                                            >{t("Next")}</button>
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
