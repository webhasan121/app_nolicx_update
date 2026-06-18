import { Head } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import { formatCurrency } from "../../../../utils/formatAmount";

export default function Details({ data = [] }) {
    return (
        <AppLayout title="Take Comissions" header={<PageHeader>Take Comissions</PageHeader>}>
            <Head title="Take Comissions" />

            <Container>
                <Section>
                    <Table data={data}>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order</th>
                                <th>Product</th>
                                <th>Buy</th>
                                <th>Sell</th>
                                <th>Profit</th>
                                <th>Rate</th>
                                <th>Take</th>
                                <th>Give</th>
                                <th>Store</th>
                                <th>Return</th>
                                <th>Confirmed</th>
                                <th>A/C</th>
                            </tr>
                        </thead>

                        <tbody>
                            {data.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.id ?? "N/A"}</td>
                                    <td>{item.order_id ?? 0}</td>
                                    <td>{item.product_id ?? 0}</td>
                                    <td>{formatCurrency(item.buying_price)}</td>
                                    <td>{formatCurrency(item.selling_price)}</td>
                                    <td>{formatCurrency(item.profit)}</td>
                                    <td>{item.comission_range ?? "0"} %</td>
                                    <td>{formatCurrency(item.take_comission)}</td>
                                    <td>{formatCurrency(item.distribute_comission)}</td>
                                    <td>{formatCurrency(item.store)}</td>
                                    <td>{formatCurrency(item.return)}</td>
                                    <td>
                                        {item.confirmed ? (
                                            <>
                                                <span className="p-1 px-2 rounded-xl bg-green-900 text-white">
                                                    Confirmed
                                                </span>
                                                <NavLink href={route("system.comissions.take.refund", { id: item.id })}>
                                                    {" "}Refund
                                                </NavLink>
                                            </>
                                        ) : (
                                            <>
                                                <span className="p-1 px-2 rounded-xl bg-gray-900 text-white">
                                                    Pending
                                                </span>
                                                <form
                                                    action={route("system.comissions.take.confirm", { id: item.id })}
                                                    method="post"
                                                >
                                                    <input
                                                        type="hidden"
                                                        name="_token"
                                                        value={document.querySelector('meta[name="csrf-token"]')?.content ?? ""}
                                                    />
                                                    <button type="submit">Confirm</button>
                                                </form>
                                            </>
                                        )}
                                    </td>
                                    <td>
                                        <div className="flex space-x-2">
                                            <NavLink href={route("system.comissions.distributes", { id: item.id })}>
                                                Details
                                            </NavLink>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </Section>
            </Container>
        </AppLayout>
    );
}
