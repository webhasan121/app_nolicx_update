import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import TextInput from "../../../../components/TextInput";
import PrimaryButton from "../../../../components/PrimaryButton";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import { formatCurrency, formatTk } from "../../../../utils/formatAmount";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import ProductName from "../../../../components/ProductName";
import useTranslation from "../../../../hooks/useTranslation";
import { todayInputDate } from "../../../../utils/dateInput";

const statusClass = {
    Pending: "text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900",
    Accept: "text-xs p-1 border rounded-md bg-green-200 text-green-900",
    Picked: "text-xs p-1 border rounded-md bg-lime-200 text-lime-900",
    Delivery: "text-xs p-1 border rounded-md bg-sky-200 text-sky-900",
    Delivered: "text-xs p-1 border rounded-md bg-blue-200 text-blue-900",
    Confirm: "text-xs p-1 border rounded-md bg-indigo-200 text-indigo-900",
    Hold: "text-xs p-1 border rounded-md bg-gray-200 text-gray-900",
    Cancel: "text-xs p-1 border rounded-md bg-red-200 text-red-900",
    Cancelled: "text-xs p-1 border rounded-md bg-red-200 text-red-900",
};

function buildParams(filters, page = null) {
    const params = {
        nav: filters?.nav ?? "sold",
        fd: filters?.fd_value ?? filters?.fd ?? "",
        lastDate: filters?.lastDate_value ?? filters?.lastDate ?? "",
        user_type: filters?.user_type ?? "user",
        find: filters?.find ?? "",
    };

    if (page) {
        params.page = page;
    }

    return params;
}

