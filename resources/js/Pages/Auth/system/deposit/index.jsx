import { Head, router } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import Table from "../../../../components/dashboard/table/Table";

function buildParams(status, sdate, edate, page) {
    const params = { status, sdate, edate };

    if (page) {
        params.page = page;
    }

    return params;
}

export default function Index({
    status = "0",
    sdate = "",
    edate = "",
    history,
}) {
    const visit = (nextStatus, nextSdate, nextEdate, page = null) => {
        router.get(
            route("system.deposit.index"),
            buildParams(nextStatus, nextSdate, nextEdate, page),
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    };

    const print = () => {
        window.open(
            route("system.deposit.print-summery", {
                status,
                sdate,
                edate,
            }),
            "_blank",
        );
    };

    const confirmDeposit = (id) => {
        router.post(route("system.deposit.confirm", { deposit: id }));
    };

    const denyDeposit = (id) => {
        router.delete(route("system.deposit.destroy", { deposit: id }));
    };

    return (
        <AppLayout title="Deposit" header={<PageHeader>Deposit</PageHeader>}>
            <Head title="Deposit" />

            <Container>
                <Section>
                    <SectionHeader
                        title=""
                        content={
                            <div className="items-center justify-between space-x-1 lg:flex">
                                <select
                                    value={status}
                                    onChange={(e) =>
                                        visit(e.target.value, sdate, edate)
                                    }
                                    className="py-1 mb-1 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 focus:ring-1"
                                >
                                    <option value="0">Pending</option>
                                    <option value="1">Confirmed</option>
                                </select>

                                <div className="flex items-center space-x-2">
                                    <PrimaryButton
                                        type="button"
                                        onClick={print}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    <TextInput
                                        type="date"
                                        id="sdate"
                                        value={sdate}
                                        onChange={(e) =>
                                            visit(status, e.target.value, edate)
                                        }
                                        className="py-1 "
                                    />
                                    <TextInput
                                        type="date"
                                        id="edate"
                                        value={edate}
                                        onChange={(e) =>
                                            visit(status, sdate, e.target.value)
                                        }
                                        className="py-1 "
                                    />
                                </div>
                            </div>
                        }
                    />

                    <br />

                    <div id="pdf-content">
                        <hr clas="my-1" />
                        <Table data={history?.data ?? []}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Trx ID</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>A/C</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(history?.data ?? []).map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>
                                            <NavLinkBtn
                                                href={route(
                                                    "system.users.edit",
                                                    { id: item.user.id },
                                                )}
                                            >
                                                {item.user.name}
                                            </NavLinkBtn>
                                        </td>
                                        <td>{item.amount ?? 0}</td>
                                        <td>
                                            <div className="flex items-center">
                                                {item.senderAccountNumber}{" "}
                                                <i className="px-2 fas fa-caret-right"></i>
                                                {item.paymentMethod}{" "}
                                                <i className="px-2 fas fa-caret-right"></i>
                                                {item.receiverAccountNumber}
                                            </div>
                                        </td>
                                        <td>{item.transactionId ?? "N/A"}</td>
                                        <td>
                                            {item.confirmed
                                                ? "Confirmed"
                                                : "Pending"}
                                        </td>
                                        <td>{item.created_at_diff}</td>
                                        <td>
                                            <div className="flex">
                                                <PrimaryButton
                                                    type="button"
                                                    onClick={() =>
                                                        confirmDeposit(item.id)
                                                    }
                                                >
                                                    <i className="fas fa-check"></i>
                                                </PrimaryButton>
                                                <DangerButton
                                                    type="button"
                                                    onClick={() =>
                                                        denyDeposit(item.id)
                                                    }
                                                >
                                                    <i className="fas fa-times"></i>
                                                </DangerButton>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td
                                        colSpan="2"
                                        className="font-bold text-right"
                                    >
                                        Total
                                    </td>
                                    <td className="font-bold">
                                        {history?.sum}
                                    </td>
                                    <td colSpan="5"></td>
                                </tr>
                            </tfoot>
                        </Table>
                        <div>
                            {history?.links?.map((link) =>
                                link.url ? (
                                    <button
                                        type="button"
                                        key={`${link.label}-${link.url}`}
                                        className={`px-2 py-1 mx-1 border rounded ${link.active ? "bg-orange-500 text-white" : ""}`}
                                        onClick={() => {
                                            const url = new URL(link.url);
                                            visit(
                                                url.searchParams.get(
                                                    "status",
                                                ) ?? status,
                                                url.searchParams.get("sdate") ??
                                                    sdate,
                                                url.searchParams.get("edate") ??
                                                    edate,
                                                url.searchParams.get("page"),
                                            );
                                        }}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ) : (
                                    <span
                                        key={`${link.label}-disabled`}
                                        className="px-2 py-1 mx-1 text-gray-400 border rounded"
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ),
                            )}
                        </div>
                    </div>
                </Section>
            </Container>
        </AppLayout>
    );
}
