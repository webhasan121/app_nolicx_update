import { Head } from "@inertiajs/react";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import AppLayout from "../../../../Layouts/App";

export default function Distributes({ takes, distributes = [] }) {
    return (
        <AppLayout title="Products Comissions" header={<PageHeader>Products Comissions</PageHeader>}>
            <Head title="Products Comissions" />

            <Container>
                <div className="flex space-x-2">
                    <PrimaryButton>Seller</PrimaryButton>
                    <PrimaryButton>Buyer</PrimaryButton>
                    <PrimaryButton>Product</PrimaryButton>
                </div>

                <Section>
                    <SectionHeader title="Comissions of Products In Order" content="" />

                    <SectionInner>
                        <Table data={[takes]}>
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
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>{takes?.id ?? "N/A"}</td>
                                    <td>{takes?.order_id ?? 0}</td>
                                    <td>
                                        {takes?.product_thumbnail ? (
                                            <img src={`/storage/${takes.product_thumbnail}`} alt="" />
                                        ) : null}
                                        {takes?.product_name ?? 0}
                                    </td>
                                    <td>{takes?.buying_price ?? 0}</td>
                                    <td>{takes?.selling_price ?? 0}</td>
                                    <td>{takes?.profit ?? "0"}</td>
                                    <td>{takes?.comission_range ?? "0"} %</td>
                                    <td>{takes?.take_comission ?? "0"}</td>
                                    <td>{takes?.distribute_comission ?? "0"}</td>
                                    <td>{takes?.store ?? "0"}</td>
                                    <td>{takes?.return ?? "0"}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>

                <Section>
                    <SectionInner>
                        <Table data={distributes}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Product</th>
                                    <th>Amount</th>
                                    <th>Range</th>
                                    <th>Confirmed</th>
                                </tr>
                            </thead>
                            <tbody>
                                {distributes.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.id}</td>
                                        <td>
                                            {item.user_name ?? 0}
                                            {item.user_id == takes?.user_id ? (
                                                <i className="px-1 fas fa-check-circle"></i>
                                            ) : null}
                                        </td>
                                        <td>{item.product_name ?? 0}</td>
                                        <td>{item.amount ?? 0}</td>
                                        <td>{item.range ?? 0} %</td>
                                        <td>
                                            {item.confirmed ? (
                                                <>
                                                    <span className="p-1 px-2 rounded-xl bg-green-900 text-white">
                                                        Confirmed
                                                    </span>
                                                    <NavLink href={route("system.comissions.distribute.refund", { id: item.id })}>
                                                        {" "}Refund{" "}
                                                    </NavLink>
                                                </>
                                            ) : (
                                                <>
                                                    <span className="p-1 px-2 rounded-xl bg-gray-900 text-white">
                                                        Pending
                                                    </span>
                                                    <NavLink href={route("system.comissions.distribute.confirm", { id: item.id })}>
                                                        {" "}Confirm{" "}
                                                    </NavLink>
                                                </>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