export default function Index({ filters, overview, products, printUrl }) {
    const { t } = useTranslation();
    const [fd, setFd] = useState(filters?.fd_value ?? "");
    const [lastDate, setLastDate] = useState(filters?.lastDate_value ?? "");
    const [search, setSearch] = useState(filters?.find ?? "");
    const today = todayInputDate();

    useEffect(() => {
        setFd(filters?.fd_value ?? "");
        setLastDate(filters?.lastDate_value ?? "");
        setSearch(filters?.find ?? "");
    }, [filters?.fd_value, filters?.find, filters?.lastDate_value]);

    const visit = (nextFilters, page = null) => {
        router.get(route("system.earn.index"), buildParams(nextFilters, page), {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            only: ["filters", "overview", "products", "printUrl"],
        });
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }
        const nextUrl = new URL(url);
        visit(
            {
                nav: nextUrl.searchParams.get("nav") ?? filters?.nav,
                fd_value: nextUrl.searchParams.get("fd") ?? fd,
                lastDate_value: nextUrl.searchParams.get("lastDate") ?? lastDate,
                user_type: nextUrl.searchParams.get("user_type") ?? filters?.user_type,
                find: nextUrl.searchParams.get("find") ?? search,
            },
            nextUrl.searchParams.get("page") ?? undefined
        );
    };

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters?.find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            visit({
                ...filters,
                fd_value: fd,
                lastDate_value: lastDate,
                find: trimmedSearch,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const pagination = useMemo(() => {
        const links = products?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [products?.links]);
    const hasActiveFilters = Boolean(
        search.trim() ||
            fd ||
            lastDate ||
            (filters?.nav ?? "sold") !== "sold" ||
            (filters?.user_type ?? "user") !== "user"
    );

    return (
        <AppLayout title={t("Earn By Sell")} header={<PageHeader>{t("Earn By Sell")}</PageHeader>}>
            <Head title={t("Earn By Sell")} />
            <Hr />
            <Container>
                <p className="text-xl">{t("Sell and Profit")}</p>
                <OverviewSection>
                    <OverviewDiv title={t("Total Sell")} content={formatTk(overview?.totalSell)} />
                    <OverviewDiv title={t("Profit")} content={formatTk(overview?.tp)} />
                    <OverviewDiv title={t("Neet")} content={formatTk(overview?.tn)} />
                    <OverviewDiv title={t("Shop")} content={overview?.shop ?? 0} />
                    <OverviewDiv title={t("Vendor Shop")} content={overview?.tpr ?? 0} />
                    <OverviewDiv title={t("Reseller Shop")} content={overview?.tprr ?? 0} />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div className="flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center xl:w-auto">
                                    <select
                                        value={filters?.nav ?? "sold"}
                                        onChange={(e) =>
                                            visit({
                                                ...filters,
                                                nav: e.target.value,
                                                fd_value: fd,
                                                lastDate_value: lastDate,
                                                find: search,
                                            })
                                        }
                                        className="h-10 w-full rounded py-1 sm:w-auto"
                                    >
                                        <option value="all">{t("Both")}</option>
                                        <option value="sold">{t("Sold")}</option>
                                        <option value="selling">{t("On-Selling")}</option>
                                    </select>

                                    <TextInput
                                        type="date"
                                        className="h-10 w-full py-1 sm:w-auto"
                                        value={fd || today}
                                        onChange={(e) => {
                                            const value = e.target.value;

                                            setFd(value);
                                            visit({
                                                ...filters,
                                                fd_value: value,
                                                lastDate_value: lastDate,
                                                find: search,
                                            });
                                        }}
                                    />
                                    <TextInput
                                        type="date"
                                        className="h-10 w-full py-1 sm:w-auto"
                                        value={lastDate}
                                        onChange={(e) => {
                                            const value = e.target.value;

                                            setLastDate(value);
                                            visit({
                                                ...filters,
                                                fd_value: fd,
                                                lastDate_value: value,
                                                find: search,
                                            });
                                        }}
                                    />
                                    <TextInput
                                        type="search"
                                        placeholder={t("Search products...")}
                                        className="h-10 w-full py-1 sm:w-56"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            visit({
                                                ...filters,
                                                fd_value: fd,
                                                lastDate_value: lastDate,
                                                find: search.trim(),
                                            });
                                        }}
                                    />
                                    <PrimaryButton
                                        type="button"
                                        className="inline-flex w-auto justify-center self-start"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    {hasActiveFilters ? (
                                        <button
                                            type="button"
                                            className="inline-flex h-10 w-auto items-center justify-center self-start rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50"
                                            onClick={() => {
                                                setFd("");
                                                setLastDate("");
                                                setSearch("");
                                                visit({
                                                    nav: "sold",
                                                    fd_value: "",
                                                    lastDate_value: "",
                                                    user_type: "user",
                                                    find: "",
                                                });
                                            }}
                                        >
                                            {t("Reset")}
                                        </button>
                                    ) : null}
                                </div>
                                <div className="flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center xl:w-auto">
                                    <select
                                        value={filters?.user_type ?? "user"}
                                        onChange={(e) =>
                                            visit({
                                                ...filters,
                                                user_type: e.target.value,
                                                fd_value: fd,
                                                lastDate_value: lastDate,
                                                find: search,
                                            })
                                        }
                                        className="h-10 w-full rounded py-1 sm:w-auto"
                                    >
                                        <option value="all">{t("Both")}</option>
                                        <option value="user">{t("Reseller Shop")}</option>
                                        <option value="reseller">{t("Vendor Shop")}</option>
                                    </select>
                                </div>
                            </div>
                        }
                        content=""
                    />
                    <Hr />
                    <SectionInner>
                        <div className="overflow-x-auto">
                            <Table data={products?.data ?? []} className="p-2">
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
                                    {(products?.data ?? []).map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{(products?.from ?? 1) + index}</td>
                                            <td>{item.id}</td>
                                            <td>
                                                <NavLink
                                                    className="text-xs"
                                                    href={route("products.details", {
                                                        id: item.product_id ?? "",
                                                        slug: item.product_slug ?? "",
                                                    })}
                                                >
                                                    {item.product_thumbnail ? (
                                                        <img
                                                            width="30"
                                                            height="30"
                                                            src={item.product_thumbnail}
                                                            alt=""
                                                            className="mr-2 rounded-full"
                                                        />
                                                    ) : null}
                                                    <ProductName value={item.product_name} />
                                                </NavLink>
                                                <br />
                                                <div className="inline-block rounded border text-xs">
                                                    {item.product_status ?? "N/A"}
                                                </div>
                                            </td>
                                            <td>
                                                <div className="flex items-center">
                                                    {item.user_type} <i className="mx-2 fas fa-angle-right"></i>
                                                    {item.belongs_to_type}
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div className="text-gray-700">{item.owner_name ?? "N/A"}</div>
                                                    {(item.is_resel_count ?? 0) > 0 ? (
                                                        <span className="rounded-full bg-indigo-900 p-1 text-xs text-white">
                                                            <i className="fas fa-caret-left"></i>R
                                                        </span>
                                                    ) : null}
                                                    {(item.resel_count ?? 0) > 0 ? (
                                                        <span className="rounded-full bg-indigo-900 p-1 text-xs text-white">
                                                            {item.resel_count}
                                                            <i className="fas fa-caret-right"></i>
                                                        </span>
                                                    ) : null}
                                                </div>
                                            </td>
                                            <td>
                                                {formatCurrency(item.product_price)}{item.offer_type ? (
                                                    <div className="flex items-center rounded bg-gray-100 p-1 text-center text-xs">{t("D:")}{item.discount ?? 0} | {item.discount_percent ?? 0}{t("% off")}</div>
                                                ) : null}
                                            </td>
                                            <td>{item.product_created_at ?? "N/A"}</td>
                                            <td>
                                                <span className={statusClass[item.status] ?? "text-xs p-1 border rounded-md bg-gray-200 text-gray-900"}>
                                                    {item.status ?? "Unknown"}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </div>

                        {(products?.total ?? 0) > 0 ? (
                            <div className="w-full pt-4">
                                <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div className="text-sm text-slate-700">
                                        {`Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} items`}
                                    </div>
                                    <div className="flex items-center md:justify-end">
                                        <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="border-r border-slate-200 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
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
                                                className="px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
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
