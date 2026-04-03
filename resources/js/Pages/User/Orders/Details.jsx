import { router, usePage } from "@inertiajs/react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import UserDash from "../../../components/user/dash/UserDash";
import Table from "../../../components/dashboard/table/Table";
import NavLink from "../../../components/NavLink";
import PrimaryButton from "../../../components/PrimaryButton";
import DangerButton from "../../../components/DangerButton";
import Hr from "../../../components/Hr";

const activeStatuses = {
    Placed: ["Pending", "Accept", "Picked", "Delivery", "Delivered", "Confirm"],
    Accept: ["Accept", "Picked", "Delivery", "Delivered", "Confirm"],
    Collecting: ["Picked", "Delivery", "Delivered", "Confirm"],
    Delivery: ["Delivery", "Delivered", "Confirm"],
    Delivered: ["Delivered", "Confirm"],
    Confirm: ["Confirm"],
};

function StatusBox({ label, orderStatus, statuses, title }) {
    const active = statuses.includes(orderStatus);

    return (
        <div
            className={`p-2 px-3 rounded-md cursor-pointer text-center ${
                active ? "bg-indigo-900 text-white" : "bg-gray-100 text-gray-600"
            }`}
            title={title}
        >
            {label}
            <br />
            <div className={active ? "block" : "hidden"}>
                <i className="fas fa-check-circle"></i>
            </div>
        </div>
    );
}

export default function OrderDetails() {
    const { order } = usePage().props;

    const markAsReceived = () => {
        router.post(route("user.orders.received", { id: order.id }));
    };

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div>
                                Order Details
                                <br />
                                <div className="text-xs">
                                    {order.created_at} at {order.created_time}
                                </div>
                            </div>
                        }
                        content={
                            <div>
                                <div>Order Id : {order.id}</div>
                                <div className="w-full overflow-hidden overflow-x-scroll md:flex justify-between items-center space-y-2">
                                    <div>
                                        <div className="flex gap-2 mb-2">
                                            <StatusBox
                                                label="Placed"
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Placed}
                                                title="Buyer placed the order. Order in Pending"
                                            />
                                            <StatusBox
                                                label="Accept"
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Accept}
                                                title="Accept the order for process"
                                            />
                                            <StatusBox
                                                label={order.status === "Picked" ? "Collected" : "Collecting"}
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Collecting}
                                                title="Find and collect the product"
                                            />
                                            <StatusBox
                                                label="Delivery"
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Delivery}
                                                title="product shipped to rider or courier."
                                            />
                                            <StatusBox
                                                label="Delivered"
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Delivered}
                                                title="product delivered to the buyer.and buyer successfully received the order"
                                            />
                                            <StatusBox
                                                label="Confirm"
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Confirm}
                                                title="Confirmed"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <div className="flex gap-2 mb-2">
                                            {order.status === "Delivered" &&
                                                (order.received_at ? (
                                                    <div className="flex items-center gap-2 p-2 px-3 text-center text-white bg-indigo-900 rounded-md cursor-pointer">
                                                        <i className="fas fa-check-circle"></i>
                                                        <span>Already Received</span>
                                                    </div>
                                                ) : (
                                                    <PrimaryButton onClick={markAsReceived}>
                                                        Mark as Received
                                                    </PrimaryButton>
                                                ))}
                                            <div
                                                className={`p-2 px-3 rounded-md cursor-pointer text-center ${
                                                    order.status === "Hold"
                                                        ? "bg-indigo-900 text-white"
                                                        : "bg-gray-100 text-gray-600"
                                                }`}
                                            >
                                                Hold
                                                <br />
                                                <div className={order.status === "Hold" ? "block" : "hidden"}>
                                                    <i className="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                            <div
                                                className={`p-2 px-3 rounded-md cursor-pointer text-center ${
                                                    order.status === "Reject"
                                                        ? "bg-indigo-900 text-white"
                                                        : "bg-gray-100 text-gray-600"
                                                }`}
                                            >
                                                Reject
                                                <br />
                                                <div className={order.status === "Reject" ? "block" : "hidden"}>
                                                    <i className="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                        </div>
                                        {order.status === "Rejecte" && (
                                            <DangerButton>Order Cancelled</DangerButton>
                                        )}
                                    </div>
                                </div>
                            </div>
                        }
                    />
                </SectionSection>

                <SectionSection>
                    <Table data={order.cart_orders}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Attr</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {order.cart_orders.map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>
                                        <NavLink
                                            className="text-xs"
                                            href={route("products.details", {
                                                id: item.product?.id ?? "",
                                                slug: item.product?.slug ?? "",
                                            })}
                                        >
                                            {item.product?.thumbnail && (
                                                <img
                                                    width="30"
                                                    height="30"
                                                    src={`/storage/${item.product.thumbnail}`}
                                                    alt=""
                                                />
                                            )}
                                            {item.product?.name ?? "N/A"}
                                        </NavLink>
                                    </td>
                                    <td>{item.quantity}</td>
                                    <td>{item.size}</td>
                                    <td>{item.price}</td>
                                    <td>{item.total}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr className="bg-gray-100">
                                <td colSpan="5" className="text-right">
                                    Total
                                </td>
                                <td>{order.total ?? "0"} TK</td>
                            </tr>
                            <tr>
                                <td colSpan="5" className="text-right">
                                    Shipping
                                </td>
                                <td>{order.shipping ?? "120"} Tk</td>
                            </tr>
                            <tr className="bg-gray-200">
                                <td colSpan="5" className="text-right">
                                    Payable
                                </td>
                                <td>{(order.shipping || 0) + (order.total || 0)} TK</td>
                            </tr>
                        </tfoot>
                    </Table>
                </SectionSection>

                <div className="max-w-md">
                    <SectionSection>
                        <div className="flex items-center justify-between">
                            <div>Shipping</div>
                            <div>
                                <div className="px-2 py-1 text-white bg-indigo-900 rounded-lg">
                                    {order.shipping ?? "0"} TK
                                </div>
                            </div>
                        </div>

                        <div className="pt-2">
                            <div className="flex items-center text-xs sapce-x-2">
                                {order.delevery} Delevery{" "}
                                {order.area_condition === "Dhaka"
                                    ? "in Dhaka"
                                    : "Outside of Dhaka"}
                            </div>
                        </div>

                        <div className="mb-10 text-sm">
                            <b>{order.location ?? "N/A"}</b>
                            <br />
                            Phone : {order.number ?? "N/A"}
                        </div>

                        <div className="mb-6">
                            <h3 className="mb-2">Assign To</h3>
                            <Hr />
                            <div>
                                {order.assigned_rider ? (
                                    <div>
                                        <div className="text-lg font-bold text-bold">
                                            {order.assigned_rider.name}
                                        </div>
                                        <p>{order.assigned_rider.phone}</p>
                                    </div>
                                ) : (
                                    <div>N/A</div>
                                )}
                            </div>
                        </div>
                    </SectionSection>
                </div>
            </Container>
        </UserDash>
    );
}

