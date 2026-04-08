import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import ApplicationName from "../../../../components/ApplicationName";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import Table from "../../../../components/dashboard/table/Table";
import PrintLayout from "../../../../Layouts/Print";

export default function PrintSummery({ sdate = "", edate = "", history }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Deposit Print Summery">
            <Head title="Deposit Print Summery" />
            <div className="my-3 ">
                <div className="w-full px-2 mx-auto space-y-6 max-w-8xl sm:px-6 lg:px-8 ">
                    <div className="mb-2 text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>
                            Deposit summery form {sdate} to {edate}
                        </p>
                    </div>
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
                                            <NavLinkBtn href={route("system.users.edit", { id: item.user.id })}>
                                                {item.user.name}
                                            </NavLinkBtn>
                                        </td>
                                        <td>{item.amount ?? 0}</td>
                                        <td>
                                            <div className="flex items-center">
                                                {item.senderAccountNumber} <i className="px-2 fas fa-caret-right"></i>
                                                {item.paymentMethod} <i className="px-2 fas fa-caret-right"></i>
                                                {item.receiverAccountNumber}
                                            </div>
                                        </td>
                                        <td>{item.transactionId ?? "N/A"}</td>
                                        <td>{item.confirmed ? "Confirmed" : "Pending"}</td>
                                        <td>{item.created_at_diff}</td>
                                        <td>
                                            <div className="flex">
                                                <button
                                                    type="button"
                                                    className="inline-flex items-center px-4 py-2 mr-1 text-xs font-semibold tracking-widest text-white uppercase transition ease-in-out duration-150 bg-orange-600 border border-transparent rounded-md hover:bg-orange-500 active:bg-orange-700 focus:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                                >
                                                    <i className="fas fa-check"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    className="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition ease-in-out duration-150 bg-red-600 border border-transparent rounded-md hover:bg-red-500 active:bg-red-700 focus:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                >
                                                    <i className="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="2" className="font-bold text-right">
                                        Total
                                    </td>
                                    <td className="font-bold">{history?.sum}</td>
                                    <td colSpan="5"></td>
                                </tr>
                            </tfoot>
                        </Table>
                    </div>
                </div>
            </div>
        </PrintLayout>
    );
}
