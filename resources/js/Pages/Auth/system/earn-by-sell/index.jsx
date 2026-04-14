import { Head, router } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

const statusClass = {
    Pending: "text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900",
    Accept: "text-xs p-1 border rounded-md bg-green-200 text-green-900",
    Picked: "text-xs p-1 border rounded-md bg-lime-200 text-lime-900",
    Delivery: "text-xs p-1 border rounded-md bg-sky-200 text-sky-900",
    Delivered: "text-xs p-1 border rounded-md bg-blue-200 text-blue-900",
    Confirm: "text-xs p-1 border rounded-md bg-indigo-200 text-indigo-900",
    Hold: "text-xs p-1 border rounded-md bg-gray-200 text-gray-900",
    Cancel: "text-xs p-1 border rounded-md bg-red-200 text-red-900",
    Cancelled: "text-xs p-1 border rounded-md bg-red-200 text-red-900",
};

function buildParams(filters, page = null) {
    const params = {
        nav: filters?.nav ?? "sold",
        fd: filters?.fd_value ?? filters?.fd ?? "",
        lastDate: filters?.lastDate_value ?? filters?.lastDate ?? "",
        user_type: filters?.user_type ?? "user",
    };

    if (page) {
        params.page = page;
    }

    return params;
}

export default function Index({ filters, overview, products }) {
    const visit = (nextFilters, page = null) => {
        router.get(route("system.earn.index"), buildParams(nextFilters, page), {
            preserveScroll: true,
            preserveState: true,
        });
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }
        const nextUrl = new URL(url);
        visit(
            {
                nav: nextUrl.searchParams.get("nav") ?? filters?.nav,
                fd_value: nextUrl.searchParams.get("fd") ?? filters?.fd_value,
                lastDate_value: nextUrl.searchParams.get("lastDate") ?? filters?.lastDate_value,
                user_type: nextUrl.searchParams.get("user_type") ?? filters?.user_type,
            },
            nextUrl.searchParams.get("page") ?? undefined
        );
    };

    return (
        <AppLayout title="Earn By Sell" header={
            <PageHeader>
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        const formData = new FormData(e.currentTarget);
                        visit({
                            ...filters,
                            fd_value: formData.get("fd"),
                            lastDate_value: formData.get("lastDate"),
                        });
                    }}
                >
                    <div className="w-full flex items-bottom justify-betweeen space-x-2">
                        <div>
                            <p className="text-xs">First Date</p>
                            <input
                                type="date"
                                id="firstDate"
                                name="fd"
                                defaultValue={filters?.fd_value ?? ""}
                                className="py-1 rounded font-normal text-sm"
                            />
                            <div className="text-xs">{filters?.fd ?? ""}</div>
                        </div>

                        <div>
                            <p className="text-xs">Last Date</p>
                            <TextInput
                                type="date"
                                name="lastDate"
                                defaultValue={filters?.lastDate_value ?? ""}
                                className="py-1 rounded font-normal text-sm"
                            />
                            <div className="text-xs">{filters?.lastDate ?? ""}</div>
                        </div>
                    </div>
                    <button className="rounded bg-lime-400 px-4 mt-1 py-1 text-sm border" type="submit">
                        Check
                    </button>
                </form>
            </PageHeader>
        }>
            <Head title="Earn By Sell" />
            <Hr />
            <Container>
                <p className="text-xl">Sell and Profit</p>
                <OverviewSection>
                    <OverviewDiv title="Total Sell" content={`${overview?.totalSell ?? 0} TK`} />
                    <OverviewDiv title="Profit" content={`${overview?.tp ?? 0} TK`} />
                    <OverviewDiv title="Neet" content={`${overview?.tn ?? 0} TK`} />
                    <OverviewDiv title="Shop" content={overview?.shop ?? 0} />
                    <OverviewDiv title="Vendor Shop" content={overview?.tpr ?? 0} />
                    <OverviewDiv title="Reseller Shop" content={overview?.tprr ?? 0} />
                </OverviewSection>

                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div className="flex space-x-2">
                                    <select
                                        value={filters?.nav ?? "sold"}
                                        onChange={(e) =>
                                            visit({
                                                ...filters,
                                                nav: e.target.value,
                                            })
                                        }
                                        className="rounded py-1"
                                    >
                                        <option value="all">Both</option>
                                        <option value="sold">Sold</option>
                                        <option value="selling">On-Selling</option>
                                    </select>
                                </div>
                                <div>
                                    <select
                                        value={filters?.user_type ?? "user"}
                                        onChange={(e) =>
                                            visit({
                                                ...filters,
                                                user_type: e.target.value,
                                            })
                                        }
                                        className="rounded py-1"
                                    >
                                        <option value="all">Both</option>
                                        <option value="user">Reseller Shop</option>
                                        <option value="reseller">Vendor Shop</option>
                                    </select>
                                </div>
                            </div>
                        }
                        content={
                            <p className="text-sm">
                                {(products?.count ?? 0) > 0
                                    ? `${products?.count ?? 0} items found / Unique : ${products?.unique_count ?? 0}`
                                    : "No Data Found"}
                            </p>
                        }
                    />
                    <Hr />
                    <SectionInner>
                        <Table data={products?.data ?? []} className="p-2">
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
                                {(products?.data ?? []).map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{(products?.from ?? 1) + index}</td>
                                        <td>{item.id}</td>
                                        <td>
                                            <NavLink
                                                className="text-xs"
                                                href={route("products.details", {
                                                    id: item.product_id ?? "",
                                                    slug: item.product_slug ?? "",
                                                })}
                                            >
                                                {item.product_thumbnail ? (
                                                    <img
                                                        width="30"
                                                        height="30"
                                                        src={item.product_thumbnail}
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
                                                {item.user_type} <i className="fas fa-angle-right mx-2"></i>
                                                {item.belongs_to_type}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div className="text-gray-700">{item.owner_name ?? "N/A"}</div>
                                                {(item.is_resel_count ?? 0) > 0 ? (
                                                    <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                        <i className="fas fa-caret-left"></i>R
                                                    </span>
                                                ) : null}
                                                {(item.resel_count ?? 0) > 0 ? (
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
                                                    D: {item.discount ?? 0} | {item.discount_percent ?? 0}% off
                                                </div>
                                            ) : null}
                                        </td>
                                        <td>{item.product_created_at ?? "N/A"}</td>
                                        <td>
                                            <span className={statusClass[item.status] ?? "text-xs p-1 border rounded-md bg-gray-200 text-gray-900"}>
                                                {item.status ?? "Unknown"}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>

                        {(products?.links ?? []).length > 0 ? (
                            <div className="w-full pt-4">
                                <div className="flex w-full items-center justify-between gap-3">
                                    <div className="text-sm text-slate-700">
                                        {products?.total > 0
                                            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} items`
                                            : "No items found"}
                                    </div>
                                    <div className="flex items-center md:justify-end">
                                        <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                            {(products?.links ?? []).map((link, index) => (
                                                <button
                                                    key={`${link.label}-${index}`}
                                                    type="button"
                                                    disabled={!link.url}
                                                    className={`min-w-10 border-r border-slate-200 px-4 py-2 text-sm font-semibold transition ${
                                                        link.active
                                                            ? "bg-slate-100 text-blue-600"
                                                            : "bg-white text-slate-700 hover:bg-slate-50"
                                                    } disabled:cursor-not-allowed disabled:opacity-50`}
                                                    onClick={() => goToPage(link.url)}
                                                >
                                                    {link.label}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : null}
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
