import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";

export default function PrintSummery({ orders = [], filters = {} }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Orders Summary">
            <Head title="Orders Summary" />

            <div id="pdf-content">
                <Container>
                    <Section>
                        <div className="text-center">
                            <h1>
                                <ApplicationName />
                            </h1>
                            <p>Orders Summary</p>
                            {filters?.find ? <p>Search: {filters.find}</p> : null}
                        </div>
                    </Section>

                    <Section>
                        <Table data={orders}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Shipping</th>
                                    <th>Contact</th>
                                    <th>Com</th>
                                </tr>
                            </thead>
                            <tbody>
                                {orders.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.sl}</td>
                                        <td>{item.id}</td>
                                        <td>{item.cart_orders_count} / {item.quantity}</td>
                                        <td>{item.total} + {item.shipping}</td>
                                        <td>{item.status}</td>
                                        <td>{item.created_at_formatted}</td>
                                        <td>{item.delevery}</td>
                                        <td>{item.user_name} / {item.number}</td>
                                        <td>{item.comission}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="9">Total {orders.length} Items</td>
                                </tr>
                            </tfoot>
                        </Table>
                    </Section>
                </Container>
            </div>
        </PrintLayout>
    );
}
