import { Head, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AppLayout from "../../../Layouts/App";
import NavLink from "../../../components/NavLink";
import NavLinkBtn from "../../../components/NavLinkBtn";
import TextInput from "../../../components/TextInput";
import PageHeader from "../../../components/dashboard/PageHeader";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";

export default function Index({ products, filters }) {
    const [nav, setNav] = useState(filters?.nav ?? "own");
    const [pd, setPd] = useState(filters?.pd ?? "Active");
    const [search, setSearch] = useState(filters?.search ?? "");

    useEffect(() => {
        setNav(filters?.nav ?? "own");
        setPd(filters?.pd ?? "Active");
        setSearch(filters?.search ?? "");
    }, [filters?.nav, filters?.pd, filters?.search]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            router.get(
                route("reseller.products.list"),
                { nav, pd, search: search || undefined },
                { preserveScroll: true, preserveState: true, replace: true }
            );
        }, 400);

        return () => clearTimeout(timeout);
    }, [nav, pd, search]);

    return (
        <AppLayout
            title="Products"
            header={
                <PageHeader>
                    <div className="flex justify-between items-start">
                        Products

                        <div className="flex space-x-1">
                            <NavLinkBtn href={route("vendor.products.create")}>
                                <i className="fas fa-plus pr-2"></i> New
                            </NavLinkBtn>
                            <NavLinkBtn href={route("reseller.resel-product.index")}>
                                Recel from vendor
                            </NavLinkBtn>
                        </div>
                    </div>
                    <br />

                    <NavLink
                        href={route("reseller.products.list", { nav: "own" })}
                        active={nav === "own"}
                    >
                        Your Product
                    </NavLink>
                    <NavLink
                        href={route("reseller.products.list", { nav: "resel" })}
                        active={nav === "resel"}
                    >
                        Resel Product
                    </NavLink>
                </PageHeader>
            }
        >
            <Head title="Products" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-center">
                                <TextInput
                                    type="search"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Search by name"
                                    className="mx-2 hidden lg:block py-1"
                                />
                            </div>
                        }
                        content={
                            <div className="flex justify-between items-center">
                                <div>
                                    <NavLink
                                        href={route("reseller.products.list", {
                                            nav,
                                            pd: "Active",
                                            search: search || undefined,
                                        })}
                                        active={pd === "Active"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setPd("Active");
                                        }}
                                    >
                                        Active
                                    </NavLink>
                                    <NavLink
                                        href={route("reseller.products.list", {
                                            nav,
                                            pd: "Trash",
                                            search: search || undefined,
                                        })}
                                        active={pd === "Trash"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setPd("Trash");
                                        }}
                                    >
                                        Trash
                                    </NavLink>
                                </div>
                            </div>
                        }
                    />
                    <SectionInner>
                        <Table data={products?.data ?? []}>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>In Stock</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th>Cost</th>
                                    <th>Price</th>
                                    <th>Sel Price</th>
                                    <th>Insert At</th>
                                    <th>A/C</th>
                                </tr>
                            </thead>

                            <tbody>
                                {(products?.data ?? []).map((product, index) => (
                                    <tr key={product.id}>
                                        <td>
                                            <input
                                                type="checkbox"
                                                className="rounded"
                                                value={product.id}
                                                style={{ width: 20, height: 20 }}
                                                onChange={() => {}}
                                            />
                                        </td>
                                        <td>{index + 1}</td>
                                        <td>
                                            <div className="flex items-start">
                                                <img
                                                    className="w-8 h-8 rounded-md shadow"
                                                    src={product.thumbnail ? `/storage/${product.thumbnail}` : ""}
                                                    alt=""
                                                />
                                            </div>
                                        </td>
                                        <td>{product.unit}</td>
                                        <td>
                                            <p>{product.name ?? "N/A"}</p>
                                            {product.has_pending && (
                                                <a
                                                    title={`Pending Order #${product.first_order_id ?? ""}`}
                                                    className="rounded text-white px-1 bg-red-900 mr-1 inline-flex text-xs block"
                                                >
                                                    {product.first_order_id ?? "N\\A"}
                                                </a>
                                            )}
                                            {product.has_accept && (
                                                <a
                                                    title={`Accept Order #${product.first_order_id ?? ""}`}
                                                    className="rounded text-white px-1 bg-green-900 mr-1 inline-flex text-xs block"
                                                >
                                                    {product.first_order_id ?? "N\\A"}
                                                </a>
                                            )}
                                        </td>
                                        <td>{product.status_label}</td>
                                        <td>{product.orders_count}</td>
                                        <td>{product.buying_price}</td>
                                        <td>{product.price}</td>
                                        <td>{product.offer_type ? product.discount : product.price}</td>
                                        <td>{product.created_at_human}</td>
                                        <td>
                                            <NavLink
                                                href={route("reseller.products.edit", {
                                                    id: product.encrypted_id,
                                                })}
                                            >
                                                edit
                                            </NavLink>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
