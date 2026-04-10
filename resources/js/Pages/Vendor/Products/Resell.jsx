import { Head } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import NavLink from "../../../components/NavLink";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";

function ProductNavigations({ productId, nav = "Resell" }) {
    return (
        <div className="flex ">
            <NavLink
                href={route("vendor.products.edit", {
                    product: productId,
                    nav: "Product",
                })}
                active={nav === "Product"}
            >
                Product
            </NavLink>

            <div>
                <NavLink
                    href={route("vendor.products.resell", { product: productId })}
                    active={nav === "Resell"}
                >
                    Resell
                </NavLink>
            </div>
        </div>
    );
}

export default function Resell({ act, productData = {}, rows = [] }) {
    return (
        <AppLayout
            title="Product Resell"
            header={
                <PageHeader>
                    Product Resell
                    <br />
                    <ProductNavigations productId={productData.encrypted_id} />
                </PageHeader>
            }
        >
            <Head title="Product Resell" />

            <Container>
                <Section>
                    <SectionHeader
                        title=""
                        content={
                            <div>
                                {act === "vendor" ? (
                                    <>{productData.resel_count ?? 0} Seller.</>
                                ) : productData.resel_from_shop ? (
                                    <>
                                        Resel from{" "}
                                        <strong>{productData.resel_from_shop}</strong>{" "}
                                        at {productData.resel_from_date}
                                    </>
                                ) : null}
                            </div>
                        }
                    />

                    {act === "vendor" ? (
                        <SectionInner>
                            <Table data={rows}>
                                <thead>
                                    <tr>
                                        <th>Relation ID</th>
                                        <th>Date</th>
                                        <th>Reseller ID</th>
                                        <th>Main Price</th>
                                        <th>Reseller Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.id}</td>
                                            <td>{item.created_at_formatted}</td>
                                            <td>{item.user_id}</td>
                                            <td>{item.main_price}</td>
                                            <td>{item.reseller_price}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </SectionInner>
                    ) : null}
                </Section>
            </Container>
        </AppLayout>
    );
}
