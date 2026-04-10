import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const { cod = [], filters = {}, summary = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    const formatDate = (value) => {
        if (!value) {
            return "";
        }

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleDateString("en-GB");
    };

    return (
        <PrintLayout title="Consignment Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Consignment Summary</p>
                        <p>Status: {filters?.type ?? "Pending"}</p>
                        {filters?.sdate || filters?.edate ? (
                            <p>
                                {filters?.sdate ? `From ${formatDate(filters.sdate)}` : ""}
                                {filters?.sdate && filters?.edate ? " " : ""}
                                {filters?.edate ? `To ${formatDate(filters.edate)}` : ""}
                            </p>
                        ) : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={cod}>
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
                            </tr>
                        </thead>
                        <tbody>
                            {cod.map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
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
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>{summary?.count ?? 0}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{summary?.amount ?? 0}</td>
                                <td>{summary?.rider_amount ?? 0}</td>
                                <td>{summary?.total_amount ?? 0}</td>
                                <td>{summary?.system_comission ?? 0}</td>
                                <td>{summary?.comission ?? 0}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
