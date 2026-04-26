import { Head, router, useForm } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "../../../Layouts/App";
import NavLinkBtn from "../../../components/NavLinkBtn";
import PageHeader from "../../../components/dashboard/PageHeader";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import Table from "../../../components/dashboard/table/Table";
import PrimaryButton from "../../../components/PrimaryButton";
import SecondaryButton from "../../../components/SecondaryButton";
import OverviewSection from "../../../components/dashboard/overview/Section";
import Div from "../../../components/dashboard/overview/Div";
import Modal from "../../../components/Modal";

const progressFlow = [
    "Pending",
    "Accept",
    "Picked",
    "Delivery",
    "Delivered",
    "Confirm",
];

const quickStatus = ["Hold", "Cancelled", "Reject"];

export default function View({ order }) {
    const [comissionOpen, setComissionOpen] = useState(false);
    const [acceptOpen, setAcceptOpen] = useState(false);
    const [riderOpen, setRiderOpen] = useState(false);
    const form = useForm({
        status: order?.status ?? "Pending",
        shipping: order?.shipping ?? "",
        rider_id: "",
    });



    const submitStatus = (status) => {
        router.post(
            route("vendor.orders.status", { order: order.id }),
            {
                ...form.data,
                status,
            },
            {
            preserveScroll: true,
            },
        );
    };

    const currentFlowIndex = progressFlow.indexOf(order?.status);
    const flowLabel = ["Placed", "Accept", "Picked", "Delivery", "Delivered", "Confirm"];

    const statusMessage = () => {
        switch (order?.status) {
            case "Pending":
                return "Order Now on pending for your confirmation !";
            case "Accept":
                return "Order Accepted ! It's time to Assign a Rider.";
            case "Picked":
                return "Packed The items.";
            case "Delivery":
                return "On Shipment.";
            case "Delivered":
                return order?.received_at
                    ? "Finally Done !"
                    : "Please wait ! The customer has not yet confirmed receiving the order.";
            case "Confirm":
                return "Congratulation ! Order has been Confirmed.";
            case "Hold":
                return "Order On-Hold.";
            case "Cancelled":
                return "Buyer cancelled the Order.";
            case "Reject":
                return "Order has beed rejected.";
            default:
                return "";
        }
    };

    const canFinish = Boolean(order?.received_at);

    const removeRider = (codId) => {
        router.delete(route("vendor.orders.rider.remove", { order: order.id, cod: codId }));
    };

    return (
        <AppLayout
            title="View Orders"
            header={
                <PageHeader>
                    View Orders
                    <br />
                    <div className="text-sm font-normal">
                        {order?.user_type} <i className="mx-2 fas fa-arrow-right"></i>{" "}
                        {order?.belongs_to_type}
                    </div>
                    <div className="flex items-center text-xs sapce-x-2">
                        {order?.delevery} Delvevery <i className="px-2 fas fa-caret-right"></i>{" "}
                        {order?.area_condition === "Dhaka" ? "Inside Dhaka" : "Outside of Dhaka"}
                    </div>
                </PageHeader>
            }
        >
            <Head title="View Orders" />

            <Container>
                <Section>
                    <div className="border rounded-md">
                        <div className="p-3">
                            <div className="flex flex-wrap gap-2 mb-2">
                                {progressFlow.map((step, idx) => {
                                    const done = currentFlowIndex >= idx;
                                    return (
                                        <div
                                            key={step}
                                            className={`p-2 px-3 rounded-md cursor-pointer text-center ${
                                                done ? "bg-indigo-900 text-white" : "bg-gray-100 text-gray-600"
                                            }`}
                                        >
                                            {flowLabel[idx]}
                                            <br />
                                            {done ? <i className="fas fa-check-circle"></i> : null}
                                        </div>
                                    );
                                })}
                                {quickStatus.map((step) => (
                                    <div
                                        key={step}
                                        className={`p-2 px-3 rounded-md cursor-pointer text-center ${
                                            order?.status === step ? "bg-indigo-900 text-white" : "bg-gray-100 text-gray-600"
                                        }`}
                                    >
                                        {step}
                                        <br />
                                        {order?.status === step ? <i className="fas fa-check-circle"></i> : null}
                                    </div>
                                ))}
                            </div>
                        </div>
                        <hr />
                        <div className="p-3">
                            <div className="pb-2">{statusMessage()}</div>
                            {order?.status === "Accept" ? (
                                <ul className="pb-2 pl-5 text-sm list-disc">
                                    <li>Order now visible at rider dashboard for confirmation.</li>
                                    <li>Or You can assign a rider from bellow button. If you assign a rider, order might hide from rider public dashboard, And assigned rider can view the order shipment.</li>
                                    <li>If you wish to send custom shipping method, skip this section and go next.</li>
                                </ul>
                            ) : null}
                            {order?.status === "Picked" ? (
                                <ul className="pb-2 pl-5 text-sm list-disc">
                                    <li>Get your order item from your wirehouse.</li>
                                    <li>Packed the items for shipment.</li>
                                    <li>Send items to rider. Or courier delivery.</li>
                                    <li>If there have multiple rider assigned, Remove all of them except your targeted one.</li>
                                    <li>If done, click next button. Next steps rider able to <strong>Make as Received</strong> the order.</li>
                                </ul>
                            ) : null}
                            {order?.status === "Delivery" ? (
                                <ul className="pb-2 pl-5 text-sm list-disc">
                                    <li>Product On the Ride. You Already Send your product to rider or direct delivery to the customer.</li>
                                    <li>Now rider able to <strong>Make as Received</strong> the order, if you send it through rider.</li>
                                    <li>If order send to rider, Check bottom rider status. Make confirm rider status changed to <strong>Received</strong>.</li>
                                    <li>Without going to next steps rider unable to <strong>Mark as Delivered</strong>.</li>
                                    <li><strong>Done !</strong> Go to next steps.</li>
                                </ul>
                            ) : null}
                            {order?.status === "Delivered" ? (
                                <ul className="pb-2 pl-5 text-sm list-disc">
                                    {canFinish ? (
                                        <>
                                            <li>Customer successfully received the order.</li>
                                            <li><strong>Finished</strong> the order.</li>
                                            <li>This dispatch your comission</li>
                                        </>
                                    ) : (
                                        <>
                                            <li>The customer has not yet confirmed receiving the order.</li>
                                            <li>The order can be completed once the customer confirms receipt.</li>
                                        </>
                                    )}
                                </ul>
                            ) : null}
                            <div className="flex flex-wrap items-center gap-2 pb-2">
                                {order?.status === "Pending" ? (
                                    <>
                                        <PrimaryButton type="button" onClick={() => setAcceptOpen(true)}>
                                            Accept order
                                        </PrimaryButton>
                                    </>
                                ) : null}

                                {order?.status === "Accept" ? (
                                    <>
                                        {Number(order?.has_rider_count ?? 0) < 1 ? (
                                            <PrimaryButton type="button" onClick={() => setRiderOpen(true)}>
                                                <i className="pr-2 fas fa-plus"></i> Rider
                                            </PrimaryButton>
                                        ) : null}
                                        <PrimaryButton type="button" onClick={() => submitStatus("Picked")}>
                                            Next
                                        </PrimaryButton>
                                    </>
                                ) : null}
                                {order?.status === "Picked" ? (
                                    <PrimaryButton type="button" onClick={() => submitStatus("Delivery")}>
                                        Next
                                    </PrimaryButton>
                                ) : null}
                                {order?.status === "Delivery" ? (
                                    <PrimaryButton type="button" onClick={() => submitStatus("Delivered")}>
                                        Next
                                    </PrimaryButton>
                                ) : null}
                                {order?.status === "Delivered" && canFinish ? (
                                    <PrimaryButton type="button" onClick={() => submitStatus("Confirm")}>
                                        Finished
                                    </PrimaryButton>
                                ) : null}

                                {order?.status !== "Confirm" ? (
                                    <>
                                        <SecondaryButton type="button" onClick={() => submitStatus("Hold")}>
                                            Hold
                                        </SecondaryButton>
                                        <SecondaryButton type="button" onClick={() => submitStatus("Cancelled")}>
                                            Cancelled
                                        </SecondaryButton>
                                        <SecondaryButton type="button" onClick={() => submitStatus("Reject")}>
                                            Reject
                                        </SecondaryButton>
                                    </>
                                ) : null}
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center justify-end mt-2 space-x-2">
                        {Number(order?.has_rider_count ?? 0) > 0 ? (
                            <PrimaryButton type="button">
                                <i className="pr-2 fas fa-truck-fast"></i> Rider Assigned
                            </PrimaryButton>
                        ) : null}

                        {order?.account_type === "vendor" && order?.name === "Resel" ? (
                            <SecondaryButton type="button">
                                Resel Profit {order?.reseller_profit_sum ?? 0} TK
                            </SecondaryButton>
                        ) : null}

                        {order?.account_type === "vendor" && order?.name === "Purchase" ? (
                            <PrimaryButton type="button">Purchase</PrimaryButton>
                        ) : null}

                        {(order?.account_type ?? "").toLowerCase() === "reseller" ? (
                            <SecondaryButton type="button" onClick={() => setComissionOpen(true)}>
                                {(order?.system_comission_rate ?? 0)} % comission {(order?.comission_sum ?? 0)} TK
                            </SecondaryButton>
                        ) : null}
                    </div>
                </Section>

                <OverviewSection>
                    <Div title="Order ID" content={order?.id ?? 0} />
                    <Div title="Products" content={order?.cart_count ?? 0} />
                    <Div title="Sub Product" content={order?.cart_quantity_sum ?? 0} />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-start justify-between px-5">
                                <div className="order-info">
                                    <div>Order ID: {order?.id}</div>
                                    <div>Date: <span className="text-xs">{order?.created_at_daytime}</span></div>
                                    <NavLinkBtn href={route("vendor.orders.cprint", { order: order?.id })}>Print</NavLinkBtn>
                                </div>
                                <div className="order-total text-end">
                                    <p>
                                        <strong>{order?.user?.name}<br /></strong>
                                        {order?.location}
                                        <br />
                                        {order?.house_no}, {order?.road_no}
                                        <br />
                                        {order?.number}
                                    </p>
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <Table data={order?.cart_orders ?? []}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Owner</th>
                                <th>Resel Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Attr</th>
                                {order?.account_type === "vendor" ? <th>Sell</th> : null}
                                <th>Cost</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            {(order?.cart_orders ?? []).map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.id}</td>
                                    <td>
                                        <div>
                                            {item.product_thumbnail ? (
                                                <img width="30" height="30" src={item.product_thumbnail} alt="" />
                                            ) : null}
                                            <div>{item.product_title}</div>
                                        </div>
                                    </td>
                                    <td>
                                        {item.is_resel && order?.account_type === "reseller" ? (
                                            <span className="px-2 text-white bg-indigo-900 rounded-lg text-md">Resel</span>
                                        ) : (
                                            <span className="px-2 text-white bg-indigo-900 rounded-lg text-md">You</span>
                                        )}
                                    </td>
                                    <td>{item.price} TK</td>
                                    <td>{item.quantity}</td>
                                    <td>{item.total} TK</td>
                                    <td>{item.size}</td>
                                    {order?.account_type === "vendor" ? <td>{item.buying_price} TK</td> : null}
                                    <td>{item.main_buying_price} TK</td>
                                    <td>{item.profit_unit} * {item.quantity} = {item.profit_total} TK</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr className="border-t">
                                <td colSpan="6" className="text-right">Sub Total</td>
                                <td>{order?.cart_sum_total} Tk</td>
                            </tr>
                            <tr>
                                <td colSpan="6" className="text-right">Delivery</td>
                                <td>{order?.shipping ?? 0} Tk</td>
                            </tr>
                            <tr className="text-lg font-bold bg-gray-100 border-t">
                                <td colSpan="6" className="text-right">Total</td>
                                <td>{(Number(order?.shipping ?? 0) + Number(order?.cart_sum_total ?? 0))} Tk</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Section>

                {Number(order?.has_rider_count ?? 0) > 0 ? (
                    <Section>
                        <SectionHeader
                            title={
                                <div className="flex items-center justify-between">
                                    RIDER
                                    <PrimaryButton type="button" onClick={() => setRiderOpen(true)}>
                                        <i className="pr-2 fas fa-plus"></i> Rider
                                    </PrimaryButton>
                                </div>
                            }
                            content="view the rider belongs to this order."
                        />
                        <Table data={order?.riders ?? []}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Shipping</th>
                                    <th>Area</th>
                                    <th>Status</th>
                                    <th>A/C</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(order?.riders ?? []).map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{item.name}</td>
                                        <td>{item.phone}</td>
                                        <td>{item.current_address}</td>
                                        <td>{item.targeted_area}</td>
                                        <td>{item.status}</td>
                                        <td>
                                            <button type="button" onClick={() => removeRider(item.id)}>
                                                <i className="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </Section>
                ) : null}
            </Container>

            <Modal show={comissionOpen} onClose={() => setComissionOpen(false)}>
                <div className="p-3">
                    COMISSIONS
                    <Table data={order?.comissions ?? []} className="mt-2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Product</th>
                            </tr>
                        </thead>
                        <tbody>
                            {(order?.comissions ?? []).map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.take_comission}</td>
                                    <td>{item.product_name}</td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </div>
            </Modal>

            <Modal show={acceptOpen} onClose={() => setAcceptOpen(false)}>
                <div className="p-3">
                    <div className="pb-2 font-semibold border-b">Accept Order</div>
                    <p className="pt-2">Add Shipping Amount</p>
                    <div className="py-2">
                        <input
                            type="number"
                            className="w-full border-gray-300 rounded"
                            value={form.data.shipping}
                            onChange={(e) => form.setData("shipping", e.target.value)}
                        />
                    </div>
                    <PrimaryButton
                        type="button"
                        onClick={() => {
                            submitStatus("Accept");
                            setAcceptOpen(false);
                        }}
                    >
                        Confirm
                    </PrimaryButton>
                </div>
            </Modal>

            <Modal show={riderOpen} onClose={() => setRiderOpen(false)}>
                <div className="p-3">
                    <div className="pb-2 font-semibold border-b">Assign Rider</div>
                    <div className="py-2">
                        <select
                            className="w-full rounded-md"
                            value={form.data.rider_id}
                            onChange={(e) => form.setData("rider_id", e.target.value)}
                        >
                            <option value="">Select Rider</option>
                            {(order?.rider_candidates ?? []).map((item) => (
                                <option key={item.id} value={item.id}>
                                    {item.user_name} - {item.phone}
                                </option>
                            ))}
                        </select>
                    </div>
                    <PrimaryButton
                        type="button"
                        onClick={() => {
                            form.post(route("vendor.orders.rider.assign", { order: order.id }), {
                                preserveScroll: true,
                                onSuccess: () => setRiderOpen(false),
                            });
                        }}
                    >
                        Assign
                    </PrimaryButton>
                </div>
            </Modal>
        </AppLayout>
    );
}
