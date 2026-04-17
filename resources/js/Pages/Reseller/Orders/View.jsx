import { Head } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import Modal from "../../../components/Modal";
import NavLink from "../../../components/NavLink";
import NavLinkBtn from "../../../components/NavLinkBtn";
import Container from "../../../components/dashboard/Container";
import Div from "../../../components/dashboard/overview/Div";
import OverviewSection from "../../../components/dashboard/overview/Section";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";

const progressFlow = ["Pending", "Accept", "Picked", "Delivery", "Delivered", "Confirm"];
const progressLabels = ["Placed", "Accept", "Collecting", "Delivery", "Delivered", "Confirm"];

function StatusBox({ active, checked, children, title }) {
    return (
        <div
            className={`p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600 text-center ${
                checked ? "bg-indigo-900 text-white" : active ? "bg-gray-100" : ""
            }`}
            title={title}
        >
            {children}
            <br />
            {checked ? (
                <div>
                    <i className="fas fa-check-circle"></i>
                </div>
            ) : null}
        </div>
    );
}

function TimelineItem({ item }) {
    return (
        <div className="relative px-2 py-2 flex items-center border-l">
            <i className="fas absolute fa-check-circle w-12 h-12" style={{ left: "-8px", top: "12px" }}></i>
            <div className="px-4">
                <p>{item.title}</p>
                {item.description ? <p className="text-xs">{item.description}</p> : null}
            </div>
        </div>
    );
}

