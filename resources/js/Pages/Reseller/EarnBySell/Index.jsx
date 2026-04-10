import { Head, router } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Modal from "../../../components/Modal";
import PrimaryButton from "../../../components/PrimaryButton";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import OverviewSection from "../../../components/dashboard/overview/Section";
import OverviewDiv from "../../../components/dashboard/overview/Div";
import Table from "../../../components/dashboard/table/Table";
import NavLink from "../../../components/NavLink";

function statusClass(status) {
    switch (status) {
        case "Pending":
            return "text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900";
        case "Accept":
            return "text-xs p-1 border rounded-md bg-green-200 text-green-900";
        case "Picked":
            return "text-xs p-1 border rounded-md bg-lime-200 text-lime-900";
        case "Delivery":
            return "text-xs p-1 border rounded-md bg-sky-200 text-sky-900";
        case "Delivered":
            return "text-xs p-1 border rounded-md bg-blue-200 text-blue-900";
        case "Confirm":
            return "text-xs p-1 border rounded-md bg-indigo-200 text-indigo-900";
        case "Hold":
            return "text-xs p-1 border rounded-md bg-gray-200 text-gray-900";
        case "Cancel":
        case "Cancelled":
            return "text-xs p-1 border rounded-md bg-red-200 text-red-900";
        default:
            return "text-xs p-1 border rounded-md bg-gray-200 text-gray-900";
    }
}

