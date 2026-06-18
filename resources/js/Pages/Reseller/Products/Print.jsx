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
    const toNumber = (value) => Number(value || 0) || 0;
    const totals = products.reduce(
        (carry, product) => ({
            orders: carry.orders + toNumber(product.orders_count),
            cost: carry.cost + toNumber(product.buying_price),
            price: carry.price + toNumber(product.price),
            sellPrice: carry.sellPrice + toNumber(product.sell_price),
        }),
        {
            orders: 0,
            cost: 0,
            price: 0,
            sellPrice: 0,
        }
    );

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
                                        <td><ProductName value={product.name} /></td>
                                        <td>{product.unit}</td>
                                        <td>{product.status_label}</td>
                                        <td>{product.orders_count}</td>
                                        <td>{formatCurrency(product.buying_price)}</td>
                                        <td>{formatCurrency(product.price)}</td>
                                        <td>{formatCurrency(product.sell_price)}</td>
                                        <td>{product.created_at_human}</td>
                                    </tr>
                                ))}
                                <tr className="font-bold">
                                    <td colSpan="5">Total {products.length} Items</td>
                                    <td>{totals.orders}</td>
                                    <td>{formatCurrency(totals.cost)}</td>
                                    <td>{formatCurrency(totals.price)}</td>
                                    <td>{formatCurrency(totals.sellPrice)}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </Table>
                    </Section>
                </Container>
            </div>
        </PrintLayout>
    );
}
