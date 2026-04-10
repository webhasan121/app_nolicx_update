import { Head, router } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import SecondaryButton from "../../../../components/SecondaryButton";
import Container from "../../../../components/dashboard/Container";
import Foreach from "../../../../components/dashboard/Foreach";
import Div from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import PageHeader from "../../../../components/dashboard/PageHeader";

function statusClass(status) {
    const classes = {
        Pending: "text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900",
        Accept: "text-xs p-1 border rounded-md bg-green-200 text-green-900",
        Picked: "text-xs p-1 border rounded-md bg-lime-200 text-lime-900",
        Delivery: "text-xs p-1 border rounded-md bg-sky-200 text-sky-900",
        Delivered: "text-xs p-1 border rounded-md bg-blue-200 text-blue-900",
        Confirm: "text-xs p-1 border rounded-md bg-indigo-200 text-indigo-900",
        Hold: "text-xs p-1 border rounded-md bg-gray-200 text-gray-900",
        Reject: "text-xs p-1 border rounded-md bg-red-200 text-red-900",
        Cancelled: "text-xs p-1 border rounded-md bg-red-200 text-red-900",
    };

    return classes[status] ?? "text-xs p-1 border rounded-md bg-gray-200 text-gray-900";
}

function buildQuery(filters, updates = {}) {
    return Object.fromEntries(
        Object.entries({ ...filters, ...updates }).filter(
            ([, value]) => value !== "" && value !== null && value !== undefined
        )
    );
}