export default function Index({
    filters = {},
    overview = {},
    products = { data: [], links: [] },
    counts = {},
}) {
    const [showFilterModal, setShowFilterModal] = useState(false);
    const [nav, setNav] = useState(filters.nav ?? "sold");
    const [fd, setFd] = useState(filters.fd ?? "");
    const [lastDate, setLastDate] = useState(filters.lastDate ?? "");

    const cleanLabel = (label) =>
        String(label)
            .replace(/&laquo;/g, "")
            .replace(/&raquo;/g, "")
            .trim();

    const changeNav = (value) => {
        setNav(value);
        router.get(
            route("reseller.sel.index"),
            {
                nav: value,
                fd: fd || undefined,
                lastDate: lastDate || undefined,
            },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    };

    const submitFilter = (e) => {
        e.preventDefault();
        router.get(
            route("reseller.sel.index"),
            {
                nav,
                fd: fd || undefined,
                lastDate: lastDate || undefined,
            },
            { preserveState: true, preserveScroll: true },
        );
        setShowFilterModal(false);
    };

    const formattedFd = useMemo(() => {
        if (!fd) return "";
        return new Date(fd).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });
    }, [fd]);

    const formattedLastDate = useMemo(() => {
        if (!lastDate) return "";
        return new Date(lastDate).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });
    }, [lastDate]);

    return (
        <AppLayout title="Sell and Profit">
            <Head title="Sell and Profit" />

            <Container>
                <p className="text-xl">Sell and Profit</p>

                <OverviewSection>
                    <OverviewDiv
                        title="Total Sell"
                        content={`${overview.totalSell ?? 0} TK`}
                    />
                    <OverviewDiv
                        title="Profit"
                        content={`${overview.tp ?? 0} TK`}
                    />
                    <OverviewDiv
                        title="Neet"
                        content={`${overview.tn ?? 0} TK`}
                    />
                    <OverviewDiv
                        title="Shop"
                        content={`${overview.shop ?? 0}`}
                    />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div className="flex space-x-2">
                                    <select
                                        value={nav}
                                        onChange={(e) =>
                                            changeNav(e.target.value)
                                        }
                                        className="rounded py-1"
                                    >
                                        <option value="all">Both</option>
                                        <option value="sold">Sold</option>
                                        <option value="selling">
                                            On-Selling
                                        </option>
                                    </select>
                                </div>
                                <PrimaryButton
                                    type="button"
                                    onClick={() => setShowFilterModal(true)}
                                >
                                    Filter <i className="fas fa-sort ms-2"></i>
                                </PrimaryButton>
                            </div>
                        }
                        content={
                            <p className="text-sm">
                                {counts.items
                                    ? `${counts.items} items found / Unique : ${counts.unique}`
                                    : "No Data Found"}
                            </p>
                        }
                    />
                    <hr />
                    <SectionInner>
                        <Table data={products.data ?? []} className="p-2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Flow</th>
                                    <th>Owner</th>
                                    <th>Price</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(products.data ?? []).map((item, idx) => (
                                    <tr key={item.id}>
                                        <td>{idx + 1}</td>
                                        <td>{item.id}</td>
                                        <td>
                                            <NavLink
                                                className="text-xs"
                                                href={route(
                                                    "products.details",
                                                    {
                                                        id:
                                                            item.product_id ??
                                                            "",
                                                        slug:
                                                            item.product_slug ??
                                                            "",
                                                    },
                                                )}
                                            >
                                                {item.product_thumbnail ? (
                                                    <img
                                                        width="30px"
                                                        height="30px"
                                                        src={
                                                            item.product_thumbnail
                                                        }
                                                        alt=""
                                                        className="mr-2 rounded-full"
                                                    />
                                                ) : null}
                                                {item.product_name ?? "N/A"}
                                            </NavLink>
                                            <br />
                                            <div className="text-xs border rounded inline-block">
                                                {item.product_status ?? "N/A"}
                                            </div>
                                        </td>
                                        <td>
                                            <div className="flex items-center">
                                                {item.user_type}{" "}
                                                <i className="fas fa-angle-right mx-2"></i>
                                                {item.belongs_to_type}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div className="text-gray-700">
                                                    {item.owner_name ?? "N/A"}
                                                </div>
                                                {item.is_resel_count ? (
                                                    <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                        <i className="fas fa-caret-left"></i>
                                                        R
                                                    </span>
                                                ) : null}
                                                {item.resel_count ? (
                                                    <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                        {item.resel_count}
                                                        <i className="fas fa-caret-right"></i>
                                                    </span>
                                                ) : null}
                                            </div>
                                        </td>
                                        <td>
                                            {item.product_price ?? 0} TK
                                            {item.offer_type ? (
                                                <div className="flex items-center text-center p-1 rounded bg-gray-100 text-xs">
                                                    D: {item.discount} |{" "}
                                                    {item.discount_percent}% off
                                                </div>
                                            ) : null}
                                        </td>
                                        <td>
                                            {item.product_created_at ?? "N/A"}
                                        </td>
                                        <td>
                                            <span
                                                className={statusClass(
                                                    item.status,
                                                )}
                                            >
                                                {item.status ?? "Unknown"}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                        <div className="mb-2 flex flex-wrap gap-2">
                            {(products.links ?? []).map((link, idx) => (
                                <button
                                    key={idx}
                                    type="button"
                                    disabled={!link.url}
                                    onClick={() =>
                                        link.url &&
                                        router.visit(link.url, {
                                            preserveScroll: true,
                                            preserveState: true,
                                        })
                                    }
                                    className={`px-3 py-1 border rounded ${
                                        link.active
                                            ? "bg-gray-900 text-white"
                                            : "bg-white"
                                    }`}
                                >
                                    {cleanLabel(link.label)}
                                </button>
                            ))}
                        </div>
                    </SectionInner>
                </Section>

                <Modal
                    show={showFilterModal}
                    onClose={() => setShowFilterModal(false)}
                >
                    <div className="p-2 flex justify-between items-center">
                        <div>Filter</div>
                        <button
                            type="button"
                            onClick={() => setShowFilterModal(false)}
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    </div>
                    <hr />
                    <div className="p-3">
                        <form onSubmit={submitFilter}>
                            <div className="w-full flex items-bottom justify-betweeen space-x-2">
                                <div>
                                    <p className="text-xs">First Date</p>
                                    <input
                                        type="date"
                                        value={fd}
                                        onChange={(e) => setFd(e.target.value)}
                                        className="py-1 rounded font-normal text-sm"
                                    />
                                    <div className="text-xs">{formattedFd}</div>
                                </div>

                                <div>
                                    <p className="text-xs">Last Date</p>
                                    <input
                                        type="date"
                                        value={lastDate}
                                        onChange={(e) =>
                                            setLastDate(e.target.value)
                                        }
                                        className="py-1 rounded font-normal text-sm"
                                    />
                                    <div className="text-xs">
                                        {formattedLastDate}
                                    </div>
                                </div>
                            </div>
                            <button
                                className="rounded bg-lime-400 px-4 mt-1 py-1 text-sm border"
                                type="submit"
                            >
                                Check
                            </button>
                        </form>
                    </div>
                </Modal>
            </Container>
        </AppLayout>
    );
}
