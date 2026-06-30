import { useEffect, useMemo, useState } from "react";
import { usePage, router } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Container from "../../../../components/dashboard/Container";
import Hr from "../../../../components/Hr";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import CoinStore from "../../../../livewire/system/store/CoinStore";
import CoastStore from "../../../../livewire/system/store/CoastStore";
import DonationStore from "../../../../livewire/system/store/DonationStore";
import useTranslation from "../../../../hooks/useTranslation";
import { todayInputDate } from "../../../../utils/dateInput";
import { formatCurrency } from "../../../../utils/formatAmount";

const formatCoin = (value) => formatCurrency(value);

export default function Index() {
    const { t } = useTranslation();
    const {
        pageTitle = "Coin Store",
        widgets = [],
        tabs = [],
        activeTab = "commissions",
        filters = {
            tab: "commissions",
            search: "",
            start_date: "",
            end_date: "",
        },
        columns1 = [],
        columns2 = [],
        storeMeta = {},
        coinStore = {},
        coastStore = {},
        donationStore = {},
        commissions = {},
        withdrawals = {},
        printUrl,
    } = usePage().props;

    const targetStore = storeMeta?.target ?? {};
    const canDistribute = Boolean(targetStore?.can_distribute);
    const targetBalance = Number(targetStore?.current_balance ?? targetStore?.total_balance ?? 0);
    const distributionStatusLabel = Boolean(Number(targetStore.generate ?? 0))
        ? "Generated"
        : targetBalance > 0
            ? "Available on 5th"
            : "No data to distribute";
    const [search, setSearch] = useState(filters.search ?? "");
    const [startDate, setStartDate] = useState(filters.start_date ?? "");
    const [endDate, setEndDate] = useState(filters.end_date ?? "");
    const [distributing, setDistributing] = useState(false);
    const today = todayInputDate();
    const shareFilters = {
        "Developer Share": "Developer Commission",
        "Management Share": "Management Commission",
        "Management TM Share": "Management TM Commission",
        "Star System Share": "Store Commission",
    };

    const requestStore = ({
        nextTab = activeTab,
        nextSearch = search,
        nextStartDate = startDate,
        nextEndDate = endDate,
        page = undefined,
    } = {}) => {
        router.get(
            route("system.store.index"),
            {
                tab: nextTab,
                search: nextSearch.trim(),
                start_date: nextStartDate,
                end_date: nextEndDate,
                page,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: [
                    "activeTab",
                    "filters",
                    "storeMeta",
                    "coinStore",
                    "coastStore",
                    "donationStore",
                    "commissions",
                    "withdrawals",
                    "printUrl",
                ],
            }
        );
    };

    useEffect(() => {
        setSearch(filters.search ?? "");
    }, [filters.search]);

    useEffect(() => {
        setStartDate(filters.start_date ?? "");
        setEndDate(filters.end_date ?? "");
    }, [filters.start_date, filters.end_date]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.search ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = window.setTimeout(() => {
            requestStore({
                nextTab: activeTab,
                nextSearch: trimmedSearch,
                nextStartDate: startDate,
                nextEndDate: endDate,
            });
        }, 400);

        return () => window.clearTimeout(timeout);
    }, [search]);

    const setTab = (tab) => {
        requestStore({
            nextTab: tab,
            nextSearch: search,
            nextStartDate: startDate,
            nextEndDate: endDate,
        });
    };

    const updateDateFilter = (key, value) => {
        const nextStartDate = key === "start" ? value : startDate;
        const nextEndDate = key === "end" ? value : endDate;

        if (key === "start") {
            setStartDate(value);
        } else {
            setEndDate(value);
        }

        requestStore({
            nextTab: activeTab,
            nextSearch: search,
            nextStartDate,
            nextEndDate,
        });
    };

    const distribute = () => {
        if (!window.confirm(t("Distribute the previous month commission to qualified users?"))) {
            return;
        }

        setDistributing(true);
        router.post(
            route("system.store.distribute"),
            {},
            {
                preserveScroll: true,
                onFinish: () => setDistributing(false),
            }
        );
    };

    const openShareList = (label) => {
        const nextSearch = shareFilters[label];

        if (!nextSearch) {
            return;
        }

        setSearch(nextSearch);
        requestStore({
            nextTab: "commissions",
            nextSearch,
            nextStartDate: startDate,
            nextEndDate: endDate,
        });
    };

    const activeCollection = activeTab === "withdrawals" ? withdrawals : commissions;

    const pagination = useMemo(() => {
        const links = activeCollection?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [activeCollection?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        requestStore({
            nextTab: nextUrl.searchParams.get("tab") ?? activeTab,
            nextSearch: nextUrl.searchParams.get("search") ?? search,
            nextStartDate: nextUrl.searchParams.get("start_date") ?? startDate,
            nextEndDate: nextUrl.searchParams.get("end_date") ?? endDate,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultLabel = activeTab === "withdrawals" ? "withdrawals" : "commissions";
    const resultSummary =
        activeCollection?.total > 0
            ? t("Showing :from-:to of :total :type", {
                  from: activeCollection?.from ?? 0,
                  to: activeCollection?.to ?? 0,
                  total: activeCollection?.total ?? 0,
                  type: t(resultLabel),
              })
            : t("No :type found", { type: t(resultLabel) });
    const hasActiveFilters = Boolean(search.trim() || startDate || endDate);
    const toolbarInputClass = "h-9 w-full rounded-md border border-gray-300 bg-white px-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-auto";
    const toolbarButtonClass = "inline-flex h-9 items-center justify-center rounded-md px-3 text-sm font-semibold";
    const dateFilterControls = (
        <>
            <TextInput
                type="date"
                className={`${toolbarInputClass} sm:w-36`}
                value={startDate || today}
                onChange={(e) => updateDateFilter("start", e.target.value)}
                title={t("Start date")}
            />
            <TextInput
                type="date"
                className={`${toolbarInputClass} sm:w-36`}
                value={endDate}
                onChange={(e) => updateDateFilter("end", e.target.value)}
                title={t("End date")}
            />
        </>
    );

    return (
        <AppLayout title={t(pageTitle)}>
            <Container>
                <div className="flex flex-col gap-3 mb-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 className="text-lg font-semibold text-gray-800">{t(pageTitle)}</h3>
                        {targetStore?.range_label ? (
                            <p className="text-sm text-gray-500">{t("Previous distribution period:")}{targetStore.range_label}
                            </p>
                        ) : null}
                    </div>
                    {canDistribute ? (
                        <button
                            type="button"
                            onClick={distribute}
                            disabled={distributing}
                            className="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white transition bg-blue-500 rounded-md hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {distributing ? t("Distributing...") : t("Distribute")}
                        </button>
                    ) : (
                        <span className="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-md">
                            {t(distributionStatusLabel)}
                        </span>
                    )}
                </div>
                <section className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                    {widgets.map((widget, index) => (
                        <OverviewDiv
                            key={`${widget.label}-${index}`}
                            title={t(widget.label)}
                            content={formatCoin(widget.value)}
                            onClick={shareFilters[widget.label] ? () => openShareList(widget.label) : null}
                            titleText={shareFilters[widget.label] ? t("View :item list", { item: t(shareFilters[widget.label]) }) : ""}
                        />
                    ))}
                </section>
            </Container>

            <Hr />

            <Container>
                <section className="mt-6 mb-6 grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-6">
                    <div className="relative p-6 bg-white rounded-md shadow-md">
                        <CoinStore
                            store={formatCoin(coinStore.store)}
                            take={formatCoin(coinStore.take)}
                            give={formatCoin(coinStore.give)}
                        />
                    </div>
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:gap-6">
                        <div className="relative p-6 bg-white rounded-md shadow-md">
                            <CoastStore store={formatCoin(coastStore.store)} />
                        </div>
                        <div className="relative p-6 bg-white rounded-md shadow-md">
                            <DonationStore store={formatCoin(donationStore.store)} />
                        </div>
                    </div>
                </section>
            </Container>

            <Hr />

            <Container>
                <div className="mt-6 flex flex-wrap gap-3">
                    {tabs.map((tab) => (
                        <button
                            key={tab}
                            type="button"
                            onClick={() => setTab(tab)}
                            className={`inline-flex w-auto items-center justify-center rounded-md px-3 py-2 text-sm font-medium ${
                                activeTab === tab
                                    ? "bg-blue-500 text-white"
                                    : "bg-gray-200 text-gray-700"
                            }`}
                        >
                            {t(tab.charAt(0).toUpperCase() + tab.slice(1))}
                        </button>
                    ))}
                </div>

                {activeTab === "commissions" ? (
                    <SectionSection>
                        <SectionHeader
                            title={
                                <div className="flex flex-col gap-3 border-b pb-4 xl:flex-row xl:items-center xl:justify-between">
                                    <h4 className="shrink-0 text-lg font-semibold leading-6">{t("Distributed Commissions")}</h4>
                                    <div className="flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end xl:w-auto xl:flex-nowrap">
                                        {dateFilterControls}
                                        <TextInput
                                            type="search"
                                            placeholder={t("Search commissions...")}
                                            className={`${toolbarInputClass} sm:w-44`}
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                            onKeyDown={(e) => {
                                                if (e.key !== "Enter") {
                                                    return;
                                                }

                                                e.preventDefault();
                                                requestStore();
                                            }}
                                        />
                                        <PrimaryButton
                                            type="button"
                                            className={`${toolbarButtonClass} min-w-10 self-start`}
                                            onClick={() => window.open(printUrl, "_blank")}
                                        >
                                            <i className="fas fa-print"></i>
                                        </PrimaryButton>
                                        {hasActiveFilters ? (
                                            <button
                                                type="button"
                                                className={`${toolbarButtonClass} self-start border border-gray-300 bg-white text-slate-700 shadow-sm hover:bg-gray-50`}
                                                onClick={() => {
                                                    setSearch("");
                                                    setStartDate("");
                                                    setEndDate("");
                                                    requestStore({
                                                        nextTab: activeTab,
                                                        nextSearch: "",
                                                        nextStartDate: "",
                                                        nextEndDate: "",
                                                        page: undefined,
                                                    });
                                                }}
                                            >
                                                {t("Reset")}
                                            </button>
                                        ) : null}
                                        {canDistribute ? (
                                            <button
                                                type="button"
                                                onClick={distribute}
                                                disabled={distributing}
                                                className={`${toolbarButtonClass} self-start bg-blue-500 text-white hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-60`}
                                            >
                                                {distributing ? t("Distributing...") : t("Distribute")}
                                            </button>
                                        ) : (
                                            <div className={`${toolbarButtonClass} self-start bg-blue-500 text-white`}>
                                                {t(distributionStatusLabel)}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            }
                            content=""
                        />

                        <SectionInner>
                            <div className="overflow-x-auto border border-gray-200 shadow-sm rounded-xl">
                                <table className="min-w-[900px] text-sm divide-y divide-gray-200 xl:min-w-full">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            {columns1.map((column, index) => (
                                                <th
                                                    key={`${column}-${index}`}
                                                    className="px-4 py-3 font-semibold text-left text-gray-600"
                                                >
                                                    <strong>{t(column)}</strong>
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-100">
                                        {(commissions?.data ?? []).length ? (
                                            commissions.data.map((item, index) => (
                                                <tr key={`${item.user_name}-${index}`} className="transition hover:bg-gray-50">
                                                    <td className="px-4 py-3 font-medium text-gray-700">
                                                        {item.sl}
                                                    </td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">
                                                        <strong>{item.user_name}</strong>
                                                    </td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{t(item.store)}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{formatCoin(item.amount)}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{item.range}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{t(item.info)}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{item.created_at}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">
                                                        <span>-</span>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan={columns1.length} className="px-4 py-6 text-center text-gray-500">
                                                    <span>{t("No histories found.")}</span>
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            {pagination.pages.length ? (
                                <div className="w-full pt-4">
                                    <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
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
                        </SectionInner>
                    </SectionSection>
                ) : null}

                {activeTab === "withdrawals" ? (
                    <SectionSection>
                        <SectionHeader
                            title={
                                <div className="flex flex-col gap-3 border-b pb-4 xl:flex-row xl:items-center xl:justify-between">
                                    <h4 className="shrink-0 text-lg font-semibold leading-6">{t("Withdrawal History")}</h4>
                                    <div className="flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end xl:w-auto xl:flex-nowrap">
                                        {dateFilterControls}
                                        <TextInput
                                            type="search"
                                            placeholder={t("Search withdrawals...")}
                                            className={`${toolbarInputClass} sm:w-44`}
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                            onKeyDown={(e) => {
                                                if (e.key !== "Enter") {
                                                    return;
                                                }

                                                e.preventDefault();
                                                requestStore();
                                            }}
                                        />
                                        <PrimaryButton
                                            type="button"
                                            className={`${toolbarButtonClass} min-w-10 self-start`}
                                            onClick={() => window.open(printUrl, "_blank")}
                                        >
                                            <i className="fas fa-print"></i>
                                        </PrimaryButton>
                                        {hasActiveFilters ? (
                                            <button
                                                type="button"
                                                className={`${toolbarButtonClass} self-start border border-gray-300 bg-white text-slate-700 shadow-sm hover:bg-gray-50`}
                                                onClick={() => {
                                                    setSearch("");
                                                    setStartDate("");
                                                    setEndDate("");
                                                    requestStore({
                                                        nextTab: activeTab,
                                                        nextSearch: "",
                                                        nextStartDate: "",
                                                        nextEndDate: "",
                                                        page: undefined,
                                                    });
                                                }}
                                            >
                                                {t("Reset")}
                                            </button>
                                        ) : null}
                                    </div>
                                </div>
                            }
                            content=""
                        />

                        <SectionInner>
                            <div className="overflow-x-auto border border-gray-200 shadow-sm rounded-xl">
                                <table className="min-w-[980px] text-sm divide-y divide-gray-200 xl:min-w-full">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            {columns2.map((column, index) => (
                                                <th
                                                    key={`${column}-${index}`}
                                                    className="px-4 py-3 font-semibold text-left text-gray-600"
                                                >
                                                    <strong>{t(column)}</strong>
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-100">
                                        {(withdrawals?.data ?? []).length ? (
                                            withdrawals.data.map((withdraw, index) => (
                                                <tr key={`${withdraw.user_name}-${index}`} className="transition hover:bg-gray-50">
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.sl}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.user_name}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.store_req}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.maintenance_fee}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.server_fee}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.pay_by}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{t(withdraw.status)}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.requested_at}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.remarks}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">-</td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="9" className="px-4 py-6 text-center text-gray-500">
                                                    <span>{t("No histories found.")}</span>
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            {pagination.pages.length ? (
                                <div className="w-full pt-4">
                                    <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
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
                        </SectionInner>
                    </SectionSection>
                ) : null}
            </Container>
        </AppLayout>
    );
}
