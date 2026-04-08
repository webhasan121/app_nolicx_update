import { Head, router } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import { useState } from "react";

export default function Index({ filters, comissions }) {
    const [showFilterModal, setShowFilterModal] = useState(false);
    const [where, setWhere] = useState(filters?.where ?? "");
    const [confirm, setConfirm] = useState(filters?.confirm ?? "");
    const [wid, setWid] = useState(filters?.wid ?? "");

    const apply = (next = {}) => {
        router.get(
            route("system.comissions.index"),
            {
                confirm: next.confirm ?? filters?.confirm ?? "",
                where: next.where ?? filters?.where ?? "",
                from: next.from ?? filters?.from ?? "",
                to: next.to ?? filters?.to ?? "",
                wid: next.wid ?? filters?.wid ?? "",
                page: next.page ?? undefined,
            },
            { preserveScroll: true, preserveState: true }
        );
    };

    const openPrintable = () => {
        window.open(
            route("system.comissions.takes", {
                confirm: filters?.confirm ?? "",
                where: filters?.where ?? "",
                from: filters?.from ?? "",
                to: filters?.to ?? "",
                wid: filters?.wid ?? "",
            }),
            "_blank"
        );
    };

    return (
        <AppLayout
            title="Comissions"
            header={
                <PageHeader>
                    <div className="flex justify-between">
                        <div>Comissions</div>
                    </div>
                </PageHeader>
            }
        >
            <Head title="Comissions" />

            <Container>
                <div className="flex justify-between items-end mb-4">
                    <div>
                        <PrimaryButton type="button" onClick={() => setShowFilterModal(true)}>
                            <i className="fas fa-filter"></i>
                        </PrimaryButton>
                    </div>
                    <div className="flex justify-start items-end mb-2 space-x-1">
                        <PrimaryButton type="button" onClick={openPrintable} className="btn">
                            <i className="fas fa-print"></i>
                        </PrimaryButton>

                        <div>
                            <TextInput
                                className=" py-1 w-full "
                                type="date"
                                value={filters?.from ?? ""}
                                onChange={(e) => apply({ from: e.target.value })}
                            />
                        </div>

                        <div>
                            <TextInput
                                className=" py-1 w-full "
                                type="date"
                                value={filters?.to ?? ""}
                                onChange={(e) => apply({ to: e.target.value })}
                            />
                        </div>
                    </div>
                </div>

                <Hr className="my-2" />

                <SectionInner>
                    <div>
                        <Table data={[comissions?.summary ?? {}]}>
                            <thead>
                                <tr>
                                    <th>Seller Total Profit</th>
                                    <th>Cut comission</th>
                                    <th>Distribute</th>
                                    <th>Store</th>
                                    <th>Return</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>{comissions?.summary?.profit ?? 0}</td>
                                    <td>{comissions?.summary?.take_comission ?? 0}</td>
                                    <td>{comissions?.summary?.distribute_comission ?? 0}</td>
                                    <td>{comissions?.summary?.store ?? 0}</td>
                                    <td>{comissions?.summary?.return ?? 0}</td>
                                </tr>
                            </tbody>
                        </Table>
                    </div>
                </SectionInner>

                <Section id="pdf-content">
                    <Hr />
                    <div>
                        {(comissions?.links ?? []).map((link) =>
                            link.url ? (
                                <button
                                    type="button"
                                    key={`${link.label}-${link.url}`}
                                    className={`px-2 py-1 mx-1 border rounded ${link.active ? "bg-orange-500 text-white" : ""}`}
                                    onClick={() => {
                                        const url = new URL(link.url);
                                        apply({
                                            confirm: url.searchParams.get("confirm") ?? filters?.confirm ?? "",
                                            where: url.searchParams.get("where") ?? filters?.where ?? "",
                                            from: url.searchParams.get("from") ?? filters?.from ?? "",
                                            to: url.searchParams.get("to") ?? filters?.to ?? "",
                                            wid: url.searchParams.get("wid") ?? filters?.wid ?? "",
                                            page: url.searchParams.get("page") ?? undefined,
                                        });
                                    }}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ) : (
                                <span
                                    key={`${link.label}-disabled`}
                                    className="px-2 py-1 mx-1 text-gray-400 border rounded"
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            )
                        )}
                    </div>
                    <Table data={comissions?.data ?? []}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>DT</th>
                                <th>ID</th>
                                <th>Order</th>
                                <th>Product</th>
                                <th>Buy</th>
                                <th>Sell</th>
                                <th>Profit</th>
                                <th>Rate</th>
                                <th>Take</th>
                                <th>Give</th>
                                <th>Store</th>
                                <th>Return</th>
                                <th>Confirmed</th>
                                <th>A/C</th>
                            </tr>
                        </thead>

                        <tbody>
                            {(comissions?.data ?? []).map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.created_at_formatted}</td>
                                    <td>{item.id ?? "N/A"}</td>
                                    <td>{item.order_id ?? 0}</td>
                                    <td>{item.product_id ?? 0}</td>
                                    <td>{item.buying_price ?? 0}</td>
                                    <td>{item.selling_price ?? 0}</td>
                                    <td>{item.profit ?? 0}</td>
                                    <td>{item.comission_range ?? 0} %</td>
                                    <td>{item.take_comission ?? 0}</td>
                                    <td>{item.distribute_comission ?? 0}</td>
                                    <td>{item.store ?? 0}</td>
                                    <td>{item.return ?? 0}</td>
                                    <td>
                                        {item.confirmed ? (
                                            <>
                                                <span className="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>
                                                <NavLink href={route("system.comissions.take.refund", { id: item.id })}>
                                                    {" "}Refund
                                                </NavLink>
                                            </>
                                        ) : (
                                            <>
                                                <span className="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>
                                                <form action={route("system.comissions.take.confirm", { id: item.id })} method="post">
                                                    <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content ?? ""} />
                                                    <button type="submit">Confirm</button>
                                                </form>
                                            </>
                                        )}
                                    </td>
                                    <td>
                                        <div className="flex space-x-2">
                                            <NavLink href={route("system.comissions.distributes", { id: item.id })}>
                                                Details
                                            </NavLink>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>{comissions?.summary?.buying_price}</th>
                                <th>{comissions?.summary?.selling_price}</th>
                                <th>{comissions?.summary?.profit}</th>
                                <td></td>
                                <th>{comissions?.summary?.take_comission}</th>
                                <th>{comissions?.summary?.distribute_comission}</th>
                                <th>{comissions?.summary?.store}</th>
                                <th>{comissions?.summary?.return}</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </Table>
                </Section>
            </Container>

            <Modal show={showFilterModal} onClose={() => setShowFilterModal(false)}>
                <div className="p-3">Filter Comissions</div>
                <Hr className="my-1" />

                <div className="p-3">
                    <div className="flex items-start justify-between my-2 space-x-1">
                        <div>
                            <select
                                value={where}
                                onChange={(e) => {
                                    setWhere(e.target.value);
                                    apply({ where: e.target.value, wid, confirm });
                                }}
                                className="w-full rounded-md py-1"
                            >
                                <option value="">-- Select -- </option>
                                <option value="user_id">User</option>
                                <option value="product_id">Product</option>
                                <option value="order_id">Order</option>
                            </select>
                        </div>
                        <div>
                            <select
                                value={confirm}
                                onChange={(e) => {
                                    setConfirm(e.target.value);
                                    apply({ confirm: e.target.value, where, wid });
                                }}
                                className="py-1 rounded-md"
                            >
                                <option value="All">Both</option>
                                <option value="true">Confirmed</option>
                                <option value="false">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <TextInput
                            className="w-full"
                            placeholder="Search By ID"
                            value={wid}
                            onChange={(e) => {
                                setWid(e.target.value);
                                apply({ wid: e.target.value, where, confirm });
                            }}
                        />
                    </div>
                </div>
                <Hr className="my-1" />
                <div className="p-3">
                    <div className="flex items-center justify-end w-full space-x-1">
                        <SecondaryButton type="button" onClick={() => setShowFilterModal(false)}>
                            Cancel
                        </SecondaryButton>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}
