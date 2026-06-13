import { Head, router } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import Modal from "../../../components/Modal";
import NavLink from "../../../components/NavLink";
import SecondaryButton from "../../../components/SecondaryButton";
import Container from "../../../components/dashboard/Container";
import Div from "../../../components/dashboard/overview/Div";
import OverviewSection from "../../../components/dashboard/overview/Section";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import useTranslation from "../../../hooks/useTranslation";
import { todayInputDate } from "../../../utils/dateInput";
import { ActionIconLink } from "../../../components/ActionIcon";

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
    const { t } = useTranslation();
    const [filterOpen, setFilterOpen] = useState(false);
    const rows = orders?.data ?? [];
    const nav = filters.nav ?? "Pending";
    const today = todayInputDate();

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
            title={t("Orders")}
            header={
                <PageHeader>{t("Orders")}<br />

                    {activeNav === "reseller" ? (
                        <div>
                            <NavLink
                                href={route("vendor.orders.index")}
                                active={route().current("vendor.orders.*")}
                            >{t("User Orders")}</NavLink>
                            <NavLink
                                href={route("reseller.resel-order.index")}
                                active={route().current("reseller.resel-order.*")}
                            >{t("My Resel Order")}</NavLink>
                        </div>
                    ) : null}
                </PageHeader>
            }
        >
            <Head title={t("Orders")} />

            <Container>
                <OverviewSection>
                    <Div title={t("Orders")} content={summary.orders ?? 0} />
                    <Div title={t("Pending")} content={summary.pending ?? 0} />
                    <Div title={t("Cancel")} content={summary.cancel ?? 0} />
                    <Div title={t("Cancel by User")} content={summary.cancelled ?? 0} />
                    <Div title={t("Accepted")} content={summary.accept ?? 0} />
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
                                >{t("Trash")}</NavLink>
                            </div>
                        }
                    />

                    <SectionInner>
                        {pagination.pages.length ? (
                                <div className="flex flex-wrap gap-1 mb-3">
                                    <button
                                        type="button"
                                        disabled={!pagination.prev?.url}
                                        className="px-3 py-1 border rounded disabled:opacity-50"
                                        onClick={() => goToPage(pagination.prev?.url)}
                                    >{t("Previous")}</button>
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
                                    >{t("Next")}</button>
                                </div>
                        ) : null}

                        <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        <th>{t("ID")}</th>
                                        <th>{t("Pd")}</th>
                                        <th>{t("Total")}</th>
                                        <th>{t("Status")}</th>
                                        <th>{t("Date")}</th>
                                        <th>{t("Shipping")}</th>
                                        <th>{t("Contact")}</th>
                                        <th>{t("Com")}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {rows.map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.sl}</td>
                                            <td>
                                                <div className="flex items-center gap-1">
                                                    <ActionIconLink href={item.view_url} action="view" title={t("view")} />
                                                    <ActionIconLink href={item.print_url} action="print" title={t("Print")} />
                                                </div>
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
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={filterOpen} onClose={() => setFilterOpen(false)} maxWidth="xl">
                <div className="p-2">
                    <div>{t("Filter")}</div>
                    <Hr />
                    <div className="md:flex">
                        <div>
                            <div>
                                <div>{t("Delevery Type")}</div>
                                <div className="px-2">
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="home_del" value="Home" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="home_del" className="p-0 m-0">{t("Home Delebery")}</label>
                                    </div>
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="courier_del" value="Courier" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="courier_del" className="p-0 m-0">{t("Courier Delebery")}</label>
                                    </div>
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="shop_del" value="Shop" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="shop_del" className="p-0 m-0">{t("Hand To Hand from shop")}</label>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-2">
                                <div>{t("Delevery Area")}</div>
                                <div className="px-2">
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="inside_dhaka" value="Dhaka" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="inside_dhaka" className="p-0 m-0">{t("Inside Dhaka")}</label>
                                    </div>
                                    <div className="flex items-center mb-2 rounded-md border p-2">
                                        <input id="outside_dhaka" value="Other" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="outside_dhaka" className="p-0 m-0">{t("Outside of Dhaka")}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="mt-2">
                            <div className="border rounded-md">
                                <div className="p-2">
                                    <div className="flex items-center p-2">
                                        <input id="filter_date" value="date" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="filter_date" className="p-0 m-0">{t("Date")}</label>
                                    </div>
                                    <div className="flex items-center p-2">
                                        <input id="filter_between" value="between" type="radio" className="w-5 h-5 p-0 m-0 mr-3" />
                                        <label htmlFor="filter_between" className="p-0 m-0">{t("Date Between")}</label>
                                    </div>
                                </div>

                                <div className="flex justify-between items-center p-2">
                                    <div>{t("Start")}<input className="rounded-md" type="date" name="start_date" defaultValue={today} />
                                    </div>
                                    <div>{t("End")}<input className="rounded-md" type="date" name="end_date" />
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
