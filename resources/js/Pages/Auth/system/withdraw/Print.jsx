import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";
import PrintLayout from "../../../../Layouts/Print";

export default function Print({ filters, withdraws = [], summary }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 500);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Withdraw Print">
            <Head title="Withdraw Print" />

            <Container>
                <div className="w-ful text-center">
                    <div className="tex-xl">
                        <ApplicationName />
                    </div>
                    <div>
                        <p className="">
                            Withdraw Summery form {filters?.sdate_formatted ?? ""} to {filters?.edate_formatted ?? ""}
                        </p>
                    </div>
                </div>
                <br />
                <Table data={withdraws}>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Amount</th>
                            <th>Com</th>
                            <th>Payable</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        {withdraws.map((item, index) => (
                            <tr key={item.id} className={!item.seen_by_admin ? "bg-gray-200 font-bold" : ""}>
                                <td>{index + 1}</td>
                                <td>{item.created_at_formatted}</td>
                                <td>
                                    <div>
                                        <div className="flex">
                                            {item.user?.name}
                                            {item.user?.subscription ? (
                                                <span className="bg-indigo-900 text-white ms-1 px-1 rounded">
                                                    vip
                                                </span>
                                            ) : null}
                                            <span className="bg-gray-900 text-white ms-1 px-1 rounded-full">
                                                U
                                            </span>
                                        </div>

                                        {item.user?.email}
                                    </div>
                                </td>
                                <td>{item.amount ?? "0"} TK</td>
                                <td>{item.total_fee ?? "0"} TK</td>
                                <td>{item.payable_amount ?? "0"} TK</td>
                                <td>
                                    {!item.is_rejected ? (
                                        item.status ? "Accept" : "Pending"
                                    ) : (
                                        <div className="p-1">Reject</div>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                    <tfoot>
                        <tr className="font-bold">
                            <td colSpan="3" className="text-right font-bold">Total</td>
                            <td className="font-bold">{summary?.sum_amount ?? 0}</td>
                            <td className="font-bold">{summary?.sum_total_fee ?? 0}</td>
                            <td className="font-bold">{summary?.sum_payable_amount ?? 0}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </Table>
            </Container>
        </PrintLayout>
    );
}
