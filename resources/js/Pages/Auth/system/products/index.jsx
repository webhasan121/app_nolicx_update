import { router, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

function buildQuery(filters, updates = {}) {
    return Object.fromEntries(
        Object.entries({ ...filters, ...updates }).filter(
            ([, value]) => value !== "" && value !== null && value !== undefined
        )
    );
}

export default function Index() {
    const {
        filters = {
            filter: "Active",
            from: "all",
            find: "",
            isIncludeResel: true,
        },
        products = {},
    } = usePage().props;

    const [from, setFrom] = useState(filters.from ?? "all");
    const [find, setFind] = useState(filters.find ?? "");
    const rows = products.data ?? [];

    const visit = (updates = {}) => {
        router.get(route("system.products.index"), buildQuery(filters, updates), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const gotoPage = (page) => {
        visit({ page });
    };

    useEffect(() => {
        if ((filters.find ?? "") === find) {
            return;
        }

        const timeout = setTimeout(() => {
            visit({
                from,
                find,
                page: 1,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [find, from]);

    return (
        <AppLayout
            title="Products"
            header={
                <PageHeader>
                    <div className="flex justify-between items-center">
                        <div>
                            Products
                            <br />
                            <NavLink href={route("reseller.resel-product.index")}>
                                Browse
                            </NavLink>
                        </div>

                        <NavLinkBtn href="#">
                            <i className="fas fa-filter pr-2"></i> Filter
                        </NavLinkBtn>
                    </div>
                </PageHeader>
            }
        >
            <div>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-center">
                                <div className="flex items-center">
                                    <select
                                        value={from}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setFrom(value);
                                            visit({
                                                from: value,
                                                page: 1,
                                            });
                                        }}
                                        id=""
                                    >
                                        <option value="all">All</option>
                                        <option value="vendor">Vendor</option>
                                        <option value="reseller">Reseller</option>
                                    </select>
                                    <TextInput
                                        type="search"
                                        placeholder="ID"
                                        value={find}
                                        onChange={(e) => setFind(e.target.value)}
                                    />
                                </div>

                                <div></div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <div className="md:flex ">
                            <div className=" block md:hidden">
                                <div className="flex">
                                    <input
                                        type="radio"
                                        value="Active"
                                        id="active"
                                        style={{ width: "20px", height: "20px" }}
                                        className="mr-3"
                                        checked={filters.filter === "Active"}
                                        onChange={() =>
                                            visit({ filter: "Active", page: 1 })
                                        }
                                    />
                                    <div> Active </div>
                                </div>
                                <div className="flex mt-2">
                                    <input
                                        type="radio"
                                        value="Disable"
                                        id="disable"
                                        style={{ width: "20px", height: "20px" }}
                                        className="mr-3"
                                        checked={filters.filter === "Disable"}
                                        onChange={() =>
                                            visit({ filter: "Disable", page: 1 })
                                        }
                                    />
                                    <div> Disable </div>
                                </div>
                            </div>
                            <div
                                className="hidden md:block text-start"
                                style={{ width: "160px", textAling: "left" }}
                            >
                                <div
                                    style={{
                                        display: "grid",
                                        justifyContent: "center",
                                        gridTemplateColumns:
                                            "repeat(auto-fill, 150px))",
                                        gridGap: "10px",
                                    }}
                                >
                                    <div>
                                        <div className="text-xs">status</div>
                                        <div className="flex">
                                            <input
                                                type="radio"
                                                value="Active"
                                                id="lactive"
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                className="mr-3"
                                                checked={
                                                    filters.filter === "Active"
                                                }
                                                onChange={() =>
                                                    visit({
                                                        filter: "Active",
                                                        page: 1,
                                                    })
                                                }
                                            />
                                            <div> Active </div>
                                        </div>
                                        <div className="flex mt-2">
                                            <input
                                                type="radio"
                                                value="Disable"
                                                id="ldisable"
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                className="mr-3"
                                                checked={
                                                    filters.filter ===
                                                    "Disable"
                                                }
                                                onChange={() =>
                                                    visit({
                                                        filter: "Disable",
                                                        page: 1,
                                                    })
                                                }
                                            />
                                            <div> Disable </div>
                                        </div>
                                        <div className="flex mt-2">
                                            <input
                                                type="radio"
                                                value="both"
                                                id="ldisable"
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                className="mr-3"
                                                checked={
                                                    filters.filter === "both"
                                                }
                                                onChange={() =>
                                                    visit({
                                                        filter: "both",
                                                        page: 1,
                                                    })
                                                }
                                            />
                                            <div> Both </div>
                                        </div>
                                    </div>
                                    {from === "reseller" ? (
                                        <div>
                                            <hr />
                                            <div className="text-xs">
                                                reseller
                                            </div>
                                            <div className="flex">
                                                <input
                                                    type="checkbox"
                                                    value="true"
                                                    id="isResel"
                                                    style={{
                                                        width: "20px",
                                                        height: "20px",
                                                    }}
                                                    className="mr-3"
                                                    checked={
                                                        filters.isIncludeResel
                                                    }
                                                    onChange={(e) =>
                                                        visit({
                                                            isIncludeResel:
                                                                e.target.checked,
                                                            page: 1,
                                                        })
                                                    }
                                                />
                                                <div> Include Resel </div>
                                            </div>

                                            <hr />
                                        </div>
                                    ) : null}
                                    <div>
                                        <div className="text-xs">
                                            order status
                                        </div>
                                        <div className="flex">
                                            <input
                                                type="checkbox"
                                                value="Active"
                                                id="accept"
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                className="mr-3"
                                            />
                                            <div> Accept </div>
                                        </div>
                                        <div className="flex mt-2">
                                            <input
                                                type="checkbox"
                                                value="Disable"
                                                id="pending"
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                className="mr-3"
                                            />
                                            <div> Pending </div>
                                        </div>
                                        <div className="flex mt-2">
                                            <input
                                                type="checkbox"
                                                value="Disable"
                                                id="cancel"
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                className="mr-3"
                                            />
                                            <div> Cancel </div>
                                        </div>
                                        <div className="flex mt-2">
                                            <input
                                                type="checkbox"
                                                value="Disable"
                                                id="reject"
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                className="mr-3"
                                            />
                                            <div> Reject </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="w-full">
                                <div className="flex justify-between items-center mb-2">
                                    <div className="text-sm text-gray-600">
                                        Page {products.current_page ?? 1} of{" "}
                                        {products.last_page ?? 1}
                                    </div>
                                    <div className="space-x-2">
                                        <SecondaryButton
                                            type="button"
                                            onClick={() =>
                                                gotoPage(
                                                    (products.current_page ??
                                                        1) - 1
                                                )
                                            }
                                            disabled={!products.prev_page_url}
                                        >
                                            Prev
                                        </SecondaryButton>
                                        <SecondaryButton
                                            type="button"
                                            onClick={() =>
                                                gotoPage(
                                                    (products.current_page ??
                                                        1) + 1
                                                )
                                            }
                                            disabled={!products.next_page_url}
                                        >
                                            Next
                                        </SecondaryButton>
                                    </div>
                                </div>
                                <Table data={rows}>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Owner</th>
                                            <th>Price</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {rows.map((item, index) => (
                                            <tr key={item.id}>
                                                <td>{index + 1}</td>
                                                <td>{item.id}</td>

                                                <td>
                                                    <NavLink
                                                        className="text-xs"
                                                        href={route(
                                                            "products.details",
                                                            {
                                                                id:
                                                                    item.id ??
                                                                    "",
                                                                slug:
                                                                    item.slug ??
                                                                    "",
                                                            }
                                                        )}
                                                    >
                                                        {item.thumbnail ? (
                                                            <img
                                                                width="30px"
                                                                height="30px"
                                                                src={
                                                                    item.thumbnail
                                                                }
                                                                alt=""
                                                                className="mr-2 rounded-full"
                                                            />
                                                        ) : null}
                                                        {item.name ?? "N/A"}
                                                    </NavLink>
                                                    <br />
                                                    <div className="text-xs border rounded inline-block">
                                                        {item.status ?? "N/A"}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div>
                                                        <div className="text-gray-700 ">
                                                            {item.owner_name}
                                                        </div>
                                                        {item.is_resel_count ? (
                                                            <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                                <i className="fas fa-caret-left"></i>
                                                                R
                                                            </span>
                                                        ) : null}
                                                        {item.resel_count ? (
                                                            <span className="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                                {
                                                                    item.resel_count
                                                                }
                                                                <i className="fas fa-caret-right"></i>
                                                            </span>
                                                        ) : null}
                                                        <div className="text-xs">
                                                            {
                                                                item.belongs_to_type
                                                            }
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    {item.price ?? 0} TK
                                                    {item.discount_meta ? (
                                                        <div className="flex items-center text-center p-1 rounded bg-gray-100 text-xs">
                                                            D:{" "}
                                                            {
                                                                item
                                                                    .discount_meta
                                                                    .discount
                                                            }{" "}
                                                            |{" "}
                                                            {
                                                                item
                                                                    .discount_meta
                                                                    .off_percent
                                                            }
                                                            % off
                                                        </div>
                                                    ) : null}
                                                </td>
                                                <td>
                                                    {
                                                        item.created_at_formatted
                                                    }
                                                </td>
                                                <td>
                                                    <div className="flex">
                                                        <NavLink
                                                            href={route(
                                                                "system.products.edit",
                                                                {
                                                                    product:
                                                                        item.id,
                                                                }
                                                            )}
                                                        >
                                                            View
                                                        </NavLink>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </Table>
                            </div>
                        </div>
                    </SectionInner>
                </SectionSection>
            </div>
        </AppLayout>
    );
}
