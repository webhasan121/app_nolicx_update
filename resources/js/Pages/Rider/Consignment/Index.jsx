import { router } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import Container from "../../../components/dashboard/Container";
import Hr from "../../../components/Hr";
import useTranslation from "../../../hooks/useTranslation";

export default function RiderConsignmentIndexPage({ riderInfo = {}, orders = [], assignedConsignments = [] }) {
    const { t } = useTranslation();
    const formatCommission = (value) => {
        const amount = Number(value);

        return Number.isFinite(amount) ? amount.toFixed(2) : (value ?? "N/A");
    };

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
        <AppLayout title={t("Consignments")}>
            <Container>
                <div className="flex items-center justify-between p-2">
                    <div>
                        {totalConsignments ? (
                            <>{totalConsignments} consignment are available.</>
                        ) : (
                            <>No consignment found !</>
                        )}
                    </div>
                    <div>
                        <div className="inline px-2 py-1 text-sm text-white bg-indigo-900 shadow rounded-xl">
                            <i className="pr-2 fas fa-location"></i>{" "}
                            {riderInfo?.targeted_area_name ?? "N/A"}
                        </div>
                    </div>
                </div>

                <Hr />

                {orders.length ? (
                    <div className="mb-3 font-semibold">Available consignments</div>
                ) : null}

                <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                    {orders.map((order) => (
                            <div
                                key={order.id}
                                className="flex flex-col justify-between text-center bg-white rounded shadow"
                            >
                                <div className="py-2 bg-gray-200">
                                    <h3 className="text-xs text-gray-500">{t("Order ID")}</h3>
                                    <div className="font-bold">{order.display_id ?? order.id}</div>
                                </div>

                                <div className="p-2">
                                    <div className="flex items-center justify-center -space-x-2 overflow-hidden">
                                        {order.thumbnails.map((thumbnail, index) => (
                                            <img
                                                key={`${order.id}-${index}`}
                                                src={`/storage/${thumbnail}`}
                                                className="inline-block rounded-full size-10 ring-2 ring-white outline -outline-offset-1 outline-black/5"
                                                alt=""
                                            />
                                        ))}
                                    </div>
                                </div>

                                <div className="px-3 py-2">
                                    <div className="flex justify-center text-2xl font-bold">
                                        {order.display_total} Tk
                                    </div>
                                    <div className="text-sm text-center text-gray-500">
                                        <div>
                                            <span className="pl-1 font-bold">{order.total_for_not_resel ?? "N/A"}</span>
                                            <span className="px-1" style={{ lineHeight: "8px" }}>+</span>
                                            <span>{formatCommission(order.system_comission)}</span>
                                        </div>
                                        <div className="text-xs text-red-500">
                                            {t("Commission")} {formatCommission(order.system_comission)}
                                        </div>
                                    </div>
                                </div>

                                <div className="px-3 py-2">
                                    {order.created_at_formatted ? (
                                        <p className="text-xs">{order.created_at_formatted}</p>
                                    ) : null}
                                    <div className="text-xs text-gray-500">
                                        <i className="pr-1 fas fa-map-marker-alt"></i>
                                        {order.location ?? "N/A"}
                                    </div>
                                </div>

                                <div>
                                    <button
                                        type="button"
                                        className="w-full p-2 font-bold text-green-900 bg-green-200"
                                        onClick={() => confirmOrder(order.route_id ?? order.id)}
                                    >
                                        <i className="fas fa-check-circle ps-2"></i>{" "}
                                        {t("Picked")} ({order.shipping}TK)
                                    </button>
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
                            className="flex flex-col justify-between text-center bg-white rounded shadow"
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
                                <div className="flex items-center justify-center -space-x-2 overflow-hidden">
                                    {cod.images.map((image, index) => (
                                        <img
                                            key={`${cod.id}-${index}`}
                                            src={`/storage/${image}`}
                                            className="inline-block rounded-full size-10 ring-2 ring-white outline -outline-offset-1 outline-black/5"
                                            alt=""
                                        />
                                    ))}
                                </div>
                            </div>

                            <div className="px-3 py-2">
                                <div className="flex items-baseline justify-center gap-1 font-bold leading-none">
                                    <span className="text-2xl tabular-nums">{cod.display_total}</span>
                                    <span className="text-sm">Tk</span>
                                </div>
                                <div className="text-sm text-gray-500">
                                    {cod.total_for_not_resel ?? "N/A"} + {cod.system_comission ?? "N/A"}
                                </div>
                                <div className="text-xs text-red-500">
                                    {t("Commission")} {formatCommission(cod.system_comission)}
                                </div>
                            </div>

                            <div className="px-3 py-2">
                                <p className="text-xs">{cod.created_at_formatted}</p>
                                <div className="text-xs text-gray-500">
                                    <i className="pr-1 fas fa-map-marker-alt"></i>
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
