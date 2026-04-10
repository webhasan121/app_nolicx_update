import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";

export default function ShopsPrint({
    filters = {},
    shops = [],
    selectedShop = null,
    products = [],
}) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Vendor Shops Summary">
            <Head title="Vendor Shops Summary" />

            <div id="pdf-content">
                <Container>
                    <Section>
                        <div className="text-center">
                            <h1>
                                <ApplicationName />
                            </h1>
                            <p>Vendor Shops Summary</p>
                            {filters?.q ? <p>Search: {filters.q}</p> : null}
                            {filters?.location ? <p>Location: {filters.location}</p> : null}
                            {selectedShop ? <p>Shop: {selectedShop.shop_name_en}</p> : null}
                        </div>
                    </Section>

                    {selectedShop ? (
                        <Section>
                            <Table data={products}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {products.map((product) => (
                                        <tr key={product.id}>
                                            <td>{product.sl}</td>
                                            <td>{product.id}</td>
                                            <td>{product.name}</td>
                                            <td>{product.unit}</td>
                                            <td>{product.price}</td>
                                            <td>{product.offer_type ? product.discount : "N/A"}</td>
                                            <td>{product.total_price}</td>
                                        </tr>
                                    ))}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colSpan="7">Total {products.length} Items</td>
                                    </tr>
                                </tfoot>
                            </Table>
                        </Section>
                    ) : (
                        <Section>
                            <Table data={shops}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Shop</th>
                                        <th>Village</th>
                                        <th>Upozila</th>
                                        <th>District</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {shops.map((shop) => (
                                        <tr key={shop.id}>
                                            <td>{shop.sl}</td>
                                            <td>{shop.id}</td>
                                            <td>{shop.shop_name_en}</td>
                                            <td>{shop.village}</td>
                                            <td>{shop.upozila}</td>
                                            <td>{shop.district}</td>
                                        </tr>
                                    ))}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colSpan="6">Total {shops.length} Items</td>
                                    </tr>
                                </tfoot>
                            </Table>
                        </Section>
                    )}
                </Container>
            </div>
        </PrintLayout>
    );
}
