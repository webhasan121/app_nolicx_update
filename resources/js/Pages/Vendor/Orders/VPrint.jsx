import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import AppLayout from "../../../Layouts/App";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";

export default function VPrint({ order }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 2000);

        return () => {
            window.clearTimeout(timer);
        };
    }, []);

    return (
        <AppLayout title="View Orders" header={<PageHeader>View Orders</PageHeader>}>
            <Head title="View Orders" />

            <Container>
                <Section>
                    <div className="flex justify-between items-start px-5">
                        <div className="order-info">
                            <div className="flex items-center"></div>
                            <div>
                                Order Date : <span className="text-xs">{order?.created_at_daytime}</span>
                            </div>
                        </div>
                        <div className="order-total text-end">
                            <table className="table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>{order?.user?.name ?? "Not Found !"}</strong>
                                            <p className="text-xs">
                                                {order?.location}, {order?.house_no},
                                            </p>
                                            <p className="text-xs">
                                                Road - {order?.road_no}, House - {order?.house_no}
                                            </p>
                                            <div className="text-xs">{order?.printed_at_daytime}</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Section>

                <Section>
                    <Table data={order?.cart_orders ?? []}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Attr</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            {(order?.cart_orders ?? []).map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.id}</td>
                                    <td>
                                        <div className="flex items-start">
                                            {item.product_thumbnail ? (
                                                <img width="30" height="30" src={item.product_thumbnail} alt="" />
                                            ) : null}
                                            <div>{item.product_title}</div>
                                        </div>
                                    </td>
                                    <td>{item.quantity}</td>
                                    <td>{item.size}</td>
                                    <td>{item.line_total}</td>
                                </tr>
                            ))}
                            <tr className="text-md bg-gray-200">
                                <td className="text-end" colSpan="4">Sub Total</td>
                                <td></td>
                                <td>{order?.total ?? 0}</td>
                            </tr>
                            <tr className="text-md">
                                <td className="text-end" colSpan="4">Shipping</td>
                                <td></td>
                                <td>{order?.shipping ?? 0}</td>
                            </tr>
                            <tr className="text-md">
                                <td className="text-end" colSpan="4">Total Payable</td>
                                <td></td>
                                <td>{Number(order?.shipping ?? 0) + Number(order?.total ?? 0)}</td>
                            </tr>
                        </tbody>
                    </Table>
                </Section>
            </Container>
        </AppLayout>
    );
}
