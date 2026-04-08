import { Head, router } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Div from "../../../../components/dashboard/overview/Div";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

export default function Index({ widgets = [], filters = {}, cod }) {
    const applyFilters = (next) => {
        router.get(route("system.consignment.index"), next, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            title="Consignment"
            header={<PageHeader>Consignment</PageHeader>}
        >
            <Head title="Consignment" />

            <Container>
                <Section>
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        {widgets.map((widget) => (
                            <Div
                                key={widget.title}
                                title={widget.title}
                                content={widget.value}
                            />
                        ))}
                    </div>
                </Section>
            </Container>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between gap-2">
                                <div className="flex items-center gap-2">
                                    <select
                                        value={filters.type ?? "Pending"}
                                        onChange={(e) =>
                                            applyFilters({
                                                type: e.target.value,
                                                sdate: filters.sdate,
                                                edate: filters.edate,
                                            })
                                        }
                                        className="py-1 rounded-md"
                                    >
                                        <option value="All">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Received">Received</option>
                                        <option value="Completed">Complete</option>
                                        <option value="Returned">Returned</option>
                                    </select>
                                </div>

                                <div className="flex gap-2 items-center">
                                    <TextInput
                                        type="date"
                                        value={filters.sdate ?? ""}
                                        onChange={(e) =>
                                            applyFilters({
                                                type: filters.type,
                                                sdate: e.target.value,
                                                edate: filters.edate,
                                            })
                                        }
                                    />
                                    <TextInput
                                        type="date"
                                        value={filters.edate ?? ""}
                                        onChange={(e) =>
                                            applyFilters({
                                                type: filters.type,
                                                sdate: filters.sdate,
                                                edate: e.target.value,
                                            })
                                        }
                                    />
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        {(cod?.links ?? []).length ? (
                            <div className="mb-3 flex flex-wrap items-center gap-2">
                                {cod.links.map((link, index) =>
                                    link.url ? (
                                        <button
                                            key={`${link.label}-${index}`}
                                            type="button"
                                            className={`px-3 py-1 border rounded ${
                                                link.active ? "bg-orange-500 text-white border-orange-500" : "bg-white"
                                            }`}
                                            onClick={() =>
                                                router.visit(link.url, {
                                                    preserveState: true,
                                                    preserveScroll: true,
                                                })
                                            }
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ) : (
                                        <span
                                            key={`${link.label}-${index}`}
                                            className="px-3 py-1 border rounded text-gray-400"
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    )
                                )}
                            </div>
                        ) : null}

                        <Table data={cod?.data ?? []} table-border="1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Order ID</th>
                                    <th>Rider</th>
                                    <th>Amount</th>
                                    <th>Rider Amount</th>
                                    <th>Total</th>
                                    <th>Comission</th>
                                    <th>C Rate</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>A/C</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(cod?.data ?? []).map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{item.id}</td>
                                        <td>{item.order_id}</td>
                                        <td>{item.rider_name}</td>
                                        <td>{item.amount}</td>
                                        <td>{item.rider_amount}</td>
                                        <td>{item.total_amount}</td>
                                        <td>{item.system_comission}</td>
                                        <td>{item.comission}</td>
                                        <td>{item.status}</td>
                                        <td>{item.created_at_formatted}</td>
                                        <td>
                                            <div className="flex gap-2 items-center">
                                                <DangerButton type="button">
                                                    <i className="fas fa-trash"></i>
                                                </DangerButton>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot className="bg-cyan-300">
                                <tr>
                                    <td>{cod?.summary?.count ?? 0}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{cod?.summary?.amount ?? 0}</td>
                                    <td>{cod?.summary?.rider_amount ?? 0}</td>
                                    <td>{cod?.summary?.total_amount ?? 0}</td>
                                    <td>{cod?.summary?.system_comission ?? 0}</td>
                                    <td>{cod?.summary?.comission ?? 0}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </Table>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
