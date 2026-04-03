import { router, usePage } from "@inertiajs/react";
import Container from "../../components/dashboard/Container";
import OrderStatus from "../../components/dashboard/OrderStatus";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionSection from "../../components/dashboard/section/Section";
import UserDash from "../../components/user/dash/UserDash";
import Table from "../../components/dashboard/table/Table";
import NavLink from "../../components/NavLink";
import SecondaryButton from "../../components/SecondaryButton";

export default function Orders() {
    const { orders, nav } = usePage().props;
    const remove = (id) => {
        if (confirm("Are you sure?")) {
            router.delete(route("user.orders.delete", id));
        }
    };

    console.log("orders", orders);


    const cancelOrder = (id) => {
        router.patch(route("user.orders.cancel", id));
    };

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader title="Your Orders" content="" />
                </SectionSection>
                <SectionSection>
                    <Table data={orders}>

                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Product</th>
                                <th>Total</th>
                                <th>Shop</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {orders.map((item) => (
                                <tr key={item.id}>
                                    <td>
                                        <NavLink
                                            href={route("user.orders.details", {
                                                id: item.id,
                                            })}
                                        >
                                            View
                                        </NavLink>
                                    </td>

                                    <td>{item.id}</td>

                                    <td>
                                        <OrderStatus status={item.status} />
                                    </td>

                                    <td>
                                        {item.cart_orders?.length ?? "N/A"} |{" "}
                                        {item.quantity ?? "N/A"}
                                    </td>

                                    <td>{item.total ?? "N/A"} TK</td>

                                    <td>
                                        {item?.shop?.shop_name_en}
                                        <i className="px-1 fas fa-caret-right"></i>
                                        {item.shop?.shop_name_bn}

                                        <br />

                                        <div className="text-xs">
                                            {item.shop?.village ?? "n/a"},{" "}
                                            {item.shop?.upozila ?? "n/a"},{" "}
                                            {item.shop?.district ?? "n/a"}
                                        </div>
                                    </td>

                                    <td>
                                        <SecondaryButton
                                            onClick={() => cancelOrder(item.id)}
                                        >
                                            cancel
                                        </SecondaryButton>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </SectionSection>
            </Container>
        </UserDash>
    );
}

{
    /* <OrderStatus status={order.status} /> */
}
