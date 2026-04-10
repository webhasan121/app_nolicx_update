import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";

export default function Print({ products = [], filters = {} }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Products Summary">
            <Head title="Products Summary" />

            <div id="pdf-content">
                <Container>
                    <Section>
                        <div className="text-center">
                            <h1>
                                <ApplicationName />
                            </h1>
                            <p>Products Summary</p>
                            {filters?.search ? <p>Search: {filters.search}</p> : null}
                        </div>
                    </Section>

                    <Section>
                        <Table data={products}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Orders</th>
                                    <th>Cost</th>
                                    <th>Price</th>
                                    <th>Sell Price</th>
                                    <th>Insert At</th>
                                </tr>
                            </thead>
                            <tbody>
                                {products.map((product) => (
                                    <tr key={product.id}>
                                        <td>{product.sl}</td>
                                        <td>{product.id}</td>
                                        <td>{product.name}</td>
                                        <td>{product.unit}</td>
                                        <td>{product.status_label}</td>
                                        <td>{product.orders_count}</td>
                                        <td>{product.buying_price}</td>
                                        <td>{product.price}</td>
                                        <td>{product.sell_price}</td>
                                        <td>{product.created_at_human}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="10">Total {products.length} Items</td>
                                </tr>
                            </tfoot>
                        </Table>
                    </Section>
                </Container>
            </div>
        </PrintLayout>
    );
}
