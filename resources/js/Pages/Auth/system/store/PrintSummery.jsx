import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const {
        activeTab = "commissions",
        filters = {},
        commissions = [],
        withdrawals = [],
    } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    const isWithdrawalTab = activeTab === "withdrawals";
    const rows = isWithdrawalTab ? withdrawals : commissions;
    const title = isWithdrawalTab ? "Withdrawal History" : "Distributed Commissions";

    return (
        <PrintLayout title={title}>
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>{title}</p>
                        <p>Tab: {isWithdrawalTab ? "Withdrawals" : "Commissions"}</p>
                        {filters?.search ? <p>Search: {filters.search}</p> : null}
                    </div>
                    <hr className="my-2" />

                    {isWithdrawalTab ? (
                        <Table data={rows}>
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name of User</th>
                                    <th>Store</th>
                                    <th>Server Cost</th>
                                    <th>Donation</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Requested At</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((item, index) => (
                                    <tr key={`${item.user_name}-${index}`}>
                                        <td>{item.sl}</td>
                                        <td>{item.user_name}</td>
                                        <td>{item.store_req}</td>
                                        <td>{item.maintenance_fee}</td>
                                        <td>{item.server_fee}</td>
                                        <td>{item.pay_by}</td>
                                        <td>{item.status}</td>
                                        <td>{item.requested_at}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="8">Total {rows.length} Items</td>
                                </tr>
                            </tfoot>
                        </Table>
                    ) : (
                        <Table data={rows}>
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name of User</th>
                                    <th>Store Info</th>
                                    <th>Given</th>
                                    <th>Range</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((item, index) => (
                                    <tr key={`${item.user_name}-${index}`}>
                                        <td>{item.sl}</td>
                                        <td>{item.user_name}</td>
                                        <td>{item.store}</td>
                                        <td>{item.amount}</td>
                                        <td>{item.range}</td>
                                        <td>{item.info}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="6">Total {rows.length} Items</td>
                                </tr>
                            </tfoot>
                        </Table>
                    )}
                </Container>
            </div>
        </PrintLayout>
    );
}
