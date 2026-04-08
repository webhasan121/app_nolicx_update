import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import PrintLayout from "../../../../Layouts/Print";

export default function Takes({ filters, comissions = [] }) {
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 500);

        return () => window.clearTimeout(timer);
    }, []);

    const where = filters?.where ?? "";
    const from = filters?.from_formatted ?? "";
    const to = filters?.to_formatted ?? "";

    return (
        <PrintLayout title="Comissions Takes">
            <Head title="Comissions Takes" />

            <Container>
                <div className="w-ful text-center">
                    <div className="tex-xl">
                        <ApplicationName />
                    </div>
                    <div>
                        <p className="">
                            Comisstion Summery form {from} to {to}
                        </p>
                    </div>
                </div>
                <Section>
                    <Table data={comissions}>
                        <thead>
                            <tr>
                                <th>ID</th>
                                {where === "user_id" ? <th>Seller</th> : null}
                                {where === "order_id" ? <th>Order</th> : null}
                                {where === "product_id" ? <th>Product</th> : null}
                                <th>Buy</th>
                                <th>Sell</th>
                                <th>Profit</th>
                                <th>Rate</th>
                                <th>Take</th>
                                <th>Give</th>
                                <th>Store</th>
                                <th>Date</th>
                                <th>Confirmed</th>
                            </tr>
                        </thead>

                        <tbody>
                            {comissions.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.id ?? "N/A"}</td>
                                    {where === "user_id" ? <th>{item.user_id}</th> : null}
                                    {where === "order_id" ? <td>{item.order_id ?? 0}</td> : null}
                                    {where === "product_id" ? <td>{item.product_id ?? 0}</td> : null}
                                    <td>{item.buying_price ?? 0}</td>
                                    <td>{item.selling_price ?? 0}</td>
                                    <td>{item.profit ?? "0"}</td>
                                    <td>{item.comission_range ?? "0"} %</td>
                                    <td>{item.take_comission ?? "0"}</td>
                                    <td>{item.distribute_comission ?? "0"}</td>
                                    <td>{item.store ?? "0"}</td>
                                    <td>{item.created_at_formatted}</td>
                                    <td>
                                        {item.confirmed ? (
                                            <span className="p-1 px-2 text-white bg-green-900 rounded-xl">
                                                Confirmed
                                            </span>
                                        ) : (
                                            <span className="p-1 px-2 text-white bg-gray-900 rounded-xl">
                                                Pending
                                            </span>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr className="py-2 bg-gray-200">
                                <td>{comissions.length}</td>
                                <td className="font-bold">
                                    {comissions.reduce((sum, item) => sum + Number(item.buying_price || 0), 0)}
                                </td>
                                <td className="font-bold">
                                    {comissions.reduce((sum, item) => sum + Number(item.selling_price || 0), 0)}
                                </td>
                                <td className="font-bold">
                                    {comissions.reduce((sum, item) => sum + Number(item.profit || 0), 0)}
                                </td>
                                <td></td>
                                <td className="font-bold">
                                    {comissions.reduce((sum, item) => sum + Number(item.take_comission || 0), 0)}
                                </td>
                                <td className="font-bold">
                                    {comissions.reduce((sum, item) => sum + Number(item.distribute_comission || 0), 0)}
                                </td>
                                <td className="font-bold">
                                    {comissions.reduce((sum, item) => sum + Number(item.store || 0), 0)}
                                </td>
                                <td className="font-bold"></td>
                                <td className="font-bold"></td>
                            </tr>
                        </tfoot>
                    </Table>
                </Section>
            </Container>
        </PrintLayout>
    );
}
