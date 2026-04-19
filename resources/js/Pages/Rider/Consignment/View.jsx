import AppLayout from "../../../Layouts/App";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import Hr from "../../../components/Hr";
import NavLinkBtn from "../../../components/NavLinkBtn";

export default function RiderConsignmentViewPage({
    id,
    cod = {},
    order = {},
    cartOrders = [],
    seller = {},
    buyer = {},
    shop = {},
}) {
    const shopAddress =
        shop?.address ||
        [shop?.district, shop?.upozila, shop?.village].filter(Boolean).join(", ");

    return (
        <AppLayout
            title={`Consignment #${id}`}
            header={
                <PageHeader>
                    <div className="md:flex justify-between items-center">
                        <div>
                            Consignment #{id}{" "}
                            <i className="fas fa-angle-right px-2"></i> assign at{" "}
                            {order?.id}
                        </div>

                        <div className="flex gap-2">
                            <NavLinkBtn href={route("rider.consignment")}>
                                <i className="fas fa-angle-left pr-2"></i> Back
                            </NavLinkBtn>
                        </div>
                    </div>
                </PageHeader>
            }
        >
            <Container>
                <div className="flex flex-wrap">
                    <div className="m-1 rounded w-72 bg-white">
                        <div className="p-2 px-4">
                            Sender ({shop?.shop_name_en ?? "N/A"})
                        </div>
                        <hr />
                        <div className="p-2 px-4">
                            <p className="text-gray-800 mb-2">
                                {seller?.name ?? "N/A"}
                            </p>

                            <p></p>
                            <p className="text-gray-600 text-sm">{shopAddress}</p>
                            <h6>{shop?.phone}</h6>
                        </div>
                    </div>

                    <div className="m-1 rounded w-72 bg-white">
                        <div className="p-2 px-4">Destination (Buyer)</div>
                        <hr />
                        <div className="p-2 px-4">
                            <div className="mb-2">{buyer?.name ?? "N/A"}</div>
                            <p className="text-gray-600">{order?.location}</p>
                            <p>{order?.number}</p>
                        </div>
                    </div>
                </div>

                <Hr />

                <Section>
                    <SectionHeader
                        title={`Products (${cartOrders.length})`}
                        content="View products of the order."
                    />

                    <SectionInner>
                        <Table data={cartOrders}>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                </tr>
                            </thead>
                            <tbody>
                                {cartOrders.map((item) => (
                                    <tr key={item.id}>
                                        <td>
                                            <div className="flex">
                                                {item.product?.thumbnail ? (
                                                    <img
                                                        src={`/storage/${item.product.thumbnail}`}
                                                        className="w-12 h-12 mr-2"
                                                        alt=""
                                                    />
                                                ) : null}
                                                {item.product?.title ?? "N/A"}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>

                <Hr />

                <Section>
                    <SectionHeader title="Consignment Amount" content="" />
                    <SectionInner>
                        <Table data={[cod]}>
                            <tbody>
                                <tr className="border-b text-end">
                                    <td>Product Price</td>
                                    <th>{cod?.amount ?? 0} TK</th>
                                </tr>
                                <tr className="border-b text-end">
                                    <td>Paid</td>
                                    <th>{cod?.paid_amount ?? 0} TK</th>
                                </tr>
                                <tr className="border-b text-end">
                                    <td>Due</td>
                                    <th>{cod?.due_amount ?? 0} TK</th>
                                </tr>
                                <tr className="bg-gray-200 text-end">
                                    <td>Sub-Total</td>
                                    <th>{cod?.due_amount ?? 0} TK</th>
                                </tr>
                                <tr className="text-end">
                                    <td>Comission</td>
                                    <th>{cod?.system_comission ?? 0} TK</th>
                                </tr>
                                <tr className="bg-gray-300 text-end">
                                    <td>Total</td>
                                    <th>{cod?.total_amount ?? 0} TK</th>
                                </tr>
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
