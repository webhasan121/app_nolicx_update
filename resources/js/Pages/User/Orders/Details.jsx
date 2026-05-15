import { useForm, usePage } from "@inertiajs/react";
import { useState } from "react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import UserDash from "../../../components/user/dash/UserDash";
import Table from "../../../components/dashboard/table/Table";
import NavLink from "../../../components/NavLink";
import PrimaryButton from "../../../components/PrimaryButton";
import DangerButton from "../../../components/DangerButton";
import SecondaryButton from "../../../components/SecondaryButton";
import Modal from "../../../components/Modal";
import InputError from "../../../components/InputError";
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

function TimelineItem({ title, description }) {
    return (
        <div className="relative flex items-center px-2 py-2 border-l">
            <i
                className="absolute w-12 h-12 fas fa-check-circle"
                style={{ left: "-8px", top: "12px" }}
            ></i>
            <div className="px-4">
                <p>{title}</p>
                {description ? <p className="text-xs">{description}</p> : null}
            </div>
        </div>
    );
}

function buildOrderTimeline(order) {
    if (!order) {
        return [];
    }

    const placed = {
        title: "Placed the order",
        description: order.created_at,
    };
    const accepted = {
        title: "Order has been accepted by seller.",
    };
    const packed = {
        title: "Order Packed.",
        description: "Order product has been packed and ready for shipment.",
    };
    const sent = {
        title: "Order Send",
        description:
            order.delevery === "cash"
                ? `Order has been send to ${order.location ?? "N/A"}`
                : `Order has beed send to ${order.location ?? "N/A"}.`,
    };
    const assigned = {
        title: order.status === "Delivery" ? "Order Assignedd to Rider" : "Order Assigned",
        description:
            order.delevery === "cash" && order.assigned_rider?.name
                ? `Assigned to rider ${order.assigned_rider.name}`
                : "",
    };
    const delivered = {
        title: "Delivered",
        description: "Order has been marked as delivered to you by rider at.",
    };
    const finished = {
        title: "Success and Finished",
    };

    switch (order.status) {
        case "Pending":
            return [placed];
        case "Accept":
            return [accepted, placed];
        case "Picked":
            return [packed, accepted, placed];
        case "Delivery":
            return order.assigned_rider
                ? [assigned, sent, packed, accepted, placed]
                : [sent, packed, accepted, placed];
        case "Delivered":
            return order.assigned_rider
                ? [delivered, assigned, sent, packed, accepted, placed]
                : [delivered, sent, packed, accepted, placed];
        case "Confirm":
            return [finished, assigned, sent, packed, accepted, placed];
        default:
            return [];
    }
}

export default function OrderDetails() {
    const { order } = usePage().props;
    const [showRatingModal, setShowRatingModal] = useState(false);
    const orderTotal = Number(order?.total ?? 0);
    const shippingTotal = Number(order?.shipping ?? 0);
    const payableTotal = orderTotal + shippingTotal;
    const orderTimeline = buildOrderTimeline(order);
    const { data, setData, post, processing, errors } = useForm({
        reviews: order.cart_orders.map((item) => ({
            cart_order_id: item.id,
            product_id: item.product?.id,
            rating: item.review?.rating ?? 5,
            comments: item.review?.comments ?? "",
        })),
    });

    const markAsReceived = () => {
        post(route("user.orders.received", { id: order.id }), {
            preserveScroll: true,
            onSuccess: () => setShowRatingModal(false),
        });
    };

    const updateReview = (index, field, value) => {
        setData(
            "reviews",
            data.reviews.map((review, reviewIndex) =>
                reviewIndex === index ? { ...review, [field]: value } : review,
            ),
        );
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
                                <div className="items-center justify-between w-full space-y-2 overflow-hidden overflow-x-scroll md:flex">
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
                                                    <PrimaryButton onClick={() => setShowRatingModal(true)}>
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
                                <td>{orderTotal} TK</td>
                            </tr>
                            <tr>
                                <td colSpan="5" className="text-right">
                                    Shipping
                                </td>
                                <td>{shippingTotal} Tk</td>
                            </tr>
                            <tr className="bg-gray-200">
                                <td colSpan="5" className="text-right">
                                    Payable
                                </td>
                                <td>{payableTotal} TK</td>
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
                                    {shippingTotal} TK
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

                        <div>
                            {orderTimeline.map((item, index) => (
                                <TimelineItem
                                    key={`${item.title}-${index}`}
                                    title={item.title}
                                    description={item.description}
                                />
                            ))}
                        </div>
                    </SectionSection>
                </div>
            </Container>

            <Modal show={showRatingModal} onClose={() => setShowRatingModal(false)} maxWidth="2xl">
                <div className="p-6">
                    <div className="mb-4">
                        <h2 className="text-lg font-bold">Rate Your Order</h2>
                        <p className="text-sm text-gray-500">
                            Submit your product rating before marking this order as received.
                        </p>
                    </div>

                    <div className="space-y-4">
                        {order.cart_orders.map((item, index) => (
                            <div key={item.id} className="p-4 border rounded-md">
                                <div className="flex items-start gap-3">
                                    {item.product?.thumbnail ? (
                                        <img
                                            width="48"
                                            height="48"
                                            className="object-cover border rounded"
                                            src={`/storage/${item.product.thumbnail}`}
                                            alt=""
                                        />
                                    ) : null}
                                    <div className="flex-1">
                                        <div className="font-semibold">{item.product?.name ?? "N/A"}</div>
                                        <div className="flex items-center gap-1 py-2">
                                            {[1, 2, 3, 4, 5].map((star) => (
                                                <button
                                                    key={star}
                                                    type="button"
                                                    className="text-xl"
                                                    onClick={() => updateReview(index, "rating", star)}
                                                    title={`${star} star`}
                                                >
                                                    <i
                                                        className="fas fa-star"
                                                        style={{
                                                            color:
                                                                Number(data.reviews[index]?.rating ?? 0) >= star
                                                                    ? "var(--brand-primary)"
                                                                    : "#9ca3af",
                                                        }}
                                                    ></i>
                                                </button>
                                            ))}
                                            <span className="pl-2 text-sm text-gray-500">
                                                {data.reviews[index]?.rating}/5
                                            </span>
                                        </div>
                                        <InputError messages={errors[`reviews.${index}.rating`]} />
                                        <textarea
                                            className="w-full rounded-md border-gray-300 text-sm"
                                            rows="3"
                                            required
                                            placeholder="Write your message"
                                            value={data.reviews[index]?.comments ?? ""}
                                            onChange={(event) =>
                                                updateReview(index, "comments", event.target.value)
                                            }
                                        />
                                        <InputError messages={errors[`reviews.${index}.comments`]} />
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="flex justify-end gap-2 mt-6">
                        <SecondaryButton onClick={() => setShowRatingModal(false)}>
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton onClick={markAsReceived} disabled={processing}>
                            Submit Rating & Mark Received
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>
        </UserDash>
    );
}
