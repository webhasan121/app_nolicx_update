import { router } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import Container from "../../../components/dashboard/Container";
import Hr from "../../../components/Hr";
import PrimaryButton from "../../../components/PrimaryButton";

export default function RiderConsignmentIndexPage({ riderInfo = {}, orders = [], assignedConsignments = [] }) {
    const confirmOrder = (orderId) => {
        router.post(
            route("rider.consignment.confirm", { order: orderId }),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const changeStatus = (id, status) => {
        router.post(
            route("rider.consignment.status", { consignment: id }),
            { status },
            {
                preserveScroll: true,
            },
        );
    };

    const totalConsignments = orders.length + assignedConsignments.length;

    return (
        <AppLayout title="Consignments">
            <Container>
                <div className="flex justify-between items-center p-2">
                    <div>
                        {totalConsignments ? (
                            <>{totalConsignments} consignment are available.</>
                        ) : (
                            <>No consignment found !</>
                        )}
                    </div>
                    <div>
                        <div className="inline px-2 py-1 rounded-xl bg-indigo-900 text-white shadow text-sm">
                            <i className="fas fa-location pr-2"></i>{" "}
                            {riderInfo?.targeted_area_name ?? "N/A"}
                        </div>
                    </div>
                </div>

                <Hr />

                {orders.length ? (
                    <div className="mb-3 font-semibold">Available consignments</div>
                ) : null}

                <div className="grid gap-4" style={{ gridTemplateColumns: "repeat(auto-fit, 160px)" }}>
                    {orders.map((order) => (
                            <div
                                key={order.id}
                                className="bg-white rounded shadow text-center flex flex-col justify-between"
                            >
                                <div className="py-2 bg-gray-200">
                                    <h3 className="text-xs text-gray-500">
                                        Order ID
                                    </h3>
                                    <div className="font-bold">{order.id}</div>
                                </div>

                                <div className="p-2">
                                    <div className="flex justify-center items-center -space-x-2 overflow-hidden">
                                        {order.thumbnails.map((thumbnail, index) => (
                                            <img
                                                key={`${order.id}-${index}`}
                                                src={`/storage/${thumbnail}`}
                                                className="inline-block size-10 rounded-full ring-2 ring-white outline -outline-offset-1 outline-black/5"
                                                alt=""
                                            />
                                        ))}
                                    </div>
                                </div>

                                <div className="px-3 py-2">
                                    <div className="text-4xl font-bold">
                                        <sup>Tk</sup>
                                        {order.display_total}
                                    </div>
                                    <div className="text-sm text-gray-500 flex justify-center items-center text-center">
                                        <div className="pl-1 font-bold">
                                            {order.total_for_not_resel ?? "N/A"}
                                        </div>
                                        <div
                                            className="px-1"
                                            style={{ lineHeight: "8px" }}
                                        >
                                            +
                                        </div>
                                        <div className="flex justify-center items-cenrer">
                                            <div>
                                                {order.system_comission ?? "N/A"}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="px-3 py-2">
                                    <div className="text-xs text-gray-500">
                                        <i className="fas fa-map-marker-alt pr-1"></i>
                                        {order.location}
                                    </div>
                                </div>

                                <div className="p-1">
                                    <PrimaryButton
                                        onClick={() => confirmOrder(order.id)}
                                    >
                                        pick{" "}
                                        <div className="px-2 text-xs">
                                            ({order.shipping}TK)
                                        </div>
                                    </PrimaryButton>
                                </div>
                            </div>
                    ))}
                </div>

                {assignedConsignments.length ? (
                    <div className="mt-6 mb-3 font-semibold">Assigned consignments</div>
                ) : null}

                <div className="grid gap-4" style={{ gridTemplateColumns: "repeat(auto-fit, 160px)" }}>
                    {assignedConsignments.map((cod) => (
                        <div
                            key={cod.id}
                            className="bg-white rounded shadow text-center flex flex-col justify-between"
                        >
                            <div className="py-2 bg-gray-200">
                                <h3 className="text-xs text-gray-500">
                                    Order ID{" "}
                                    <a
                                        href={route("rider.consignment.view", { id: cod.id })}
                                        className="inline-block px-2 text-xs text-white bg-indigo-900 shadow rounded-xl"
                                    >
                                        View
                                    </a>
                                </h3>
                                <div className="font-bold">{cod.order_id}</div>
                            </div>

                            <div className="p-2">
                                <div className="flex justify-center items-center -space-x-2 overflow-hidden">
                                    {cod.images.map((image, index) => (
                                        <img
                                            key={`${cod.id}-${index}`}
                                            src={`/storage/${image}`}
                                            className="inline-block size-10 rounded-full ring-2 ring-white outline -outline-offset-1 outline-black/5"
                                            alt=""
                                        />
                                    ))}
                                </div>
                            </div>

                            <div className="px-3 py-2">
                                <div className="text-2xl font-bold">{cod.display_total} Tk</div>
                                <div className="text-sm text-gray-500">
                                    {cod.total_for_not_resel ?? "N/A"} + {cod.system_comission ?? "N/A"}
                                </div>
                            </div>

                            <div className="px-3 py-2">
                                <p className="text-xs">{cod.created_at_formatted}</p>
                                <div className="text-xs text-gray-500">
                                    <i className="fas fa-map-marker-alt pr-1"></i>
                                    {cod.location ?? "N/A"}
                                </div>
                            </div>

                            {cod.status === "Pending" ? (
                                <div className="pb-2">
                                    <button
                                        className="px-2 py-1 text-sm text-white bg-indigo-900 border rounded shadow"
                                        onClick={() => changeStatus(cod.id, "Received")}
                                    >
                                        Mark as Received
                                    </button>
                                </div>
                            ) : null}

                            {cod.status === "Received" ? (
                                <div className="pb-2">
                                    <button
                                        className="px-2 py-1 text-sm text-white bg-indigo-900 border rounded shadow"
                                        onClick={() => changeStatus(cod.id, "Completed")}
                                    >
                                        Mark as Delivered
                                    </button>
                                </div>
                            ) : null}

                            {cod.status === "Completed" ? (
                                <p className="p-2 font-bold text-green-900 bg-green-200">
                                    <i className="fas fa-check-circle ps-2"></i> Earn ({cod.shipping}TK)
                                </p>
                            ) : null}
                        </div>
                    ))}
                </div>
            </Container>
        </AppLayout>
    );
}
