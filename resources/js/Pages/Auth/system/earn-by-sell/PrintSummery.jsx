import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function PrintSummery() {
    const { filters = {}, products = [] } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Sell and Profit Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Sell and Profit Summary</p>
                        <p>
                            Flow: {filters?.nav ?? "sold"} | Shop Type: {filters?.user_type ?? "user"}
                        </p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                        {filters?.fd || filters?.lastDate ? (
                            <p>
                                {filters?.fd ? `From ${filters.fd}` : ""}
                                {filters?.fd && filters?.lastDate ? " " : ""}
                                {filters?.lastDate ? `To ${filters.lastDate}` : ""}
                            </p>
                        ) : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={products}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Flow</th>
                                <th>Owner</th>
                                <th>Price</th>
                                <th>Created</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {products.map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.id}</td>
                                    <td>
                                        {item.product_name}
                                        <br />
                                        <span className="text-xs">{item.product_status}</span>
                                    </td>
                                    <td>
                                        {item.user_type} to {item.belongs_to_type}
                                    </td>
                                    <td>{item.owner_name}</td>
                                    <td>{item.product_price ?? 0} TK</td>
                                    <td>{item.product_created_at}</td>
                                    <td>{item.status}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="8">Total {products.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