export default function View({ order }) {
    const [comissionOpen, setComissionOpen] = useState(false);
    const statusIndex = progressFlow.indexOf(order?.status);
    const shippingTotal = Number(order?.shipping ?? 0) + Number(order?.cart_sum_total ?? 0);

    return (
        <AppLayout
            title="Your Reseller Orders"
            header={
                <PageHeader>
                    Your Reseller Orders
                    <br />
                    <div className="text-sm font-normal">
                        {order?.user_type} <i className="fas fa-caret-right mx-2"></i> {order?.belongs_to_type}
                    </div>
                    <div className="text-xs flex items-center sapce-x-2">
                        {order?.delevery} Delvevery <i className="fas fa-caret-right px-2"></i>{" "}
                        {order?.area_condition === "Dhaka" ? "Inside Dhaka" : "Outside of Dhaka"}
                    </div>
                </PageHeader>
            }
        >
            <Head title="Your Reseller Orders" />

            <Container>
                <Section>
                    <div className="flex justify-between items-center space-y-2">
                        <div className="md:flex justify-between items-center space-y-2 w-full overflow-hidden overflow-x-scroll">
                            <div>
                                <div className="mb-2 flex gap-2">
                                    {progressFlow.map((step, index) => (
                                        <StatusBox
                                            key={step}
                                            checked={statusIndex >= index}
                                            active={index > 0 && order?.status === progressFlow[index - 1]}
                                            title={[
                                                "Buyer placed the order. Order in Pending",
                                                "Accept the order for process",
                                                "Find and collect the product",
                                                "product shipped to rider or courier.",
                                                "product delivered to the buyer.and buyer successfully received the order",
                                                "",
                                            ][index]}
                                        >
                                            {step === "Picked" && order?.status === "Picked" ? "Collected" : progressLabels[index]}
                                        </StatusBox>
                                    ))}
                                </div>
                            </div>
                            <div>
                                <div className="mb-2 flex gap-2">
                                    {["Hold", "Reject"].map((step) => (
                                        <StatusBox key={step} checked={order?.status === step} active={order?.status === "Delivered"}>
                                            {step}
                                        </StatusBox>
                                    ))}
                                </div>
                                {order?.status === "Rejecte" ? (
                                    <button type="button" className="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase">
                                        Order Cancelled
                                    </button>
                                ) : null}
                            </div>
                        </div>
                    </div>

                    <div className="flex justify-end items-center space-x-2">
                        {order?.name === "Resel" ? (
                            order?.synced ? (
                                <div className="inline-flex items-center px-2 bg-gray-200 text-xs rounded shadow">
                                    <NavLink href={order.synced.url}>
                                        synced {order.synced.user_order_id} / {order.synced.user_cart_order_id} view
                                    </NavLink>
                                </div>
                            ) : null
                        ) : (
                            <div className="px-4 rounded-md shadow py-1 text-indigo-900 font-bold">Purchase</div>
                        )}
                    </div>
                </Section>

                <OverviewSection>
                    <Div title="Order ID" content={order?.id ?? 0} />
                    <Div title="Products" content={order?.cart_count ?? 0} />
                    <Div title="Sub Product" content={order?.cart_quantity_sum ?? 0} />
                    <Div title="Your Profit" content={order?.name === "Resel" ? order?.reseller_profit_sum ?? 0 : ""} />
                </OverviewSection>

                <Section>
                    <div className="flex justify-between items-start px-5">
                        <div className="order-info">
                            <div>Order ID: {order?.id}</div>
                            <div>
                                Date: <span className="text-xs"> {order?.created_at_daytime}</span>
                            </div>
                            <NavLinkBtn href={route("vendor.orders.cprint", { order: order?.id })}>Print</NavLinkBtn>
                        </div>
                        <div className="order-total text-end">
                            <table className="table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <p>
                                                <strong>
                                                    {order?.user?.name ?? "Not Found !"} <br />
                                                </strong>{" "}
                                                {order?.location}
                                                <br />
                                                {order?.house_no ?? "Not Defined !"}, {order?.road_no ?? "Not Defined !"}
                                                <br />
                                                {order?.number}
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <Table data={order?.cart_orders ?? []}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Owner</th>
                                <th>Resel Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Attr</th>
                                <th>Buying Price</th>
                                <th>Profit</th>
                                <th>Comissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {(order?.cart_orders ?? []).map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.id ?? "N/A"}</td>
                                    <td>
                                        <div>
                                            {item.product_thumbnail ? (
                                                <img width="30px" height="30px" src={item.product_thumbnail} alt="" />
                                            ) : null}
                                            <div>{item.product_title ?? "N/A"}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <NavLink href={item.owner_shop_url}>{item.owner_shop_name}</NavLink>
                                        {item.owner_phone}
                                    </td>
                                    <td>{item.price} TK</td>
                                    <td>{item.quantity}</td>
                                    <td>{item.total} TK</td>
                                    <td>{item.size ?? "N/A"}</td>
                                    <td>{order?.name === "Resel" ? `${item.buying_price ?? "N/A"} TK` : ""}</td>
                                    <td>{order?.name === "Resel" ? item.profit : ""}</td>
                                    <th></th>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr className="border-t">
                                <td colSpan="6" className="text-right">
                                    Sub Total
                                </td>
                                <td>{order?.cart_sum_total} Tk</td>
                            </tr>
                            <tr>
                                <td colSpan="6" className="text-right">
                                    Shipping
                                </td>
                                <td>{order?.shipping ?? 0} Tk</td>
                            </tr>
                            <tr className="border-t font-bold text-lg bg-gray-100">
                                <td colSpan="6" className="text-right">
                                    Total
                                </td>
                                <td>{shippingTotal} Tk</td>
                                <td colSpan="6"></td>
                            </tr>
                        </tfoot>
                    </Table>
                </Section>

                <div className="max-w-md">
                    <Section>
                        <div className="flex justify-between items-center">
                            <div>Shipping</div>
                            <div>
                                <div className="px-2 py-1 bg-indigo-900 text-white rounded-lg">{order?.shipping ?? "0"} TK</div>
                            </div>
                        </div>

                        <div className="pt-2">
                            <div className="text-xs flex items-center sapce-x-2">
                                {order?.delevery} Delevery {order?.area_condition === "Dhaka" ? "in Dhaka" : "Outside of Dhaka"}
                            </div>
                        </div>

                        <div className="text-sm mb-10">
                            <b>{order?.location ?? "N/A"}</b>
                            <br />
                            Phone : {order?.number ?? "N/A"}
                        </div>

                        <div className="mb-6">
                            <h3 className="mb-2">Assign To</h3>
                            <Hr />
                            <div>
                                {order?.rider ? (
                                    <>
                                        <div className="text-lg text-bold font-bold">{order.rider.name}</div>
                                        <p>{order.rider.phone}</p>
                                    </>
                                ) : (
                                    <div>N/A</div>
                                )}
                            </div>
                        </div>

                        <div>
                            {(order?.timeline ?? []).map((item, index) => (
                                <TimelineItem key={`${item.title}-${index}`} item={item} />
                            ))}
                        </div>
                    </Section>
                </div>
            </Container>

            <Modal show={comissionOpen} onClose={() => setComissionOpen(false)}>
                <div className="p-2">
                    COMISSIONS
                    <Hr />
                    <Table data={order?.comissions ?? []}>
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
                                    <td>{item.take_comission ?? 0}</td>
                                    <td>{item.product_name ?? "N/A"}</td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </div>
            </Modal>
        </AppLayout>
    );
}
