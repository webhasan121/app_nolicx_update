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
        <PrintLayout title="Sell and Profit Summary">
            <Head title="Sell and Profit Summary" />

            <div id="pdf-content">
                <Container>
                    <Section>
                        <div className="text-center">
                            <h1>
                                <ApplicationName />
                            </h1>
                            <p>Sell and Profit Summary</p>
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
                                    <th>Flow</th>
                                    <th>Owner</th>
                                    <th>Price</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {products.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.sl}</td>
                                        <td>{item.id}</td>
                                        <td>{item.product_name}</td>
                                        <td>{item.user_type} to {item.belongs_to_type}</td>
                                        <td>{item.owner_name}</td>
                                        <td>{item.product_price} TK</td>
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
                    </Section>
                </Container>
            </div>
        </PrintLayout>
    );
}
