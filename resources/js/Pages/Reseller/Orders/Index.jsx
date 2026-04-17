import { Head, router } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import Modal from "../../../components/Modal";
import NavLink from "../../../components/NavLink";
import NavLinkBtn from "../../../components/NavLinkBtn";
import SecondaryButton from "../../../components/SecondaryButton";
import Container from "../../../components/dashboard/Container";
import Foreach from "../../../components/dashboard/Foreach";
import Div from "../../../components/dashboard/overview/Div";
import OverviewSection from "../../../components/dashboard/overview/Section";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";

const statusItems = [
    ["Pending", "Pending"],
    ["Accept", "Accept"],
    ["Picked", "Picked"],
    ["Delivery", "Delivery"],
    ["Delivered", "Delivered"],
    ["Confirm", "Confirm"],
    ["Hold", "Confirm"],
    ["Cancel", "Cancel"],
    ["Cancelled", "Cancel by Buyer"],
];

export default function Index({
    activeNav,
    filters = {},
    summary = {},
    orders = {},
}) {
    const [filterOpen, setFilterOpen] = useState(false);
    const rows = orders?.data ?? [];
    const nav = filters.nav ?? "Pending";

    const pagination = useMemo(() => {
        const links = orders?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [orders?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        router.get(url, {}, { preserveScroll: true, preserveState: true });
    };

    return (
        <AppLayout
            title="Orders"
            header={
                <PageHeader>
                    Orders
                    <br />

                    {activeNav === "reseller" ? (
                        <div>
                            <NavLink
                                href={route("vendor.orders.index")}
                                active={route().current("vendor.orders.*")}
                            >
                                User Orders
                            </NavLink>
                            <NavLink
                                href={route("reseller.resel-order.index")}
                                active={route().current("reseller.resel-order.*")}
                            >
                                My Resel Order
                            </NavLink>
                        </div>
                    ) : null}
                </PageHeader>
            }
        >
            <Head title="Orders" />

            <Container>
                <OverviewSection>
                    <Div title="Orders" content={summary.orders ?? 0} />
                    <Div title="Pending" content={summary.pending ?? 0} />
                    <Div title="Cancel" content={summary.cancel ?? 0} />
                    <Div title="Cancel by User" content={summary.cancelled ?? 0} />
                    <Div title="Accepted" content={summary.accept ?? 0} />
                    <Div />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-center">
                                <SecondaryButton
                                    type="button"
                                    onClick={() => setFilterOpen(true)}
                                >
                                    <i className="fas fa-filter"></i>
                                </SecondaryButton>
                            </div>
                        }
                        content={
                            <div className="flex justify-between">
                                <div>
                                    {statusItems.map(([value, label]) => (
                                        <NavLink
                                            key={value}
                                            href={route("reseller.order.index", { nav: value })}
                                            active={nav === value}
                                        >
                                            {label}
                                        </NavLink>
                                    ))}
                                </div>

                                <NavLink
                                    href={route("reseller.order.index", { nav: "Trash" })}
                                    active={nav === "Trash"}
                                >
                                    Trash
                                </NavLink>
                            </div>
                        }
                    />

                    <SectionInner>
                        <Foreach data={rows}>
                            {pagination.pages.length ? (
                                <div className="flex flex-wrap gap-1 mb-3">
                                    <button
                                        type="button"
                                        disabled={!pagination.prev?.url}
                                        className="px-3 py-1 border rounded disabled:opacity-50"
                                        onClick={() => goToPage(pagination.prev?.url)}
                                    >
                                        Previous
                                    </button>
                                    {pagination.pages.map((link, index) => (
                                        <button
                                            key={`${link.label}-${index}`}
                                            type="button"
                                            disabled={!link.url}
                                            className={`px-3 py-1 border rounded ${link.active ? "bg-gray-900 text-white" : ""}`}
                                            onClick={() => goToPage(link.url)}
                                        >
                                            {link.label}
                                        </button>
                                    ))}
                                    <button
                                        type="button"
                                        disabled={!pagination.next?.url}
                                        className="px-3 py-1 border rounded disabled:opacity-50"
                                        onClick={() => goToPage(pagination.next?.url)}
                                    >
                                        Next
                                    </button>
                                </div>
                            ) : null}

                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Pd</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Shipping</th>
                                        <th>Contact</th>
                                        <th>Com</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {rows.map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.sl}</td>
                                            <td>
                                                <NavLinkBtn href={item.view_url}>
                                                    view
                                                </NavLinkBtn>
                                                <NavLink href={item.print_url}>
                                                    Pint
                                                </NavLink>
                                            </td>
                                            <td>{item.id ?? "N/A"}</td>
                                            <td>
                                                {item.cart_orders_count ?? "N/A"} / {item.quantity ?? "N/A"}
                                            </td>
                                            <td>
                                                {item.total ?? "N/A"}
                                                <br />
                                                <span className="text-xs">
                                                    + {item.shipping}
                                                </span>
                                            </td>
                                            <td>{item.status ?? "Pending"}</td>
                                            <td>
                                                <div className="text-nowarp">
                                                    <div>{item.created_at_human}</div>
                                                    <div className="text-xs">
                                                        {item.created_at_formatted}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p>{item.delevery}</p>
                                                <p className="border px-2 rounded bg-gray-900 text-white inline-block bold">
                                                    {item.area_condition}
                                                </p>
                                            </td>
                                            <td>
                                                <span className="text-xs">
                                                    {item.number ?? "N/A"}
                                                </span>
                                            </td>
                                            <th>{item.take_comission_sum}</th>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </Foreach>
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={filterOpen} onClose={() => setFilterOpen(false)} maxWidth="xl">
                <div className="p-2">
                    <div>Filter</div>
                    <Hr />
                    <div className="md:flex">
                        <div>
                            <div>
                                <div>Delevery Type</div>
                                <div className="px-2">
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="home_del" value="Home" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="home_del" className="p-0 m-0"> Home Delebery </label>
                                    </div>
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="courier_del" value="Courier" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="courier_del" className="p-0 m-0"> Courier Delebery </label>
                                    </div>
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="shop_del" value="Shop" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="shop_del" className="p-0 m-0"> Hand To Hand from shop </label>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-2">
                                <div>Delevery Area</div>
                                <div className="px-2">
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="inside_dhaka" value="Dhaka" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="inside_dhaka" className="p-0 m-0"> Inside Dhaka </label>
                                    </div>
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="outside_dhaka" value="Other" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="outside_dhaka" className="p-0 m-0"> Outside of Dhaka </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="mt-2">
                            <div className="border rounded-md">
                                <div className="p-2">
                                    <div className="flex items-center p-2">
                                        <input id="filter_date" value="date" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="filter_date" className="p-0 m-0"> Date </label>
                                    </div>
                                    <div className="flex items-center p-2">
                                        <input id="filter_between" value="between" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="filter_between" className="p-0 m-0"> Date Between </label>
                                    </div>
                                </div>

                                <div className="flex justify-between items-center p-2">
                                    <div>
                                        Start
                                        <input className="rounded-md" type="date" name="start_date" />
                                    </div>
                                    <div>
                                        End
                                        <input className="rounded-md" type="date" name="end_date" />
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