export default function Index({ activeNav, filters = {}, summary = {}, list = {} }) {
    const [filterOpen, setFilterOpen] = useState(false);
    const rows = list?.data ?? [];

    const updateFilters = (updates) => {
        router.get(route("reseller.resel-order.index"), buildQuery(filters, updates), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    return (
        <AppLayout
            title="Resel Orders"
            header={
                <PageHeader>
                    Resel Orders
                    <br />
                    {activeNav === "reseller" ? (
                        <div>
                            <NavLink href={route("vendor.orders.index")} active={route().current("vendor.orders.*")}>
                                To Me
                            </NavLink>
                            <NavLink href={route("reseller.resel-order.index")} active={route().current("reseller.resel-order.*")}>
                                Resel Order
                            </NavLink>
                        </div>
                    ) : null}
                </PageHeader>
            }
        >
            <Head title="Resel Orders" />

            <Container>
                <OverviewSection>
                    <Div title="Orders" content={summary.orders ?? 0} />
                    <Div title="Pending" content={summary.pending ?? 0} />
                    <Div title="Cancel" content={summary.cancel ?? 0} />
                    <Div title="Cancel by User" content={summary.cancelled ?? 0} />
                    <Div title="Accepted" content={summary.accept ?? 0} />
                </OverviewSection>

                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-start space-x-2">
                                <SecondaryButton type="button" onClick={() => setFilterOpen(true)}>
                                    <i className="fas fa-filter pr-2"></i> Filter
                                </SecondaryButton>

                                <select
                                    id="status"
                                    value={filters.nav ?? "Pending"}
                                    onChange={(e) => updateFilters({ nav: e.target.value })}
                                    className="py-1 px-2 rounded-md border"
                                >
                                    <option value="All">Any</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Accept">Accept</option>
                                    <option value="Picked">Picked</option>
                                    <option value="Delivery">Delivery</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Confirm">Confirm</option>
                                    <option value="Reject">Reject</option>
                                    <option value="Hold">Hold</option>
                                </select>

                                <select
                                    id="type"
                                    value={filters.type ?? "All"}
                                    onChange={(e) => updateFilters({ type: e.target.value })}
                                    className="py-1 px-2 rounded-md border"
                                >
                                    <option value="All">All</option>
                                    <option value="Resel">Resel</option>
                                    <option value="Purchase">Purchase</option>
                                </select>
                            </div>
                        }
                        content={
                            <p>
                                View your resel product, income and comission here. You might find the order that have
                                already passed to the vendor for your resel product.
                            </p>
                        }
                    />

                    <SectionInner>
                        <Foreach data={rows}>
                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th> </th>
                                        <th> ID </th>
                                        <th> Shop </th>
                                        <th> Sync</th>
                                        <th> Total </th>
                                        <th> Profit </th>
                                        <th> Shipping </th>
                                        <th> Date </th>
                                        <th> Status </th>
                                        <th> A/C </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {rows.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{index + 1}</td>
                                            <td>{item.id}</td>
                                            <td>
                                                {item.shop_id ? (
                                                    <a
                                                        className="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm text-gray-700 hover:bg-gray-50 focus:outline-none"
                                                        href={route("shops", {
                                                            get: item.shop_id,
                                                            slug: item.shop_name_en || "not_found",
                                                        })}
                                                    >
                                                        {item.shop_name_en}
                                                    </a>
                                                ) : null}
                                                <div>{item.seller_phone}</div>
                                            </td>
                                            <td>
                                                {item.sync ? (
                                                    <div>
                                                        <div className="px-2 bg-gray-200 rounded shadow flex">
                                                            {item.sync.user_order_id}/{item.sync.user_cart_order_id}
                                                        </div>
                                                        <NavLink href={item.sync.view_url}>view</NavLink>
                                                    </div>
                                                ) : (
                                                    <div className="px-2 inline-flex rounded bg-indigo-900 text-white">Purchase</div>
                                                )}
                                            </td>
                                            <td>
                                                {item.total} + {item.shipping}
                                            </td>
                                            <td className="font-bold">{item.profit}</td>
                                            <td>
                                                <p className={`inline-flex text-xs px-1 rounded ${item.delevery === "cash" ? "bg-green-200" : "bg-blue-200"}`}>
                                                    {item.delevery}
                                                </p>
                                                <p>{item.location}</p>
                                            </td>
                                            <td>{item.created_at_formatted ?? 0}</td>
                                            <td>
                                                <span className={statusClass(item.status)}>
                                                    {item.status === "Reject" ? "Rejected" : item.status ?? "Unknown"}
                                                </span>
                                            </td>
                                            <td>
                                                <NavLink href={item.view_url}>view</NavLink>
                                                <NavLink href={item.print_url}>Print</NavLink>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </Foreach>
                    </SectionInner>
                </SectionSection>
            </Container>

            <Modal show={filterOpen} onClose={() => setFilterOpen(false)} maxWidth="xl">
                <div className="p-2">
                    <div>Filter</div>
                    <Hr />
                    <div className="md:flex justify-between">
                        <div>
                            <div>
                                <div>Delevery Type</div>
                                <div className="px-2">
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={(filters.delivery ?? "all") === "all"}
                                            onChange={() => updateFilters({ delivery: "all" })}
                                        />{" "}
                                        Not Defined
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.delivery === "cash"}
                                            onChange={() => updateFilters({ delivery: "cash" })}
                                        />{" "}
                                        Home Delivery
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.delivery === "courier"}
                                            onChange={() => updateFilters({ delivery: "courier" })}
                                        />{" "}
                                        Courier Delivery
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.delivery === "hand"}
                                            onChange={() => updateFilters({ delivery: "hand" })}
                                        />
                                        Hand-to-Hand
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="mt-2 w-1/2">
                            <div className=" border rounded-md">
                                <div className=" p-2 ">
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={(filters.create ?? "all") === "all"}
                                            onChange={() => updateFilters({ create: "all" })}
                                        />
                                        All Time
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.create === "day"}
                                            onChange={() => updateFilters({ create: "day" })}
                                        />
                                        From First Date
                                    </div>
                                    <hr />
                                    <div className="flex items-center w-full p-2 text-sm">
                                        <input
                                            type="radio"
                                            style={{ width: 20, height: 20 }}
                                            className="mr-2"
                                            checked={filters.create === "between"}
                                            onChange={() => updateFilters({ create: "between" })}
                                        />
                                        Between in Range
                                    </div>
                                </div>

                                <div className="space-y-2 p-2 ">
                                    <div>
                                        First Date
                                        <input
                                            className="rounded-md"
                                            type="date"
                                            value={filters.start_date ?? ""}
                                            onChange={(e) => updateFilters({ start_date: e.target.value })}
                                        />
                                    </div>
                                    <div>
                                        Last Date
                                        <input
                                            className="rounded-md"
                                            type="date"
                                            value={filters.end_date ?? ""}
                                            onChange={(e) => updateFilters({ end_date: e.target.value })}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}

