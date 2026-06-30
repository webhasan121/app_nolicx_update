import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Div from "../../../../components/dashboard/overview/Div";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import useTranslation from "../../../../hooks/useTranslation";
import { todayInputDate } from "../../../../utils/dateInput";
import { formatCurrency } from "../../../../utils/formatAmount";

export default function Index({ widgets = [], filters = {}, cod, printUrl }) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.find ?? "");
    const [sdate, setSdate] = useState(filters.sdate ?? "");
    const [edate, setEdate] = useState(filters.edate ?? "");
    const today = todayInputDate();

    const requestConsignment = ({
        nextType = filters.type ?? "Pending",
        nextFind = search,
        nextSdate = sdate,
        nextEdate = edate,
        page = undefined,
    } = {}) => {
        router.get(
            route("system.consignment.index"),
            {
                type: nextType,
                find: nextFind.trim(),
                sdate: nextSdate,
                edate: nextEdate,
                page,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ["filters", "cod", "printUrl"],
            }
        );
    };

    useEffect(() => {
        setSearch(filters.find ?? "");
        setSdate(filters.sdate ?? "");
        setEdate(filters.edate ?? "");
    }, [filters.edate, filters.find, filters.sdate]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            requestConsignment({
                nextType: filters.type ?? "Pending",
                nextFind: trimmedSearch,
                nextSdate: sdate,
                nextEdate: edate,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const pagination = useMemo(() => {
        const links = cod?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [cod?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        requestConsignment({
            nextType: nextUrl.searchParams.get("type") ?? filters.type ?? "Pending",
            nextFind: nextUrl.searchParams.get("find") ?? search,
            nextSdate: nextUrl.searchParams.get("sdate") ?? sdate,
            nextEdate: nextUrl.searchParams.get("edate") ?? edate,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultSummary =
        cod?.total > 0
            ? `Showing ${cod?.from ?? 0}-${cod?.to ?? 0} of ${cod?.total ?? 0} consignments`
            : "No consignments found";
    const hasActiveFilters = Boolean(search.trim() || sdate || edate || (filters.type ?? "Pending") !== "Pending");

    return (
        <AppLayout
            title={t("Consignment")}
            header={<PageHeader>{t("Consignment")}</PageHeader>}
        >
            <Head title={t("Consignment")} />

            <Container>
                <Section>
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        {widgets.map((widget) => (
                            <Div
                                key={widget.title}
                                title={widget.title}
                                content={widget.value}
                            />
                        ))}
                    </div>
                </Section>
            </Container>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex flex-col gap-3 xl:flex-row xl:items-start xl:justify-between">
                                <div className="w-full xl:w-auto">
                                    <select
                                        value={filters.type ?? "Pending"}
                                        onChange={(e) =>
                                            requestConsignment({
                                                nextType: e.target.value,
                                                nextFind: search,
                                                nextSdate: sdate,
                                                nextEdate: edate,
                                            })
                                        }
                                        className="w-full rounded-md border-gray-300 shadow-sm sm:w-auto"
                                    >
                                        <option value="All">{t("All")}</option>
                                        <option value="Pending">{t("Pending")}</option>
                                        <option value="Received">{t("Received")}</option>
                                        <option value="Completed">{t("Complete")}</option>
                                        <option value="Returned">{t("Returned")}</option>
                                    </select>
                                </div>

                                <div className="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 xl:flex xl:w-auto xl:flex-wrap xl:items-center xl:justify-end">
                                    <TextInput
                                        type="date"
                                        className="h-10 w-full py-2 xl:w-40"
                                        value={sdate || today}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setSdate(value);
                                            requestConsignment({
                                                nextType: filters.type ?? "Pending",
                                                nextFind: search,
                                                nextSdate: value,
                                                nextEdate: edate,
                                            });
                                        }}
                                    />
                                    <TextInput
                                        type="date"
                                        className="h-10 w-full py-2 xl:w-40"
                                        value={edate}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setEdate(value);
                                            requestConsignment({
                                                nextType: filters.type ?? "Pending",
                                                nextFind: search,
                                                nextSdate: sdate,
                                                nextEdate: value,
                                            });
                                        }}
                                    />
                                    <div className="flex w-full gap-2 sm:col-span-2 xl:w-auto">
                                        <TextInput
                                            type="search"
                                            className="h-10 w-full py-2 xl:w-52"
                                            value={search}
                                            placeholder={t("Search consignment...")}
                                            onChange={(e) => setSearch(e.target.value)}
                                            onKeyDown={(e) => {
                                                if (e.key !== "Enter") {
                                                    return;
                                                }

                                                e.preventDefault();
                                                requestConsignment();
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
                                            className="h-10 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50 sm:col-span-2 xl:col-auto"
                                            onClick={() => {
                                                setSearch("");
                                                setSdate("");
                                                setEdate("");
                                                requestConsignment({
                                                    nextType: "Pending",
                                                    nextFind: "",
                                                    nextSdate: "",
                                                    nextEdate: "",
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
                        <Table
                            data={cod?.data ?? []}
                            tableClassName="min-w-[1180px] xl:min-w-full"
                            table-border="1"
                        >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{t("ID")}</th>
                                    <th>{t("Order ID")}</th>
                                    <th>{t("Rider")}</th>
                                    <th>{t("Amount")}</th>
                                    <th>{t("Rider Amount")}</th>
                                    <th>{t("Total")}</th>
                                    <th>{t("Comission")}</th>
                                    <th>{t("C Rate")}</th>
                                    <th>{t("Status")}</th>
                                    <th>{t("Date")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(cod?.data ?? []).map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{(cod?.from ?? 1) + index}</td>
                                        <td>{item.id}</td>
                                        <td>{item.order_id}</td>
                                        <td>{item.rider_name}</td>
                                        <td>{formatCurrency(item.amount)}</td>
                                        <td>{formatCurrency(item.rider_amount)}</td>
                                        <td>{formatCurrency(item.total_amount)}</td>
                                        <td>{formatCurrency(item.system_comission)}</td>
                                        <td>{formatCurrency(item.comission)}</td>
                                        <td>{item.status}</td>
                                        <td>{item.created_at_formatted}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot className="bg-cyan-300">
                                <tr>
                                    <td>{cod?.summary?.count ?? 0}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{formatCurrency(cod?.summary?.amount)}</td>
                                    <td>{formatCurrency(cod?.summary?.rider_amount)}</td>
                                    <td>{formatCurrency(cod?.summary?.total_amount)}</td>
                                    <td>{formatCurrency(cod?.summary?.system_comission)}</td>
                                    <td>{formatCurrency(cod?.summary?.comission)}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
