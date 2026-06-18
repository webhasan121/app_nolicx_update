import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import { formatCurrency } from "../../../../utils/formatAmount";

export default function Print({ orders = [], filters = {} }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Resel Orders Summary">
            <Head title="Resel Orders Summary" />

            <div id="pdf-content">
                <Container>
                    <Section>
                        <div className="text-center">
                            <h1>
                                <ApplicationName />
                            </h1>
                            <p>Resel Orders Summary</p>
                            {filters?.find ? <p>Search: {filters.find}</p> : null}
                            <p>
                                Status: {filters?.nav ?? "Pending"} | Type:{" "}
                                {filters?.type ?? "All"}
                            </p>
                        </div>
                    </Section>

                    <Section>
                        <Table data={orders}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Shop</th>
                                    <th>Sync</th>
                                    <th>Total</th>
                                    <th>Profit</th>
                                    <th>Shipping</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                {orders.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.sl}</td>
                                        <td>{item.id}</td>
                                        <td>
                                            <div>{item.shop_name_en || "N/A"}</div>
                                            <div>{item.seller_phone || "N/A"}</div>
                                        </td>
                                        <td>
                                            {item.sync
                                                ? `${item.sync.user_order_id}/${item.sync.user_cart_order_id}`
                                                : "Purchase"}
                                        </td>
                                        <td>
                                            {formatCurrency(item.total)} + {formatCurrency(item.shipping)}
                                        </td>
                                        <td>{formatCurrency(item.profit)}</td>
                                        <td>
                                            <div>{item.delevery || "N/A"}</div>
                                            <div>{item.location || "N/A"}</div>
                                        </td>
                                        <td>{item.created_at_formatted ?? "N/A"}</td>
                                        <td>{item.status ?? "Unknown"}</td>
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
