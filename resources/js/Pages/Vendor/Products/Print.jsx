import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";
import ProductName from "../../../components/ProductName";
import { formatCurrency } from "../../../utils/formatAmount";

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
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Build Cost</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Insert At</th>
                                </tr>
                            </thead>
                            <tbody>
                                {products.map((product) => (
                                    <tr key={product.id}>
                                        <td>{product.sl}</td>
                                        <td><ProductName value={product.name} /></td>
                                        <td>{product.unit}</td>
                                        <td>{formatCurrency(product.buying_price)}</td>
                                        <td>{formatCurrency(product.price)}</td>
                                        <td>{formatCurrency(product.discount)}</td>
                                        <td>{product.orders_count ?? 0}</td>
                                        <td>{product.status}</td>
                                        <td>{product.created_at_human}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="9">Total {products.length} Items</td>
                                </tr>
                            </tfoot>
                        </Table>
                    </Section>
                </Container>
            </div>
        </PrintLayout>
    );
}
