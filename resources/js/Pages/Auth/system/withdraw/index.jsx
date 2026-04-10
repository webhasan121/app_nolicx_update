import { Head, router } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import Modal from "../../../../components/Modal";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import Table from "../../../../components/dashboard/table/Table";
import AppLayout from "../../../../Layouts/App";

export default function Index({ filters, stats, withdraw }) {
    const [showFilterModal, setShowFilterModal] = useState(false);
    const [queryValue, setQueryValue] = useState(filters?.q ?? "");
    const [modalWhere, setModalWhere] = useState(filters?.where ?? "find");
    const [modalSdate, setModalSdate] = useState(filters?.sdate ?? "");
    const [modalEdate, setModalEdate] = useState(filters?.edate ?? "");

    const apply = (next = {}) => {
        router.get(
            route("system.withdraw.index"),
            {
                where: next.where ?? filters?.where ?? "",
                q: next.q ?? filters?.q ?? "",
                fst: next.fst ?? filters?.fst ?? "All",
                sdate: next.sdate ?? filters?.sdate ?? "",
                edate: next.edate ?? filters?.edate ?? "",
                page: next.page ?? undefined,
            },
            { preserveScroll: true, preserveState: true }
        );
    };

    useEffect(() => {
        setQueryValue(filters?.q ?? "");
    }, [filters?.q]);

    useEffect(() => {
        setModalWhere(filters?.where ?? "find");
        setModalSdate(filters?.sdate ?? "");
        setModalEdate(filters?.edate ?? "");
    }, [filters?.where, filters?.sdate, filters?.edate]);

    useEffect(() => {
        const timer = window.setTimeout(() => {
            if (queryValue !== (filters?.q ?? "")) {
                apply({ q: queryValue });
            }
        }, 400);

        return () => window.clearTimeout(timer);
    }, [queryValue]);

    const print = () => {
        window.open(
            route("system.withdraw.print", {
                where: filters?.where ?? "",
                fst: filters?.fst ?? "Pending",
                sdate: filters?.sdate ?? "",
                edate: filters?.edate ?? "",
            }),
            "_blank"
        );
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        apply({
            where: nextUrl.searchParams.get("where") ?? filters?.where,
            q: nextUrl.searchParams.get("q") ?? filters?.q,
            fst: nextUrl.searchParams.get("fst") ?? filters?.fst,
            sdate: nextUrl.searchParams.get("sdate") ?? filters?.sdate,
            edate: nextUrl.searchParams.get("edate") ?? filters?.edate,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = withdraw?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [withdraw?.links]);

    const resultSummary =
        withdraw?.total > 0
            ? `Showing ${withdraw?.from ?? 0}-${withdraw?.to ?? 0} of ${withdraw?.total ?? 0} withdraws`
            : "No withdraws found";

    return (
        <AppLayout title="Withdraws" header={<PageHeader>Withdraws</PageHeader>}>
            <Head title="Withdraws" />

            <Container>
                <OverviewSection>
                    <OverviewDiv title="Amount" content={stats?.amount ?? 0} />
                    <OverviewDiv title="Payable" content={stats?.payable ?? 0} />
                    <OverviewDiv title="Comission" content={`${stats?.server_fee ?? 0} | ${stats?.maintenance_fee ?? 0}`} />
                    <OverviewDiv title="Paid" content={stats?.paid ?? 0} />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title=""
                        content={
                            <div className="flex items-center justify-between overflow-x-scroll" style={{ scrollBehavior: "smooth" }}>
                                <div>
                                    <select value={filters?.fst ?? "All"} onChange={(e) => apply({ fst: e.target.value })} className="py-1 mb-2 border rounded" id="filter_status">
                                        <option value="All">All {stats?.total ?? 0}</option>
                                        <option value="Pending">Pending {stats?.pending ?? 0}</option>
                                        <option value="Accept">Accepted {stats?.paid ?? 0}</option>
                                        <option value="Reject">Rejected {stats?.reject ?? 0}</option>
                                    </select>
                                </div>

                                <div className="flex space-x-2">
                                    <SecondaryButton onClick={() => setShowFilterModal(true)}>
                                        <i className="fas fa-filter"></i>
                                    </SecondaryButton>
                                    <PrimaryButton type="button" onClick={print}>
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                    />
                    <br />


                    <Table data={withdraw?.data ?? []}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>A/C</th>
                            </tr>
                        </thead>

                        <tbody>
                            {(withdraw?.data ?? []).map((item, index) => (
                                <tr key={item.id} className={!item.seen_by_admin ? "bg-gray-200 font-bold" : ""}>
                                    <td>{(withdraw?.from ?? 1) + index}</td>
                                    <td>{item.id}</td>
                                    <td>
                                        <div>
                                            <div className="flex">
                                                {item.user?.name}
                                                {item.user?.subscription ? (
                                                    <span className="px-1 text-white bg-indigo-900 rounded ms-1">
                                                        vip
                                                    </span>
                                                ) : null}
                                                <span className="px-1 text-white bg-gray-900 rounded-full ms-1">
                                                    U
                                                </span>
                                            </div>

                                            {item.user?.email}
                                        </div>
                                    </td>
                                    <td>{item.amount ?? "0"} TK</td>
                                    <td>
                                        {!item.is_rejected ? (
                                            item.status ? "Accept" : "Pending"
                                        ) : (
                                            <div className="p-1">Reject</div>
                                        )}
                                    </td>
                                    <td>{item.created_at_formatted}</td>
                                    <td>
                                        <div className="flex">
                                            <NavLink href={route("system.withdraw.view", { id: item.id })}>Details</NavLink>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr className="font-bold">
                                <td colSpan="3" className="font-bold text-right">Total</td>
                                <td className="font-bold">{withdraw?.sum_amount ?? 0}</td>
                                <td colSpan="3"></td>
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

                </Section>
            </Container>

            <Modal show={showFilterModal} onClose={() => setShowFilterModal(false)} maxWidth="sm">
                <div className="p-3">
                    Filter
                </div>
                <Hr />
                <div className="p-3">
                    <div>
                        <p>
                            Search Criteria
                        </p>
                        <select value={modalWhere} onChange={(e) => setModalWhere(e.target.value)} id="search_where" className="py-1 border-0 rounded-md shadow-none">
                            <option value="find"> ID </option>
                            <option value="query"> User </option>
                        </select>
                        <br />
                        <TextInput type="text" className="w-full" value={queryValue} onChange={(e) => setQueryValue(e.target.value)} placeholder="Search by User Name or ID" />
                    </div>
                    <Hr className="my-2" />
                    <div className="flex items-center justify-between">
                        <TextInput type="date" value={modalSdate} onChange={(e) => setModalSdate(e.target.value)} placeholder="From Date" />
                        <TextInput type="date" value={modalEdate} onChange={(e) => setModalEdate(e.target.value)} placeholder="To Date" />
                    </div>
                </div>
                <Hr className="my-2" />
                <div className="p-3">
                    <SecondaryButton className="mr-1" onClick={() => setShowFilterModal(false)}>
                        Close
                    </SecondaryButton>
                    <PrimaryButton
                        type="button"
                        onClick={() => {
                            setShowFilterModal(false);
                            apply({
                                where: modalWhere,
                                q: queryValue,
                                sdate: modalSdate,
                                edate: modalEdate,
                                page: undefined,
                            });
                        }}
                    >
                        Filter
                    </PrimaryButton>
                </div>
            </Modal>
        </AppLayout>
    );
}
