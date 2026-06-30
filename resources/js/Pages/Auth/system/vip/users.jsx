import { router, usePage } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import { ActionIconLink } from "../../../../components/ActionIcon";
import useTranslation from "../../../../hooks/useTranslation";
import { formatCurrency } from "../../../../utils/formatAmount";

export default function Users() {
    const { t } = useTranslation();
    const { vip, filters = {}, printUrl } = usePage().props;
    const [search, setSearch] = useState(filters.search ?? "");
    const [nav, setNav] = useState(filters.nav ?? "All");
    const [type, setType] = useState(filters.type ?? "All");
    const [validity, setValidity] = useState(filters.validity ?? "All");
    const [quickFilter, setQuickFilter] = useState("task:All");
    const [sdate, setSdate] = useState(filters.sdate ?? "");
    const [edate, setEdate] = useState(filters.edate ?? "");

    const applyFilters = (next = {}) => {
        router.get(
            route("system.vip.users"),
            {
                nav,
                search,
                sdate,
                edate,
                type,
                validity,
                ...next,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ["filters", "vip", "printUrl"],
            }
        );
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        applyFilters({
            nav: nextUrl.searchParams.get("nav") ?? nav,
            search: nextUrl.searchParams.get("search") ?? search,
            sdate: nextUrl.searchParams.get("sdate") ?? sdate,
            edate: nextUrl.searchParams.get("edate") ?? edate,
            type: nextUrl.searchParams.get("type") ?? type,
            validity: nextUrl.searchParams.get("validity") ?? validity,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = vip?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [vip?.links]);

    const resultSummary =
        vip?.total > 0
            ? t("Showing :from-:to of :total vip users", {
                  from: vip?.from ?? 0,
                  to: vip?.to ?? 0,
                  total: vip?.total ?? 0,
              })
            : t("No vip users found");
    const hasActiveFilters = Boolean(
        search.trim() ||
            sdate ||
            edate ||
            nav !== "All" ||
            type !== "All" ||
            validity !== "All"
    );

    return (
        <AppLayout
            title={t("VIP Users")}
            header={
                <PageHeader>
                    <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div className="mb-1">{t("VIP Users")}</div>
                    </div>
                </PageHeader>
            }
        >
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 xl:flex xl:flex-wrap xl:items-center">
                                <select
                                    className="h-10 w-full rounded-md border-gray-300 py-1 text-sm xl:w-32"
                                    value={nav}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setNav(value);

                                        if (value === "All") {
                                            setSearch("");
                                            setType("All");
                                            setValidity("All");
                                            setQuickFilter("task:All");
                                            setSdate("");
                                            setEdate("");
                                            applyFilters({
                                                nav: "All",
                                                search: "",
                                                sdate: "",
                                                edate: "",
                                                type: "All",
                                                validity: "All",
                                                page: undefined,
                                            });
                                            return;
                                        }

                                        applyFilters({ nav: value, page: undefined });
                                    }}
                                >
                                    <option value="All">{t("All")}</option>
                                    <option value="Pending">{t("Pending")}</option>
                                    <option value="Confirmed">{t("Active")}</option>
                                    <option value="Trash">{t("Trash")}</option>
                                </select>
                                <select
                                    className="h-10 w-full rounded-md border-gray-300 py-1 text-sm xl:w-52"
                                    value={quickFilter}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        const [filterType, filterValue] = value.split(":");

                                        setQuickFilter(value);

                                        if (filterType === "task") {
                                            setType(filterValue);
                                            applyFilters({ type: filterValue, page: undefined });
                                            return;
                                        }

                                        setValidity(filterValue);
                                        applyFilters({ validity: filterValue, page: undefined });
                                    }}
                                    title={t("Task Type and Validity")}
                                >
                                    <optgroup label={t("Task Type")}>
                                        <option value="task:daily">{t("Daily Tasks")}</option>
                                        <option value="task:monthly">{t("Monthly Tasks")}</option>
                                        <option value="task:All">{t("Both")}</option>
                                    </optgroup>
                                    <optgroup label={t("Package Validity")}>
                                        <option value="validity:valid">{t("Only Valid")}</option>
                                        <option value="validity:invalid">{t("Only Invalid")}</option>
                                        <option value="validity:All">{t("Both")}</option>
                                    </optgroup>
                                </select>
                                <TextInput
                                    type="date"
                                    className="h-10 w-full py-1 text-sm xl:w-36"
                                    value={sdate}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setSdate(value);
                                        applyFilters({ sdate: value, page: undefined });
                                    }}
                                    title={t("Start Date")}
                                />
                                <TextInput
                                    type="date"
                                    className="h-10 w-full py-1 text-sm xl:w-36"
                                    value={edate}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setEdate(value);
                                        applyFilters({ edate: value, page: undefined });
                                    }}
                                    title={t("End Date")}
                                />
                                <div className="flex w-full gap-2 sm:col-span-2 xl:w-auto">
                                    <input
                                        type="search"
                                        className="h-10 w-full rounded-lg border-gray-400 py-1"
                                        placeholder={t("find name, id")}
                                        value={search}
                                        onChange={(e) => {
                                            setSearch(e.target.value);
                                            applyFilters({ search: e.target.value, page: undefined });
                                        }}
                                    />
                                    <PrimaryButton
                                        type="button"
                                        className="h-10 shrink-0 justify-center px-4"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                                {hasActiveFilters ? (
                                    <button
                                        type="button"
                                        className="h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50 sm:col-span-2 xl:w-auto"
                                        onClick={() => {
                                            setSearch("");
                                            setNav("All");
                                            setType("All");
                                            setValidity("All");
                                            setQuickFilter("task:All");
                                            setSdate("");
                                            setEdate("");
                                            applyFilters({
                                                nav: "All",
                                                search: "",
                                                sdate: "",
                                                edate: "",
                                                type: "All",
                                                validity: "All",
                                                page: undefined,
                                            });
                                        }}
                                    >
                                        {t("Reset")}
                                    </button>
                                ) : null}
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <div>
                            <Table data={vip?.data ?? []} tableClassName="min-w-[980px] xl:min-w-full">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{t("Name")}</th>
                                            <th>{t("VIP")}</th>
                                            <th>{t("Wallet")}</th>
                                            <th>{t("Status")}</th>
                                            <th>{t("Date")}</th>
                                            <th>{t("Validity")}</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {(vip?.data ?? []).map((item) => (
                                            <tr key={item.id}>
                                                <td>{item.sl}</td>
                                                <td>
                                                    {item.name ?? t("N/A")}
                                                    <br />
                                                    <div className="text-xs ">
                                                        {item.user_email ?? t("N/A")}
                                                    </div>
                                                </td>
                                                <td>
                                                    {item.package_name ?? t("N/A")}
                                                    <div className="text-xs">
                                                        {" "}
                                                        {item.task_type ? t(item.task_type) : t("N/A")}{" "}
                                                    </div>
                                                </td>
                                                <td>{formatCurrency(item.user_coin)}</td>
                                                <td>
                                                    {t(item.status)}
                                                    <br />
                                                    {item.deleted_at_formatted ? (
                                                        <span className="text-xs text-red-900 text-bold ">
                                                            {item.deleted_at_formatted}
                                                        </span>
                                                    ) : null}
                                                </td>
                                                <td>
                                                    <div className="text-nowrap">
                                                        {item.created_at_formatted}
                                                    </div>
                                                </td>
                                                <td>
                                                    {item.valid_till_formatted}
                                                    <div className="text-xs">
                                                        {item.valid_till_human}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div className="flex items-center gap-1">
                                                        <ActionIconLink
                                                            href={route("system.vip.edit", {
                                                                vip: item.id,
                                                            })}
                                                            action="view"
                                                            title={t("View")}
                                                        />
                                                        <ActionIconLink
                                                            href="#"
                                                            action="details"
                                                            title={t("User")}
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                            </Table>

                            {pagination.pages.length ? (
                                    <div className="w-full pt-4">
                                        <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
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
                                                        {t("Previous")}
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
                                                        {t("Next")}
                                                    </button>
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
