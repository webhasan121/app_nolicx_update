import { Head, router } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import Table from "../../../../components/dashboard/table/Table";

export default function Details({ nav = "tab", order, earnFilters, earnComissions = [], resellerProfit, reseller_profit_sum }) {
    const changeNav = (target) => {
        router.get(route("system.orders.details", { id: order.id }), { nav: target }, { preserveScroll: true, preserveState: true });
    };

    const confirmResellerProfit = () => {
        router.post(route("system.orders.reseller-profit.confirm", { id: order.id }));
    };

    const refundResellerProfit = () => {
        router.post(route("system.orders.reseller-profit.refund", { id: order.id }));
    };

    return (
        <AppLayout
            title="Order Details"
            header={
                <PageHeader>
                    Order Details
                    <br />
                    <div className="flex items-center text-xs">
                        {order.user_type} <i className="fas fa-arrow-right px-2"></i> {order.belongs_to_type}
                    </div>
                    <Hr />
                    <div>
                        <NavLink href="?nav=tab" active={nav === "tab"} onClick={(e) => { e.preventDefault(); changeNav("tab"); }}>Details</NavLink>
                        <NavLink href="?nav=earn" active={nav === "earn"} onClick={(e) => { e.preventDefault(); changeNav("earn"); }}>comissions</NavLink>
                        {order.user_type === "reseller" ? (
                            <NavLink href="?nav=profit" active={nav === "profit"} onClick={(e) => { e.preventDefault(); changeNav("profit"); }}>Reseller Profit</NavLink>
                        ) : null}
                    </div>
                </PageHeader>
            }
        >
            <Head title="Order Details" />

            <Container>
                {nav === "tab" ? (
                    <>
                        <SectionHeader
                            title={
                                <div className="flex justify-between items-start px-5">
                                    <div className="order-info">
                                        <div>Order ID: {order.id}</div>
                                        <div><span className="text-xs"> {order.created_at_daytime}</span></div>
                                        <NavLinkBtn href={route("vendor.orders.cprint", { order: order.id })}>Print</NavLinkBtn>
                                    </div>
                                    <div className="order-total text-end">
                                        <table className="table">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p>
                                                            <strong>{order.user.name} <br /></strong>
                                                            {order.location}
                                                            <br />
                                                            {order.house_no}, {order.road_no}
                                                            <br />
                                                            {order.number}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            }
                            content={<div className="flex"><form action=""></form></div>}
                        />

                        <Section>
                            <Table data={order.cart_orders}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th>Owner</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Attr</th>
                                        <th>Buy</th>
                                        <th>Profit</th>
                                        <th>Comissions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {order.cart_orders.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{index + 1}</td>
                                            <td>{item.id ?? "N/A"}</td>
                                            <td>
                                                <div className=" ">
                                                    {item.product_thumbnail ? <img width="30px" height="30px" src={`/storage/${item.product_thumbnail}`} alt="" /> : null}
                                                    <div>{item.product_title}</div>
                                                </div>
                                            </td>
                                            <td>
                                                {item.is_resel ? (
                                                    <span className="bg-indigo-900 text-md text-white rounded-lg px-2"> Vendor </span>
                                                ) : (
                                                    <span className="bg-indigo-900 text-md text-white rounded-lg px-2"> Reseller </span>
                                                )}
                                            </td>
                                            <td>{item.price} TK</td>
                                            <td>{item.quantity}</td>
                                            <td>{item.total} TK</td>
                                            <td>{item.size ?? "N/A"}</td>
                                            <td>{item.buying_price} TK</td>
                                            <td>{item.profit}</td>
                                            <th>
                                                <div className="flex rounded border justify-between bg-gray-200">
                                                    <div className="flex space-x-1 px-1">
                                                        <form action={route("system.comissions.destroy")} method="post" className="px-2">
                                                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content ?? ""} />
                                                            <input type="hidden" name="_method" value="POST" />
                                                            <input type="hidden" name="id" value={order.id} />
                                                            <button><i className="fas fa-trash"></i></button>
                                                        </form>

                                                        <form method="post" action={route("system.comissions.confirm", { id: order.id })}>
                                                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content ?? ""} />
                                                            <button><i className="fas fa-sync"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    ))}
                                </tbody>
                                <tfoot>
                                    <tr className="border-t">
                                        <td colSpan="6" className="text-right">Sub Total</td>
                                        <td>{order.cart_sum_total} Tk</td>
                                    </tr>
                                    <tr>
                                        <td colSpan="6" className="text-right">Shipping</td>
                                        <td>{order.shipping ?? 0} Tk</td>
                                    </tr>
                                    <tr className="border-t font-bold text-lg bg-gray-100">
                                        <td colSpan="6" className="text-right">Total</td>
                                        <td>{order.shipping + order.cart_sum_total} Tk</td>
                                        <td colSpan="5"></td>
                                    </tr>
                                </tfoot>
                            </Table>
                        </Section>
                    </>
                ) : null}

                {nav === "earn" ? (
                    <>
                        <div className="w-ful text-center">
                            <div className="tex-xl">
                                <ApplicationName />
                            </div>
                            <div>
                                <p className="">
                                    Comisstion Summery form {earnFilters?.from_formatted ?? ""} to {earnFilters?.to_formatted ?? ""}
                                </p>
                            </div>
                        </div>
                        <Section>
                            <Table data={earnComissions}>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Order</th>
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
                                    {earnComissions.map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.id ?? "N/A"}</td>
                                            <td>{item.order_id ?? 0}</td>
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
                                        <td>{earnComissions.length}</td>
                                        <td className="font-bold">{earnComissions.reduce((sum, item) => sum + Number(item.buying_price || 0), 0)}</td>
                                        <td className="font-bold">{earnComissions.reduce((sum, item) => sum + Number(item.selling_price || 0), 0)}</td>
                                        <td className="font-bold">{earnComissions.reduce((sum, item) => sum + Number(item.profit || 0), 0)}</td>
                                        <td></td>
                                        <td className="font-bold">{earnComissions.reduce((sum, item) => sum + Number(item.take_comission || 0), 0)}</td>
                                        <td className="font-bold">{earnComissions.reduce((sum, item) => sum + Number(item.distribute_comission || 0), 0)}</td>
                                        <td className="font-bold">{earnComissions.reduce((sum, item) => sum + Number(item.store || 0), 0)}</td>
                                        <td className="font-bold"></td>
                                        <td className="font-bold"></td>
                                    </tr>
                                </tfoot>
                            </Table>
                        </Section>
                    </>
                ) : null}

                {nav === "profit" ? (
                    <>
                        <div>Total Reseller Profit : {reseller_profit_sum}</div>
                        <Hr />
                        <Section>
                            <SectionHeader
                                title={
                                    <div className="flex">
                                        <PrimaryButton type="button" onClick={confirmResellerProfit}>Confirm</PrimaryButton>
                                        <DangerButton type="button" onClick={refundResellerProfit}>Refund</DangerButton>
                                    </div>
                                }
                                content=""
                            />
                            <Table data={resellerProfit}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Buy</th>
                                        <th>Sell</th>
                                        <th>Profit</th>
                                        <th>Confirmed</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {resellerProfit.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{index + 1}</td>
                                            <td>{item.id}</td>
                                            <td>{item.buy}</td>
                                            <td>{item.sel}</td>
                                            <td>{item.profit}</td>
                                            <td>
                                                {item.confirmed ? (
                                                    <span className="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>
                                                ) : (
                                                    <span className="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>
                                                )}
                                            </td>
                                            <td>{item.created_at_formatted}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </Section>
                    </>
                ) : null}
            </Container>
        </AppLayout>
    );
}
