import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Table from "../../../components/dashboard/table/Table";

export default function Print() {
    const { orders = [], filters = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="My Orders">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>My Orders</p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={orders}>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Product</th>
                                <th>Total</th>
                                <th>Shop</th>
                            </tr>
                        </thead>
                        <tbody>
                            {orders.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.id}</td>
                                    <td>{item.status}</td>
                                    <td>
                                        {item.cart_orders_count ?? "N/A"} | {item.quantity ?? "N/A"}
                                    </td>
                                    <td>{item.total ?? "N/A"} TK</td>
                                    <td>
                                        {item?.shop?.shop_name_en}
                                        {" / "}
                                        {item?.shop?.shop_name_bn}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="5">Total {orders.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
