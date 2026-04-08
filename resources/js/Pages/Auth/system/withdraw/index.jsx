import { Head, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
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

    const apply = (next = {}) => {
        router.get(
            route("system.withdraw.index"),
            {
                where: next.where ?? filters?.where ?? "",
                q: next.q ?? filters?.q ?? "",
                fst: next.fst ?? filters?.fst ?? "Pending",
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

    const renderPagination = () =>
        (withdraw?.links ?? []).map((link) =>
            link.url ? (
                <button
                    type="button"
                    key={`${link.label}-${link.url}`}
                    className={`px-2 py-1 mx-1 border rounded ${link.active ? "bg-orange-500 text-white" : ""}`}
                    onClick={() => {
                        const url = new URL(link.url);
                        apply({
                            where: url.searchParams.get("where") ?? filters?.where,
                            q: url.searchParams.get("q") ?? filters?.q,
                            fst: url.searchParams.get("fst") ?? filters?.fst,
                            sdate: url.searchParams.get("sdate") ?? filters?.sdate,
                            edate: url.searchParams.get("edate") ?? filters?.edate,
                            page: url.searchParams.get("page") ?? undefined,
                        });
                    }}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                />
            ) : (
                <span
                    key={`${link.label}-disabled`}
                    className="px-2 py-1 mx-1 text-gray-400 border rounded"
                    dangerouslySetInnerHTML={{ __html: link.label }}
                />
            )
        );

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
                                    <select value={filters?.fst ?? "Pending"} onChange={(e) => apply({ fst: e.target.value })} className="py-1 mb-2 border rounded" id="filter_status">
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
                                    <td>{index + 1}</td>
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
                    <div>{renderPagination()}</div>

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
                        <select value={filters?.where ?? ""} onChange={(e) => apply({ where: e.target.value })} id="search_where" className="py-1 border-0 rounded-md shadow-none">
                            <option value="find"> ID </option>
                            <option value="query"> User </option>
                        </select>
                        <br />
                        <TextInput type="text" className="w-full" value={queryValue} onChange={(e) => setQueryValue(e.target.value)} placeholder="Search by User Name or ID" />
                    </div>
                    <Hr className="my-2" />
                    <div className="flex items-center justify-between">
                        <TextInput type="date" value={filters?.sdate ?? ""} onChange={(e) => apply({ sdate: e.target.value })} placeholder="From Date" />
                        <TextInput type="date" value={filters?.edate ?? ""} onChange={(e) => apply({ edate: e.target.value })} placeholder="To Date" />
                    </div>
                </div>
                <Hr className="my-2" />
                <div className="p-3">
                    <SecondaryButton className="mr-1" onClick={() => setShowFilterModal(false)}>
                        Close
                    </SecondaryButton>
                    <PrimaryButton type="button" onClick={() => apply({ q: queryValue })}>
                        Filter
                    </PrimaryButton>
                </div>
            </Modal>
        </AppLayout>
    );
}
