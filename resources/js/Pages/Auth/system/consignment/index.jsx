import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Div from "../../../../components/dashboard/overview/Div";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

export default function Index({ widgets = [], filters = {}, cod, printUrl }) {
    const [search, setSearch] = useState(filters.find ?? "");
    const [sdate, setSdate] = useState(filters.sdate ?? "");
    const [edate, setEdate] = useState(filters.edate ?? "");

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

    return (
        <AppLayout
            title="Consignment"
            header={<PageHeader>Consignment</PageHeader>}
        >
            <Head title="Consignment" />

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
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-2">
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
                                        className="rounded-md border-gray-300 shadow-sm"
                                    >
                                        <option value="All">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Received">Received</option>
                                        <option value="Completed">Complete</option>
                                        <option value="Returned">Returned</option>
                                    </select>
                                </div>

                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <TextInput
                                        type="date"
                                        value={sdate}
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
                                    <TextInput
                                        type="search"
                                        value={search}
                                        placeholder="Search consignment..."
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
                        <Table data={cod?.data ?? []} table-border="1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Order ID</th>
                                    <th>Rider</th>
                                    <th>Amount</th>
                                    <th>Rider Amount</th>
                                    <th>Total</th>
                                    <th>Comission</th>
                                    <th>C Rate</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>A/C</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(cod?.data ?? []).map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{(cod?.from ?? 1) + index}</td>
                                        <td>{item.id}</td>
                                        <td>{item.order_id}</td>
                                        <td>{item.rider_name}</td>
                                        <td>{item.amount}</td>
                                        <td>{item.rider_amount}</td>
                                        <td>{item.total_amount}</td>
                                        <td>{item.system_comission}</td>
                                        <td>{item.comission}</td>
                                        <td>{item.status}</td>
                                        <td>{item.created_at_formatted}</td>
                                        <td>
                                            <div className="flex gap-2 items-center">
                                                <DangerButton type="button">
                                                    <i className="fas fa-trash"></i>
                                                </DangerButton>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot className="bg-cyan-300">
                                <tr>
                                    <td>{cod?.summary?.count ?? 0}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{cod?.summary?.amount ?? 0}</td>
                                    <td>{cod?.summary?.rider_amount ?? 0}</td>
                                    <td>{cod?.summary?.total_amount ?? 0}</td>
                                    <td>{cod?.summary?.system_comission ?? 0}</td>
                                    <td>{cod?.summary?.comission ?? 0}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
