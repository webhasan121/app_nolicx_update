import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Table from "../../../components/dashboard/table/Table";

export default function Print() {
    const { withdraw = [], filters = {}, available_balance } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Wallet Withdraw Requests">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Wallet Withdraw Requests</p>
                        <p>Available Balance: {available_balance ?? 0} TK</p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={withdraw}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {withdraw.map((item, index) => (
                                <tr key={item.id}>
                                    <td>#{index + 1}</td>
                                    <td>{item.amount} TK</td>
                                    <td>{item.status}</td>
                                    <td>
                                        {item.created_at} - {item.created_at_human}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="4">Total {withdraw.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
